<{include file="header.tpl"}>
<script type="text/javascript" charset="utf-8">
    function toggle (parentid) 
    {
        var obj_id = 'parentid_' + parentid;
        $('.'+obj_id).toggle();
    }
</script>
<div class="wrap">
    <div class="container">

        <div id="main">
            
            <div class="con">
            	<form action="?c=city_cityclass&a=list_edit" method="post">
                    <input type="hidden" name="referer" value="<{$referer}>"/>
                  <div class="table">
                  	<div class="th">
                    	<div class="form">
                        <input type="button" value="��ӳ��з���" onclick="window.location='?c=city_cityclass&a=edit&action=add'" />&nbsp;
                        </div>
                    </div>
                   
                    <table class="admin-tb" id="js_data_source">
                    <tr>
                    	<th width="41" class="text-center">ɾ��</th>    
                        <th width="100">����</th>            	
                        <th width="180">���з�������</th>
                        <th width="200">�Զ���·��/�ļ�����</th>
                        <th width="200">�Զ���ģ�壺</th>
                        <th>����</th>
                    </tr>
                    <{if $list}>
                    <{foreach from=$list item=i}>
                    <tr>
                        <td class="text-center"><input rel="del" type="checkbox" name="delete[<{$i.id}>]"  /></td>
                        <td><input type="text" name="order[<{$i.id}>]" value="<{$i.displayorder}>" class="textinput" tabindex="11" /></td>                 
                        <td><{$i.name}></td>
                        <td><input type="text" name="path[<{$i.id}>]" value="<{$i.path}>" style="width:270" /></td>
                        <td><input type="text" name="template[<{$i.id}>]" value="<{$i.template}>" style="width:270" /></td>
                        <td><a href="?c=city_cityclass&a=edit&action=modify&id=<{$i.id}>">�޸�</a> <a href="<{$URL_HTML}>/city/<{if $i.path}><{$i.path}><{else}>city_<{$i.id}><{/if}>/index.htm" target="_blank">�鿴</a>
                        <a href="?c=city_cityclass&a=make_js&id=<{$i.id}>">������ҳ����JS</a></td>
                    </tr>
                    <{/foreach}>
                    <{else}>
                    <tr>
                        <td colspan=5><font color=red>���޿�վ����</font></td>
                    </tr>
                    <{/if}>
                    </table>
                    <script type="text/javascript">
                        $(document).ready(function(){
							$(".admin-tb").find("input[rel='del']").each(function(i){
								$(this).bind("click",function(){
									
									
								})
							});	
							
							
                            $("#js_data_source").find("input[rel='del']").each(function(i){
                                $(this).bind("click",function(){
                                    var tr = $(this).parent().parent();
                                    var input = tr.find("input[rel='dis']");
                                    if(this.checked){
										$(input).attr("oledchecked",$(input).attr("checked"));
										$(input).attr("checked","");
										$(input).attr("disabled","disabled");
									}
									else{
										$(input).attr("disabled","");
										$(input).attr("checked",$(input).attr("oledchecked"));
									}
									
									$(".admin-tb").find("input[rel='del']").each(function(i){
										var tr2 = $(this).parent().parent();
										if(this.checked){
											tr2.addClass("checked");
										}
										else{
											tr2.removeClass("checked");
										}
									})
                                });                             
                            });
                            $("#js_data_source").find("input[rel='dis']").change(function(){
                                if ($(this).next('input[type="hidden"]').val() == 1) $(this).next('input[type="hidden"]').val(0);
                                else $(this).next('input[type="hidden"]').val(1);
							});
                        });
                    </script>
                    <div class="th">
                    	<div class="form">
                        <input type="submit" value="�ύ�޸�" />&nbsp; <input type="button" value="����ȫ����ҳ����JS" onclick="location.href='?c=city_cityclass&a=make_js'" />
                        </div>
                    </div>
                </div>
				</form>
            </div><!--/ con-->
        </div>    
    </div><!--/ container-->
    </div><!--/ wrap-->
<{include file="footer.tpl"}>
