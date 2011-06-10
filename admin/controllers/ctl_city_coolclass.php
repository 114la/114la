<?php
/**
 * ���е�����վ�������
 *
 * @since 2011-01-10
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_city_coolclass
{
    /**
     * ��ʾ��Ϣ
     * @var string
     */
    private static $message = '';

    /**
     * ��ʾ
     *
     * @return void
     */
    public static function index()
    {
        try
        {
            app_tpl::assign( 'npa', array('���е���', '��վ�����б�') );

            $result = mod_city_coolclass::get_list();
            app_tpl::assign('list', $result);

            app_tpl::assign('referer', $_SERVER['REQUEST_URI']);
        }
        catch (Exception $e)
        {

        }
        app_tpl::display('city_coolclass_list.tpl');
    }


    /**
     * ����
     *
     * @return void
    */
    public static function save()
    {
        try
        {
            $action = (empty($_POST['action'])) ? '' : $_POST['action'];
            $referer = (!empty($_POST['referer'])) ? $_POST['referer'] : '?c=city_coolclass';
            if ($action != 'add' && $action != 'modify')
            {
                throw new Exception('����ʧ��', 10);
            }
            $data = array();

            $id = (empty($_POST['id'])) ? '' : $_POST['id'];
            $name = (empty($_POST['name'])) ? '' : $_POST['name'];
            $path = (empty($_POST['path'])) ? '' : $_POST['path'];

            $data['name'] = $name;
            $data['path'] = $path;

            // ����
            if ($action == 'add')
            {
                $tmp = app_db::select('ylmf_city_coolclass', '*', "name = '{$name}'");
                if (!empty($tmp))
                {
                    throw new Exception('�÷��������Ѵ���', 10);
                }

                if (false === app_db::insert('ylmf_city_coolclass', array_keys($data), array_values($data)))
                {
                    throw new Exception('���ݿ����ʧ��', 10);
                }
                else
                {
                    mod_login::message('��ӳɹ�', '?c=city_coolclass');
                }

            }
            // �޸�
            elseif ($action == 'modify')
            {
                $tmp = app_db::select('ylmf_city_coolclass', '*', "name = '{$name}' AND id != {$id}");
                if (!empty($tmp))
                {
                    throw new Exception('�÷��������Ѵ���', 10);
                }

                if ($id < 1 || false === app_db::update('ylmf_city_coolclass', $data, "id = {$id}"))
                {
                    throw new Exception('���ݿ����ʧ��', 10);
                }
                else
                {
                    mod_login::message('�޸ĳɹ�', '?c=city_coolclass');
                }
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }

        app_tpl::assign('action', $action);
        app_tpl::assign('referer', $referer);
        app_tpl::assign('data', $_POST);

        app_tpl::display('city_coolclass_edit.tpl');
    }


    /**
     * �б����
     *
     * @return void
     */
    public static function list_edit()
    {
        try
        {
            $referer = (empty($_POST['referer'])) ? '?c=city_coolclass' : $_POST['referer'];
            // ����
            if (!empty($_POST['order']))
            {
                foreach ($_POST['order'] as $key => $val)
                {
                    if (!is_numeric($key) || $key < 1)
                    {
                        continue;
                    }
                    $key = (int)$key;
                    $val = (empty($val)) ? 100 : (int)$val;
                    app_db::update('ylmf_city_coolclass', array('displayorder' => $val), "id = {$key}");
                }
            }

            // ɾ��
            if (!empty($_POST['delete']))
            {
                if (empty($_POST['step']))
                {
                    $name = '';
                    $delete = array();
                    foreach ($_POST['delete'] as $key => $val)
                    {
                        $key = (int)$key;
                        $delete[] = $key;
                        $tmp = mod_city_coolclass::get_one($key);
                        //�жϸ÷��������Ƿ�����վ
                        $class = app_db::select('ylmf_city_coolsite', '*', "coolclass_id = {$key} LIMIT 1");
                        if (!empty($class)) 
                        {
                            mod_login::message("����ɾ���Ŀ�վ���� <font color=red>{$tmp['name']}</font> ���� <font color=red>��վ</font>������ֱ��ɾ��", $referer);
                        }

                        $name .= $tmp['name'] . ', ';
                    }
                    (!empty($name)) && $name = substr($name, 0, -2);
                    app_tpl::assign('message', '����ɾ�����з��ࣺ<strong>' . $name . '</strong>��ȷ��ɾ����');
                    app_tpl::assign('delete', implode(',', $delete));
                    app_tpl::assign('referer', $referer);
                    app_tpl::assign('action_url', '?c=city_coolclass&a=list_edit');

                    app_tpl::display('confirm.tpl');
                    exit;
                }
                else
                {
                    $condition = '';
                    $_POST['delete'] = explode(',', $_POST['delete']);
                    foreach ($_POST['delete'] as $key => $val)
                    {
                        $val = (int)$val;
                        $condition .= (empty($condition)) ? "{$val}" : ", {$val}";
                    }
                    if (!empty($condition))
                    {
                        app_db::delete('ylmf_city_coolclass', "id IN ($condition)");
                    }
                }
            }
            mod_login::message('�����ɹ�', $referer);
        }
        catch (Exception $e)
        {
            exit($e->getMessage());
        }
    }


    /**
     * �༭
     *
     * @return void
     */
    public static function edit()
    {
        try
        {
            $action = (empty($_GET['action'])) ? '' : $_GET['action'];
            $referer = (empty($_SERVER['HTTP_REFERER'])) ? '?c=city_coolclass' : $_SERVER['HTTP_REFERER'];
            if (empty($action) || ($action != 'modify' && $action != 'add'))
            {
                throw new Exception('����ʧ��', 10);
            }

            if ($action == 'modify')
            {
                app_tpl::assign( 'npa', array('���е���', '�༭��վ����') );
                $id = (empty($_GET['id'])) ? 0 : $_GET['id'];
                if ($id < 1)
                {
                    throw new Exception('����ʧ��', 10);
                }
                $id = (int)$id;

                $result = mod_city_coolclass::get_one($id);
                if (empty($result))
                {
                    throw new Exception('û���ҵ�����', 10);
                }
                app_tpl::assign('data', $result);
                app_tpl::assign('action', 'modify');
            }
            else
            {
                app_tpl::assign( 'npa', array('���е���', '������վ����') );
                app_tpl::assign('action', 'add');
            }

            app_tpl::assign('referer', $referer);
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('city_coolclass_edit.tpl');
    }
}
?>
