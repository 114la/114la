<{include file=header.tpl}>
<div class="wrap">
    <div class="container">
        <div id="main">
            <div class="con box-green">
                <form action="?c=zhuanti_class&a=save" method="post">
                <input type="hidden" name="action" value="<{$action}>"/>
                <input type="hidden" name="id" value="<{$data.id}>"/>
                <input type="hidden" name="referer" value="<{$referer}>"/>
                <div class="box-header">
                    <h4><{if $action eq 'modify'}>修改<{else}></>添加<{/if}>专题分类</h4>
                </div>
                <div class="box-content">
                    <table class="table-font">
                        <tr>
                            <th class="w120">分类名称：</th>
                            <td><input type="text" name="name" value="<{$data.name}>" class="textinput w270" /></td>
                        </tr>                                          
                        <tr>
                            <th class="w120">外链地址：</th>
                            <td><input type="text" name="url" value="<{$data.url}>" class="textinput w270" /></td>
                        </tr>                                          
                        <tr>
                        <tr>
                            <th  style="vertical-align:top;">所属专题：</th>
                            <td>                               
                                <select name="zhuanti">
                                    <option value="0">请选择……</option>
                                    <{foreach from=$zhuanti_list item = i}>
                                    <option value="<{$i.id}>"<{if $data.zhuanti eq $i.id}> selected="selected"<{/if}>><{$i.name}></option>
                                    <{/foreach}>
                                </select>
                            </td>
                        </tr>                        
                    </table>
                </div>
                <div class="box-footer">
                    <div class="box-footer-inner">
                    	<input type="submit" value="提交" /></a>
                    </div>
                </div>
                </form>
            </div><!--/ con-->
            
        </div>    
    </div><!--/ container-->

</div><!--/ wrap-->
<{include file=footer.tpl}>
