<?php
/**
 * 城市导航城市分类管理
 *
 * @since 2011-01-10
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_city_cityclass
{
    /**
     * 提示信息
     * @var string
     */
    private static $message = '';

    /**
     * 显示
     *
     * @return void
     */
    public static function index()
    {
        try
        {
            app_tpl::assign( 'npa', array('城市导航', '城市分类列表') );

            $result = mod_city_cityclass::get_list();
            app_tpl::assign('list', $result);

            $staticfolder = empty($GLOBALS ['database'] ['staticfolder']) ? '/' : $GLOBALS ['database'] ['staticfolder'];
            app_tpl::assign('staticfolder', $staticfolder);

            app_tpl::assign('referer', $_SERVER['REQUEST_URI']);
        }
        catch (Exception $e)
        {

        }
        app_tpl::display('city_cityclass_list.tpl');
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
            $referer = (!empty($_POST['referer'])) ? $_POST['referer'] : '?c=city_cityclass';
            if ($action != 'add' && $action != 'modify')
            {
                throw new Exception('操作失败', 10);
            }
            $data = array();

            $id = (empty($_POST['id'])) ? '' : $_POST['id'];
            $name = (empty($_POST['name'])) ? '' : $_POST['name'];
            $path = (empty($_POST['path'])) ? '' : $_POST['path'];
            $tempalte = (empty($_POST['template'])) ? '' : $_POST['template'];

            $data['name'] = $name;
            $data['path'] = $path;
            $data['template'] = $tempalte;

            // 新增
            if ($action == 'add')
            {
                $tmp = app_db::select('ylmf_city_cityclass', '*', "name = '{$name}'");
                if (!empty($tmp))
                {
                    throw new Exception('该分类名称已存在', 10);
                }

                if (false === app_db::insert('ylmf_city_cityclass', array_keys($data), array_values($data)))
                {
                    throw new Exception('数据库操作失败', 10);
                }
                else
                {
                    mod_login::message('添加成功', '?c=city_cityclass');
                }

            }
            // 修改
            elseif ($action == 'modify')
            {
                $tmp = app_db::select('ylmf_city_cityclass', '*', "name = '{$name}' AND id != {$id}");
                if (!empty($tmp))
                {
                    throw new Exception('该分类名称已存在', 10);
                }

                if ($id < 1 || false === app_db::update('ylmf_city_cityclass', $data, "id = {$id}"))
                {
                    throw new Exception('数据库操作失败', 10);
                }
                else
                {
                    mod_login::message('修改成功', '?c=city_cityclass');
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

        app_tpl::display('city_cityclass_edit.tpl');
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
            $referer = (empty($_POST['referer'])) ? '?c=city_cityclass' : $_POST['referer'];
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
                    app_db::update('ylmf_city_cityclass', array('displayorder' => $val), "id = {$key}");
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
                    app_db::update('ylmf_city_cityclass', array('path' => $val), "id = {$key}");
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
                    app_db::update('ylmf_city_cityclass', array('template' => $val), "id = {$key}");
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
                        $tmp = mod_city_cityclass::get_one($key);
                        //判断该分类下面是否有网站
                        $class = app_db::select('ylmf_city_mingzhan', '*', "cityclass_id = {$key} LIMIT 1");
                        if (!empty($class)) 
                        {
                            mod_login::message("您将删除的城市分类 <font color=red>{$tmp['name']}</font> 下有 <font color=red>名站</font>，不能直接删除", $referer);
                        }
                        $class = app_db::select('ylmf_city_coolsite', '*', "cityclass_id = {$key} LIMIT 1");
                        if (!empty($class)) 
                        {
                            mod_login::message("您将删除的城市分类 <font color=red>{$tmp['name']}</font> 下有 <font color=red>酷站</font>，不能直接删除", $referer);
                        }

                        $name .= $tmp['name'] . ', ';
                    }
                    (!empty($name)) && $name = substr($name, 0, -2);
                    app_tpl::assign('message', '您将删除下列分类：<strong>' . $name . '</strong>，确认删除吗？');
                    app_tpl::assign('delete', implode(',', $delete));
                    app_tpl::assign('referer', $referer);
                    app_tpl::assign('action_url', '?c=city_cityclass&a=list_edit');

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
                        app_db::delete('ylmf_city_cityclass', "id IN ($condition)");
                    }
                }
            }
            mod_login::message('操作成功', $referer);
        }
        catch (Exception $e)
        {
            exit($e->getMessage());
        }
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
            $referer = (empty($_SERVER['HTTP_REFERER'])) ? '?c=city_cityclass' : $_SERVER['HTTP_REFERER'];
            if (empty($action) || ($action != 'modify' && $action != 'add'))
            {
                throw new Exception('操作失败', 10);
            }

            if ($action == 'modify')
            {
                app_tpl::assign( 'npa', array('城市导航', '编辑城市分类') );
                $id = (empty($_GET['id'])) ? 0 : $_GET['id'];
                if ($id < 1)
                {
                    throw new Exception('操作失败', 10);
                }
                $id = (int)$id;

                $result = mod_city_cityclass::get_one($id);
                if (empty($result))
                {
                    throw new Exception('没有找到数据', 10);
                }
                app_tpl::assign('data', $result);
                app_tpl::assign('action', 'modify');
            }
            else
            {
                app_tpl::assign( 'npa', array('城市导航', '新增城市分类') );
                app_tpl::assign('action', 'add');
            }

            app_tpl::assign('referer', $referer);
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('city_cityclass_edit.tpl');
    }

    public static function make_js()
    {
        try
        {
            $id = intval($_GET['id']);
            if (!empty($id)) 
            {
                $result = mod_city_cityclass::make_js($id);
                if (!$result) 
                {
                    throw new Exception('生成失败', 10);
                }

            }
            else 
            {
                app_db::query("SELECT id, name FROM `ylmf_city_cityclass`");
                $rows = app_db::fetch_all();
                $success_name = $false_name = $msg = "";
                foreach ($rows as $row) 
                {
                    $id = intval($row['id']);
                    $result = mod_city_cityclass::make_js($id);
                    if (!$result) 
                    {
                        $false_name .= $row['name'] . ', ';
                        $is_all_ok = false;
                    }
                    else 
                    {
                        $success_name .= $row['name'] . ', ';
                    }
                }
                if (!empty($success_name))
                {
                    $success_name = substr($success_name, 0, -2);
                    $msg .= "<font color=red>{$success_name}</font>生成成功；<br/>";
                }
                if (!empty($false_name))
                {
                    $false_name = substr($false_name, 0, -2);
                    $msg .= "<font color=red>{$false_name}</font>生成失败；<br/>";
                }
                throw new Exception($msg, 10);
            }
        }
        catch (Exception $e)
        {
            mod_login::message($e->getMessage(), '?c=city_cityclass');
        }
        mod_login::message('生成成功', '?c=city_cityclass');

    }
}
?>
