<{include file=header.tpl}>
<div class="wrap">
    <div class="container">
        <div id="main">
            <div class="con box-green">
                <form action="?c=zhuanti&a=save" method="post">
                <input type="hidden" name="action" value="<{$action}>"/>
                <input type="hidden" name="id" value="<{$data.id}>"/>
                <input type="hidden" name="referer" value="<{$referer}>"/>
                <div class="box-header">
                    <h4><{if $action eq 'modify'}>�޸�<{else}></>���<{/if}>ר��</h4>
                </div>
                <div class="box-content">
                    <table class="table-font">
                        <tr>
                            <th class="w120">�������ƣ�</th>
                            <td><input type="text" name="name" value="<{$data.name}>" class="textinput w270" /></td>
                        </tr>                                          
                        <tr>
                            <th class="w120">�Զ���·��/�ļ�����</th>
                            <td><input type="text" name="path" value="<{$data.path}>" class="textinput w270" /></td>
                        </tr>

                        <tr>
                            <th class="w120">����ģ�壺</th>
                            <td><input type="text" name="template" value="<{$data.template}>" class="textinput w270" /></td>
                        </tr>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="box-footer-inner">
                    	<input type="submit" value="�ύ" /></a>
                    </div>
                </div>
                </form>
            </div><!--/ con-->
            
        </div>    
    </div><!--/ container-->

</div><!--/ wrap-->
<{include file=footer.tpl}>
