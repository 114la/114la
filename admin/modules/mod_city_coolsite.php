<?php
/**
 * 城市导航酷站管理
 *
 * @since 2011-01-10
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_city_coolsite
{
	/**
	 * 获取列表
	 *
	 * @return array
	*/
    public static function get_list($cityclass_id = 0, $coolclass_id = 0, $isend = false, $start = 0, $num = 0, $extends = '')
	{
		$condition = '';
		// 分类
        if ($cityclass_id > 0)
		{
            $condition .= (!empty($condition)) ? " AND a.`cityclass_id` = {$cityclass_id}" :
            " AND a.`cityclass_id` = {$cityclass_id}";
		}
        if ($coolclass_id > 0)
		{
            $condition .= (!empty($condition)) ? " AND a.`coolclass_id` = {$coolclass_id}" :
            " AND a.`coolclass_id` = {$coolclass_id}";
		}

        // 过期
		if ($isend)
		{
			$condition .= (!empty($condition)) ? ' AND a.`endtime` > 0 AND a.`endtime` < ' . time() :
												 ' AND a.`endtime` > 0 AND a.`endtime` < ' . time();
		}

        $condition .= ' ORDER BY a.cityclass_id, a.displayorder';
		if ($start > -1 && $num > 0)
		{
			$condition .= " LIMIT {$start}, {$num}";
		}

        $sql = 'SELECT SQL_CALC_FOUND_ROWS 
            a.id,
            b.id AS cityclass_id,
            b.name AS cityclass_name, 
            c.id AS coolclass_id, 
            c.name AS coolclass_name, 
            a.name, 
            a.url, 
            a.displayorder, 
            a.starttime, 
            a.endtime, 
            a.namecolor 
        FROM `ylmf_city_coolsite` AS a, `ylmf_city_cityclass` AS b, `ylmf_city_coolclass` AS c
        WHERE a.cityclass_id = b.id AND a.coolclass_id = c.id ' . $condition;
		$query = app_db::query($sql);
        $data = app_db::fetch_all();
		if (empty($data))
		{
			return false;
		}

		$output = array();
		$total = app_db::query('SELECT FOUND_ROWS() AS rows');
		$total = app_db::fetch_one();
		$output['total'] = $total['rows'];
		$output['data'] = $data;
		return $output;
	}


	/**
	 * 获取一个的信息
	 */
	public static function get_one($id)
	{
		if ($id < 1)
		{
			return false;
		}
		$id = (int)$id;

		$data = app_db::select('ylmf_city_coolsite', '*', "id = {$id}");
		return (empty($data)) ? false : $data[0];
	}
}
?>
