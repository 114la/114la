<?php

/**
 * 模块管理
 *
 * @copyright http://www.114la.com
 * @version    $Id: ctl_template_manage.php 1093 2009-11-28 02:50:16Z syh $
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_template_manage
{
    /**
     * 前台模板目录
     * @var string
     */
    private static $dir_tpl_main;

    /**
     * pre钩子 [构造函数功能] 请看 applications/app_router.php
     *
     * @return void
     */
    public static function pre()
    {
        if (!self::$dir_tpl_main = mod_config::get_one_config('yl_dirtplmain'))
        {
            self::$dir_tpl_main = 'default';
        }
    }

    /**
     * 模板管理列表
     *
     * @return void
     */
    public static function template_list()
    {
        try
        {
            $folder = empty($_GET['folder']) ? 0 : $_GET['folder'];
            app_tpl::assign('folder', $folder);
            if (empty($folder)) 
            {
                throw new Exception("模板分类不存在！");
            }
            app_tpl::assign('npa', array('模板管理', '模板管理列表'));
            $data = mod_template::template_list($folder);
            app_tpl::assign('data',$data);
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        $sys = array('goback'=>'?c=template_manage&a=template_list&folder='.$folder, 'subform'=>'?c=template_manage&a=template_delete&folder='.$folder);
        app_tpl::assign('sys', $sys);
        app_tpl::display('template_list.tpl');
    }

    /**
     * 删除模板
     *
     * @return void
     */
    public static function template_delete()
    {
        $referer = $_SERVER['HTTP_REFERER'];
        $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
        $tpl_files = ($_GET['tpl_file']) ? (array)$_GET['tpl_file'] : $_POST['tpl_file'];
        empty($tpl_files) && exit(mod_login::message("请选择要删除的模板!", $_POST['referer']));
        $msg = '';
        $all_success = TRUE; //所有删除都成功
        foreach ($tpl_files as $tpl_file) 
        {
            $filename = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder . '/' . $tpl_file;
            $filename_bak = $filename . TPLS_BACKUP_EXT;
            //删除模板文件和模板备份文件
            if (is_file($filename))
            {
                //如果删除不成功，拼凑报错信息
                if (!@unlink($filename))
                {
                    $msg .= "模板文件 " .$tpl_file . "删除<font color=red>失败</font>，请检查该文件是否有删除权限<br/>";
                }
            }
            if (is_file($filename_bak))
            {
                if (!@unlink($filename_bak))
                {
                    $msg .= "模板备份文件 " .$tpl_file . TPLS_BACKUP_EXT . "删除<font color=red>失败</font>，请检查该文件是否有删除权限<br/>";
                }
            }
            //两个文件都不存在了
            if (is_file($filename) || is_file($filename_bak))
            {
                $all_success = FALSE;
            }
        }
        //所有都成功的话直接显示删除模板成功
        if ($all_success)
        {
            exit(mod_login::message("删除模板成功!", '?c=template_manage&a=template_list&folder='.$folder));
        }
        //只要有一个不成功，就显示所有错误详细信息
        else
        {
            exit(mod_login::message($msg, '?c=template_manage&a=template_list&folder='.$folder));
        }
    }

    /**
     * 添加模板
     *
     * @return void
     */
    public static function template_add()
    {
        try
        {
            $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
            app_tpl::assign('folder', $folder);
            if (empty($_POST))
            {
                app_tpl::assign('npa', array('模板管理', '添加模板'));
                $sys = array('goback'=>'?c=template_manage&a=template_list&folder='.$folder, 'subform'=>'?c=template_manage&a=template_add&folder='.$folder);
                app_tpl::assign('sys', $sys);
            }
            else
            {
                $content = (empty($_POST['content'])) ? '' : htmlspecialchars_decode(stripslashes($_POST['content']));
                app_tpl::assign('content', $content);

                $tpl_file = empty($_POST['tpl_file']) ? '' : $_POST['tpl_file'];
                if (empty($tpl_file)) 
                {
                    //class_1.tpl topic_1.tpl city_1.tpl
                    $tpl_file = $folder . "_" . mod_template::get_max_id($folder) . ".tpl";
                }

                //保存模板文件和模板备份文件,删除的时候记得两个都删除了
                $filepath = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder;
                if (path_exists($filepath) === false) 
                {
                    throw new Exception('目录 <strong>' . $filepath . '</strong> 没有写入权限。');
                }
                $filename = $filepath . '/' . $tpl_file;
                if (false == mod_file::write($filename, $content, 'wb+'))
                {
                    throw new Exception('文件' . $tpl_filename . ' 保存失败');
                }
                @chmod($filename, 0777);
                //保存备份文件
                $filename_bak = $filename . TPLS_BACKUP_EXT;
                if (false == mod_file::write($filename_bak, $content, 'wb+'))
                {
                    throw new Exception('文件' . $tpl_filename . TPLS_BACKUP_EXT . ' 保存失败');
                }
                @chmod($filename, 0777);

                exit(mod_login::message("添加模板成功!", '?c=template_manage&a=template_list&folder='.$folder));
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('template_form.tpl');
        
    }

    /**
     * 添加模板
     *
     * @return void
    */
    public static function template_edit()
    {
        try
        {
            $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
            app_tpl::assign('folder', $folder);
            $tpl_file = empty($_GET['tpl_file']) ? '' : $_GET['tpl_file'];
            app_tpl::assign('tpl_file', $tpl_file);

            $filepath = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder;
            $filename = $filepath  . '/' . $tpl_file;

            //编辑修改模板
            if (empty($_POST))
            {
                app_tpl::assign('npa', array('模板管理', '修改模板'));
                $sys = array('goback'=>'?c=template_manage&a=template_list&folder='.$folder, 'subform'=>'?c=template_manage&a=template_edit&folder='.$folder);
                app_tpl::assign('sys', $sys);

                app_tpl::assign('content', htmlspecialchars(mod_file::read($filename), ENT_QUOTES));
            }
            //保存修改模板
            else
            {
                $content = (empty($_POST['content'])) ? '' : htmlspecialchars_decode(stripslashes($_POST['content']));
                //过滤前后空格
                $tpl_file = (empty($_POST['tpl_file'])) ? '' : trim($_POST['tpl_file']);
                $filename = $filepath  . '/' . $tpl_file;
                $tpl_file_old = (empty($_POST['tpl_file_old'])) ? '' : trim($_POST['tpl_file_old']);
                $filename_old = $filepath  . '/' . $tpl_file_old;
                if ($tpl_file != $tpl_file_old) 
                {
                    //改名字
                    @rename($filename_old, $filename);
                    if (file_exists($filename_old.'.bak')) 
                    {
                        @rename($filename_old.'.bak', $filename.'.bak');
                    }
                }

                //保存模板文件和模板备份文件,删除的时候记得两个都删除了
                if (false == mod_file::write($filename, $content, 'wb+'))
                {
                    throw new Exception('文件' . $tpl_file . ' 保存失败');
                }

                exit(mod_login::message("修改模板成功!", '?c=template_manage&a=template_list&folder='.$folder));
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('template_form.tpl');
    }

    /**
     * 备份模板
     *
     * @return void
     */
    public static function backup()
    {
        $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
        $tpl_file = empty($_GET['tpl_file']) ? '' : $_GET['tpl_file'];
        empty($tpl_file) && exit(mod_login::message("模板不存在!", '?c=template_manage&a=template_list&folder='.$folder));

        $filepath = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder;
        $filename = $filepath  . '/' . $tpl_file;
        $filename_bak = $filename . TPLS_BACKUP_EXT;
        if (@copy($filename, $filename_bak))
        {
            exit(mod_login::message("模板备份成功!", '?c=template_manage&a=template_list&folder='.$folder));
        }
        exit(mod_login::message("模板备份失败,请检查文件权限!", '?c=template_manage&a=template_list&folder='.$folder));
    }

    /**
     * 恢复模板
     *
     * @return void
     */
    public static function restore()
    {

        $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
        $tpl_file = empty($_GET['tpl_file']) ? '' : $_GET['tpl_file'];
        empty($tpl_file) && exit(mod_login::message("模板不存在!", '?c=template_manage&a=template_list&folder='.$folder));

        $filepath = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder;
        $filename = $filepath  . '/' . $tpl_file;
        $filename_bak = $filename . TPLS_BACKUP_EXT;
        !file_exists($filename_bak) && exit(mod_login::message("模板备份 " . $tpl_file . TPLS_BACKUP_EXT . " 不存在!", '?c=template_manage&a=template_list&folder='.$folder));

        if (@copy($filename_bak, $filename))
        {
            exit(mod_login::message("模板还原成功!", '?c=template_manage&a=template_list&folder='.$folder));
        }
        exit(mod_login::message("模板还原失败,请检查文件权限!", '?c=template_manage&a=template_list&folder='.$folder));
    }

    /**
     * 模板管理
     *
     * @return void
     */
    public static function index()
    {
        try
        {
            app_tpl::assign('npa', array('模板管理', '模板编辑'));
            //$action = 'modify' | 'save' | 'restore' 修改 保存 还原
            $action = (empty($_GET['action'])) ? '' : $_GET['action'];

            // 读文件或恢复到默认模板
            if ($action == 'modify' || $action == 'restore')
            {
                $ini_filename = (empty($_GET['filename'])) ? '' : $_GET['filename'];
                if (empty($ini_filename))
                {
                    throw new Exception('文件 ' . $ini_filename . ' 不存在');
                }

                $filename = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $ini_filename;
                if ($action == 'restore')
                {
                    $filename .= TPLS_BACKUP_EXT;
                }

                if (!file_exists($filename))
                {
                    throw new Exception('文件 ' . $ini_filename . ' 不存在');
                }
                if ($action == 'modify')
                {
                    // 不存在备份文件则备份一下原始文件
                    if (!file_exists($filename . TPLS_BACKUP_EXT) && !@copy($filename, $filename . TPLS_BACKUP_EXT))
                    {
                        throw new Exception('文件 ' . $ini_filename . TPLS_BACKUP_EXT . ' 备份失败');
                    }
                }
                else
                {
                    // 恢复模板
                    $old_filename = substr($filename, 0, -strlen((TPLS_BACKUP_EXT)));
                    $bak_filename = $filename;
                    if (!mod_file::write($old_filename, mod_file::read($bak_filename)))
                    {
                        throw new Exception('文件' . $ini_filename . ' 恢复失败');
                    }

                    if (preg_match('/index\.tpl/i', $filename))
                    {
                        mod_make_html::auto_update('index');
                    }
                    elseif (preg_match('/other\_(header|footer|body)\.tpl/i', $filename))
                    {
                        mod_make_html::auto_update('other');
                    }
                }

                app_tpl::assign('content', htmlspecialchars(mod_file::read($filename), ENT_QUOTES));
                app_tpl::assign('filename', $ini_filename);
                app_tpl::assign('back', '?c=template_manage&action=modify&filename=' . $ini_filename);
            }
            // 写文件
            elseif ($action == 'save')
            {
                $content = (empty($_POST['content'])) ? '' : htmlspecialchars_decode(stripslashes($_POST['content']));
                if (empty($content))
                {
                    throw new Exception('请输入模块内容', 10);
                }

                $ini_filename = (empty($_POST['filename'])) ? '' : $_POST['filename'];
                if (empty($ini_filename))
                {
                    throw new Exception('文件 ' . $ini_filename . ' 不存在');
                }

                $filename = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $ini_filename;
                // 不能修改原始备份文件
                if (!file_exists($filename) || substr($filename, -(strlen(TPLS_BACKUP_EXT))) == TPLS_BACKUP_EXT)
                {
                    throw new Exception('文件 ' . $ini_filename . ' 不存在');
                }

                if (false == mod_file::write($filename, $content, 'wb+'))
                {
                    throw new Exception('文件' . $ini_filename . ' 保存失败');
                }
                @chmod($filename, 0777);

                if (preg_match('/index\.tpl/i', $filename))
                {
                    mod_make_html::auto_update('index');
                }
                elseif (preg_match('/other\_(header|footer|body)\.tpl/i', $filename))
                {
                    mod_make_html::auto_update('other');
                }

                mod_login::message('操作成功', (empty($_POST['referer'])) ? '?c=template_manage' : $_POST['referer']);
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        if (preg_match('/class\_(header|footer|body)\.tpl/i', $ini_filename))
        {
            app_tpl::assign('show_msg', 1);
        }
        app_tpl::display('template_manage.tpl');
    }

    /**
     * 模板选择
     *
     * @return void
     */
    public static function cur_tpl()
    {
        app_tpl::assign('npa', array('模板管理', '模板选择'));
        // 删除
        if (!empty($_GET['delete']))
        {
            $tpl = PATH_TPLS_MAIN . '/' . $_GET['delete'];
            if (is_dir($tpl))
            {
                mod_file::rm_recurse($tpl);
                mod_login::message('操作成功', '?c=template_manage&a=cur_tpl');
            }
        }
        // 启用
        elseif (!empty($_GET['apply']))
        {
            mod_config::set_configs(array('yl_dirtplmain' => $_GET['apply']));
            if (!empty($_GET['mkhtml']))
            {
                mod_make_html::flush('正在生成首页……');
                mod_make_html::make_html_index();
                mod_make_html::flush($ok);
            }
            mod_login::message('操作成功', '?c=template_manage&a=cur_tpl');
        }

        $dirs = mod_file::ls(PATH_TPLS_MAIN, 'dir');
        if (file_exists(PATH_TPLS_MAIN . '/' . self::$dir_tpl_main))
        {
            $cur_dir = self::$dir_tpl_main;
        }
        else
        {
            $cur_dir = current($dirs);
            if ($cur_dir === false)
            {
                //无模板
                //显示相关页，退出
            }
        }

        //显示正常模板
        $other_tpls = array();
        foreach ($dirs as $dir)
        {
            $name_filename = PATH_TPLS_MAIN . '/' . $dir . '/NAME';
            if (is_file($name_filename))
            {
                $cur_name = @file_get_contents($name_filename);
                if (empty($cur_name))
                {
                    $cur_name = $dir;
                }
            }
            else
            {
                $cur_name = $dir;
            }

            $tpl = array('path' => $dir, 'name' => $cur_name, 'preview' => ADMIN . '/tpls/tpls/main/' . $dir . '/PREVIEW.jpg');
            if ($dir == $cur_dir)
            {
                $cur_tpl = $tpl; // 当前模板的数据
            }
            else
            {
                $other_tpls[] = $tpl;
            }
        }

        if (!empty($cur_tpl))
        {
            app_tpl::assign('cur_tpl', $cur_tpl);
        }
        if (!empty($other_tpls))
        {
            app_tpl::assign('other_tpls', $other_tpls);
        }

        app_tpl::display('template_select.tpl');
    }

}

?>
