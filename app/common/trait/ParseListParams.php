<?php  
declare (strict_types = 1);
namespace app\common\trait;

trait ParseListParams
{
    /**
     * 把请求参数分拣成 service 需要的结构
     *
     * @param array $filterKeys  允许作为 where 条件的字段白名单
     * @param array $controlKeys 控制参数（分页/排序等）
     */
    protected function parseListParams(array $filterKeys, array $controlKeys = []): array
    {
        $request = request();

        $controlKeys = $controlKeys ?: [
            'page', 'limit', 'order', 'field', 'with', 'withCount', 'isTrashed',
        ];

        $control = $request->only($controlKeys);

        // 筛选字段：白名单 + 去空
        $filter = $request->only($filterKeys);
        $filter = array_filter($filter, fn($v) => $v !== '' && $v !== null);

        return array_merge($control, [
            'where'     => $filter,
            'keyword'   => trim((string)$request->param('keyword', '')),
            'startTime' => $request->param('startDate', ''),
            'endTime'   => $request->param('endDate', ''),
            'timeType'  => $request->param('timeType', ''),
        ]);
    }
}

?>