<?php

/**
 * ģ�����
 *
 * @copyright http://www.114la.com
 * @version    $Id: ctl_template_manage.php 1093 2009-11-28 02:50:16Z syh $
 */
!defined('PATH_ADMIN') && exit('Forbidden');

class ctl_template_manage
{
    /**
     * ǰ̨ģ��Ŀ¼
     * @var string
     */
    private static $dir_tpl_main;

    /**
     * pre���� [���캯������] �뿴 applications/app_router.php
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
     * ģ������б�
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
                throw new Exception("ģ����಻���ڣ�");
            }
            app_tpl::assign('npa', array('ģ�����', 'ģ������б�'));
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
     * ɾ��ģ��
     *
     * @return void
     */
    public static function template_delete()
    {
        $referer = $_SERVER['HTTP_REFERER'];
        $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
        $tpl_files = ($_GET['tpl_file']) ? (array)$_GET['tpl_file'] : $_POST['tpl_file'];
        empty($tpl_files) && exit(mod_login::message("��ѡ��Ҫɾ����ģ��!", $_POST['referer']));
        $msg = '';
        $all_success = TRUE; //����ɾ�����ɹ�
        foreach ($tpl_files as $tpl_file) 
        {
            $filename = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder . '/' . $tpl_file;
            $filename_bak = $filename . TPLS_BACKUP_EXT;
            //ɾ��ģ���ļ���ģ�屸���ļ�
            if (is_file($filename))
            {
                //���ɾ�����ɹ���ƴ�ձ�����Ϣ
                if (!@unlink($filename))
                {
                    $msg .= "ģ���ļ� " .$tpl_file . "ɾ��<font color=red>ʧ��</font>��������ļ��Ƿ���ɾ��Ȩ��<br/>";
                }
            }
            if (is_file($filename_bak))
            {
                if (!@unlink($filename_bak))
                {
                    $msg .= "ģ�屸���ļ� " .$tpl_file . TPLS_BACKUP_EXT . "ɾ��<font color=red>ʧ��</font>��������ļ��Ƿ���ɾ��Ȩ��<br/>";
                }
            }
            //�����ļ�����������
            if (is_file($filename) || is_file($filename_bak))
            {
                $all_success = FALSE;
            }
        }
        //���ж��ɹ��Ļ�ֱ����ʾɾ��ģ��ɹ�
        if ($all_success)
        {
            exit(mod_login::message("ɾ��ģ��ɹ�!", '?c=template_manage&a=template_list&folder='.$folder));
        }
        //ֻҪ��һ�����ɹ�������ʾ���д�����ϸ��Ϣ
        else
        {
            exit(mod_login::message($msg, '?c=template_manage&a=template_list&folder='.$folder));
        }
    }

