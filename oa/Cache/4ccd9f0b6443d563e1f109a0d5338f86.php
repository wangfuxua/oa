<style>
form input{ border:none}
#addfile{padding:10px 0 0 0px; position:relative; height:16px; overflow:hidden;}
#addfile a{ color:#005DAA; text-decoration:none}
#addfile a:hover{color:#005DAA;}
#myfile{position:absolute;left:-18px;*left:-40px;top:4px; height:20px;cursor:pointer;opacity:0;filter:alpha(opacity=0)}
#addfilebox{ display:none; height:30px; padding:0 10px 5px; margin:5px 0 0;background:#f6fcff; border:1px solid #ccc; overflow-y:scroll}
#addfilebox span{ float:left; margin-right:5px; margin-top:5px; padding:2px 8px; border:1px solid #ccc; background:#f9f9f9; cursor:pointer}
</style>
<div id="title" class="clearfix">
      <h2>收件箱</h2>

      <span class="rtact">全局查询
     
      <input type="hidden" id="READ_FLAG" name="READ_FLAG" value="-1">
      <input type="hidden" id="BOX_ID" name="BOX_ID" value="<?php echo ($BOX_ID); ?>">
      <input type="text" id="keyword" />
      <button type="button" id="searchsub">搜索</button>
      
    </span> 
      </div>
    <div id="active" class="active">
     <span id="back-btm"><a href="#">返回</a></span> 
     <span id="re-mail-btm"><a href="#">回复</a></span>    
      <span id="z-mail-btm"><a href="#">转发</a></span> 
     <span id="mail-del-btm"><a href="#">删除</a></span> 
      <p class="goupordown"><?php echo ($nextpre); ?></p>
     </div>
<div id="mail-centens" class="clearfix" style="padding:0">
    <div class="mailfrom"> 
      <h3><?php echo (is_array($row)?$row["SUBJECT"]:$row->SUBJECT); ?></h3> 
        <ul class="clearfix">
          <li style="position:relative"><strong id="sjr">发件人：</strong>
               <em></em><span><?php echo (is_array($row)?$row["FROM_NAME"]:$row->FROM_NAME); ?></span></li>
          <li><strong id="zt">日 &nbsp;期：</strong><span><?php echo (is_array($row)?$row["SEND_TIME"]:$row->SEND_TIME); ?></span></li>         
         <li><strong id="cs">收件人：</strong><em></em><span><?php echo (getList_name(is_array($row)?$row["TO_ID2"]:$row->TO_ID2)); ?></span></li>
         <?php if($row[COPY_TO_ID]): ?><li><strong id="cs">抄 &nbsp;送：</strong><em></em><span><?php echo (getList_name(is_array($row)?$row["COPY_TO_ID"]:$row->COPY_TO_ID)); ?></span></li><?php endif; ?>
                  
         <li><strong id="cs">附件：</strong><em></em><span><?php echo (is_array($row)?$row["att"]:$row->att); ?></span></li>
        </ul>
      </div>
      <div class="mailctn">
       <?php echo (is_array($row)?$row["CONTENT"]:$row->CONTENT); ?>
      </div>
</div>

<script type="text/javascript">
//查看邮件
function view_att(part){
		   $("#mail-act").html('');
		   $("#mail-act").load("/index.php/Email/view/idx/<?php echo ($row[idx]); ?>/viewatt/1/part/"+part);
}
</script>
<script Language="JavaScript">

$("#re-mail-btm").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/add/REPLAY/0/EMAIL_ID/<?php echo (is_array($row)?$row["EMAIL_ID"]:$row->EMAIL_ID); ?>/BOX_ID/<?php echo ($BOX_ID); ?>");
});

$("#z-mail-btm").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/add/FW/1/EMAIL_ID/<?php echo (is_array($row)?$row["EMAIL_ID"]:$row->EMAIL_ID); ?>/BOX_ID/<?php echo ($BOX_ID); ?>");
});

$("#mail-del-btm").click(function(){
var msg="真得要删除此邮件吗？"
if(confirm(msg)==1){
	$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/delete/ret/1/DELETE_STR/<?php echo (is_array($row)?$row["EMAIL_ID"]:$row->EMAIL_ID); ?>,");
	
}


});

$("#back-btm").click(function(){
$("#mail-act").html('');
<?php if($from == 'sentbox'): ?>$("#mail-act").load("/index.php/Email/sentbox/BOX_ID/<?php echo (is_array($row)?$row["BOX_ID"]:$row->BOX_ID); ?>");
<?php elseif($from == 'deletebox'): ?>
$("#mail-act").load("/index.php/Email/deletebox/BOX_ID/<?php echo (is_array($row)?$row["BOX_ID"]:$row->BOX_ID); ?>");
<?php else: ?>
$("#mail-act").load("/index.php/Email/inbox/BOX_ID/<?php echo (is_array($row)?$row["BOX_ID"]:$row->BOX_ID); ?>");<?php endif; ?>
});


function copy_email()
{
  document.execCommand('selectall'); 
  document.execCommand('copy');
  document.execCommand('unselect');
  alert("邮件全文已复制到剪贴板！");
}
</script>