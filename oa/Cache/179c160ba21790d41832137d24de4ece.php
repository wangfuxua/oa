<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="JKTD-D-丁亚娟" />
<title>工作流程设置</title>
<link href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/jquery-1.2.6-vsdoc-cn.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/iframe.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
<script type="text/javascript">
    $(function() {
        setDomHeight("leftpannel", 56);
        setDomHeight("mainPannel", 56);
        setDomHeight("news_main",56);
        setDomWidth("mainPannel", 200);
    });
    $(window).resize(function() { 
        setDomHeight("leftpannel", 56);
        setDomHeight("mainPannel", 56);
        setDomHeight("news_main",56);
        setDomWidth("mainPannel", 200);   
    
    });

</script>
</head>

<body>
    <div id="titbar">
        <h2>
            <b class="ico ico-head-news"></b>工作流</h2>
    </div>
    <div id="tabbar">
        <!-- funbar btn start -->
        <ul>
            <li><a href="/index.php/ZworkFlow/index" title=""><b class="ico ico-list"></b>待办工作</a></li>
            <li><a href="news" title="" class="on"><b class="ico ico-add"></b>新建工作</a></li>
            <li><a href="/index.php/ZworkFlow/workControl" title=""><b class="ico ico-list2"></b>工作监控</a></li>
        </ul>
    </div>
 <div id="main" class="KDStyle">
  <div id="leftpannel">
  			    <div class="userTree">
			        <div>请选择工作流程</div>
			        <script type="text/javascript">
				        <!--
			            var online = new sTree('online');
			            online.icon = {
			                root: '/oa/Tpl/default/Public/img/empty.gif',
			                folder: '/oa/Tpl/default/Public/img/folder.gif',
			                folderOpen: '/oa/Tpl/default/Public/img/folderopen.gif',
			                node: '/oa/Tpl/default/Public/images/bg_4x7.gif',
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

			            <?php echo ($treeOnline); ?>;
			            document.write(online);
				        //-->
			        </script>
			    </div>
  
  
   </div>
     <div id="mainPannel">
     <script type="text/javascript">
         iframe('/index.php/ZworkFlow/<?php echo ($URL); ?>', '100%', '', 'news_main');
	            </script>
     </div>
     
</div>     
</body>
</html>