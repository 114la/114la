<?php
/**
 * 城市导航城市分类管理
 *
 * @since 2011-01-10
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_city_cityclass
{
	/**
	 * 获取列表
	 *
	 * @return array
	*/
    public static function get_list()
    {
        app_db::query("SELECT * FROM `ylmf_city_cityclass` ORDER BY `displayorder` ASC");
        $datas = app_db::fetch_all();
        if (empty($datas))
        {
            return false;
        }
        return $datas;
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

		$data = app_db::select('ylmf_city_cityclass', '*', "id = {$id}");
		return (empty($data)) ? false : $data[0];
	}

    public static function make_js($id)
    {
        if (empty($id)) 
        {
            return false;
        }
        $city_id = '101010100'; //默认北京
        $data = array();

        app_db::query("SELECT * FROM `ylmf_city_cityclass` WHERE `id`='{$id}'");
        $city = app_db::fetch_one();
        //城市名称, 名称转成utf-8的，因为json只支持utf-8
        $data['name'] = iconv('gbk', 'utf-8', $city['name']);
        //城市路径
        $path = empty($city['path']) ? 'city_'.$city['id'] : $city['path']; 
        $data['url'] = URL_HTML . '/city/' . $path . '/index.htm';

        app_db::query("SELECT `city_id` FROM `ylmf_city_id_name` WHERE `city_name`='{$city['name']}'");
        $city_id_name = app_db::fetch_one();
        if (empty($city_id_name)) 
        {
            return false;
        }
        else 
        {
            //城市ID
            $city_id = $city_id_name['city_id'];
        }
        //查该城市下的名站
        app_db::query("SELECT `name`, `url` FROM `ylmf_city_mingzhan` WHERE `cityclass_id`='{$id}' AND `inindex`=1");
        $mingzhan = app_db::fetch_all();
        if (!empty($mingzhan)) 
        {
            //名称转成utf-8的，因为json只支持utf-8
            $i=0;
            foreach ($mingzhan as $row) 
            {
                $mingzhan[$i]['name'] = iconv('gbk', 'utf-8', $row['name']);
                $i++;
            }
            unset($i);
        }
        $data['son'] = $mingzhan;
        $result =  "var Local_city_arr = " . json_encode($data) . "; if (typeof(LocalData_callback) != 'undefined') { LocalData_callback(); }";
        $filename = PATH_ROOT . '/static/js/city/' . $city_id . '.js';
        if (false == mod_file::write($filename, $result, "wb+", 0)) 
        {
            return false;
        }
        @chmod($filename, 0777);
        return true;
    }
}
?>
