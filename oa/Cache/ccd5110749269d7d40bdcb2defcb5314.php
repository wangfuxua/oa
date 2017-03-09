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

	<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/tree.css" />
	<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/tongxun.css" />
	<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
	<!--script src="/oa/Tpl/default/Public/js/commoned2.js" type="text/javascript"></script-->
	<style type="text/css">
		body{height:100%}
		b {float:none}
		#title p {float:right; line-height:22px}
		
	</style>
	<script type="text/javascript">
    $(function() {
        setDomHeight("leftpannel", 56);
        setDomHeight("mainPannel", 56);
        setDomHeight("news_main",56);
        setDomWidth("mainPannel", 200);
		
		
		createHeader({
        Title: "通讯簿",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "个人通讯簿", Url: "/index.php/Address/privateaddress", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "公共通讯簿", Url: "/index.php/AddressPublic", Cls: "", IconCls: "ico ico-list2" }
        ]
    });
    });
    $(window).resize(function() { 
        setDomHeight("leftpannel", 56);
        setDomHeight("mainPannel", 56);
        setDomHeight("news_main",56);
        setDomWidth("mainPannel", 200);   
    
    });

</script>
	<body>
 	<div id="main" class="KDStyle">	
		<div id="leftpannel">
		<script type="text/javascript">
	        <!--
            var online= new sTree('online');
            online.config = {
				target				: null,		//所有节点的target
				folderLinks			: true,		//文件夹可链接
				useSelection		: true,		//节点可被选择(高亮)
				useCookies			: true,		//树可以使用cookies记住状态
				useLines			: false,	//创建带线的树
				useIcons			: true,		//创建带有图标的树
				useStatusText		: false,	//用节点名替代显示在状态栏的节点url
				closeSameLevel		: true,		//只有一个有父级的节点可以被展开,当这个函数可用时openAll() 和 closeAll() 函数将不可用
				inOrder				: true		//如果父级节点总是添加在子级节点之前,使用这个参数可以加速菜单显示.

			}

            online.icon = {
				root				: '/oa/Tpl/default/Public/img/empty.gif',				// 根节点图标
				folder				: '/oa/Tpl/default/Public/img/c_b3.gif',				// 枝节点文件夹图标
				folderOpen			: '/oa/Tpl/default/Public/img/c_b3.gif',			// 枝节点打开状态文件夹图标
				node				: '/oa/Tpl/default/Public/img/user.gif',			// 叶节点图标
				empty				: '/oa/Tpl/default/Public/img/empty.gif',				// 空白图标
				line				: '/oa/Tpl/default/Public/img/line.gif',				// 竖线图标
				join				: '/oa/Tpl/default/Public/img/join.gif',				// 丁字线图标
				joinBottom			: '/oa/Tpl/default/Public/img/joinbottom.gif',			// L线图标
				plus				: '/oa/Tpl/default/Public/img/plus.gif',				// 丁字折叠图标
				plusBottom			: '/oa/Tpl/default/Public/img/plusbottom.gif',			// L折叠图标
				minus				: '/oa/Tpl/default/Public/img/minus.gif',				// 丁字展开图标
				minusBottom			: '/oa/Tpl/default/Public/img/minusbottom.gif',		// L展开图标
				nlPlus				: '/oa/Tpl/default/Public/img/nolines_plus.gif',		// 无线折叠图标
				nlMinus				: '/oa/Tpl/default/Public/img/nolines_minus.gif'		// 无线展开图标
			};
			online.root = new Node(-1);
	        // id, pid, name, url, title, target, icon, iconOpen, open, cls
            online.add(0, -1, '金凯通达', '', '', '', '', '', '', 'tree-root');

		        online.add(1,0,'联系人分组','','','','','','','');
		            online.add(10001,1,'默认','/index.php/Address/privateaddress/GROUP_ID/0','','','/oa/Tpl/default/Public/images/ico/user.png','','','');
					<?php if(is_array($menulist)): ?><?php $k = 0;?><?php $__LIST__ = $menulist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?>online.add(<?php echo ($vo[GROUP_ID]+1000); ?>,1,'<?php echo (is_array($vo)?$vo["GROUP_NAME"]:$vo->GROUP_NAME); ?>','/index.php/Address/privateaddress/GROUP_ID/<?php echo (is_array($vo)?$vo["GROUP_ID"]:$vo->GROUP_ID); ?>','','','/oa/Tpl/default/Public/images/ico/user.png','','','');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				online.add(2,0,'索引（按姓氏）','','','','','','','');
					<?php if(is_array($names)): ?><?php $m = 0;?><?php $__LIST__ = $names?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$m;?><?php $mod = (($m % 2 )==0)?>online.add(<?php echo ($m+2000); ?>, 2, '<?php echo (is_array($vo)?$vo["TABLE_STR"]:$vo->TABLE_STR); ?>', '/index.php/Address/SearchSubmit/ID_STR/<?php echo (is_array($vo)?$vo["ID_STR"]:$vo->ID_STR); ?>/TABLE_STR/<?php echo ($vo[TABLE_STR_URL]); ?>', '', '', '/oa/Tpl/default/Public/images/ico/user.png', '', '', '');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				online.add(3,0,'查找（关键字）','/index.php/Address/search','','','/oa/Tpl/default/Public/img/c_b3.gif','','','');

				online.add(4,0,'添加联系人','/index.php/Address/add','','','/oa/Tpl/default/Public/img/c_b3.gif','','','');
				online.add(5,0,'管理分组','/index.php/Address/group','','','/oa/Tpl/default/Public/img/c_b3.gif','','','');
						online.add(369,5,'分组列表','/index.php/Address/group','','','','','','');
					online.add(368,5,'新建分组','/index.php/Address/groupform','','','','','','');
			document.write(online);
	        //-->
        </script>
		
	</div>

	



