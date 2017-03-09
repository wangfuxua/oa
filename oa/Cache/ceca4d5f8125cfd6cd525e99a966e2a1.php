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
 
<link href="/oa/Tpl/default/Public/swfupload/css/default.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/oa/Tpl/default/Public/swfupload/js/swfupload.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/swfupload/js/handlers.js"></script> 
<script type="text/javascript">
		var swfu;

		//window.onload = function() {
		function UploadInit() {

					var settings = {
				flash_url : "/oa/Tpl/default/Public/swfupload/swfupload.swf",
				upload_url: "/index.php/Swfupload/index",	// Relative to the SWF file
				post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
				file_size_limit : "<?php echo ($upload_max_filesize); ?> MB",
				file_types : "*.*",
				file_types_description : "All Files",
				file_upload_limit : 100,
				file_queue_limit : 0,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				// Button settings
				button_image_url: "/oa/Tpl/default/Public/swfupload/images/TestImageNoText_65x29.png",	// Relative to the Flash file
				button_width: "65",
				button_height: "29",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: '<span class="theFont">添加附件</span>',
				button_text_style: ".theFont { font-size: 12;font-color:#333aaa;border:1px solid #97b8cc; }",
				button_text_left_padding: 7,
				button_text_top_padding: 4,

				// The event handler functions are defined in handlers.js
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess
			};

			swfu = new SWFUpload(settings);
	     };

	        
$(document).ready(function(){
	$.formValidator.initConfig({formid:"form1",onerror:function(msg){alert(msg)}});
	$("#SUBJECT").formValidator({onshow:"请输入 公告通知的标题",onfocus:"公告通知的标题不能为空"});
}); 
 
