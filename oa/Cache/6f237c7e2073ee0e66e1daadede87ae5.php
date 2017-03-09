<div id="title" class="clearfix">
      <h2><?php echo ($curtitle); ?></h2>
      <p>(共<span id="allmail"> <?php echo ($COUNT); ?></span> 封，其中<em> 未读邮件</em> <span id="newmail"><?php echo ($NEW_COUNT); ?></span> 封)</p>
            <span class="rtact">全局查询
     
      <input type="hidden" id="READ_FLAG" name="READ_FLAG" value="-1">
      <input type="hidden" id="BOX_ID" name="BOX_ID" value="<?php echo ($BOX_ID); ?>">
      <input type="text" id="keyword" />
      <button type="button" id="searchsub">搜索</button>
      
    </span> </div>
<div id="active" class="active">
        <span id="mail-del"><a href="#">删除</a></span>
        <span id="maill-delAll"><a href="#">全部删除</a></span>     
       </div>     
  <div id="mail-centens" style="border:none">
      <table id="mail-date"  border="0" cellspacing="0" cellpadding="0">
      <thead>
          <th width="14"><input type="checkbox" name="topcheck" id="topcheck" onclick="checkAll()"/></th>
          <th width="14"><img src="/oa/Tpl/default/Public/images/ico/mail_tit_1.gif" width="15" height="12" align="absmiddle" /></th>
          <th width="160">收件人</th>
          <th>主题</th>
          <th width="30">附件</th>
          <th width="30" >日期</th>
          <th width="40">大小</th>
          </thead>
        <tr>
		<tbody id="mail-list">
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
          <td align="center"><input type="checkbox" name="email_select" value="<?php echo (is_array($vo)?$vo["EMAIL_ID"]:$vo->EMAIL_ID); ?>" /></td>
          <td><?php if($vo[READ_FLAG] == 1): ?><span class="readed">已读</span><?php else: ?><span class="readnow">未读</span><?php endif; ?></td>
          <td><?php echo (csubstr(getList_name(is_array($vo)?$vo["TO_ID"]:$vo->TO_ID),0,100)); ?>&nbsp;</td>
          <td><span class="til">
          				<?php if($vo[READ_FLAG] == 1): ?><a href="#" onclick='javascript:$("#mail-act").load("/index.php/Email/inboxRead/EMAIL_ID/<?php echo (is_array($vo)?$vo["EMAIL_ID"]:$vo->EMAIL_ID); ?>/BOX_ID/<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>")'><?php echo (is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT); ?></a>
						 <?php else: ?>
						<a href="#" onclick='javascript:$("#mail-act").load("/index.php/Email/inboxRead/EMAIL_ID/<?php echo (is_array($vo)?$vo["EMAIL_ID"]:$vo->EMAIL_ID); ?>/BOX_ID/<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>")'><strong><?php echo (is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT); ?></strong></a><?php endif; ?>
          </span></td>
          <td><?php if($vo[ATTACHMENT_ID] != ''): ?><img src="/oa/Tpl/default/Public/images/ico/mail_files.gif" width="14" height="12" align="absmiddle" /><?php else: ?>&nbsp;<?php endif; ?></td>
          <td align="center"><?php echo (is_array($vo)?$vo["SEND_TIME"]:$vo->SEND_TIME); ?></td>
          <td align="center"><?php echo (email_size("$vo[EMAIL_ID]")); ?></td>
        </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
		</tbody>

      </table>
      <div class="tab-ft">
		<span id="check-all" style="width:50px;"><label for="btmcheck"><input type="checkbox" name="btmcheck" id="btmcheck" onclick="checkAll()"/>全选</label></span>
        <div class="btm-act">
		<span>转移到：
		      <select name="BOX_IDS" onchange="change_box();" style="border:1px solid #ccc">
         <option value="0" <?php if(($BOX_ID)  ==  "0"): ?>selected<?php endif; ?>>收件箱</option>
         <option value="outbox" <?php if(($folder)  ==  "outbox"): ?>selected<?php endif; ?>>草稿箱</option>
         <option value="sentbox" <?php if(($folder)  ==  "sentbox"): ?>selected<?php endif; ?>>已发送</option>
         <option value="deletebox" <?php if(($folder)  ==  "deletebox"): ?>selected<?php endif; ?>>已删除</option>
                  
         <?php if(is_array($boxlist)): ?><?php $i = 0;?><?php $__LIST__ = $boxlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>" <?php if(($BOX_ID)  ==  $vo[BOX_ID]): ?>selected<?php endif; ?>><?php echo (is_array($vo)?$vo["BOX_NAME"]:$vo->BOX_NAME); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
     
		</select>
		</span>       </div>
        <div id="page-Num"><?php echo ($page); ?></div>
      </div>
    </div>
<script src="/oa/Tpl/default/Public/script/mudel.js" type="text/javascript"></script>
<script type="text/javascript">
<?php if(is_array($boxlist)): ?><?php $i = 0;?><?php $__LIST__ = $boxlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?>$("#boxlists_<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/inbox/BOX_ID/<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>");
});<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>

//删除邮件
$("#maill-delAll").click(function(){
if(confirm("您确定要删除全部文件（永久性不可恢复）！")==1){
$("#mail-list").find('tr').remove();
$.ajax({
   	type:'post',
  	url:'/index.php/Email/deleteall/from/sentbox'
})	
$("#page-Num").html("")
}
else return
})

function change_box()
{
  delete_str="";
  for(i=0;i<document.all("email_select").length;i++)
  {

      el=document.all("email_select").item(i);
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }
  
  if(i==0)
  {
      el=document.all("email_select");
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }

  if(delete_str=="")
  {
     alert("要转移邮件，请至少选择其中一封。");
     document.form1.reset();
     return;
  }
  
  box_id=document.all("BOX_IDS").value;
 // window.location="/index.php/Email/change/EMAIL_ID_STR/"+ delete_str +"/BOX_ID/"+box_id;
   url="/index.php/Email/change/EMAIL_ID_STR/"+ delete_str +"/BOX_ID/"+box_id;
  $("#mail-act").html('');
  $("#mail-act").load(url);
  //window.location=URL;
}

</script>