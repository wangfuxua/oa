<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="JKTD-D-丁亚娟" />
<title>人事档案</title>
<link href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/iframe.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
<script type="text/javascript">
			////////////////////////////////
$(document).ready(function() {
    //var val = $("#userSearch input").val(); 
    $("#userSearch input").keyup(function() {
        var val = $(this).val();
        var tree = $("#onlineTree");
        var box = $("#searchResult");
        if (val != "") {
            tree.hide();
            box.show();
            box.empty();
            var result = $(".sTreeNode:contains('" + $(this).val() + "')");
           result.each(function(i) {
                $(this).clone().appendTo(box);
            });
			$("#searchResult .oTree-bg").remove();
	        $("#searchResult img[src$='empty.gif']").remove();
        } else {
            tree.show();
            box.empty();
            box.hide();
        }
    });
});
		</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 30);
        setDomHeight("hrms", 32);
        setDomHeight("main",30);
        setDomWidth("mainPannel", 200);

		createHeader({
        Title: "人事档案管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ]

    });		   
});
    $(window).resize(function() { 
        setDomHeight("KDMain", 30);
        setDomHeight("hrms", 32);
        setDomHeight("main",30);
        setDomWidth("mainPannel", 200);   
    
    });

</script>
</head>

<body>
<div class="KDStyle" id="KDMain">
	<div id="leftpannel">
		<div class="leftPanelbox">
	<div id="userSearch" style="width:98%; margin-left:0px;padding-left:0px">
		<form action="">
			<input type="text" value="请输入姓名" style="width:100%;margin-left:0px;padding-left:0px" onclick="this.value=''"/>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>	
	<div id="searchResult">	</div>			
	<div class="addr-menu" style="width:100%" id="onlineTree">
	
		<script type="text/javascript">
	        <!--
            var online = new sTree('online');
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
				folder				: '/oa/Tpl/default/Public/images/treeicon/t_ini.gif',				// 枝节点文件夹图标
				folderOpen			: '/oa/Tpl/default/Public/images/treeicon/t_ini.gif',			// 枝节点打开状态文件夹图标
				node				: '/oa/Tpl/default/Public/img/folder.gif',			// 叶节点图标
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
			            
            online.root = new Node(-2);
	        // id, pid, name, url, title, target, icon, iconOpen, open, cls
            online.add(-1, -2, '金凯通达', '', '', '', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '', 'tree-root');
            
            online.add(0,-1,'在职人员','','','','','','','oTree-bg');
            online.add(1,-1,'离职人员','','','','','','','oTree-bg');
			<?php if(is_array($deptlist)): ?><?php $k = 0;?><?php $__LIST__ = $deptlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?>online.add(<?php echo ($vo[DEPT_ID]); ?>,<?php echo ($vo[DEPT_PARENT]); ?>,'[<?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?>]','javascript:;','','','/oa/Tpl/default/Public/images/user/user_group.gif','/oa/Tpl/default/Public/images/user/user_group.gif','','');
			    <?php if(is_array($vo[user])): ?><?php $i = 0;?><?php $__LIST__ = $vo[user]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$user): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?>online.add('<?php echo ($user[USER_ID]); ?>',<?php echo ($vo[DEPT_ID]); ?>,'<?php echo (is_array($user)?$user["USER_NAME"]:$user->USER_NAME); ?>','/index.php/Hrms/form/USER_ID/<?php echo (is_array($user)?$user["USER_ID"]:$user->USER_ID); ?>','','hrms','/oa/Tpl/default/Public/images/ico/user.png','','','');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			<?php echo ($treeoutuser); ?>
	        document.write(online);
	        //-->
        </script>
	</div>
		</div>
	</div>
	
	<div id="mainPannel">
			<script type="text/javascript">
			    iframe('/index.php/Hrms/blank', '100%', '', 'hrms');
	        </script>
	</div>
</div>

</body>
</html>