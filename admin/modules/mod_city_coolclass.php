<?php
/**
 * ���е�����վ�������
 *
 * @since 2011-01-10
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_city_coolclass
{
	/**
	 * ��ȡ�б�
	 *
	 * @return array
	*/
    public static function get_list()
    {
        app_db::query("SELECT * FROM `ylmf_city_coolclass` ORDER BY `displayorder` ASC");
        $datas = app_db::fetch_all();
        if (empty($datas))
        {
            return false;
        }
        return $datas;
    }
    
	/**
	 * ��ȡһ������Ϣ
	 */
	public static function get_one($id)
	{
		if ($id < 1)
		{
			return false;
		}
		$id = (int)$id;

		$data = app_db::select('ylmf_city_coolclass', '*', "id = {$id}");
		return (empty($data)) ? false : $data[0];
	}
}
?>
