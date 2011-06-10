<?php
/**
 * 专题管理
 *
 * @since 2009-7-13
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_zhuanti_site
{
	/**
	 * 返回搜索结果
	 *
	 * @param string $keyword
	 * @param int $start[optional]
	 * @param int $num[optional]
	 * @return array
	 */
	public static function search($keyword, $search_type, $start = 0, $num = 20)
	{
		$condition = '';
        $condition .= " and a.{$search_type} LIKE '%{$keyword}%'";
		$condition .= ' ORDER BY a.class, a.displayorder';
		if ($start > -1 && $num > 0)
		{
			$condition .= " LIMIT {$start}, {$num}";
		}

        $sql = 'SELECT SQL_CALC_FOUND_ROWS a.*, b.id AS class_id, b.name AS class_name
                FROM ylmf_zhuantisite AS a, ylmf_zhuanticlass AS b
                WHERE a.class = b.id ' . $condition;
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
	 * 获取列表
	 *
	 * @param int $class_id
	 * @param boolean $isend
	 * @param boolean $inindex
	 * @param int $start[optional]
	 * @param int $num[optional]
	 * @return array
	 */
	public static function get_list($class_id = 0, $isend = false, $inindex = false, $start = 0, $num = 0, $extends = '')
	{
		$condition = '';
		// 分类
		if ($class_id > 0)
		{
			$condition .= (!empty($condition)) ? " AND a.`class` = {$class_id}" :
												 " AND a.`class` = {$class_id}";
		}
		// 过期
		if ($isend)
		{
			$condition .= (!empty($condition)) ? ' AND a.`endtime` > 0 AND a.`endtime` < ' . time() :
												 ' AND a.`endtime` > 0 AND a.`endtime` < ' . time();
		}
        // 显示在首页
		elseif ($inindex)
		{
			$condition .= (!empty($condition)) ? ' AND a.inindex = 1 ' : ' AND a.inindex = 1 ' ;
		}

		$condition .= ' ORDER BY a.class, a.displayorder';
		if ($start > -1 && $num > 0)
		{
			$condition .= " LIMIT {$start}, {$num}";
		}

        $sql = 'SELECT SQL_CALC_FOUND_ROWS a.*, b.id AS class_id, b.name AS class_name
                       FROM ylmf_zhuantisite AS a, ylmf_zhuanticlass AS b
                WHERE a.class = b.id ' . $condition;
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

		$data = app_db::select('ylmf_zhuantisite', '*', "id = {$id}");
		return (empty($data)) ? false : $data[0];
	}
}
?>