    /**
     * ���ģ��
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
                app_tpl::assign('npa', array('ģ�����', '���ģ��'));
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

                //����ģ���ļ���ģ�屸���ļ�,ɾ����ʱ��ǵ�������ɾ����
                $filepath = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder;
                if (path_exists($filepath) === false) 
                {
                    throw new Exception('Ŀ¼ <strong>' . $filepath . '</strong> û��д��Ȩ�ޡ�');
                }
                $filename = $filepath . '/' . $tpl_file;
                if (false == mod_file::write($filename, $content, 'wb+'))
                {
                    throw new Exception('�ļ�' . $tpl_filename . ' ����ʧ��');
                }
                @chmod($filename, 0777);
                //���汸���ļ�
                $filename_bak = $filename . TPLS_BACKUP_EXT;
                if (false == mod_file::write($filename_bak, $content, 'wb+'))
                {
                    throw new Exception('�ļ�' . $tpl_filename . TPLS_BACKUP_EXT . ' ����ʧ��');
                }
                @chmod($filename, 0777);

                exit(mod_login::message("���ģ��ɹ�!", '?c=template_manage&a=template_list&folder='.$folder));
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('template_form.tpl');
        
    }

    /**
     * ���ģ��
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

            //�༭�޸�ģ��
            if (empty($_POST))
            {
                app_tpl::assign('npa', array('ģ�����', '�޸�ģ��'));
                $sys = array('goback'=>'?c=template_manage&a=template_list&folder='.$folder, 'subform'=>'?c=template_manage&a=template_edit&folder='.$folder);
                app_tpl::assign('sys', $sys);

                app_tpl::assign('content', htmlspecialchars(mod_file::read($filename), ENT_QUOTES));
            }
            //�����޸�ģ��
            else
            {
                $content = (empty($_POST['content'])) ? '' : htmlspecialchars_decode(stripslashes($_POST['content']));
                //����ǰ��ո�
                $tpl_file = (empty($_POST['tpl_file'])) ? '' : trim($_POST['tpl_file']);
                $filename = $filepath  . '/' . $tpl_file;
                $tpl_file_old = (empty($_POST['tpl_file_old'])) ? '' : trim($_POST['tpl_file_old']);
                $filename_old = $filepath  . '/' . $tpl_file_old;
                if ($tpl_file != $tpl_file_old) 
                {
                    //������
                    @rename($filename_old, $filename);
                    if (file_exists($filename_old.'.bak')) 
                    {
                        @rename($filename_old.'.bak', $filename.'.bak');
                    }
                }

                //����ģ���ļ���ģ�屸���ļ�,ɾ����ʱ��ǵ�������ɾ����
                if (false == mod_file::write($filename, $content, 'wb+'))
                {
                    throw new Exception('�ļ�' . $tpl_file . ' ����ʧ��');
                }

                exit(mod_login::message("�޸�ģ��ɹ�!", '?c=template_manage&a=template_list&folder='.$folder));
            }
        }
        catch (Exception $e)
        {
            app_tpl::assign('error', $e->getMessage());
        }
        app_tpl::display('template_form.tpl');
    }

    /**
     * ����ģ��
     *
     * @return void
     */
    public static function backup()
    {
        $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
        $tpl_file = empty($_GET['tpl_file']) ? '' : $_GET['tpl_file'];
        empty($tpl_file) && exit(mod_login::message("ģ�岻����!", '?c=template_manage&a=template_list&folder='.$folder));

        $filepath = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder;
        $filename = $filepath  . '/' . $tpl_file;
        $filename_bak = $filename . TPLS_BACKUP_EXT;
        if (@copy($filename, $filename_bak))
        {
            exit(mod_login::message("ģ�屸�ݳɹ�!", '?c=template_manage&a=template_list&folder='.$folder));
        }
        exit(mod_login::message("ģ�屸��ʧ��,�����ļ�Ȩ��!", '?c=template_manage&a=template_list&folder='.$folder));
    }

    /**
     * �ָ�ģ��
     *
     * @return void
     */
    public static function restore()
    {

        $folder = empty($_GET['folder']) ? 'class' : $_GET['folder'];
        $tpl_file = empty($_GET['tpl_file']) ? '' : $_GET['tpl_file'];
        empty($tpl_file) && exit(mod_login::message("ģ�岻����!", '?c=template_manage&a=template_list&folder='.$folder));

        $filepath = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $folder;
        $filename = $filepath  . '/' . $tpl_file;
        $filename_bak = $filename . TPLS_BACKUP_EXT;
        !file_exists($filename_bak) && exit(mod_login::message("ģ�屸�� " . $tpl_file . TPLS_BACKUP_EXT . " ������!", '?c=template_manage&a=template_list&folder='.$folder));

        if (@copy($filename_bak, $filename))
        {
            exit(mod_login::message("ģ�廹ԭ�ɹ�!", '?c=template_manage&a=template_list&folder='.$folder));
        }
        exit(mod_login::message("ģ�廹ԭʧ��,�����ļ�Ȩ��!", '?c=template_manage&a=template_list&folder='.$folder));
    }

