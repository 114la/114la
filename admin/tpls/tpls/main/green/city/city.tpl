<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
        <title><{$current_city}></title>
        <meta name="description" content="114��<{$current_city}>�������ṩ<{$current_city}>�������Ϣ" />
        <link rel="stylesheet" type="text/css" href="<{$URL}>/static/css/new_green/style.css" />
        <link rel="stylesheet" type="text/css" href="<{$URL}>/static/css/new_green/city.css" media="all" />
        <base target="_blank" />
        <style type="text/css">
            body,td,th <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
        <title><{$current_city}>����</title>
        <meta name="description" content="114��<{$current_city}>�������ṩ<{$current_city}>�������Ϣ" />
        <link rel="stylesheet" type="text/css" href="<{$URL}>/static/css/style.css" />
        <link rel="stylesheet" type="text/css" href="<{$URL}>/static/css/city.css" media="all" />
        <base target="_blank" />
        <style type="text/css">
            body,td,th {
                font-family: Tahoma, Helvetica, Arial, \5b8b\4f53, sans-serif;
            }
        </style>
    </head>
    <body>
        <div id="page" class="container">
        <div id="header" class="box">
			<div class="con clearfix">
            <h1 id="logo"><a href="<{$URL}>"><img src="<{$URL}>/static/images/logo.gif" alt="" /></a></h1>
            <div class="searchform">
                <form id="searchForm" action="http://115.com/s" method="get" target="_blank">
                    <a class="label" href="http://115.com"><img width="105" height="35" alt="115����" src="<{$URL}>/static/images/s/115.gif"></a>
                    <input type="text" name="q" class="text" autocomplete="off">
                    <input type="submit" class="submit" value="115����">
                    <input type="hidden" name="tn" value="ylmf_4_pg">
                    <input type="hidden" name="ch" value="6">
                </form>
                <div class="ctrl">
                    <form id="ctrl_form">
                        <label for="s115_item"><input class="radio" type="radio" value="s115" name="search_select"  checked="checked" id="s115_item" />115����</label>
                        <label for="baidu_item"><input class="radio" type="radio" value="baidu" name="search_select" id="baidu_item" />�ٶ�</label>
                        <label for="google_item"><input class="radio" type="radio" value="google" name="search_select" id="google_item" />Google</label>
                    </form>
                </div>
            </div>
        </div>
		</div>
            <div class="guide clearfix">
                <span class="location">
                    <strong>����ǰ��λ�ã�</strong>
                    <a href="<{$URL}>" target="_parent">������ҳ</a> &raquo; <{$current_city}>���е���
                </span>
                <span class="meta">
                    <a href="<{$URL}>/feedback/" class="feedback">���Է���</a>
                    <a href="javascript://"  onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('<{$URL}>')" class="sethome" target="_parent">��114����Ϊ��ҳ</a>
                </span>
            </div>
            <!-- / giude-->


            <div id="content" class="clearfix">
                <div id="side"> <b class="rc-tp1"><b></b></b>
                    <div class="con">
                        <div id="tool">
                            <h2 class="tool-title">ʵ�ù���<span><a href="http://tool.115.com/" rel="nr">����&raquo;</a></span></h2>
                            <ul>
                                 <{*ʵ�ù���*}>
                                 <{foreach from = $tools item = i}>
                                 <li><a href="<{$i.url}>"<{if $i.color}> style="color:<{$i.color}>"<{/if}>><{$i.name}></a></li>
                                 <{/foreach}>
                            </ul>
                        </div>
                        <!-- / tool-->

                        <{*��վ����*}>
                        <{foreach from = $site_class key = k item = parent}>
                        <h2><{$k}></h2>
                        <ul<{if $parent.0.classname_len > 6}> class="c2"<{/if}>>
                            <{foreach from = $parent item = i}>
                            <li ><a href="<{$i.urlpath}>"><{$i.classname}></a></li>
                            <{/foreach}>
                        </ul>
                <{/foreach}>

                    </div>
                    <!-- / con-->
                    <b class="rc-bt"><b></b></b> </div>
                <!-- / size-->

                <div id="main">

                    <div id="board"> <b class="rc-tp1"><b></b></b>
                        <div class="box">
                            <div id="bm">
                                <ul id="bm_tab" class="clearfix">
                                    <li id="bm-def" class="active" rel="fm"><{$current_city}>��վ</li>
                                </ul>
                            </div>
                            <!-- / bm-->

                            <div id="bb">
                                <div class="con">
                                    <ul id="fm" class="clearfix">
                                        <{if $mingzhan_list}>
                                        <{foreach from=$mingzhan_list item=mingzhan}>
                                        <li><a href="<{$mingzhan.url}>"><{$mingzhan.name}></a></li>
                                        <{/foreach}>
                                        <{/if}>
                                    </ul>

                                </div>
                                <!-- / con-->
                            </div>
                            <!-- / bb-->
                        </div>

                        <!-- / box-->
                        <b class="rc-bt"><b></b></b> </div>
                    <!-- / board-->

                    <div id="ls"> <b class="rc-tp1"><b></b></b>
                        <div class="box">
                            <{if $coolclass_list}>
                            <{foreach from=$coolclass_list item=coolclass name=foo}>
                            <dl <{if $smarty.foreach.foo.index is not odd}>class="alt"<{/if}> id="ls<{$smarty.foreach.foo.index}>">
                                <dt><a href="<{$coolclass.url}>"><{$coolclass.name}></a></dt>
                                <dd class="l">
                                    <{if $coolclass.son}>
                                    <ul>
                                        <{foreach from=$coolclass.son item=son}>
                                        <li><a href="<{$son.url}>" ><{$son.name}></a></li>
                                        <{/foreach}>
                                    </ul>
                                    <{/if}>
                                </dd>
                                <dd class="m"><a href="<{$coolclass_list.url}>">���� &raquo;</a></dd>
                            </dl>
                            <{/foreach}>
                            <{/if}>
                        </div>
                        <!-- / box-->
                        <b class="rc-bt"><b></b></b> </div>
                </div>
                <!-- / main-->

            </div>
            <!-- / content-->

            <div id="citys"> <b class="rc-tp1"><b></b></b>

                <div class="box">
                    <div class="con"> <strong>�������У�</strong> <{if $other_city_list}><{foreach from=$other_city_list item=other_city}><a href="<{$other_city.url}>"><{$other_city.name}></a> <{/foreach}><{/if}></div>
                </div>
                <b class="rc-bt"><b></b></b> </div>
            <!-- / citys-->

            <div id="footer" class="clearfix"> <a href="<{$URL}>" target="_parent" class="goback">������ҳ</a> </div>
            <div id="gotop"><a href="#page" target="_self">���ض���</a></div>
        </div>
		<script type="text/javascript" src="<{$URL}>/public/js/ylmf.js"></script>
		<script type="text/javascript" src="<{$URL}>/public/page/js/common.js"></script>
		<script type="text/javascript" src="<{$URL}>/static/js/backtop.js"></script>
    </body>
	<script type="text/javascript" src="<{$URL}>/static/js/opensug_resoucre.js"></script>
</html>
