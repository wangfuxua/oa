<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title></title>
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/default.css" />
				<script src="/oa/Tpl/default/Public/js/jquery_last.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css"></link>
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script language="javascript" src="/oa/Tpl/default/Public/js/DateTimeMask.js" type="text/javascript"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>	
	</head>

<script Language="JavaScript">
function delete_user(DEPT_ID,USER_ID)
{
 msg='确认要删除用户 '+USER_ID+' 么？';
 if(window.confirm(msg))
 {
  URL="/index.php/User/delete/?DEPT_ID=" + DEPT_ID + "&USER_ID=" + USER_ID;
  window.location=URL;
 }
}
</script>
<body>
<div class="addr-main">
	<ul class="dm_submenuul">
		<li class="dm_on"><a href="/index.php/UserAssign/lists" title=""><span>人员配置列表</span></a></li>
		<li><a href="/index.php/UserAssign/create" title=""><span>添加人员配置</span></a></li>
	</ul>
	<div>
<table>
     <thead>
     <tr>
      <td>用户名</td>
      <td>真实姓名</td>
      <td>部门</td>
      <td>角色</td>
      <td>管理人员</td>
      <td>操作</td>
      </tr>
    </thead>
     <tbody>
     <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
      <td><?php echo ($vo['USER_ID']); ?></td>
      <td><?php echo ($vo['USER_NAME']); ?></td>
      <td><?php echo ($vo['DEPT_NAME']); ?></td>
      <td><?php echo ($vo['PRIV_NAME']); ?></td>
      <td>查看 <a href="/index.php/UserAssign/assignuser/id/<?php echo ($vo['id']); ?>" target="_blank" style="color:red;"><?php echo ($vo['USER_NAME']); ?></a> 的管理用户</td>
      <td><a href="/index.php/UserAssign/create/id/<?php echo ($vo['id']); ?>"> 编辑</a><a href="/index.php/UserAssign/del/id/<?php echo ($vo['id']); ?>" onclick="return confirm('确定要删除!');"> 删除</a>
      
      </td>
      </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
    </tbody>
     <tfoot>
     <tr>
      <td colspan="6"><?php echo ($page); ?></td>
      </tr>
    </tfoot>
</table>
    
</body>
</html>