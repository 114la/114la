<?php
/**
 * 专题管理
 *
 * @since 2011-01-13
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_zhuanti
{
    /**
     * 显示
     *
     * @return void
     */
    public static function index()
    {
        try
        {
            app_tpl::assign( 'npa', array('专题管理', '专题列表') );

            // 结果
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
     * 保存
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
                throw new Exception('操作失败', 10);
            }
            $data = array();

            $id = (empty($_POST['id'])) ? '' : $_POST['id'];

            $name = (empty($_POST['name'])) ? '' : htmlspecialchars($_POST['name'], ENT_QUOTES);
            if (empty($name))
            {
                throw new Exception('操作失败', 10);
            }
            $data['name'] = $name;

            $data['path'] = (empty($_POST['path'])) ? '' : htmlspecialchars($_POST['path'], ENT_QUOTES);

            $data['template'] = (empty($_POST['template'])) ? '' : htmlspecialchars($_POST['template'], ENT_QUOTES);

            // 新增
            if ($action == 'add')
            {
                $tmp = app_db::select('ylmf_zhuanti', 'id', "name = '{$name}'");
                if (!empty($tmp))
                {
                    throw new Exception('该专题已存在');
                }
                if (false === app_db::insert('ylmf_zhuanti', array_keys($data), array_values($data)))
                {
                    throw new Exception('数据库操作失败', 10);
                }
                else
                {
                    mod_login::message('添加成功', '?c=zhuanti');
                }

            }
            // 修改
            elseif ($action == 'modify')
            {
                $tmp = app_db::select('ylmf_zhuanti', 'id', "name = '{$name}' AND id != {$id}");
                if (!empty($tmp))
                {
                    throw new Exception('该专题已存在');
                }

                if (false === app_db::update('ylmf_zhuanti', $data, "id = {$id}"))
                {
                    throw new Exception('数据库操作失败', 10);
                }
                else
                {
                    mod_login::message('修改成功', $referer);
                }
            }
            else
            {
                mod_login::message('操作失败', '?c=zhuanti');
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
     * 编辑
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
                throw new Exception('操作失败', 10);
            }


            if ($action == 'modify')
            {
                app_tpl::assign( 'npa', array('专题管理', '编辑专题') );
                $id = (empty($_GET['id'])) ? 0 : $_GET['id'];
                if ($id < 1)
                {
                    throw new Exception('操作失败', 10);
                }
                $id = (int)$id;

                $result = mod_zhuanti::get_one($id);
                if (empty($result))
                {
                    throw new Exception('没有找到数据', 10);
                }
                app_tpl::assign('data', $result);
                app_tpl::assign('action', 'modify');
                app_tpl::assign('referer', $_SERVER['HTTP_REFERER']);
            }
            else
            {
                app_tpl::assign( 'npa', array('专题管理', '新增专题') );
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
     * 列表操作
     *
     * @return void
     */
    public static function list_edit()
    {
        try
        {
            $referer = (empty($_POST['referer'])) ? '?c=zhuanti' : $_POST['referer'];
            // 排序
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

            // 首页显示
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
            // 自定义路径
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
            // 自定义模板
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

            // 删除
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
                        //判断改分类下面是否有网站
                        $class = app_db::select('ylmf_zhuanticlass', '*', "zhuanti = {$key} LIMIT 1");
                        if (!empty($class)) 
                        {
                            mod_login::message("您将删除的专题 <font color=red>{$tmp['name']}</font> 下有 <font color=red>专题分类</font>，不能直接删除", $referer);
                        }
                        $name .= $tmp['name'] . ', ';
                    }
                    (!empty($name)) && $name = substr($name, 0, -2);
                    app_tpl::assign('message', '您将删除下列站点：<strong>' . $name . '</strong>，确认删除吗？');
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

            mod_login::message('操作成功', $referer);
        }
        catch (Exception $e)
        {

        }
    }

}
?>
