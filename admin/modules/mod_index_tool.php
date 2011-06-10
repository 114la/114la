<?php
/**
 * 首页工具
 *
 * @since 2009-7-11
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_index_tool
{

	/**
	 * 获取首页工具列表
	 *
	 * @return array
	 */
	public static function get_index_tool_list()
	{
        app_db::query("SELECT * FROM `ylmf_index_tool` ORDER BY `order` ASC");
        return app_db::fetch_all();
	}

	/**
	 * 添加首页工具
	 *
     * @param array $data 新首页工具数据
	 * @return array
	 */
	public static function add_index_tool($data = array())
	{
        if(empty($data))
        {
            return false;
        }
        if (false === app_db::insert('ylmf_index_tool', array_keys($data), array_values($data)))
        {
            return false;
        }
        return true;
    }

	/**
	 * 排序首页工具
	 *
	 * @param array $order 首页工具顺序
	 * @return array
	 */
	public static function order_index_tool($order = null)
	{
        if($order === null)
        {
            return false;
        }
        foreach ($order as $key => $val)
        {
            if (!is_numeric($key) || $key < 1)
            {
                continue;
            }
            $key = (int)$key;
            $val = (empty($val)) ? 100 : (int)$val;
            app_db::update('ylmf_index_tool', array('order' => $val), "id = {$key}");
        }
        return true;

    }

	/**
	 * 修改首页工具
	 *
	 * @param int $id 首页工具id
     * @param array $data 首页工具数据
	 * @return array
	 */
    public static function edit_index_tool($id = null, $data = array())
	{
        if($id === null || empty($data))
        {
            return false;
        }
        if ($id < 1 || false === app_db::update('ylmf_index_tool', $data, "id = {$id}"))
        {
            return false;
        }
        return true;
    }

	/**
	 * 获取一个首页工具的信息
     *
	 * @param int $id 首页工具id
	 * @return array
	 */
	public static function get_index_tool($id)
    {
        if (empty($id)) 
        {
            return false;
        }
        app_db::query("SELECT * FROM `ylmf_index_tool` WHERE `id`='{$id}'");
        $data = app_db::fetch_one();
        if (!empty($data)) 
        {
            return $data;
        }
        return false;
	}

	/**
	 * 删除首页工具
     *
	 * @param int/array $id 首页工具id
	 * @return array
	 */
	public static function delete_index_tool($id = null)
	{
        if($id === null)
        {
            return false;
        }
        $condition = '';
        foreach ($id as $key => $val)
        {
            $val = (int)$val;
            $condition .= (empty($condition)) ? "{$val}" : ", {$val}";
        }
        if (!empty($condition))
        {
            app_db::delete('ylmf_index_tool', "id IN ($condition)");
        }

    }

}
?>
