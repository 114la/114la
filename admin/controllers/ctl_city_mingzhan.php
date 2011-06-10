<?php
/**
 * 城市导航名站管理
 *
 * @since 2011-01-10
 * @copyright http://www.114la.com
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_city_mingzhan
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
            app_tpl::assign( 'npa', array('城市导航', '酷站列表') );
            // 按分类查看
            $cityclass_id = (empty($_GET['cityclass_id'])) ? 0 : $_GET['cityclass_id'];
            // 显示过期站点
            $isend = (empty($_GET['isend'])) ? false : true;
            // 显示首页显示站点
            $inindex = (empty($_GET['inindex'])) ? false : true;

            $start = (empty($_GET['start'])) ? 0 : (int)$_GET['start'];

            // 分类列表
            app_tpl::assign('cityclass_list', mod_city_cityclass::get_list());
            app_tpl::assign('cityclass_id', $cityclass_id);

            // 结果
            $result = mod_city_mingzhan::get_list($cityclass_id, $isend, $start, PAGE_ROWS);
            if (!empty($result))
            {
                app_tpl::assign('page_url', "?c=city_mingzhan&cityclass_id={$cityclass_id}&isend={$isend}");
                app_tpl::assign('pages', mod_pager::get_page_number_list($result['total'], $start, PAGE_ROWS));
                app_tpl::assign('list', $result['data']);

                app_tpl::assign('referer', $_SERVER['REQUEST_URI']);
            }
        }
        catch (Exception $e)
        {

        }
        app_tpl::display('city_mingzhan_list.tpl');
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
            $referer = (!empty($_POST['referer'])) ? $_POST['referer'] : '?c=city_mingzhan';
            if ($action != 'add' && $action != 'modify')
            {
                throw new Exception('操作失败', 10);
            }
            $data = array();

            $id = (empty($_POST['id'])) ? '' : $_POST['id'];

            $site_name = (empty($_POST['site_name'])) ? '' : htmlspecialchars($_POST['site_name'], ENT_QUOTES);
            if (empty($site_name))
            {
                throw new Exception('操作失败', 10);
            }
            $data['name'] = $site_name;

            $site_url = (empty($_POST['site_url'])) ? '' : $_POST['site_url'];
            if (empty($site_url) || !preg_match('#^http[s]?://#', $site_url))
            {
                throw new Exception('网站地址不能为空或请以http://开头', 10);
            }
            $data['url'] = $site_url;

            $color = (empty($_POST['color'])) ? '' : trim($_POST['color']);
            if (!empty($color) && !preg_match("/^#?([a-f]|[0-9]){3}(([a-f]|[0-9]){3})?$/i", $color))
            {
                throw new Exception('颜色代码不正确，（正确方式：#FF0000）', 10);
            }
            $data['namecolor'] = $color;

            $cityclass_id = (empty($_POST['cityclass_id'])) ? 0 : $_POST['cityclass_id'];
            if (empty($cityclass_id))
            {
                throw new Exception('请选择分类', 10);
            }
            $data['cityclass_id'] = $cityclass_id;

            $order = (empty($_POST['order'])) ? 100 : $_POST['order'];
            $data['displayorder'] = $order;

            $start_time = (empty($_POST['start_time'])) ? 0 : strtotime($_POST['start_time']);
            $data['starttime'] = $start_time;

            $end_time = (empty($_POST['end_time'])) ? 0 : strtotime($_POST['end_time']);
            if ($end_time < $start_time)
            {
                throw new Exception('结束时间不能早于开始时间', 10);
            }
            $data['endtime'] = $end_time;

            $remark = (empty($_POST['remark'])) ? '' : trim($_POST['remark']);
            $data['remark'] = $remark;


            // 新增
            if ($action == 'add')
            {
                $tmp = app_db::select('ylmf_city_mingzhan', 'id', "(name = '{$site_name}' OR url = '{$site_url}' ) AND cityclass_id = {$cityclass_id}");
                if (!empty($tmp))
                {
                    throw new Exception('该网站已存在');
                }
                if (false === app_db::insert('ylmf_city_mingzhan', array_keys($data), array_values($data)))
                {
                    throw new Exception('数据库操作失败', 10);
                }
                else
                {
                    mod_login::message('添加成功', '?c=city_mingzhan');
                }

            }
            // 修改
            elseif ($action == 'modify')
            {
                $tmp = app_db::select('ylmf_city_mingzhan', 'id', "(name = '{$site_name}' OR url = '{$site_url}' ) AND cityclass_id = {$cityclass_id} AND id != {$id}");
                if (!empty($tmp))
                {
                    throw new Exception('该网站已存在');
                }

                $old_class = app_db::select('ylmf_city_mingzhan', 'cityclass_id', "id = {$id}");
                if ($id < 1 || false === app_db::update('ylmf_city_mingzhan', $data, "id = {$id}"))
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
                mod_login::message('操作失败', '?c=city_mingzhan');
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }

        app_tpl::assign('action', $action);
        app_tpl::assign('referer', $referer);

        $_POST['name'] = $_POST['site_name'];
        $_POST['url'] = $_POST['site_url'];
        $_POST['starttime'] = $_POST['start_time'];
        $_POST['endtime'] = $_POST['end_time'];
        $_POST['displayorder'] = $_POST['order'];
        $_POST['namecolor'] = $_POST['color'];
        $_POST['remark'] = $_POST['remark'];
        $_POST['cityclass'] = $_POST['cityclass_id'];
        app_tpl::assign('data', $_POST);

        // 分类列表
        $class_list = mod_city_cityclass::get_list();
        app_tpl::assign('class_list', $class_list);

        app_tpl::display('city_mingzhan_edit.tpl');
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
                app_tpl::assign( 'npa', array('城市导航', '编辑名站') );
                $id = (empty($_GET['id'])) ? 0 : $_GET['id'];
                if ($id < 1)
                {
                    throw new Exception('操作失败', 10);
                }
                $id = (int)$id;

                $result = mod_city_mingzhan::get_one($id);
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
                app_tpl::assign( 'npa', array('城市导航', '新增名站') );
                app_tpl::assign('action', 'add');
            }

            // 分类列表
            $class_list = mod_city_cityclass::get_list();
            if (empty($class_list))
            {
                throw new Exception('没有创建分类', 10);
            }
            app_tpl::assign('class_list', $class_list);
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('city_mingzhan_edit.tpl');
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
            $referer = (empty($_POST['referer'])) ? '?c=city_mingzhan' : $_POST['referer'];
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
                    app_db::update('ylmf_city_mingzhan', array('displayorder' => $val), "id = {$key}");
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
                    app_db::update('ylmf_city_mingzhan', array('inindex' => 1), "id IN ($condition)");
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
                    app_db::update('ylmf_city_mingzhan', array('inindex' => 0), "id IN ($condition)");
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
                        $tmp = mod_city_mingzhan::get_one($key);
                        $name .= $tmp['name'] . ', ';
                    }
                    (!empty($name)) && $name = substr($name, 0, -2);
                    app_tpl::assign('message', '您将删除下列站点：<strong>' . $name . '</strong>，确认删除吗？');
                    app_tpl::assign('delete', implode(',', $delete));
                    app_tpl::assign('referer', $referer);
                    app_tpl::assign('action_url', '?c=city_mingzhan&a=list_edit');

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
                        app_db::delete('ylmf_city_mingzhan', "id IN ($condition)");
                    }
                }
            }
            mod_login::message('操作成功', $referer);
        }
        catch (Exception $e)
        {

        }
    }

}
?>
