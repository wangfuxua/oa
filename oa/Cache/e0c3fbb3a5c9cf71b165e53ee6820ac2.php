<div id="title" class="clearfix">
      <h2><?php echo ($curtitle); ?></h2>
      <p>(共<span id="allmail"> <?php echo ($COUNT); ?></span> 封，其中<em> 未读邮件</em> <span id="newmail"><?php echo ($countnew); ?></span> 封)</p>
            <span class="rtact">全局查询
      <input type="hidden" id="setid" name="setid" value="<?php echo ($setid); ?>">
      <input type="text" id="keyword" />
      <button type="button" id="searchsub_wb">搜索</button>
      </span>

      </div>
<div id="active" class="active">
        <span id="mail-del-one"><a href="#">删除</a></span>
        <span id="maill-delAll"><a href="#">全部删除</a></span> 
       </div>     
  <div id="mail-centens" style="border:none">
      <table id="mail-date"  border="0" cellspacing="0" cellpadding="0">
      <thead>
          <th width="14"><input type="checkbox" name="topcheck" id="topcheck" onclick="checkAll()"/></th>
          <th width="14"><img src="/oa/Tpl/default/Public/images/ico/mail_tit_1.gif" width="15" height="12" align="absmiddle" /></th>
          <th width="160">发件人</th>
          <th>主题</th>
          <th width="30">附件</th>
          <th width="30" >日期</th>
          <th width="40">大小</th>
          </thead>
        <tr>
		<tbody id="mail-list">
		<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
          <td align="center"><input type="checkbox" name="email_select" value="<?php echo (is_array($vo)?$vo["idx"]:$vo->idx); ?>" /></td>
          <td><?php if($vo[meta_read] == 'r'): ?><span class="readed">已读</span><?php elseif($vo[meta_read] == 'u'): ?><span class="readnow">未读</span><?php endif; ?></td>
          <td><a href="javascript:view_mail('<?php echo (is_array($vo)?$vo["idx"]:$vo->idx); ?>')"><?php echo (parse_email_address(is_array($vo)?$vo["hfrom"]:$vo->hfrom)); ?></a></td>
          <td><span class="til"><a href="javascript:view_mail('<?php echo (is_array($vo)?$vo["idx"]:$vo->idx); ?>')"><?php echo (is_array($vo)?$vo["hsubject"]:$vo->hsubject); ?></a></span></td>
          <td><?php if($vo[attachments] == 1): ?><img src="/oa/Tpl/default/Public/images/ico/mail_files.gif" width="14" height="12" align="absmiddle" /><?php else: ?>&nbsp;<?php endif; ?></td>
          <td align="center"><?php echo (is_array($vo)?$vo["hdate_sent"]:$vo->hdate_sent); ?></td>
          <td align="center"><?php echo (bitsize(is_array($vo)?$vo["hsize"]:$vo->hsize)); ?></td>
        </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
		</tbody>
		</tr>
      </table>
      <div class="tab-ft">
		<span id="check-all" style="width:50px;"><label for="btmcheck"><input type="checkbox" name="btmcheck" id="btmcheck" onclick="checkAll()"/>全选</label></span>
        <div class="btm-act">
				<span>转移到：		
		<select name="BOX_IDS" onchange="change_box();" style="border:1px solid #ccc">
         <?php if(is_array($boxlist)): ?><?php $i = 0;?><?php $__LIST__ = $boxlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo (is_array($vo)?$vo["idx"]:$vo->idx); ?>" <?php if(($folderid)  ==  $vo[idx]): ?>selected<?php endif; ?>><?php echo (is_array($vo)?$vo["friendly_name"]:$vo->friendly_name); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
		</select>
		
		</span>        </div>
        <div id="page-Num"><?php echo ($page); ?></div>
      </div>
    </div>
<script type="text/javascript">
$("#searchsub_wb").click(function(){
	var keyword=$('#keyword').val();
	//var setid=1;
	var setid=$('#setid').val();
	$("#mail-act").html('');
	$("#mail-act").load("/index.php/WebMail/search/keyword/"+keyword+"/setid/"+setid);
})

//查看邮件
function view_mail(mailid){
		   $("#mail-act").html('');
		   $("#mail-act").load("/index.php/WebMail/writeMail/setid_id/<?php echo ($setid); ?>/?idx="+mailid);
}

$("#mail-del-one").click(function(){
var gh=$("#mail-list").find("input");
for(var i=0; i<gh.length; i++){
   if(gh[i].checked==true){
	   if(confirm("您确定要删所选邮件吗？")==1)
		for(var j=0; j<gh.length; j++){
			if(gh[j].checked==true){
		      var py=gh[j].parentNode;
		     if(py.tagName!='tr')
			    py=py.parentNode;				
			   $(py).remove();	
			   $.ajax({
			   	type:'post',
			   	url:'/index.php/WebMail/delete/folder/drafts/idxs/'+gh[j].value+','
			   })			   			   			   
			 }
		  }		 
		  return 
	    }
	  }
	if(i<1){
	alert("邮件为空");
	return
	}
	alert("请选择你要删除的邮件")
})


//删除邮件
$("#maill-delAll").click(function(){
if(confirm("您确定要删除全部邮件")==1){
$("#mail-list").find('tr').remove();
$.ajax({
   	type:'post',
  	url:'/index.php/WebMail/deleteall/folderid/'+<?php echo ($folderid); ?>
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
     //document.form1.reset();
     return;
  }
  
  box_id=document.all("BOX_IDS").value;
 // window.location="/index.php/Email/change/EMAIL_ID_STR/"+ delete_str +"/BOX_ID/"+box_id;
  //url="/index.php/WebMail/change/EMAIL_ID_STR/"+ delete_str +"/BOX_ID/"+box_id;
  url="/index.php/WebMail/change/idxs/"+ delete_str +"/folderid/"+box_id;
  $("#mail-act").html('');
  $("#mail-act").load(url);
  
  //window.location=URL;
}

</script>

<script src="/oa/Tpl/default/Public/script/mudel.js" type="text/javascript"></script>