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

<script language="JavaScript">
function CheckForm()
{
   if(document.form1.ITEM_NAME.value=="")
   { alert("工资项目名称不能为空！");
     return (false);
   }
}
</script>
<script>
function delete_item(ITEM_ID)
{
 msg='确认要删除该项工资项目么？';
 if(window.confirm(msg))
 {
  URL="/index.php/salary/itemDel/ITEM_ID/" + ITEM_ID;
  window.location=URL;
 }
}


function delete_all()
{
 msg='确认要删除所有工资项目么？';
 if(window.confirm(msg))
 {
  URL="/index.php/salary/itemDellAll";
  window.location=URL;
 }
}
</script>
<script>
function check_all()
{
 for (i=0;i<document.all("item_select").length;i++)
 {
   if(document.all("allbox").checked)
      document.all("item_select").item(i).checked=true;
   else
      document.all("item_select").item(i).checked=false;
 }
 
 if(i==0)
 {
   if(document.all("allbox").checked)
      document.all("item_select").checked=true;
   else
      document.all("item_select").checked=false;
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
  for(i=0;i<document.all("item_select").length;i++)
  {

      el=document.all("item_select").item(i);
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }
  
  if(i==0)
  {
      el=document.all("item_select");
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }

  if(delete_str=="")
  {
     alert("请至少选择其中一项工资项目。");
     return;
  }

  msg='确认要删除所选工资项目么？';
  if(window.confirm(msg))
  {
    window.location="/index.php/salary/deleteSelectItem/DELETE_STR/"+ delete_str;
    
  }
}

</script>

<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "工资上报管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "工资项目定义", Url: "/index.php/salary/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "工资上报流程管理", Url: "/index.php/salary/flowIndex", Cls: "", IconCls: "ico ico-list2" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });

</script>
<body>
	<div class="KDStyle" id="main">
	<form action="itemAdd" method="post" name="form1" onSubmit="return CheckForm();">
		<table>
		    <colgroup>
		    <col width="200"></col>
		    <col></col>
		    </colgroup>
			<thead>
				<tr>
					<th colspan="2" >添加工资项目</th>
				</tr>
			</thead>
			<tbody>
					<tr>
						<td>序号</td>
						<td> <input name="ITEM_ORDER" type="text" class="dm_inputsen" value="0"/></td>
					</tr>			
					<tr>
						<td>工资项目名称</td>
						<td> <input name="ITEM_NAME" type="text" class="dm_inputsen" /></td>
					</tr>

			</tbody>
			<tfoot>
					<tr>
						<th colspan="2" class="dm_btnzan">
						<button name="Abutton1" type="submit">添加</button>
						</th>
					</tr>
			</tfoot>
			
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		<table>
			<thead>
				<tr>
					<th colspan="4" >已定义的工资项目（最多50条）</th>
				</tr>
			</thead>
			<tbody>
				<col width="30px" />
				<col width="50px" />
				<col />
				<col width="100px" />
				<tr class="tcenter">
						<th>选择</th><th>序号</th><th>名称</th><th>操作</th>
					</tr>
			<?php if(is_array($salList)): ?><?php $i = 0;?><?php $__LIST__ = $salList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
					    <td class="tcenter">
					    <input type="checkbox" name="item_select" value="<?php echo (is_array($vo)?$vo["ITEM_ID"]:$vo->ITEM_ID); ?>" onClick="check_one(self);">
					    </td>
						<td class="tcenter"><?php echo (is_array($vo)?$vo["ITEM_ORDER"]:$vo->ITEM_ORDER); ?> </td>
						<td><?php echo (is_array($vo)?$vo["ITEM_NAME"]:$vo->ITEM_NAME); ?> </td>
						<td class="tcenter"><a href="/index.php/salary/itemEdit/ITEM_ID/<?php echo (is_array($vo)?$vo["ITEM_ID"]:$vo->ITEM_ID); ?>"> 编辑</a> <a href="javascript:delete_item('<?php echo (is_array($vo)?$vo["ITEM_ID"]:$vo->ITEM_ID); ?>');"> 删除</a> </td>
					</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
					<tr>
						<th><input type="checkbox" name="allbox" onClick="check_all();"></th>
					    <td colspan="2">
					    <button onClick="delete_select();" title="删除所选项目" />删除已选</button>
					    <button name="Abutton1" onClick="delete_all();">全部删除</button>
						</td>
					    
						<td colspan="1" class="dm_btnzan">
						 
						</td>
					</tr>
			</tfoot>
		</table>
</div>
</body>
</html>