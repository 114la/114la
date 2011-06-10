<?php
/**
 * 专题分类管理
 *
 * @since 2011-01-13
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_zhuanti_class
{
	/**
	 * 获取列表
	 *
	 * @return array
	*/
    public static function get_list($zhuanti_id = 0, $start = 0, $num = 0)
    {
		$condition = '';
        if (!empty($zhuanti_id)) 
        {
            $condition .= " AND `zhuanti`='{$zhuanti_id}' ";
        }
        $condition .= ' ORDER BY a.displayorder';
		if ($start > -1 && $num > 0)
		{
			$condition .= " LIMIT {$start}, {$num}";
		}

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.*,b.name as zhuanti_name FROM `ylmf_zhuanticlass` a LEFT JOIN `ylmf_zhuanti` b ON a.zhuanti=b.id WHERE 1=1 " . $condition;
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

        $data = app_db::select('ylmf_zhuanticlass', '*', "id = {$id}");
		return (empty($data)) ? false : $data[0];
	}
}
?>
