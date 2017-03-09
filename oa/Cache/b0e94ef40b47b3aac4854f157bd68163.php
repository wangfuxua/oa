<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($curtitle); ?></title>
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/default.css" />
		
	</head>
<script type="text/javascript">
    var ifrFree=setInterval("reinitIframe()", 200);


    function reinitIframe() {
        var iframe = document.getElementById("kdForum");
        try {
            var bHeight = iframe.contentWindow.document.body.scrollHeight;
            var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
            var height = Math.max(bHeight, dHeight);

            iframe.height = height;
        } catch (ex) { }
    }
    window.close(function() {
        clearInterval(ifrFree);
    });
</script>
	<body>
		<div class="warp">
<iframe id="kdForum" name="" allowtransparency="true" align="top" marginwidth="0" marginheight="0" width="100%" height="550" border="0" frameborder="0" scrolling="auto" src="../../forum/index.php?username=<?php echo ($username); ?>"></iframe>
		</div>
</body>
</html>