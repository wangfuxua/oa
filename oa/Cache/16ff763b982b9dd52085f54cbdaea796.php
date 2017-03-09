<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($curtitle); ?></title>
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/default.css" />
		
	</head>
	<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/tree.css" />
	<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
	<style type="text/css">
		body{height:100%}
	</style>
	<body>
	<div class="addr-menu">
		<script type="text/javascript">
	        <!--
            var online = new sTree('online');
	        // id, pid, name, url, title, target, icon, iconOpen, open, cls
            online.add(0, -1, '金凯通达', '', '', '', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '', 'tree-root');
            
            online.add(1,0,'联系人分组','','','','','','','oTree-bg');
            	online.add(10001,1,'默认','/index.php/AddressSys/index/GROUP_ID/0','','','/oa/Tpl/default/Public/images/ico/user.png','','','');
			<?php if(is_array($menulist)): ?><?php $k = 0;?><?php $__LIST__ = $menulist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?>online.add(<?php echo ($vo[GROUP_ID]+1000); ?>,1,'<?php echo (is_array($vo)?$vo["GROUP_NAME"]:$vo->GROUP_NAME); ?>','/index.php/AddressSys/index/GROUP_ID/<?php echo (is_array($vo)?$vo["GROUP_ID"]:$vo->GROUP_ID); ?>','','','/oa/Tpl/default/Public/images/ico/user.png','','','');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			
	        online.add(2,0,'索引(按姓氏)','','','','','','','oTree-bg');
			<?php if(is_array($names)): ?><?php $m = 0;?><?php $__LIST__ = $names?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$m;?><?php $mod = (($m % 2 )==0)?>online.add(<?php echo ($m+2000); ?>, 2, '<?php echo (is_array($vo)?$vo["TABLE_STR"]:$vo->TABLE_STR); ?>', '/index.php/AddressSys/SearchSubmit/ID_STR/<?php echo (is_array($vo)?$vo["ID_STR"]:$vo->ID_STR); ?>/TABLE_STR/<?php echo ($vo[TABLE_STR_URL]); ?>', '', '', '/oa/Tpl/default/Public/images/ico/user.png', '', '', '');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			
		        online.add(3,0,'查找(关键字)','/index.php/AddressSys/search','','','/oa/Tpl/default/Public/img/folder.gif','','','oTree-bg');
		        online.add(4,0,'管理分组','/index.php/AddressSys/group','','','/oa/Tpl/default/Public/img/folder.gif','/img/folder.gif','','oTree-bg');
	        document.write(online);
	        //-->
        </script>
	</div>
			
<SCRIPT>
function check_all()
{
 for (i=0;i<document.all("add_select").length;i++)
 {
   if(document.all("allbox").checked)
      document.all("add_select").item(i).checked=true;
   else
      document.all("add_select").item(i).checked=false;
 }
 
 if(i==0)
 {
   if(document.all("allbox").checked)
      document.all("add_select").checked=true;
   else
      document.all("add_select").checked=false;
 }
}

function check_one(el)
{
   if(!el.checked)
      document.all("allbox").checked=false;
}

</SCRIPT>
<div class="addr-main">
	<ul class="dm_submenuul">
		<li class="dm_on"><a href="/index.php/AddressSys/index/GROUP_ID/<?php echo ($GROUP_ID); ?>" title=""><span>联系人列表(<?php echo ($GROUP_NAME); ?>)</span></a></li>
		<li><a href="/index.php/AddressSys/add/GROUP_ID/<?php echo ($GROUP_ID); ?>" title=""><span>添加联系人(<?php echo ($GROUP_NAME); ?>)</span></a></li>
	</ul>

	<div>
		<form method="post" action="">
			<table>
 
				<thead>
					<tr>
						<th>选择</th>
						<th>姓名</th>
						<th>性别</th>
						<th>单位（部门）</th>
						<th>职务</th>
						<th>单位电话</th>
						<th>家庭电话</th>
						<th>手机</th>
						<th>电子邮件</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					
   <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr class="tdcenter">
     <td><input type="checkbox" name="add_select" value="<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>" onClick="check_one(self);"></td>
      <td><a href="/index.php/AddressSys/detail/ADD_ID/<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>" target="_blank"><?php echo (is_array($vo)?$vo["PSN_NAME"]:$vo->PSN_NAME); ?></a></td>
      <td>
      <?php if($vo[SEX] == 0): ?>男
      <?php elseif($vo[SEX] == 1): ?>
      女<?php endif; ?>
      
      </td>
      <td><?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?></td>
      <td><?php echo (is_array($vo)?$vo["MINISTRATION"]:$vo->MINISTRATION); ?></td>
      <td><?php echo (is_array($vo)?$vo["TEL_NO_DEPT"]:$vo->TEL_NO_DEPT); ?></td>
      <td><?php echo (is_array($vo)?$vo["TEL_NO_HOME"]:$vo->TEL_NO_HOME); ?></td>
      <td><?php echo (is_array($vo)?$vo["MOBIL_NO"]:$vo->MOBIL_NO); ?></td>
      <td><a href="mailto:<?php echo (is_array($vo)?$vo["EMAIL"]:$vo->EMAIL); ?>"><?php echo (is_array($vo)?$vo["EMAIL"]:$vo->EMAIL); ?></a></td>
      <td width="100">
          <a href="/index.php/AddressSys/detail/ADD_ID/<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>" target="_blank"> 详情</a>
          <a href="/index.php/AddressSys/edit/ADD_ID/<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>"> 编辑</a>
          <a href="javascript:delete_add(<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>);"> 删除</a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?> 
  
				</tbody>
			<tfoot>
			<tr>
			<td colspan="3"><input type="checkbox" name="allbox" onClick="check_all();">
			<input name="Button1" type="button" class="btnFnt" value="删除" onClick="delete_all('<?php echo ($GROUP_ID); ?>');" title="删除所选人员" />
			</td>
			<td colspan="7"><?php echo ($page); ?></td>
			</tr>
			</tfoot>
				
			</table>		
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>

	</div>
<script>
function add_detail(ADD_ID)
{
 URL="/index.php/AddressSys/detail/ADD_ID/"+ADD_ID;
 myleft=(screen.availWidth-500)/2;
 window.open(URL,"detail","height=500,width=400,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
}

function delete_add(ADD_ID)
{
 msg='确认要删除该联系人么？';
 if(window.confirm(msg))
 {
  URL="/index.php/AddressSys/delete/ADD_ID/" + ADD_ID + "/GROUP_ID/<?php echo ($GROUP_ID); ?>";
  window.location=URL;
 }
}

function delete_all(GROUP_ID)
{
  delete_str="";
  for(i=0;i<document.all("add_select").length;i++)
  {

      el=document.all("add_select").item(i);
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }
  
  if(i==0)
  {
      el=document.all("add_select");
      if(el.checked)
      {  val=el.value;
         delete_str+=val + ",";
      }
  }

  if(delete_str=="")
  {
     alert("要删除人员，请至少选择其中一个人员。");
     return;
  }

  msg='确认要删除所选人员么？';
  if(window.confirm(msg))
  {
    window.location="/index.php/AddressSys/deleteall/DELETE_STR/"+ delete_str +"/GROUP_ID/"+ GROUP_ID;
    
  }
}

</script>
</body>
</html>