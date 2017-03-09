<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>个人邮箱</title>
<script src="/oa/Tpl/default/Public/script/jquery-1.2.6.pack.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/style/default.css" />
<link href="/oa/Tpl/default/Public/style/mail.css" type="text/css" rel="stylesheet" charset="utf-8" />

   
</head>
<body>
<div id="mail-box">
  <div id="mail-info">
     <div class="mainact clearfix">
         <span id="re-mail" onmouseover="this.className='overbg'" onmouseout="this.className=''"><em class="<?php echo ($o_cls); ?>">收信</em></span>
         <span id="rt-mail"  onmouseover="this.className='overbg'" onmouseout="this.className=''"><em class="<?php echo ($o_cls); ?>">写信</em></span>
       </div> 
    <div class="myfodertitle">
      <h2>我的邮件</h2>
          <span id="create-folder"><a href="#"><em class="<?php echo ($o_cls); ?>">邮箱管理</em></a></span>
    </div>
    <div id="nb-mail" class="mailtype">
      <h3 id="nb" <?php echo ($hcls); ?>>内部邮箱</h3>
    </div>
    <div id="nb-mailbox-list" class="foldername" style="<?php echo ($disp_nb); ?>">      
               <ul>
                 <li><a href="#" id="received"><img src="/oa/Tpl/default/Public/images/bg_15.gif" width="14" height="14" align="absmiddle" /><strong>收件箱</strong></a><span id="mail-num"></span></li>
                 <li><a href="#" id="sending"><img src="/oa/Tpl/default/Public/images/bg_16.gif" width="14" height="14" align="absmiddle" /><strong>草稿箱</strong></a></li>
                 <li><a href="#" id="sended"><img src="/oa/Tpl/default/Public/newimg/sendfolder.gif" width="15" height="12" align="absmiddle" /><strong>已发送</strong></a></li>
                 <li><a href="#" id="deleted"><img src="/oa/Tpl/default/Public/newimg/del.gif" width="16" height="15" align="absmiddle" /><strong>已删除</strong></a></li>
                 <?php if(is_array($boxlist)): ?><?php $i = 0;?><?php $__LIST__ = $boxlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><a href="#" id="boxlists_<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>"><img src="/oa/Tpl/default/Public/images/bg_15.gif" width="14" height="14" align="absmiddle" /><strong><?php echo (is_array($vo)?$vo["BOX_NAME"]:$vo->BOX_NAME); ?></strong></a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
      </ul> 
    </div>
    <?php if($folderlist): ?><?php if(is_array($folderlist)): ?><?php $i = 0;?><?php $__LIST__ = $folderlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div id="wb-mail" class="mailtype">
         <h3 id="set<?php echo ($vo[setid]); ?>" <?php echo ($vo[hcls]); ?>><?php echo ($vo[email]); ?></h3>
      </div>
          <div id="wb-mailbox-list" class="foldername" style="<?php echo ($vo[disp]); ?>">
            <ul>
                <?php if(is_array($vo[sub])): ?><?php $i = 0;?><?php $__LIST__ = $vo[sub]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$sub): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><a href="#" id="<?php echo ($sub[folder_path]); ?><?php echo ($vo[setid]); ?>"><img src=/oa/Tpl/default/Public/<?php if($sub[folder_path] == 'inbox'): ?>images/bg_15.gif<?php elseif($sub[folder_path] == 'sent'): ?>newimg/sendfolder.gif<?php elseif($sub[folder_path] == 'drafts'): ?>images/bg_16.gif<?php elseif($sub[folder_path] == 'waste'): ?>newimg/del.gif<?php endif; ?>  width="14" height="14" align="absmiddle" /><strong><?php echo ($sub[friendly_name]); ?></strong></a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
              </ul>
          </div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>      
    <?php else: ?>
      <div id="wb-mail2" class="mailtype2">
         <h3>外部邮箱</h3><span><a href="#" id="mailset">设置</a></span>
     </div><?php endif; ?>
  </div>

  <div id="mail-act" style="">
      <div id="title" class="clearfix">
      <h2>收件箱</h2>
      <p>(共<span id="allmail"><?php echo ($COUNT); ?></span> 封，其中<em> 未读邮件</em> <span id="newmail"><?php echo ($NEW_COUNT); ?></span> 封)</p>
      <span class="rtact">全局查询
	      <input type="hidden" id="READ_FLAG" name="READ_FLAG" value="-1">
	      <input type="hidden" id="BOX_ID" name="BOX_ID" value="<?php echo ($BOX_ID); ?>">
	      <input type="text" id="keyword" />
	      <button type="button" id="searchsub">搜索</button>
      </span> 
    </div>
       <div id="active" class="active">
        <span id="mail-del"><a href="#">删除</a></span>
        <span id="maill-delAll"><a href="#">全部删除</a></span> 
       </div>      
       
  <div id="mail-centens"  style="border:none">
      <table id="mail-date"  border="0" cellspacing="0" cellpadding="0">
        <thead>
          <th width="14"><input type="checkbox" name="topcheck" id="topcheck" onclick="checkAll()"/></th>
          <th width="14"><img src="/oa/Tpl/default/Public/images/ico/mail_tit_1.gif" width="15" height="12" align="absmiddle" /></th>
          <th width="160">发件人</th>
          <th>主题</th>
          <th width="30">附件</th>
          <th width="30" >日期</th>
          <th width="40">大小</th>
          </tr>
        <tr>
		<tbody id="mail-list">
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
          <td align="center"><input type="checkbox" name="email_select" value="<?php echo (is_array($vo)?$vo["EMAIL_ID"]:$vo->EMAIL_ID); ?>" /></td>
          <td><?php if($vo[READ_FLAG] == 1): ?><span class="readed">已读</span><?php else: ?><span class="readnow">未读</span><?php endif; ?></td>
          <td><u title="部门：<?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?>" style="cursor:hand"><?php echo (is_array($vo)?$vo["FROM_NAME"]:$vo->FROM_NAME); ?></u></td>
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
      <div class="tab-ft" >
		<span id="check-all" style="width:50px;"><label for="btmcheck"><input type="checkbox" name="btmcheck" id="btmcheck" style="vertical-align:middle" onclick="checkAll()" />全选</label></span>
        <div class="btm-act">
		<span id="selecto">转移到：
         <select name="BOX_IDS" onchange="change_box();" style="border:1px solid #ccc">
         <option value="0" <?php if(($BOX_ID)  ==  "0"): ?>selected<?php endif; ?>>收件箱</option>
         <?php if(is_array($boxlist)): ?><?php $i = 0;?><?php $__LIST__ = $boxlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>" <?php if(($BOX_ID)  ==  $vo[BOX_ID]): ?>selected<?php endif; ?>><?php echo (is_array($vo)?$vo["BOX_NAME"]:$vo->BOX_NAME); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
     
		</select></span></div>
        <div id="page-Num"><?php echo ($page); ?></div>
      </div>
    </div>
