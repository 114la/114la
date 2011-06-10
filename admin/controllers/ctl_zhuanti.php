<?php
/**
 * ר�����
 *
 * @since 2011-01-13
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_zhuanti
{
    /**
     * ��ʾ
     *
     * @return void
     */
    public static function index()
    {
        try
        {
            app_tpl::assign( 'npa', array('ר�����', 'ר���б�') );

            // ���
            $result = mod_zhuanti::get_list($class_id, $isend, $inindex, $start, PAGE_ROWS);
            if (!empty($result))
            {
                app_tpl::assign('list', $result);
                app_tpl::assign('referer', $_SERVER['REQUEST_URI']);
            }
        }
        catch (Exception $e)
        {

        }
        app_tpl::display('zhuanti_list.tpl');
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
            $referer = (!empty($_POST['referer'])) ? $_POST['referer'] : '?c=zhuanti';
            if ($action != 'add' && $action != 'modify')
            {
                throw new Exception('����ʧ��', 10);
            }
            $data = array();

            $id = (empty($_POST['id'])) ? '' : $_POST['id'];

            $name = (empty($_POST['name'])) ? '' : htmlspecialchars($_POST['name'], ENT_QUOTES);
            if (empty($name))
            {
                throw new Exception('����ʧ��', 10);
            }
            $data['name'] = $name;

            $data['path'] = (empty($_POST['path'])) ? '' : htmlspecialchars($_POST['path'], ENT_QUOTES);

            $data['template'] = (empty($_POST['template'])) ? '' : htmlspecialchars($_POST['template'], ENT_QUOTES);

            // ����
            if ($action == 'add')
            {
                $tmp = app_db::select('ylmf_zhuanti', 'id', "name = '{$name}'");
                if (!empty($tmp))
                {
                    throw new Exception('��ר���Ѵ���');
                }
                if (false === app_db::insert('ylmf_zhuanti', array_keys($data), array_values($data)))
                {
                    throw new Exception('���ݿ����ʧ��', 10);
                }
                else
                {
                    mod_login::message('��ӳɹ�', '?c=zhuanti');
                }

            }
            // �޸�
            elseif ($action == 'modify')
            {
                $tmp = app_db::select('ylmf_zhuanti', 'id', "name = '{$name}' AND id != {$id}");
                if (!empty($tmp))
                {
                    throw new Exception('��ר���Ѵ���');
                }

                if (false === app_db::update('ylmf_zhuanti', $data, "id = {$id}"))
                {
                    throw new Exception('���ݿ����ʧ��', 10);
                }
                else
                {
                    mod_login::message('�޸ĳɹ�', $referer);
                }
            }
            else
            {
                mod_login::message('����ʧ��', '?c=zhuanti');
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }

        app_tpl::assign('action', $action);
        app_tpl::assign('referer', $referer);

        $_POST['name'] = $_POST['name'];
        $_POST['path'] = $_POST['path'];
        $_POST['template'] = $_POST['template'];
        app_tpl::assign('data', $_POST);

        app_tpl::display('zhuanti_edit.tpl');
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
            if (empty($action) || ($action != 'modify' && $action != 'add'))
            {
                throw new Exception('����ʧ��', 10);
            }


            if ($action == 'modify')
            {
                app_tpl::assign( 'npa', array('ר�����', '�༭ר��') );
                $id = (empty($_GET['id'])) ? 0 : $_GET['id'];
                if ($id < 1)
                {
                    throw new Exception('����ʧ��', 10);
                }
                $id = (int)$id;

                $result = mod_zhuanti::get_one($id);
                if (empty($result))
                {
                    throw new Exception('û���ҵ�����', 10);
                }
                app_tpl::assign('data', $result);
                app_tpl::assign('action', 'modify');
                app_tpl::assign('referer', $_SERVER['HTTP_REFERER']);
            }
            else
            {
                app_tpl::assign( 'npa', array('ר�����', '����ר��') );
                app_tpl::assign('action', 'add');
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('zhuanti_edit.tpl');
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
            $referer = (empty($_POST['referer'])) ? '?c=zhuanti' : $_POST['referer'];
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
                    app_db::update('ylmf_zhuanti', array('displayorder' => $val), "id = {$key}");
                }
            }

            // ��ҳ��ʾ
            if (!empty($_POST['inindex']))
            {
                $condition = '';
                foreach ($_POST['inindex'] as $key => $val)
                {
                    if (!is_numeric($key) || $key < 1)
                    {
                        continue;
                    }
                    $condition .= (empty($condition)) ? "{$key}" : ", {$key}";
                }
                if (!empty($condition))
                {
                    app_db::update('ylmf_zhuanti', array('inindex' => 1), "id IN ($condition)");
                }
            }
            if (!empty($_POST['noindex']))
            {
                $condition = '';
                foreach ($_POST['noindex'] as $key => $val)
                {
                    if (!is_numeric($key) || $key < 1 || $val != 1)
                    {
                        continue;
                    }
                    $condition .= (empty($condition)) ? "{$key}" : ", {$key}";
                }
                if (!empty($condition))
                {
                    app_db::update('ylmf_zhuanti', array('inindex' => 0), "id IN ($condition)");
                }
            }
            // �Զ���·��
            if (!empty($_POST['path']))
            {
                foreach ($_POST['path'] as $key => $val)
                {
                    if (!is_numeric($key) || $key < 1)
                    {
                        continue;
                    }
                    $key = (int)$key;
                    $val = (empty($val)) ? '' : trim($val);
                    app_db::update('ylmf_zhuanti', array('path' => $val), "id = {$key}");
                }
            }
            // �Զ���ģ��
            if (!empty($_POST['template']))
            {
                foreach ($_POST['template'] as $key => $val)
                {
                    if (!is_numeric($key) || $key < 1)
                    {
                        continue;
                    }
                    $key = (int)$key;
                    $val = (empty($val)) ? '' : trim($val);
                    app_db::update('ylmf_zhuanti', array('template' => $val), "id = {$key}");
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
                        $tmp = mod_zhuanti::get_one($key);
                        //�жϸķ��������Ƿ�����վ
                        $class = app_db::select('ylmf_zhuanticlass', '*', "zhuanti = {$key} LIMIT 1");
                        if (!empty($class)) 
                        {
                            mod_login::message("����ɾ����ר�� <font color=red>{$tmp['name']}</font> ���� <font color=red>ר�����</font>������ֱ��ɾ��", $referer);
                        }
                        $name .= $tmp['name'] . ', ';
                    }
                    (!empty($name)) && $name = substr($name, 0, -2);
                    app_tpl::assign('message', '����ɾ������վ�㣺<strong>' . $name . '</strong>��ȷ��ɾ����');
                    app_tpl::assign('delete', implode(',', $delete));
                    app_tpl::assign('referer', $referer);
                    app_tpl::assign('action_url', '?c=zhuanti&a=list_edit');

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
                        app_db::delete('ylmf_zhuanti', "id IN ($condition)");
                    }
                }
            }
            mod_make_html::auto_update('index');
            mod_make_html::auto_update('zhuanti');

            mod_login::message('�����ɹ�', $referer);
        }
        catch (Exception $e)
        {

        }
    }

}
?>
