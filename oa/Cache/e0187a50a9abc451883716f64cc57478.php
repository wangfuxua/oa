<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="JKTD-D-丁亚娟" />
<title>部门管理</title>
<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/js/iframe.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/js.js"></script>
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
</head>

<body>
<table>
	<colgroup>
		<col width="150"></col>
		<col width=""></col>
	</colgroup>
	<tr>
		<td class="dm_personl tdtop">
	<div id="userSearch" style="width:98%; margin-left:0px;padding-left:0px">
		<form action="">
			<input type="text" value="请输入部门名称" style="width:100%;margin-left:0px;padding-left:0px" />
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>	
			
			<div class="userTree" id="onlineTree">
			        <script type="text/javascript">
				        <!--
			            var online = new sTree('online');
	                    online.config.folderLinks = true;
			            online.icon = {
					       root: '/oa/Tpl/default/Public/images/ico/company-ico.gif', 			
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
			            online.add(0, -1, '金凯通达', '', '', '', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '', 'user-root');
					        
			<?php if(is_array($deptlist)): ?><?php $k = 0;?><?php $__LIST__ = $deptlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?>online.add(<?php echo ($vo[DEPT_ID]); ?>,<?php echo ($vo[DEPT_PARENT]); ?>,'[<?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?>]','/index.php/Dept/deptEdit/DEPT_ID/<?php echo ($vo[DEPT_ID]); ?>','dept','blank','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','','oTree-bg');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
								        
				        document.write(online);
				        //-->
			        </script>
			    </div>
<div id="searchResult">	</div>				    
		</td>
		<td class="tdtop">
			<div class="dm_personr">
			<script type="text/javascript">
			    iframe('/index.php/Dept/blank', '100%', '400', 'blank');
	        </script>
</div>
		</td>
	</tr>
</table>
	

</body>
</html>