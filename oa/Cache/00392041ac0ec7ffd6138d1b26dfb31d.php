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
function delete_priv(USER_PRIV)
{
 msg="确认要删除该角色么？";
 if(window.confirm(msg))
 {
  URL="/index.php/UserPriv/delete/USER_PRIV/"+USER_PRIV;
  window.location=URL;
 }
}
</script>

<body>
	<ul class="dm_submenuul">
		<li class="dm_on"><a href="/index.php/UserPriv/index" title=""><span>管理角色</span></a></li>
		<li><a href="/index.php/UserPriv/form" title=""><span>新建角色</span></a></li>
	</ul>

<div align="center">
<table>
    <thead>
      <td nowrap align="center">角色排序号</td>
      <td nowrap align="center">角色名称</td>
      <td nowrap align="center">操作</td>
    </thead>
    <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["PRIV_NO"]:$vo->PRIV_NO); ?></td>
      <td><?php echo (is_array($vo)?$vo["PRIV_NAME"]:$vo->PRIV_NAME); ?></td>
      <td>
        <a href="/index.php/UserPriv/form/USER_PRIV/<?php echo (is_array($vo)?$vo["USER_PRIV"]:$vo->USER_PRIV); ?>"> 编辑角色</a>&nbsp;&nbsp;
        <a href="/index.php/UserPriv/editpriv/USER_PRIV/<?php echo (is_array($vo)?$vo["USER_PRIV"]:$vo->USER_PRIV); ?>"> 编辑权限</a>&nbsp;&nbsp;
        <a href="javascript:delete_priv('<?php echo (is_array($vo)?$vo["USER_PRIV"]:$vo->USER_PRIV); ?>')"> 删除</a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
 </table>        
</div>



</body>
</html>