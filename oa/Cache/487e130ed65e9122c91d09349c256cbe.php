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

<script language="JavaScript">
function CheckForm(){
   if(document.form1.TO_ID.value==""){
   	 alert("请添加收信人！");
     return (false);
   } 
   return (true);
}
function CheckSend(){
  uploadEdit(obj);
  if(event.keyCode==10){
    if(CheckForm())
       document.form1.submit();
  }
}
</script>
    <!-- Editor Start -->
    <script type="text/javascript" src="/oa/Tpl/default/Public/neweditor/tiny_mce.js"></script> 
    <script type="text/javascript">
        tinyMCE.init({
            mode: "exact",
            elements: "CONTENT",          // 要显示编辑器的textarea容器ID
            language: "zh",
            theme: "advanced",
            plugins: "table,emotions",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_buttons1: "formatselect,fontselect,fontsizeselect,bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,outdent,indent,separator,link,image,forecolor,backcolor,table,emotions",
            theme_advanced_buttons2: ""
        });
    </script>
     <!--Editor End 
    <script language="javascript" src="/oa/Tpl/default/Public/editor/editor_function.js"></script>
-->
    
<script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "内部短信息",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "发送短消息", Url: "/index.php/Sms/smsform", Cls: "", Icon: "", IconCls: "ico ico-add" },
            { Title: "已接收短信", Url: "/index.php/Sms/index", Cls: "", IconCls: "ico ico-accept" },
            { Title: "已发送短信", Url: "/index.php/Sms/send", Cls: "", IconCls: "ico ico-send" },
            { Title: "短信查询", Url: "/index.php/Sms/query", Cls: "", IconCls: "ico ico-query" }
        ]
    });		   
});
</script>
<body>

	<div class="KDStyle" id="main">
		<table>
		  <form action="/index.php/Sms/submit"  method="post" name="form1" onSubmit="return CheckForm();" id="forum_thread_submit">
		   <colgroup>
		     <col width="100"></col>
		     <col></col>
		     
		   </colgroup>
		   <thead><tr><th colspan="2"></th></tr></thead>
			<tr>
				<td class="dm_zanal">收信人：</td>
				<td>
				<input type="hidden" name="TO_ID" id="TO_ID" value="<?php echo ($TO_ID); ?>">
					<input name="TO_NAME" id="TO_NAME" type="text" title="发给多人时用英文逗号隔开" value="<?php echo ($TO_NAME); ?>" class="dm_inputsen" readonly />
					<button title="添加收信人" type="button" onclick="setInput('TO_ID','TO_NAME')"/>添加</button>
					<!--<button onClick="chclear('TO')" title="清空收信人" />清空</button>-->
				</td>
			</tr>
			<tr>
				<td valign="top">短信内容：</td>
				<td class="clearTable">
                <textarea class="userData" name="CONTENT" id="forum-ttHtmlEditor" style="height:300px;width:100%;border:0px"></textarea>
				
				</td>
			</tr>
			<tfoot>
			<tr>
				<th colspan="2">
				
				 <button type="submit" onClick="validate(this);">发送</button>
                 <button value="重填" onClick="location='/index.php/Sms/smsform'">重填</button>
				
				</th>
			</tr>
			</tfoot>
			<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		</table>
</div>


<link href="/oa/Tpl/default/Public/css/addcentcater.css" rel="stylesheet" type="text/css" />
<!--弹出框开始--> 
<div id="addDrug" style="display:none"></div>
<div id="addbox" style="display:none"> 
		<div id="titlebar">
		   <h2>添加联系人</h2>
		   <span class="closed"><img src="/oa/Tpl/default/Public/newimg/mail_close.gif" width="17" height="17" alt="close" /></span>
		</div>
		<div id="top-sh">
			<ul class="clearfix">
				<li class="all_c"><a href="#">全部</a></li>
				<li><a href="#">A</a></li>
				<li><a href="#">B</a></li>
				<li><a href="#">C</a></li>
				<li><a href="#">D</a></li>
				<li><a href="#">E</a></li>
				<li><a href="#">F</a></li>
				<li><a href="#">G</a></li>
				<li><a href="#">H</a></li>
				<li><a href="#">I</a></li>
				<li><a href="#">J</a></li>
				<li><a href="#">K</a></li>
				<li><a href="#">L</a></li>
				<li><a href="#">M</a></li>
				<li><a href="#">N</a></li>
				<li><a href="#">O</a></li>
				<li><a href="#">P</a></li>
				<li><a href="#">Q</a></li>
				<li><a href="#">R</a></li>
				<li><a href="#">S</a></li>
				<li><a href="#">T</a></li>
				<li><a href="#">U</a></li>
				<li><a href="#">V</a></li>
				<li><a href="#">W</a></li>
				<li><a href="#">X</a></li>
				<li><a href="#">Y</a></li>
				<li><a href="#">Z</a></li>
     		</ul>
     	</div>    
		<div class="selectbox clearfix">
			<div class="selectboxbder1">
			  	<div id="waitselect">
			    	<p class="ss"><input type="text" value="输入搜索内容汉字或拼音" name="" id="sc-name"  onclick="slect()"/><span id="go-sc"><img src="/oa/Tpl/default/Public/newimg/mail_input_sc_bg.gif" width="18" height="16" /></span></p>    
			    	<h3>金凯通达（北京）国际投资有限公司</h3>
				    <div class="selecte-btn">
				      	<h4 class="over">按部门选择</h4><span id="selecte-all-part"><a href="#">全选</a></span>
				    </div>
					<div id="bumen">
						<ul> <?php echo ($list_d); ?> </ul>    
				  	</div> 
				    <div class="selecte-btn">
				    	<h4>按角色选择</h4><span id="selecte-all-role"><a href="#">全选</a></span> 
				    </div> 
				    <div id="juese" style="display:none">      
				        <ul> <?php echo ($list_p); ?> </ul>    
				    </div> 
			  	</div> 
			</div> 
			<div class="selectboxbder3">
			    <div id="rel">
			        <h5>选择部门或角色</h5>
			         	<ul> </ul>
			        <span onclick="selectAll(this)" style="position:absolute; right:10px; top:8px; color:#f60; cursor:pointer">全选</span>
			    </div>
			</div> 
			<div class="selectboxbder2">
			  	<div id="selected">
						<div class="tl">
						<h3>已添加联系人：</h3><span>清空</span>
						</div> 
						<div id="sld-list">
							<ul class="bumen">
							</ul>
							<ul class="juese">
							</ul>
							<ul class="renyuan">
							</ul>
						</div>    
			  	</div>
			</div>
		</div> 
		<p class="subotton"><button type="button" id="qd">确定</button><button type="button" id="qx">取消</button></p>
</div>
<script type="text/javascript">
var person={ 
	<?php echo ($js_list); ?>
   }   
</script>
<script src="/oa/Tpl/default/Public/js/writeAndaddcter.js" type="text/javascript"></script>
<!--弹出框结束--> 
</body>
</html>