function sendForm(){
		if(document.form1.SUBJECT.value==""){
 			return (false);
		} 
   			document.form1.submit();  
}
function delete_attach(ATTACHMENT_ID,ATTACHMENT_NAME)
{
  msg="确定要删除文件 '"+ ATTACHMENT_NAME +"' 吗?";
  if(window.confirm(msg))
  { 
  $.ajax({
		type: "POST",
		url: "/index.php/manage/deleteAttach/NOTIFY_ID/<?php echo (is_array($ROW)?$ROW["NOTIFY_ID"]:$ROW->NOTIFY_ID); ?>/ATTACHMENT_ID/"+ATTACHMENT_ID+"/from/news/ATTACHMENT_NAME/"+ATTACHMENT_NAME,
		success: function(){
		alert("删除成功！");
		$('#a_'+ATTACHMENT_ID).remove();
		}
	}); 
  } 
}
function delete_attach_uploading(ATTACHMENT_ID,ATTACHMENT_NAME,fileid)
{
  msg="确定要删除文件 '"+ ATTACHMENT_NAME +"' 吗?";
  if(window.confirm(msg))
  {
     $.ajax({
		type: "POST",
		url: "/index.php/manage/deleteAttach/NOTIFY_ID/<?php echo (is_array($ROW)?$ROW["NOTIFY_ID"]:$ROW->NOTIFY_ID); ?>/ATTACHMENT_ID/"+ATTACHMENT_ID+"/from/news/ATTACHMENT_NAME/"+ATTACHMENT_NAME,
		success: function(){
			$('#'+fileid).remove(); 
		}
	}); 
  } 
} 
</script>  
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
 
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
    <!-- Editor End -->
    <script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "公告通知管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 2,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "管理已发布的公告", Url: "/index.php/manage/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加公告", Url: "/index.php/manage/news", Cls: "", IconCls: "ico ico-add" }
        ]
    });		   
});
</script>
<body>

	<div class="KDStyle" id="main">
		<form enctype="multipart/form-data" action="/index.php/manage/submit"  method="post" name="form1">
			<table style="vertical-align:middle">
				<colgroup>
					<col width="120"></col>
					<col width=""></col>
				</colgroup>
				<tr>
					<td>发布范围（部门）：</td>
					<input type="hidden" name="TO_ID" id="TO_ID" value="<?php echo (is_array($ROW)?$ROW["TO_ID"]:$ROW->TO_ID); ?>">
					<td><textarea cols="20" name=TO_NAME id="TO_NAME" rows="2" class="selectuser" wrap="yes" readonly><?php echo (getDeptnames(is_array($ROW)?$ROW["TO_ID"]:$ROW->TO_ID)); ?></textarea> 
					<button name="Abutton2"  type="button" onclick="setInput('TO_ID','TO_NAME')">添加</button>
					<!--<button name="Abutton3" onClick="chclear('TO')">清空</button>-->(留空则面向所有用户)</td>
				</tr>
				<tr>
					<td>标题：</td>
					<td><div style="float:left;"><input name="SUBJECT" id="SUBJECT" type="text" class="w300" value="<?php echo (is_array($ROW)?$ROW["SUBJECT"]:$ROW->SUBJECT); ?>" /></div><div id="SUBJECTTip" style="width:50%;float:left;"></div></td>
				</tr>
				<tr>
					<td>有效期：</td>
					<td>
						<fieldset class="date">
							<label>生效日期：</label>
							<input name="BEGIN_DATE" type="text" value="<?php if($ROW[BEGIN_DATE]): ?><?php echo (date('Y-m-d H:i:s',is_array($ROW)?$ROW["BEGIN_DATE"]:$ROW->BEGIN_DATE)); ?><?php endif; ?>" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
							<img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onClick="WdatePicker({el:$dp.$('BEGIN_DATE'),dateFmt:'yyyy-MM-dd HH:mm:ss'})"  />
							<span>为空为立即生效</span>
						</fieldset>
						<fieldset class="date">
							<label>终止日期：</label>
							<input name="END_DATE" type="text" value="<?php if($ROW[END_DATE]): ?><?php echo (date('Y-m-d H:i:s',is_array($ROW)?$ROW["END_DATE"]:$ROW->END_DATE)); ?><?php endif; ?>" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
							<img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onClick="WdatePicker({el:$dp.$('END_DATE'),dateFmt:'yyyy-MM-dd HH:mm:ss'})"  />
							<span>为空为手动终止</span>
						</fieldset>

					</td>
				</tr>
				<tr>
					<td>附件：</td>
					<td>
						 
						<?php if(is_array($listatt)): ?><?php $i = 0;?><?php $__LIST__ = $listatt?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><span  id="a_<?php echo ($vo[attid]); ?>" style="float:left"><img src="/oa/Tpl/default/Public/images/email_atta.gif">
						<a href="/index.php/Attach/view/attid/<?php echo ($vo[attid]); ?>"><?php echo ($vo[filename]); ?></a>&nbsp;&nbsp;(<?php echo ($vo[filesize]); ?>字节)&nbsp;&nbsp;<a href="javascript:delete_attach('<?php echo ($vo[attid]); ?>','<?php echo ($vo[filename]); ?>');">删除</a><br>
						<input type="hidden" name="oldattid[]" value="<?php echo ($vo[attid]); ?>">
						<input type="hidden" name="oldattname[]" value="<?php echo ($vo[filename]); ?>"></span><br><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
						 
						<div style="float:left;display:inline;overflow:hidden;width:auto" id="fsUploadProgress"></div>
						<div style="float:left;display:none;" id="divStatus"></div>					
					</td>
				</tr>				
				<tr>
					<td>上传：</td>
					<td>
						<div>
							  	<div style="float:left;width:70px;"><span id="spanButtonPlaceHolder"></span></div> 
								<input type="button" class="upBtn" value="开始上传" onClick="swfu.startUpload();"  />
								&nbsp;&nbsp;(附件最大：<?php echo ($upload_max_filesize); ?>)
						</div>
						
					</td>
				</tr>
				<tr>
					<td>提醒：</td>
					<td><input name="SMS_REMIND" type="checkbox" checked /><label>使用短信息提醒员工 </label></td>
				</tr>
				<tr>
				    <td>内容：</td>
					<td class="clearTable">
						<!-- 这里插入编辑器 -->	
						<textarea cols="20" id="CONTENT" name="CONTENT" style="height:205px"><?php echo (is_array($ROW)?$ROW["CONTENT"]:$ROW->CONTENT); ?></textarea>
					</td>
				</tr>
				<tfoot>
					<tr>
						<th colspan="2">
							
							<input type="hidden" name="NOTIFY_ID" value="<?php echo ($NOTIFY_ID); ?>">
							<input type="hidden" name="OP" value="">
							<button name="Abutton1" onClick="sendForm()">发布</button>
							<button name="Abutton1" onClick="location='/index.php/manage/index'">返回</button>
						</th>
					</tr>
				</tfoot>
			</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
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
<script type="text/javascript">
UploadInit();
</script>   
</body>
</html>