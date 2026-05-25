<?php  
declare (strict_types = 1);

namespace app\admin\middleware;

class UploadCheck{
	public function handle($request, \Closure $next)
	{
		$files = $request->file();
		foreach ($files as $file) {
			if ($file->getSize() > config('video.max_size')) {
				return json(['code' => 400, 'msg' => '文件过大']);
			}
		}
		return $next($request);
	}
}
?>