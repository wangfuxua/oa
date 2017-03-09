    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
				<ul class="ul1 note" id="c1">
<script>				
	function open_notify(NOTIFY_ID)
	{
 	URL="/index.php/manage/view/NOTIFY_ID/"+NOTIFY_ID;
 	myleft=(screen.availWidth-500)/2;
 	window.open(URL,"read_notify","height=400,width=550,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
	}

</script>
				
       <?php if($notifylist): ?><?php if(is_array($notifylist)): ?><?php $i = 0;?><?php $__LIST__ = $notifylist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><?php if(($vo[ATTACHMENT_ID])  !=  ""): ?><img src="/oa/Tpl/default/Public/images/ico/mail_files.gif"><?php endif; ?><a href="javascript:open_notify('<?php echo (is_array($vo)?$vo["NOTIFY_ID"]:$vo->NOTIFY_ID); ?>');"><?php echo (is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT); ?></a>&nbsp;<?php echo (formatDate(is_array($vo)?$vo["BEGIN_DATE"]:$vo->BEGIN_DATE)); ?><?php if(!find_id($vo['READERS'],$_SESSION[LOGIN_USER_ID]))
        echo "<img src='/oa/Tpl/default/Public/images/new.gif' height=11 width=28 align=absmiddle>";
        ?>
        </li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
       <?php else: ?>
       没有公告通知<?php endif; ?>
				</ul>