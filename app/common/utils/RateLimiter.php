<?php
declare(strict_types=1);
namespace app\common\utils;

use app\common\model\LoginAttempt;
use RuntimeException;

final class RateLimiter
{
	public function __construct(
		private int $maxAttempts = 5,
		private int $lockoutSeconds = 900
	) {}

	public function assertNotLocked(string $identifier, string $action): void
	{
		$row = LoginAttempt::where('identifier', $identifier)
			->where('action_type', $action)
			->find();

		if ($row === null) {
			return;
		}

		$elapsed = time() - (int) $row->last_attempt_at;

		if ($elapsed >= $this->lockoutSeconds) {
			return;
		}

		if ((int) $row->attempt_count >= $this->maxAttempts) {
			throw new RuntimeException(
				'尝试次数过多，请 ' . (int) ceil(($this->lockoutSeconds - $elapsed) / 60) . ' 分钟后再试。'
			);
		}
	}

	public function hit(string $identifier, string $action): int
	{
		$row = LoginAttempt::where('identifier', $identifier)
			->where('action_type', $action)
			->find();
		$now = time();

		if ($row === null || ($now - (int) $row->last_attempt_at) >= $this->lockoutSeconds) {
			// 用 replace 实现 INSERT ... ON DUPLICATE KEY UPDATE
			LoginAttempt::create([
				'identifier'      => $identifier,
				'action_type'     => $action,
				'attempt_count'   => 1,
				'last_attempt_at' => $now,
			]);   // true = replace

			return 1;
		}

		$count = (int) $row->attempt_count + 1;

		LoginAttempt::where('identifier', $identifier)
			->where('action_type', $action)
			->update([
				'attempt_count'   => $count,
				'last_attempt_at' => $now,
			]);

		return $count;
	}

	public function clear(string $identifier, string $action): void
	{
		LoginAttempt::where('identifier', $identifier)
			->where('action_type', $action)
			->delete();
	}
}