<script src="/oa/Tpl/default/Public/script/mudel.js" type="text/javascript"></script>
<script type="text/javascript">
<?php if(is_array($boxlist)): ?><?php $i = 0;?><?php $__LIST__ = $boxlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?>$("#boxlists_<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/inbox/BOX_ID/<?php echo (is_array($vo)?$vo["BOX_ID"]:$vo->BOX_ID); ?>");
});<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>

<?php if(is_array($folderlist)): ?><?php $i = 0;?><?php $__LIST__ = $folderlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if(is_array($vo[sub])): ?><?php $i = 0;?><?php $__LIST__ = $vo[sub]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$sub): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?>$("#<?php echo ($sub[folder_path]); ?><?php echo ($vo[setid]); ?>").click(function(){
	$("#mail-act").html('');
	$("#mail-act").load("/index.php/WebMail/<?php echo ($sub[folder_path]); ?>/setid/<?php echo ($vo[setid]); ?>");
	});<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>

	$("#receivemail<?php echo ($vo[setid]); ?>").click(function(){
	$("#mail-act").html('');
	$("#mail-act").load("/index.php/WebMail/receivelist/setid/<?php echo ($vo[setid]); ?>");
	});<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>


//删除邮件
$("#maill-delAll").click(function(){
if(confirm("您确定要删除全部邮件！")==1){
$("#mail-list").find('tr').remove();
$.ajax({
   	type:'post',
  	url:'/index.php/Email/deleteall/from/inbox'
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
  url="/index.php/Email/change/EMAIL_ID_STR/"+ delete_str +"/BOX_ID/"+box_id;
  $("#mail-act").html('');
  $("#mail-act").load(url);

  //window.location="/index.php/Email/change/EMAIL_ID_STR/"+ delete_str +"/BOX_ID/"+box_id;
  //window.location=URL;
}

</script>  
  </div>
</div>


<script src="/oa/Tpl/default/Public/script/mail.js" type="text/javascript"></script>
  
</body>
</html>