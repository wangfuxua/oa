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
function CheckForm()
{
   if(document.form1.USER_ID.value=="")
   { alert("用户名不能为空！");
     return (false);
   }
   if(document.form1.USER_NAME.value=="")
   { alert("真实姓名不能为空！");
     return (false);
   }
}

function delete_user(USER_ID,uid)
{
 msg='确认要删除用户 '+USER_ID+' 么？';
 if(window.confirm(msg))
 {
  URL="/index.php/User/delete/?type=<?php echo ($type); ?>&DEPT_ID=<?php echo (is_array($users)?$users["DEPT_ID"]:$users->DEPT_ID); ?>&USER_ID=" + USER_ID + "&uid=" + uid;
  window.location=URL;
 }
}


function select_dept()
{
   if (form1.POST_PRIV.value=="2")
       dept.style.display='';
   else
       dept.style.display="none";
}
</script>
<body>
<div class="addr-main">
	<ul class="dm_submenuul">
		<li><a href="/index.php/User/userlist/?DEPT_ID=<?php echo ($users[DEPT_ID]); ?>" title=""><span>在职人员</span></a></li>
		<li><a href="/index.php/User/userlist/type/other" title=""><span>离职人员</span></a></li>
		<li><a href="/index.php/User/news/?DEPT_ID=<?php echo ($users[DEPT_ID]); ?>" title=""><span>添加人员</span></a></li>
		<li class="dm_on"><a href="/index.php/User/news/?DEPT_ID=<?php echo ($users[DEPT_ID]); ?>" title=""><span>修改人员</span></a></li>
	</ul>
</div>
	
