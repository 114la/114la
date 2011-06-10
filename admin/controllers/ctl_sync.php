<?php
/**
 * ͬ���ٷ�����
 *
 * @since 2011-01-11
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_sync
{
    /**
     * URL(������ת)
     * @var unknown_type
    */
    private static $base_url = '?c=sync';
    
    //
    private static $sync_file;
    private static $sync_path;

    /**
     * pre���� [���캯������] �뿴 applications/app_router.php
     *
     * @return void
     */
    public static function pre()
    {
        self::$sync_file = mod_update::$source_url . '/' . mod_update::$current_version . '/sync.txt';
        self::$sync_path = PATH_DATA . "/sync";
    }


    /**
     * ��ʾ��Ҫͬ���ı�
     *
     * @return void
     */
    public static function index()
    {
        try
        {
            app_tpl::assign( 'npa', array('���ݹ���', 'ͬ���ٷ�����') );
        	$output = array();
            $total_size = 0;

            $table_pre = $GLOBALS ['database'] ['table_prefix'];
            $files = @file(self::$sync_file);

            $i=0;
            foreach ($files as $file) 
            {
                $tmp = explode('|', $file);
                $output[$i]['table_name'] = trim($tmp[0]);
                $output[$i]['zh_name'] = iconv('utf-8', 'gbk', trim($tmp[1]));
                $output[$i]['size'] = bytes_to_string(trim($tmp[2]));
                $output[$i]['date'] = trim($tmp[3]);
                $tmp_file = self::$sync_path . "/" . trim($tmp[0]) . ".txt";
                $time = @file_get_contents($tmp_file);
                //��һ��ͬ�����ڲ�Ϊ�գ�˵��֮ǰͬ����
                if (!empty($time)) 
                {
                    $local_time = strtotime($time); //����ʱ��
                    $online_time = strtotime(trim($tmp[3])); //������ʱ��
                    //����ͬ������ ���ڻ���� �ٷ����ڣ�����Ҫ��ͬ��
                    if ($local_time >= $online_time)
                    {
                        $output[$i]['is_sync'] = true;
                    }
                }
                $total_size += trim($tmp[2]);
                $i++;
            }
            unset($i);
            $total_size = bytes_to_string($total_size); //���ݿ��С
            app_tpl::assign('list', $output);
            app_tpl::assign('total_size', $total_size);

            app_tpl::display('sync.tpl');
        }
        catch (Exception $e)
        {
            echo $e->Message();
        }
    }


    /**
     * ͬ��
     *
     * @return void
     */
	public static function sync()
	{
	    try
	    {
	        function_exists('set_time_limit') && set_time_limit(0);
            $table_pre = $GLOBALS ['database'] ['table_prefix'];

            if (empty($_POST['step']))
            {
                $table_list = (empty($_POST['table_list'])) ? '' : $_POST['table_list'];
                if (empty($table_list))
                {
                    throw new Exception('��ѡ����Ҫ���ݵı�', 10);
                }

                $name = implode($table_list, ',');
                app_tpl::assign('message', '����ͬ���������ݱ�<strong>' . $name . '</strong>��ͬ������Щ��ı������ݽ��ᱻ���ǣ������ñ������ݱ��ݣ�ȷ��ͬ����');
                app_tpl::assign('sync', $name);
                app_tpl::assign('referer', '?c=sync');
                app_tpl::assign('action_url', '?c=sync&a=sync');

                app_tpl::display('sync_confirm.tpl');
                exit;
            }
            else 
            {
                $table_list = explode(',', $_POST['sync']);
                foreach ($table_list as $list) 
                {
                    $sql = @file_get_contents(mod_update::$source_url . '/' . mod_update::$current_version . '/sync/'. $list . ".txt");
                    if (empty($sql)) 
                    {
                        throw new Exception("����ͬ���� {$list} �����ڣ�����ϵ114��");
                    }
                    $sql = str_replace("\r",'',$sql);
                    $sqlarray = array();
                    $sqlarray = explode(";\n",$sql);
                    foreach ($sqlarray as $query) 
                    {
                        $query = trim(str_replace("\n",'',$query));
                        //�滻���ݱ���ǰ׺
                        $sql = preg_replace("#^ylmf_#", $table_pre, $sql); 
                        $query && app_db::query($query);
                    }
                    $time = date("Y-m-d", time());
                    $put_file = self::$sync_path . "/" . $list . ".txt";
                    @file_put_contents($put_file, $time);
                }
            }

            mod_login::message('�����ɹ�!', '?c=sync');
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

}
?>
