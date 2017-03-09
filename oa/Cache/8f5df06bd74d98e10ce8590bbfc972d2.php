<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($curtitle); ?></title>
		
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" />
        <script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js" ></script>
        <script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js" ></script>		
	</head>
<script>
function delete_all(DEL_TYPE)
{
 if(DEL_TYPE==1)
    msg="确认要删除所有已接收的短信息吗？";
 else
    msg="确认要删除所有已发送的短信息吗？";
 if(window.confirm(msg))
 {
  window.location="/index.php/Sms/deleteall/to/send/DEL_TYPE/"+DEL_TYPE;
  
 }
}
	function open_sms(SMS_ID)
	{
 	URL="/index.php/Sms/view/SMS_ID/"+SMS_ID;
 	myleft=(screen.availWidth-500)/2;
 	window.open(URL,"READ_SMS","height=400,width=550,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
	}
	
</script>
<script>
function check_all()
{
 for (i=0;i<document.all("sms_select").length;i++)
 {
   if(document.all("allbox").checked)
      document.all("sms_select").item(i).checked=true;
   else
      document.all("sms_select").item(i).checked=false;
 }
 
 if(i==0)
 {
   if(document.all("allbox").checked)
      document.all("sms_select").checked=true;
   else
      document.all("sms_select").checked=false;
 }
}

function check_one(el)
{
   if(!el.checked)
      document.all("allbox").checked=false;
}


function delete_select()
{
  delete_str="";
  for(i=0;i<document.all("sms_select").length;i++)
  {

      el=document.all("sms_select").item(i);
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }
  
  if(i==0)
  {
      el=document.all("sms_select");
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }

  if(delete_str=="")
  {
     alert("要删除短信息，请至少选择其中一条。");
     return;
  }

  msg='确认要删除所选短信息么？';
  if(window.confirm(msg))
  {
    window.location="/index.php/Sms/deleteAllSelect/DELETE_STR/"+ delete_str +"/to/send/";
  }
}
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "内部短信息",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "发送短消息", Url: "/index.php/Sms/smsform", Cls: "", Icon: "", IconCls: "ico ico-add" },
            { Title: "已接收短信", Url: "/index.php/Sms/index", Cls: "", IconCls: "ico ico-accept" },
            { Title: "已发送短信", Url: "/index.php/Sms/send", Cls: "", IconCls: "ico ico-send" },
            { Title: "短信查询", Url: "/index.php/Sms/query", Cls: "", IconCls: "ico ico-query" }
        ]
    });		   
});
</script>
<body>

	<div class="KDStyle" id="main">
		<table>
				<colgroup>
					<col width="30"></col>
					<col width="30"></col>
					<col width="60"></col>
					<col></col>
					<col width="110px"></col>
					<col width="30px"></col>
					<col width="60px"></col>
				</colgroup>			
			<thead>
				<tr>
				<th>选择</th>
					<th>类别</th>
					<th>收信人</th>
					<th>内容</th>
					<th>发送时间</th>
					<th>提醒</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
   <?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
    <td class="tcenter"><input type="checkbox" name="sms_select" value="<?php echo (is_array($vo)?$vo["SMS_ID"]:$vo->SMS_ID); ?>" onClick="check_one(self);"></td>  
     <td class="tcenter">
     <?php if($vo[SMS_TYPE] == 0): ?><img src="/oa/Tpl/default/Public/images/avatar/<?php echo (is_array($vo)?$vo["AVATAR"]:$vo->AVATAR); ?>.gif" alt="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>" title="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>" width="15" height="15">
     <?php else: ?>
     <img src="/oa/Tpl/default/Public/images/sms_type<?php echo (is_array($vo)?$vo["SMS_TYPE"]:$vo->SMS_TYPE); ?>.gif" alt="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>" title="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>"><?php endif; ?>
      </td>
      <td><?php echo (is_array($vo)?$vo["USER_NAME"]:$vo->USER_NAME); ?></td>
      <td><a href="javascript:open_sms('<?php echo ($vo[SMS_ID]); ?>')"><?php echo (csubstr(stripcslashes(strip_tags(is_array($vo)?$vo["CONTENT"]:$vo->CONTENT)),0,90)); ?></a></td>
      <td><?php echo (is_array($vo)?$vo["SEND_TIME"]:$vo->SEND_TIME); ?></td>
      <td class="tcenter"><?php if($vo[REMIND_FLAG] == 0): ?>否<?php else: ?>是<?php endif; ?></td>
      <td>
      <a href="/index.php/Sms/delete/to/send/SMS_ID/<?php echo (is_array($vo)?$vo["SMS_ID"]:$vo->SMS_ID); ?>"> 删除</a>
      
      <?php if($vo[REMIND_FLAG] == 0): ?><a href="/index.php/Sms/resubmit/SMS_ID/<?php echo (is_array($vo)?$vo["SMS_ID"]:$vo->SMS_ID); ?>">重发</a><?php endif; ?>
      
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<p>
						  <input type="checkbox" name="allbox" onClick="check_all();">
						   <button onClick="delete_select();" title="删除对方未读的短信后，对方将不会接收到" />删除所选</button>
						</p>
					</td>					
					<td colspan="4">
						<p>
						   <?php echo ($page); ?> 
							<button onClick="delete_all(2);" title="" />全部删除</button>
						</p>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	
	
</body>
</html>