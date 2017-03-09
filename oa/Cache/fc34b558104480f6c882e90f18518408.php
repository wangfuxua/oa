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

	



     	<div id="mainPannel">
    <!--active 为该文件夹操作-->
  <table>
  <caption>联系人查询
  	<p><form action="/index.php/Address/SearchSubmit" name="form2" method="POST">
全局查询 <input type="text" name="PSN_NAME" size="10" class="BigInput"><button type="button" onclick="document.form2.submit();" class="btn2">搜索</button>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form></p>
  </caption>
  <col width="100px" />
  <form action="/index.php/Address/SearchSubmit" name="form1" method="POST">
  <thead><tr><th colspan="2"></th></tr></thead>
    <tr>
    <td>分组：</td>
    <td>
         <select name="GROUP_ID" class="BigSelect">
         <option value="-1">所有</option>
        <option value="0">默认</option>
         <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo (is_array($vo)?$vo["GROUP_ID"]:$vo->GROUP_ID); ?>" ><?php echo (is_array($vo)?$vo["GROUP_NAME"]:$vo->GROUP_NAME); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
        </select>
    </td>
   </tr>
    <tr>
      <td>姓名：</td>
      <td><input type="text" name="PSN_NAME" size="10" class="BigInput"></td>
    </tr>
    <tr>
      <td> 性别：</td>
      <td>
        <select name="SEX" class="BigSelect">
          <option value="ALL">所有</option>
          <option value="0">男</option>
          <option value="1">女</option>
        </select>
      </td>
    </tr>
    <tr>
      <td> 昵称：</td>
      <td>
        <input type="text" name="NICK_NAME" size="25" class="BigInput">
      </td>
    </tr>
    <tr>
      <td> 单位名称：</td>
      <td>
        <input type="text" name="DEPT_NAME" size="25" class="BigInput">
      </td>
    </tr>
    <tr>
      <td>单位地址：</td>
      <td>
        <input type="text" name="ADD_DEPT" size="25" class="BigInput">
      </td>
    </tr>
    <tr>
      <td> 家庭住址：</td>
      <td>
        <input type="text" name="ADD_HOME" size="25" class="BigInput">
      </td>
    </tr>
    <tr>
      <td> 备注：</td>
      <td>
        <input type="text" name="NOTES" size="25" class="BigInput">
      </td>
    </tr>
    <tfoot>
    <tr>
      <th colspan="2" align="center">
      <button type="submit" title="进行查询" class="btn2">查询</button>
      </th>
    </tr>
    </tfoot>
    <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
  </table>
</div>
</div>

</body>
</html>