<form action="/index.php/User/update"  method="post" name="form1" onSubmit="return CheckForm();">
<table>
<colgroup>
<col width="120"></col>
<col></col>
</colgroup>
			<thead>
				<tr>
					<th colspan="2">用户管理</th>
				</tr>
			</thead>
			<tbody>
					<tr>
						<td width="40%">用户名：</td>
						<td><?php echo ($users['USER_ID']); ?> <input name="USER_ID" value="<?php echo ($users['USER_ID']); ?>"type="hidden" class="dm_number1" />
						<input name="uid" value="<?php echo ($users['uid']); ?>"type="hidden" class="dm_number1" />
						</td>
					</tr>
					<tr>
						<td>真实姓名：</td><td><input name="USER_NAME" value="<?php echo ($users['USER_NAME']); ?>"type="text" class="dm_number1" /></td>
					</tr>
					<tr>
						<td>姓名拼音：</td><td><input name="USER_SPELL" value="<?php echo ($users['USER_SPELL']); ?>"type="text" class="dm_number1" /></td>
					</tr>					
					<tr>
						<td>工号：</td><td><input name="USER_LABOR" value="<?php echo ($users['USER_LABOR']); ?>"  type="text" class="dm_number1" /></td>
					</tr>						
					<tr>
						<td>状态：</td>
						<td>
							<select name="job_status" class="BigSelect">
									<option value="1" <?php if(($users['job_status'])  ==  "1"): ?>selected<?php endif; ?>>在职</option>
									<option value="0" <?php if(($users['job_status'])  ==  "0"): ?>selected<?php endif; ?>>离职</option>
							 </select></td>
					</tr>

					
					<tr>
						<td>性别：</td>
						<td>
							<select name="SEX" class="BigSelect">
									<option value="0" <?php if(($users['SEX'])  ==  "0"): ?>selected<?php endif; ?>>男</option>
									<option value="1" <?php if(($users['SEX'])  ==  "1"): ?>selected<?php endif; ?>>女</option>
							 </select></td>
					</tr>
					<tr>
						<td>部门：</td>
						<td>
							<select name="DEPT_ID" class="BigSelect">
							<?php echo ($select); ?>
							<?php if($POST_PRIV == 1): ?><option value="0" <?php if(($users['DEPT_ID'])  ==  "0"): ?>selected<?php endif; ?>>离职人员/外部人员</option><?php endif; ?>
							 </select></td>
					</tr>
					<tr>
						<td>角色：</td>
						<td>
						        <select name="USER_PRIV" class="BigSelect">
						        	<?php if(is_array($userPriv1)): ?><?php $i = 0;?><?php $__LIST__ = $userPriv1?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['USER_PRIV']); ?>" <?php if(($vo['USER_PRIV'])  ==  $users['USER_PRIV']): ?>selected<?php endif; ?>><?php echo ($vo['PRIV_NAME']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
								</select>
						</td>
					</tr>				
					<!--
					<tr>
						<td>角色：</td>
						<td>
                       <?php if(is_array($userprivs)): ?><?php $i = 0;?><?php $__LIST__ = $userprivs?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><input name="privs[]" type="checkbox" value="<?php echo ($vo['USER_PRIV']); ?>" <?php if(find_id($users[privs],$vo[USER_PRIV])): ?>checked<?php endif; ?> ><?php echo ($vo['PRIV_NAME']); ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
						</td>
					</tr>
					
					<tr>
						<td>用户组：</td>
						<td>
						<?php if(is_array($usergroups)): ?><?php $i = 0;?><?php $__LIST__ = $usergroups?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><input name="groupid[]" type="checkbox" value="<?php echo ($vo['groupid']); ?>" <?php if(find_id($users[groupids],$vo[groupid])): ?>checked<?php endif; ?> ><?php echo ($vo['groupname']); ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
								
						</td>
					</tr>-->
					<tr>
						<td>管理范围（如果有权限执行管理型模块）：</td>
						<td>		<select name="POST_PRIV" OnChange="select_dept()">
						            <option value="0">本部门</option>
						            <?php if($POST_PRIV == 1): ?><option value="1" <?php if(($users['POST_PRIV'])  ==  "1"): ?>selected<?php endif; ?>>全体</option>
									<option value="2" <?php if(($users['POST_PRIV'])  ==  "2"): ?>selected<?php endif; ?>>指定部门</option>
									<?php elseif($POST_PRIV == 2): ?>
									<option value="2" <?php if(($users['POST_PRIV'])  ==  "2"): ?>selected<?php endif; ?>>指定部门</option><?php endif; ?>
								</select>
						</td>
					</tr>
				   <tr id="dept" style='display:<?php if($users[POST_PRIV] != 2): ?>none<?php endif; ?>;'>
				      <td>管理范围（部门）：</td>
				      <td>
				        <input type="hidden" name="TO_ID" value="<?php echo ($users[POST_DEPT]); ?>">
				        <input type="text" name="TO_NAME" size="50" readonly value="<?php echo ($POST_DEPT_NAME); ?>">
				        <input type="button" value="添 加" class="btnFnt" onClick="oWin('TO')" title="添加部门" name="button">
				        <input type="button" value="清 空" class="btnFnt" onClick="chclear('TO')" title="清空部门" name="button">
				      </td>
				    </tr>
			</tbody>
			<tfoot>
					<tr>
						<td class="dm_btnzan" colspan="2">
						<button name="Abutton1" type="submit">修改</button>
						<?php if($LOGIN_USER_ID == 'admin'): ?><button onclick="javascript:location.href='/index.php/User/editpass/USER_ID/<?php echo ($users[USER_ID]); ?>'">修改密码</button>
						<button onclick="javascript:delete_user('<?php echo (is_array($users)?$users["USER_ID"]:$users->USER_ID); ?>','<?php echo (is_array($users)?$users["uid"]:$users->uid); ?>');">删除用户</button><?php endif; ?>
						<button onclick="javascript:window.history.back();">返回</button>
						</td>
					</tr>
			</tfoot>
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		
		<!--选择部门模块开始-->
         <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<div id="mask"></div>
		<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
		<div class="win" id="massage_box">
	        <form action="" method="post">
		        <table>
			        <caption>选择部门</caption>
			        <tr>
				        <td>
				        <div class="usertree">
				        <script type="text/javascript">
			                <!--
				            var user = new sTree('user');
				            user.config.folderLinks = true;
				            user.icon = {
				                root: '/oa/Tpl/default/Public/img/empty.gif',
				                folder: '/oa/Tpl/default/Public/img/folder.gif',
				                folderOpen: '/oa/Tpl/default/Public/img/folderopen.gif',
				                node: '../images/bg_4x7.gif',
				                empty: '/oa/Tpl/default/Public/img/empty.gif',
				                line: '/oa/Tpl/default/Public/img/line.gif',
				                join: '/oa/Tpl/default/Public/img/join.gif',
				                joinBottom: '/oa/Tpl/default/Public/img/joinbottom.gif',
				                plus: '/oa/Tpl/default/Public/img/plus.gif',
				                plusBottom: '/oa/Tpl/default/Public/img/plusbottom.gif',
				                minus: '/oa/Tpl/default/Public/img/minus.gif',
				                minusBottom: '/oa/Tpl/default/Public/img/minusbottom.gif',
				                nlPlus: '/oa/Tpl/default/Public/img/nolines_plus.gif',
				                nlMinus: '/oa/Tpl/default/Public/img/nolines_minus.gif'
				            };
				            // id, pid, name, url, title, target, icon, iconOpen, open, cls
				            user.add(0, -1, '金凯通达', '', '', '', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '', 'tree-root');
			    user.add('ALL_DEPT',0,'[所有部门]','javascript:ch(\'所有部门\',\'ALL_DEPT\')','','','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','','oTree-bg');
			<?php if(is_array($deptlist)): ?><?php $k = 0;?><?php $__LIST__ = $deptlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?>user.add(<?php echo ($vo[DEPT_ID]); ?>,<?php echo ($vo[DEPT_PARENT]); ?>,'[<?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?>]','javascript:ch(<?php echo (addDyh(is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME)); ?>,<?php echo (addDyh($vo[DEPT_ID])); ?>)','','','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','','oTree-bg');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
                     
				            document.write(user);
			                //-->
		                </script>
				            </div>
				            <input name="hiddenName" type="hidden" value="" id="hiddenName" />
				        </td>
			        </tr>
			        <tfoot>
				        <tr>
					        <td>
					            
					            <button name="Abutton1" onclick="senddata()">关闭</button>
					        </td>
				        </tr>
			        </tfoot>
		        </table>
	        <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
        </div>
        <script type="text/javascript" defer="defer" src="/oa/Tpl/default/Public/js/selectuser.js"></script>
        <!--选择部门模块结束-->
        		
</body>
</html>