    /**
     * ģ�����
     *
     * @return void
     */
    public static function index()
    {
        try
        {
            app_tpl::assign('npa', array('ģ�����', 'ģ��༭'));
            //$action = 'modify' | 'save' | 'restore' �޸� ���� ��ԭ
            $action = (empty($_GET['action'])) ? '' : $_GET['action'];

            // ���ļ���ָ���Ĭ��ģ��
            if ($action == 'modify' || $action == 'restore')
            {
                $ini_filename = (empty($_GET['filename'])) ? '' : $_GET['filename'];
                if (empty($ini_filename))
                {
                    throw new Exception('�ļ� ' . $ini_filename . ' ������');
                }

                $filename = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $ini_filename;
                if ($action == 'restore')
                {
                    $filename .= TPLS_BACKUP_EXT;
                }

                if (!file_exists($filename))
                {
                    throw new Exception('�ļ� ' . $ini_filename . ' ������');
                }
                if ($action == 'modify')
                {
                    // �����ڱ����ļ��򱸷�һ��ԭʼ�ļ�
                    if (!file_exists($filename . TPLS_BACKUP_EXT) && !@copy($filename, $filename . TPLS_BACKUP_EXT))
                    {
                        throw new Exception('�ļ� ' . $ini_filename . TPLS_BACKUP_EXT . ' ����ʧ��');
                    }
                }
                else
                {
                    // �ָ�ģ��
                    $old_filename = substr($filename, 0, -strlen((TPLS_BACKUP_EXT)));
                    $bak_filename = $filename;
                    if (!mod_file::write($old_filename, mod_file::read($bak_filename)))
                    {
                        throw new Exception('�ļ�' . $ini_filename . ' �ָ�ʧ��');
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
            // д�ļ�
            elseif ($action == 'save')
            {
                $content = (empty($_POST['content'])) ? '' : htmlspecialchars_decode(stripslashes($_POST['content']));
                if (empty($content))
                {
                    throw new Exception('������ģ������', 10);
                }

                $ini_filename = (empty($_POST['filename'])) ? '' : $_POST['filename'];
                if (empty($ini_filename))
                {
                    throw new Exception('�ļ� ' . $ini_filename . ' ������');
                }

                $filename = PATH_TPLS_MAIN . '/' . self::$dir_tpl_main . '/' . $ini_filename;
                // �����޸�ԭʼ�����ļ�
                if (!file_exists($filename) || substr($filename, -(strlen(TPLS_BACKUP_EXT))) == TPLS_BACKUP_EXT)
                {
                    throw new Exception('�ļ� ' . $ini_filename . ' ������');
                }

                if (false == mod_file::write($filename, $content, 'wb+'))
                {
                    throw new Exception('�ļ�' . $ini_filename . ' ����ʧ��');
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

                mod_login::message('�����ɹ�', (empty($_POST['referer'])) ? '?c=template_manage' : $_POST['referer']);
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
     * ģ��ѡ��
     *
     * @return void
     */
    public static function cur_tpl()
    {
        app_tpl::assign('npa', array('ģ�����', 'ģ��ѡ��'));
        // ɾ��
        if (!empty($_GET['delete']))
        {
            $tpl = PATH_TPLS_MAIN . '/' . $_GET['delete'];
            if (is_dir($tpl))
            {
                mod_file::rm_recurse($tpl);
                mod_login::message('�����ɹ�', '?c=template_manage&a=cur_tpl');
            }
        }
        // ����
        elseif (!empty($_GET['apply']))
        {
            mod_config::set_configs(array('yl_dirtplmain' => $_GET['apply']));
            if (!empty($_GET['mkhtml']))
            {
                mod_make_html::flush('����������ҳ����');
                mod_make_html::make_html_index();
                mod_make_html::flush($ok);
            }
            mod_login::message('�����ɹ�', '?c=template_manage&a=cur_tpl');
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
                //��ģ��
                //��ʾ���ҳ���˳�
            }
        }

        //��ʾ����ģ��
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
                $cur_tpl = $tpl; // ��ǰģ�������
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
