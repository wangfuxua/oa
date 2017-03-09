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

<meta http-equiv="refresh" content="60">

	<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/tree.js"></script>	
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js"></script>
		<script type="text/javascript">
		    var checkStateID = null;
		    function go(id, name) {
		       window.parent.talkOnclick(id,name);
		    }
			////////////////////////////////
	    $(document).ready(function() {
	        // 下拉框选择
	        $("#searchState").change(function() {
	            formValue();
	        });

	        // input表单输入
	        $("#searchKey").keyup(function() {
	            formValue();
	        });

	        $(".sTreeNode").mouseover(function() {
	            var offset = $(this).find("a").offset().left;
	            var base = $(this).find(".onlinebar");
	            var btn = $(this).find(".onlinebar a");
	            base.show().css("margin-left", offset - 24);
	            btn.eq(0).click(function() {
	                //alert(1);
	                go($(this).parent().attr("id"), $(this).parent().attr("title"));
	            });
	            btn.eq(1).click(function() {
	                //alert(2);
	                $(this).attr({ href: "/index.php/Sms/smsform/TO_ID/" + $(this).parent().attr("id") + ",/TO_NAME/" + $(this).parent().attr("title") + ",", target: "icontent" })
	            });
	            btn.eq(2).click(function() {
	            //alert(3);
	                $(this).attr({ href: "/index.php/WebMail/index/to/add/toid/" + $(this).parent().attr("id") , target: "icontent" })
				
	                //$(this).attr({ href: "/index.php/Email/add/TO_ID/" + $(this).parent().attr("id") + "/TO_NAME/" + $(this).parent().attr("title") , target: "icontent" })
	            });
	        }).mouseout(function() {
	            $(this).find("p.onlinebar").hide();
	        });
	        // 更换头像为离线
	        var snode = $(".sTreeNode").find("img[name^='']");
	        $.each(snode, function() {
	            if ($(this).prev("a").length > 0) {

	            } else {
	                //$(this).attr("src", "/oa/Tpl/default/Public/images/user/user_offline.gif");
	            }
	        });
            //getState();
	        //var checkStateID = setInterval("getState()", 5000);
	        //userIco(101,"usLeave");
            checkState();
        });
        


	    function filterData(box, toBox, val) {
	        var result = box.find("p[name*='" + val + "']").parents(".sTreeNode");
	        var s;
	        if (val == "On") s = "在线";
	        else if (val == "Off") s = "离线";
	        else if (val == "Busy") s = "忙碌";
	        else if (val == "Leave") s = "离开";
	        else s = "全部";
	        $("#userNum").html(s+"<span>"+result.length+"</span>人");
	        result.each(function(i) {
	            $(this).clone().appendTo(toBox);
	        });
	    }
	    function sRemove(box) {
	        box.find(".oTree-bg").remove().end()
	             .find("img[src$='empty.gif']").remove();
	    }
	    function formValue() {
	        var sValue = $("#searchState").val();
	        var kValue = $("#searchKey").val();
	        var uTree = $("#onlineTree");
	        var vbox = $("#ResultBox");    // 结果box：valueBox
	        var hbox = $("#StateBox");      // 中转box：hiddenBox

	            uTree.hide();
	            hbox.empty().hide();
	            vbox.empty().show();
	            
	            if (sValue != "" && kValue != "") {
	                filterData(uTree, hbox, sValue);
	                filterData(hbox, vbox, kValue);
	            } else if (sValue == "" && kValue != "") {
	                filterData(uTree, vbox, kValue);
	            } else if (sValue != "" && kValue == "") {
	                filterData(uTree, vbox, sValue);
	            } else {
	                uTree.show();
	                vbox.empty().hide();
					$("#userNum").empty();
	            }
	        sRemove(vbox);
	    }
	    // 请求服务器获得用户状态数据
	    function getState() {
	        var url = "/index.php/userOnline/refresh";
	        var data = "";
	        $.post(url, data, function(val) {
	            //$("#test").text(val.length);
				for (var i=0;i<val.length;i++){
					var id=val[i].userid;
					var s=val[i].user_status;
					userIco(id, s);
					//onlineNum(val.length);
					//$("#test").text(id+" || "+s);
				}
	        }, "json");
	    }
	    
	    // 处理用户状态函数
	    function userIco(id,type) {
	        //
	        var ico, text;
	        var imgurl = "/oa/Tpl/default/Public/images/user/";
	        //alert(type);
	        switch (type) {
	            case "": case "usOnline": case null:
	                text = "On";
	                ico = imgurl + "user_ico_online.gif";
	                break;
	            case "usLeave":
	                text = "Leave";
	                ico = imgurl + "user_ico_leave.gif";
	                break;
	            case "usBusy":
	                text = "Busy";
	                ico = imgurl + "user_ico_busy.gif";
	                break;
	            default:
	                text = "Off";
	                ico = imgurl + "user_offline.gif";
	                break;
	        }
	        
	        var node = $(".sTreeNode").find("p#" + id);
			if (node.length>0){
				var v = node.attr("name").split("&");
				var newName = v[0] + "&" + v[1] + "&" + text + "&" + v[3] + "&" + v[4];
				node.attr("name", newName);
			}
	        node.end().find("img[name='"+id+"']").attr("src",ico);
			//$("#test").text(id+" | "+type+" | "+text+" | "+ico);
	    }
	    /* online ////////////////////////////////////////// */
	    function onlineNum(num) {
	        $(window.parent.document).find("#onlineNum").text(num);
	    }
	    /* refresh ////////////////////////////////////////// */

	    function checkState() {
	        var _state = getCookie("uRefreshState");
	        //alert(_state);
	        if (_state != null && _state != "" && _state == "start") {
	            reStart();
	        } else {
	            reStop();
	        }
	    }

	    function reStart() {
	        checkStateID = setInterval("getState()", 1000);
	    }
	    function reStop() {
	        if (checkStateID) {
	            clearInterval(checkStateID);
	        }
	    }
		</script>
		
	<body>
	<div id="userSearch">
		<form action="" name="selectUser">
		    <select name="userState" id="searchState">
		        <option value="">全部</option>
		        <option value="On">在线</option>
		        <option value="Off">离线</option>
		        <option value="Busy">忙碌</option>
		        <option value="Leave">离开</option>

		    </select>
			<!--<div id="userNum"></div>-->
			<input type="text" value="" name="selectKey" id="searchKey"/>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>

		<div class="onlineTree" id="onlineTree">
	
 <script type="text/javascript">
			                <!--
				            var user = new sTree('user');
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
			 
				            user.add('admin',0,'系统管理员','javascript:go(\'admin\',\'系统管理员\')','','','/oa/Tpl/default/Public/images/ico/user1.gif','','','',{"UserState":{"Labor":"","Spell":"xitongguanliyuan","State":"","Sex":"1"},"test":""});
			 	            
			<?php if(is_array($deptuserlist)): ?><?php $k = 0;?><?php $__LIST__ = $deptuserlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?>user.add(<?php echo ($vo[DEPT_ID]); ?>,<?php echo ($vo[DEPT_PARENT]); ?>,'[<?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?>]','javascript:;','','','/oa/Tpl/default/Public/images/user/user_group.gif','/oa/Tpl/default/Public/images/user/user_group.gif','','oTree-bg');
			    <?php if(is_array($vo[user])): ?><?php $i = 0;?><?php $__LIST__ = $vo[user]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$user): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?>user.add('<?php echo ($user[USER_ID]); ?>',<?php echo ($vo[DEPT_ID]); ?>,'<?php echo (is_array($user)?$user["USER_NAME"]:$user->USER_NAME); ?>','javascript:go(<?php echo (addDyh($user[USER_ID])); ?>,<?php echo (addDyh($user[USER_NAME])); ?>);','','','<?php echo (getOnlineStatusImg($user[USER_ID])); ?>','','','',<?php echo (json_encode($user[UserState])); ?>);<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
                     
				            document.write(user);
			                //-->
		                </script>
		                
		</div>
		<div id="StateBox"></div>
        <div id="ResultBox">	</div>
		<div id="test" style="display:none"></div>
</body>
</html>