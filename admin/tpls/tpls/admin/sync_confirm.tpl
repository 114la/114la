<{include file=header.tpl}>
<div class="wrap">
    <div class="container">
        <div id="main">
            <div class="con">
                <div class="tips warn-ico">
                <form action="<{$action_url}>" method="post">
                    <input type="hidden" name="step" value="1"/>
                    <input type="hidden" name="sync" value="<{$sync}>"/>
                    <input type="hidden" name="referer" value="<{$referer}>"/>
                   	    <{$message}><input type="submit" value="确定同步" /> 或 <a href="<{$referer}>">取消</a>
                    </form>
                </div>                
      </div><!--/ con-->
        </div>    
    </div><!--/ container-->

</div><!--/ wrap-->
</body>
</html>
