
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/iframe.js" ></script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 30);
        setDomHeight("tree", 32);
        setDomHeight("main",30);
        setDomWidth("mainPannel", 200);

		createHeader({
        Title: "员工日志查询",
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
        setDomHeight("tree", 32);
        setDomHeight("main",30);
        setDomWidth("mainPannel", 200);   
    
    });

</script>
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
<body>
	 	<div id="KDMain" class="KDStyle">	
		<div id="leftpannel">

		<script type="text/javascript">
		    // <![CDATA[
		    iframe('/index.php/Diary/infotree', '100%', '100%', 'tree');
		    // ]]>
        </script>
		</div>
     	<div id="mainPannel" style="width:80%;">

		<script type="text/javascript">
		    // <![CDATA[
		    iframe('/index.php/Diary/infoblank', '100%', '100%', 'main');
		    // ]]>
        </script>

		</div>
	</div>

</body>
</html>