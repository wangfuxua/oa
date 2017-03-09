<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($curtitle); ?></title>
		
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/default.css" />
				<script src="/oa/Tpl/default/Public/js/js.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css"></link>
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script language="javascript" src="/oa/Tpl/default/Public/js/DateTimeMask.js" type="text/javascript"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>	
	</head>

<script>
var MENU_ID_ARRAY = new Array();
<?php echo ($MENU_ID_ARRAY); ?>


function check_all(menu_all,MENU_ID)
{

 for (i=0;i<document.all(MENU_ID).length;i++)
 {
   if(menu_all.checked)
      document.all(MENU_ID).item(i).checked=true;
   else
      document.all(MENU_ID).item(i).checked=false;
 }

 if(i==0)
 {
   if(menu_all.checked)
      document.all(MENU_ID).checked=true;
   else
      document.all(MENU_ID).checked=false;
 }
}

function mysubmit()
{
  func_id_str="";

  for(j=1;j<=<?php echo ($menu_id_count); ?>;j++)
  {
    menu_id=MENU_ID_ARRAY[j-1]+'';

    for(i=0;i<document.all(menu_id).length;i++)
    {
        el=document.all(menu_id).item(i);
        if(el.checked)
        {  val=el.value;
           func_id_str+=val + ",";
        }
    }

    if(i==0)
    {
        el=document.all(menu_id);
        if(el.checked)
        {  val=el.value;
           func_id_str+=val + ",";
        }
    }
  }

  location="/index.php/UserPriv/updatepriv/FUNC_ID_STR/"+ func_id_str +"/USER_PRIV/<?php echo ($USER_PRIV); ?>";
}
</script>

<body>

<table>
   <caption class="nostyle">编辑角色权限 - （<?php echo (is_array($ROW)?$ROW["PRIV_NAME"]:$ROW->PRIV_NAME); ?>）
     <input type="button" value="确定" class="btnFnt" onclick="mysubmit();">&nbsp;&nbsp;
     <input type="button" value="返回" class="btnFnt" onclick="location='/index.php/UserPriv/index'">
   </caption>
   <tr>
   <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><td class="tdtop">
	  <table>
	    <thead>
	     <tr title="<?php echo (is_array($vo)?$vo["MENU_NAME"]:$vo->MENU_NAME); ?>">
	      <th>
	        <input type="checkbox" name="MENU_<?php echo (is_array($vo)?$vo["MENU_ID"]:$vo->MENU_ID); ?>" onClick="check_all(this,'<?php echo (is_array($vo)?$vo["MENU_ID"]:$vo->MENU_ID); ?>');">
	        <?php echo (is_array($vo)?$vo["MENU_NAME"]:$vo->MENU_NAME); ?>
	      </td>
	     </tr>
	     </thead>
        <?php if(is_array($vo['sub'])): ?><?php $i = 0;?><?php $__LIST__ = $vo['sub']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$sub): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr title="<?php echo (is_array($sub)?$sub["FUNC_NAME"]:$sub->FUNC_NAME); ?>">
          <td>
          <input type="checkbox" name="<?php echo ($vo[MENU_ID]); ?>" value="<?php echo ($sub[FUNC_ID]); ?>" <?php if(find_id($ROW[FUNC_ID_STR],$sub[FUNC_ID])): ?>checked<?php endif; ?>>
          <?php echo ($sub[FUNC_NAME]); ?>
             
            <?php if(is_array($sub['sub2'])): ?><?php $i = 0;?><?php $__LIST__ = $sub['sub2']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$sub2): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><br>&nbsp;&nbsp;&nbsp;&nbsp;
                          <input type="checkbox" name="<?php echo ($vo[MENU_ID]); ?>" value="<?php echo ($sub2[FUNC_ID]); ?>" <?php if(find_id($ROW[FUNC_ID_STR],$sub2[FUNC_ID]))echo "checked";?>>
                          <?php echo ($sub2[FUNC_NAME]); ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>  
            </td>
        </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
    </table>
  </td><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
 </tr>
 <tfoot>
 <tr>
   <td colspan="50">
     <input type="hidden" value="<?php echo ($USER_PRIV); ?>" name="USER_PRIV">
     <input type="button" value="确定" class="btnFnt" onclick="mysubmit();">
     <input type="button" value="返回" class="btnFnt" onclick="location='/index.php/UserPriv/index'">
   </td>
 </tr>
 </tfoot>
</table>

</body>
</html>