<?php
/**
 * ר�����
 *
 * @since 2011-01-13
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_zhuanti
{
	/**
	 * ��ȡ�б�
	 *
	 * @return array
	 */
	public static function get_list()
	{
		$sql = "SELECT * FROM `ylmf_zhuanti`"; 
		$query = app_db::query($sql);
        $data = app_db::fetch_all();
		if (empty($data))
		{
			return false;
		}
		return $data;
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

		$data = app_db::select('ylmf_zhuanti', '*', "id = {$id}");
		return (empty($data)) ? false : $data[0];
	}
}
?>
