<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="JKTD-D-丁亚娟" />
<title>用户管理</title>
<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/default.css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/js/iframe.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/js.js"></script>
</head>
		<script type="text/javascript">
			////////////////////////////////
$(document).ready(function() {
    //var val = $("#userSearch input").val(); 
    /* 
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
*/    
    	   $("#searchKey").keyup(function() {
	            formValue();
	        });
});
	    function filterData(box, toBox, val) {
	        var result = box.find("p[name*='" + val + "']").parents(".sTreeNode");
	        result.each(function(i) {
	            $(this).clone().appendTo(toBox);
	        });
	    }
	    
	    function sRemove(box) {
	        box.find(".oTree-bg").remove().end()
	             .find("img[src$='empty.gif']").remove();
	    }
	    
	    function formValue() {
	       // var sValue = $("#searchState").val();
	        var kValue = $("#searchKey").val();
	        var uTree = $("#onlineTree");
	        var vbox = $("#ResultBox");    // 结果box：valueBox
	        var hbox = $("#StateBox");      // 中转box：hiddenBox

	            uTree.hide();
	            hbox.empty().hide();
	            vbox.empty().show();
	            
	            if (kValue != "") {
	                filterData(hbox, vbox, kValue);
	            } else {
	                uTree.show();
	                vbox.empty().hide();
	            }
	        sRemove(vbox);
	    }
		</script>
<body>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dm_personl tdtop" valign="top">
	<div id="userSearch" style="width:98%; margin-left:0px;padding-left:0px">
		<form action="">
			<input type="text" value="请输入姓名" id="searchKey" style="width:100%;margin-left:0px;padding-left:0px" onclick="this.value=''"/>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>			
			    <div class="userTree" id="onlineTree">
			        <div class="dm_menu_a"><b></b>在职人员</div>
			        <script type="text/javascript">
				        <!--
			            var online = new sTree('online');
			            online.config.folderLinks = true;
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
			            online.add(0, -1, '金凯通达', '', '', '', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '', 'tree-root');
			            
			<?php if(is_array($deptlist)): ?><?php $k = 0;?><?php $__LIST__ = $deptlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?>online.add(<?php echo ($vo[DEPT_ID]); ?>,<?php echo ($vo[DEPT_PARENT]); ?>,'[<?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?>]','/index.php/User/userlist/DEPT_ID/<?php echo ($vo[DEPT_ID]); ?>','','user_main','/oa/Tpl/default/Public/images/user/user_group.gif','/oa/Tpl/default/Public/images/user/user_group.gif','','');
			    <?php if(is_array($vo[user])): ?><?php $i = 0;?><?php $__LIST__ = $vo[user]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$user): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?>online.add('<?php echo ($user[USER_ID]); ?>',<?php echo ($vo[DEPT_ID]); ?>,'<?php echo (is_array($user)?$user["USER_NAME"]:$user->USER_NAME); ?>','/index.php/User/userEdit/USER_ID/<?php echo (is_array($user)?$user["USER_ID"]:$user->USER_ID); ?>','','user_main','/oa/Tpl/default/Public/images/ico/user.png','','','',<?php echo (json_encode($user[UserState])); ?>);<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
						            
			            document.write(online);
				        //-->
			        </script>
			        <div class="dm_menu_a"><b></b>离职/外部人员</div>
			        
			        <script type="text/javascript">
				        <!--
			            var outuser = new sTree('outuser');
			            outuser.config.folderLinks = true;
			            outuser.icon = {
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
			            outuser.add(0, -1, '金凯通达', '', '', '', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '/oa/Tpl/default/Public/images/ico/company-ico.gif', '', 'tree-root');
			            <?php echo ($treeoutuser); ?>
			            document.write(outuser);
				        //-->
			        </script>
			        			        
			    </div>
		<div id="StateBox"></div>
        <div id="ResultBox">	</div>
		<div id="test" style="display:none"></div>			    
<div id="searchResult">	</div>			    
			</td>
			<td class="dm_personr tdtop">

			<script type="text/javascript">
			    iframe('/index.php/User/userlist/<?php echo ($depturl); ?>', '100%', '600', 'user_main');
	        </script>
			</td>
		</tr>
	</table>
</body>
</html>