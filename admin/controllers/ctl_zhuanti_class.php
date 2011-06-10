<?php
/**
 * ר��������
 *
 * @since 2009-7-13
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_zhuanti_class
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
            app_tpl::assign( 'npa', array('ר�����', 'ר������б�') );

            $zhuanti_id = (empty($_GET['zhuanti_id'])) ? 0 : (int)$_GET['zhuanti_id'];
            app_tpl::assign('zhuanti_id', $zhuanti_id);
            $start = (empty($_GET['start'])) ? 0 : (int)$_GET['start'];

            $zhuanti_list = mod_zhuanti::get_list();
            app_tpl::assign('zhuanti_list', $zhuanti_list);
            
            // ���
            $result = mod_zhuanti_class::get_list($zhuanti_id, $start, PAGE_ROWS );
            if (!empty($result))
            {
                app_tpl::assign('page_url', "?c=zhuanti_class&zhuanti_id={$zhuanti_id}");
                app_tpl::assign('pages', mod_pager::get_page_number_list($result['total'], $start, PAGE_ROWS));
                app_tpl::assign('list', $result['data']);

                app_tpl::assign('referer', $_SERVER['REQUEST_URI']);
            }
        }
        catch (Exception $e)
        {

        }
        app_tpl::display('zhuanti_class_list.tpl');
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
            $name = (empty($_POST['name'])) ? '' : $_POST['name'];
            $url = (empty($_POST['url'])) ? '' : $_POST['url'];
            $zhuanti = (empty($_POST['zhuanti'])) ? 0 : $_POST['zhuanti'];

            $data['name'] = $name;
            $data['url'] = $url;
            $data['zhuanti'] = $zhuanti;

            // ����
            if ($action == 'add')
            {
                if (empty($zhuanti)) 
                {
                    throw new Exception('��ѡ��ר��', 10);
                }
                if (empty($name)) 
                {
                    throw new Exception('�������Ʋ���Ϊ��', 10);
                }
                $tmp = app_db::select('ylmf_zhuanticlass', '*', "zhuanti = '{$zhuanti}' AND name = '{$name}'");
                if (!empty($tmp))
                {
                    throw new Exception('�÷��������Ѵ���', 10);
                }

                if (false === app_db::insert('ylmf_zhuanticlass', array_keys($data), array_values($data)))
                {
                    throw new Exception('���ݿ����ʧ��', 10);
                }
                else
                {
                    mod_login::message('��ӳɹ�', '?c=zhuanti_class');
                }

            }
            // �޸�
            elseif ($action == 'modify')
            {
                if (empty($zhuanti)) 
                {
                    throw new Exception('��ѡ��ר��', 10);
                }
                if (empty($name)) 
                {
                    throw new Exception('�������Ʋ���Ϊ��', 10);
                }
                $tmp = app_db::select('ylmf_zhuanticlass', '*', "zhuanti = '{$zhuanti}' AND name = '{$name}' AND id != {$id}");
                if (!empty($tmp))
                {
                    throw new Exception('�÷��������Ѵ���', 10);
                }

                if ($id < 1 || false === app_db::update('ylmf_zhuanticlass', $data, "id = {$id}"))
                {
                    throw new Exception('���ݿ����ʧ��', 10);
                }
                else
                {
                    mod_login::message('�޸ĳɹ�', '?c=zhuanti_class');
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

        // �����б�
        $zhuanti_list = mod_zhuanti::get_list();
        app_tpl::assign('zhuanti_list', $zhuanti_list);

        app_tpl::display('zhuanti_class_edit.tpl');
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
            $referer = (empty($_POST['referer'])) ? '?c=zhuanti_class' : $_POST['referer'];
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
                    app_db::update('ylmf_zhuanticlass', array('displayorder' => $val), "id = {$key}");
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
                    app_db::update('ylmf_zhuanticlass', array('inindex' => 1), "id IN ($condition)");
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
                    app_db::update('ylmf_zhuanticlass', array('inindex' => 0), "id IN ($condition)");
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
                        $tmp = mod_zhuanti_class::get_one($key);
                        //�жϸķ��������Ƿ�����վ
                        $site = app_db::select('ylmf_zhuantisite', '*', "class = {$key} LIMIT 1");
                        if (!empty($site)) 
                        {
                            mod_login::message("����ɾ����ר����� <font color=red>{$tmp['name']}</font> ���� <font color=red>ר����վ</font>������ֱ��ɾ��", $referer);
                        }
                        $name .= $tmp['name'] . ', ';
                    }
                    (!empty($name)) && $name = substr($name, 0, -2);
                    app_tpl::assign('message', '����ɾ�����з��ࣺ<strong>' . $name . '</strong>��ȷ��ɾ����');
                    app_tpl::assign('delete', implode(',', $delete));
                    app_tpl::assign('referer', $referer);
                    app_tpl::assign('action_url', '?c=zhuanti_class&a=list_edit');

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
                        app_db::delete('ylmf_zhuanticlass', "id IN ($condition)");
                    }
                }
            }
            mod_make_html::auto_update('index');

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
            $referer = (empty($_SERVER['HTTP_REFERER'])) ? '?c=zhuant_class' : $_SERVER['HTTP_REFERER'];
            if (empty($action) || ($action != 'modify' && $action != 'add'))
            {
                throw new Exception('����ʧ��', 10);
            }

            if ($action == 'modify')
            {
                app_tpl::assign( 'npa', array('ר�����', '�༭ר�����') );
                $id = (empty($_GET['id'])) ? 0 : $_GET['id'];
                if ($id < 1)
                {
                    throw new Exception('����ʧ��', 10);
                }
                $id = (int)$id;

                $result = mod_zhuanti_class::get_one($id);
                if (empty($result))
                {
                    throw new Exception('û���ҵ�����', 10);
                }
                app_tpl::assign('data', $result);
                app_tpl::assign('action', 'modify');
            }
            else
            {
                app_tpl::assign( 'npa', array('ר�����', '����ר�����') );
                app_tpl::assign('action', 'add');
            }

            app_tpl::assign('referer', $referer);

            $zhuanti_list = mod_zhuanti::get_list();
            /*
            $i=0;
            foreach ($zhuanti_list as $list) 
            {
                $zhuanti_list[$i]['son'] = mod_zhuanti_class::get_list($list['id']);
                $i++;
            }
            unset($i);*/
            app_tpl::assign('zhuanti_list', $zhuanti_list);
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('zhuanti_class_edit.tpl');
    }
}
?>