<script>
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

</script>
     	<div id="mainPannel">
			<div class="mainpanelHead">
       <h2><a href="/index.php/Address/privateaddress/GROUP_ID/<?php echo ($GROUP_ID); ?>" title="">联系人列表(<?php echo ($GROUP_NAME); ?>)</a></h2>
	   <p>
<form action="/index.php/Address/SearchSubmit" name="form1" method="POST">
<label>全局查询</label><input type="text" name="PSN_NAME" size="10" class="BigInput"><button type="button" onclick="document.form1.submit();" class="">搜索</button>
    <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</p>
	</div>

    <!--active 为该文件夹操作-->

			<table>
	 		<col width="30px" />
			   <thead>
			   <tr>
				<th> </th>
				  <th>姓名</th>
				  <th>性别</th>
				  <th>单位（部门）</th>
				  <th>职位</th>
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

      <td><a href="/index.php/Address/detail/ADD_ID/<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>" target="_blank"><?php echo (is_array($vo)?$vo["PSN_NAME"]:$vo->PSN_NAME); ?></a></td>
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
          <a href="/index.php/Address/detail/ADD_ID/<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>" target="_blank"> 详情</a>
          <a href="/index.php/Address/edit/ADD_ID/<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>"> 编辑</a>
          <a href="javascript:delete_add(<?php echo (is_array($vo)?$vo["ADD_ID"]:$vo->ADD_ID); ?>);"> 删除</a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			  </tbody>
						<tfoot>
						<tr>
							<td  colspan="10"><input type="checkbox" name="allbox" onClick="check_all();">
							<button onClick="delete_all('<?php echo ($GROUP_ID); ?>');" title="删除所选人员" class="" />删除</button>

							 <button onClick="delete_all2('<?php echo ($GROUP_ID); ?>');" title="删除本组全部人员" class="" />全部删除</button>
							 <?php echo ($page); ?></td>
						</tr>
						</tfoot>
			  </table>


	<!--end                      per-box-->
  </div>
</div>



<script>

function add_detail(ADD_ID)
{
 URL="/index.php/Address/detail/ADD_ID/"+ADD_ID;
 myleft=(screen.availWidth-500)/2;
 window.open(URL,"detail","height=500,width=400,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
}

function delete_add(ADD_ID)
{
 msg='确认要删除该联系人么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Address/delete/ADD_ID/" + ADD_ID + "/GROUP_ID/<?php echo ($GROUP_ID); ?>";
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
    window.location="/index.php/Address/deleteall/DELETE_STR/"+ delete_str +"/GROUP_ID/"+ GROUP_ID;

  }
}
function delete_all2(GROUP_ID)
{
  msg='确认要删除全部人员么？';
  if(window.confirm(msg))
  {
    window.location="/index.php/Address/deleteall2/GROUP_ID/"+ GROUP_ID;

  }
}

</script>

</body>
</html>