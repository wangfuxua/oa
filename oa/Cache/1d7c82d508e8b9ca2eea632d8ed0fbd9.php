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
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/js.js" ></script>

<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css" />
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script language="javascript" src="/oa/Tpl/default/Public/js/DateTimeMask.js" type="text/javascript"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>	
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
  window.location="/index.php/Sms/deleteall/to/index/DEL_TYPE/"+DEL_TYPE;
  
 }
}

function delete_sms(SMS_ID)
{
 msg='确认要删除该短信息吗？';
 if(window.confirm(msg))
 {
  URL="/index.php/Sms/delete/to/index/SMS_ID/" + SMS_ID;
  window.location=URL;
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
    window.location="/index.php/Sms/deleteAllSelect/DELETE_STR/"+ delete_str +"/to/index/";
  }
}
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "内部短信息",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 2,
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
 			<col width="30px"/>
			<col width="30px"/>
			<col width="60px" />
			<col />
			<col width="110px" />
			<col width="30px" />
			<col width="60px" />
			<thead>
				<tr>
				    <th>选择</th>
					<th>类别</th>
					<th>发送人</th>
					<th>内容</th>
					<th>发送时间</th>
					<th>提醒</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
   <?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
<td><input type="checkbox" name="sms_select" value="<?php echo (is_array($vo)?$vo["SMS_ID"]:$vo->SMS_ID); ?>" onClick="check_one(self);"></td>    
     <td class="tcenter">
     <?php if($vo[SMS_TYPE] == 0): ?><img src="/oa/Tpl/default/Public/images/avatar/<?php echo (is_array($vo)?$vo["AVATAR"]:$vo->AVATAR); ?>.gif" alt="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>" title="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>" width="15" height="15">
     <?php else: ?>
     <img src="/oa/Tpl/default/Public/images/sms_type<?php echo (is_array($vo)?$vo["SMS_TYPE"]:$vo->SMS_TYPE); ?>.gif" alt="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>" title="<?php echo ($smstype_array[$vo[SMS_TYPE]]); ?>"><?php endif; ?>
      </td>
      <td class="tcenter"><?php echo (is_array($vo)?$vo["USER_NAME"]:$vo->USER_NAME); ?></td>
      <td><a href="javascript:open_sms('<?php echo ($vo[SMS_ID]); ?>')"><?php echo (csubstr(stripcslashes(strip_tags($vo[CONTENT])),0,90)); ?></a></td>
      <td><?php echo (is_array($vo)?$vo["SEND_TIME"]:$vo->SEND_TIME); ?></td>
      <td  class="tcenter"><?php if($vo[REMIND_FLAG] == 0): ?>否<?php else: ?>是<?php endif; ?></td>
      <td>
      <a href="javascript:delete_sms('<?php echo (is_array($vo)?$vo["SMS_ID"]:$vo->SMS_ID); ?>');"> 删除</a>
      <?php if($vo[SMS_TYPE] == 0): ?><a href="/index.php/Sms/smsform/TO_ID/<?php echo (is_array($vo)?$vo["FROM_ID"]:$vo->FROM_ID); ?>/TO_NAME/<?php echo (is_array($vo)?$vo["USER_NAME"]:$vo->USER_NAME); ?>">回复</a><?php endif; ?>
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
							<button onClick="delete_all(1);" title="删除对方未读的短信后，对方将不会接收到" />全部删除</button>
							<button onClick="location='/index.php/Sms/readall'" title="取消所有的短信息提醒">全部取消提醒</button>							
						</p>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	
	
</body>
</html>