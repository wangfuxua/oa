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

<script Language="JavaScript">
function delete_user(DEPT_ID,USER_ID,uid)
{
 msg='确认要删除用户 '+USER_ID+' 么？';
 if(window.confirm(msg))
 {
  URL="/index.php/User/delete/?type=<?php echo ($type); ?>&DEPT_ID=" + DEPT_ID + "&USER_ID=" + USER_ID + "&uid=" + uid;
  window.location=URL;
 }
}

function no_pass(DEPT_ID,USER_ID)
{
 msg='确认要清空用户 '+USER_ID+' 的密码么？';
 if(window.confirm(msg))
 {
  URL="/index.php/User/nopass/?DEPT_ID=" + DEPT_ID + "&USER_ID=" + USER_ID;
  window.location=URL;
 }
}
</script>
<body>
<div class="addr-main">
	<ul class="dm_submenuul">
		<li <?php if($type == ''): ?>class="dm_on"<?php endif; ?>><a href="/index.php/User/userlist/?DEPT_ID=<?php echo ($DEPT_ID); ?>" title=""><span>在职人员</span></a></li>
		<li <?php if($type == 'other'): ?>class="dm_on"<?php endif; ?>><a href="/index.php/User/userlist/type/other" title=""><span>离职人员</span></a></li>
		<li><a href="/index.php/User/news/?DEPT_ID=<?php echo ($DEPT_ID); ?>" title=""><span>添加人员</span></a></li>
	</ul>
	<div>
<table>
<caption class="nostyle"><?php echo ($desc); ?></caption>
     <thead>
     <tr>
      <th>用户名</th>
      <th>真实姓名</th>
      <th>工号</th>
      <th>部门</th>
      <th>角色</th>
      <th>管理范围</th>
      <th>最后一次登录时间</th>
      <th>登录次数</th>
      <th>操作</th>
      </tr>
    </thead>
     <tbody>
     <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["USER_ID"]:$vo->USER_ID); ?></td>
      <td><?php echo (is_array($vo)?$vo["USER_NAME"]:$vo->USER_NAME); ?></td>
      <td><?php echo (is_array($vo)?$vo["USER_LABOR"]:$vo->USER_LABOR); ?></td>
      <td><?php echo (getDeptname(is_array($vo)?$vo["DEPT_ID"]:$vo->DEPT_ID)); ?></td>
      <td><?php echo (is_array($vo)?$vo["PRIV_NAME"]:$vo->PRIV_NAME); ?></td>
      <td><?php if($vo[POST_PRIV] == 0): ?>本部门<?php elseif($vo[POST_PRIV] == 1): ?>全体<?php elseif($vo[POST_PRIV] == 2): ?>指定部门<?php endif; ?></td>
      <td><?php if($vo[LAST_LOGIN_TIME] != 0): ?><?php echo (date("Y-m-d H:i:s",is_array($vo)?$vo["LAST_LOGIN_TIME"]:$vo->LAST_LOGIN_TIME)); ?><?php else: ?>从未登录<?php endif; ?></td>
      <td><?php echo (is_array($vo)?$vo["LOGIN_COUNT"]:$vo->LOGIN_COUNT); ?></td>
      <td>
      <a href="/index.php/User/userEdit/USER_ID/<?php echo (is_array($vo)?$vo["USER_ID"]:$vo->USER_ID); ?>"> 编辑</a>
<?php
      if($vo[USER_ID]!="admin" && $vo[USER_ID]!=$LOGIN_USER_ID)
      {
?>
      <a href="javascript:delete_user('<?php echo ($DEPT_ID); ?>','<?php echo (is_array($vo)?$vo["USER_ID"]:$vo->USER_ID); ?>','<?php echo (is_array($vo)?$vo["uid"]:$vo->uid); ?>');"> 删除</a>
<?php
      }
      if($LOGIN_USER_ID=="admin")
      {
?>
      <a href="javascript:no_pass('<?php echo (is_array($vo)?$vo["DEPT_ID"]:$vo->DEPT_ID); ?>','<?php echo (is_array($vo)?$vo["USER_ID"]:$vo->USER_ID); ?>');">清空密码</a>
      <a href="/index.php/User/editpass/USER_ID/<?php echo (is_array($vo)?$vo["USER_ID"]:$vo->USER_ID); ?>">修改密码</a>
<?php
      }
?>
      
      </td>
      </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?> 
    </tbody>
     <tfoot>
     <tr>
      <td colspan="10"><?php echo ($page); ?></td>
      </tr>
    </tfoot>
</table>
    
</body>
</html>