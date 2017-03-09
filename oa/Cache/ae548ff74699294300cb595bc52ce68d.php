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
				file_size_limit : "100 MB",
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
	</script>
	
<script Language="JavaScript">
function CheckForm(valu)
{
   if (document.form1.TO_ID.value=="")
   { alert("请添加收件人！");
     return (false);
   }

   if (document.form1.SUBJECT.value=="")
   { alert("请填写邮件主题！");
     document.form1.SUBJECT.focus();
     return (false);
   }
   if(valu==1)
      document.form1.SEND_FLAG.value="1";
   else 
      document.form1.SEND_FLAG.value="0";
      
 return (true);
}

function save()
{
  if(CheckForm())
  {
   document.form1.SEND_FLAG.value="0";
   document.getElementById("subs").click();
   //document.form1.submit();
  }
}
function sub()
{
  if(CheckForm())
  {
   document.form1.SEND_FLAG.value="1";
   document.getElementById("subs").click();
   //document.form1.submit();
  }
}

function canc(){
  url="/index.php/Email/inbox";
  $("#mail-act").html('');
  $("#mail-act").load(url);	
}

function delete_attach_uploading(ATTACHMENT_ID,ATTACHMENT_NAME,fileid)
{
  msg="确定要删除文件 '"+ ATTACHMENT_NAME +"' 吗?";
  if(window.confirm(msg))
  {
     $.ajax({
		type: "POST",
		url: "/index.php/Email/deleteattach/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>/CONTENT_ID/<?php echo ($_REQUEST[CONTENT_ID]); ?>/ATTACHMENT_ID/"+ATTACHMENT_ID+"/ATTACHMENT_NAME/"+ATTACHMENT_NAME,
		success: function(){
			$('#'+fileid).remove();

		}
	});

  }

}
</script>

<style>
form input{ border:none}
#addfile{padding:10px 0 0 0px; position:relative; height:16px; overflow:hidden;}
#addfile a{ color:#005DAA; text-decoration:none}
#addfile a:hover{color:#005DAA;}
#myfile{position:absolute;left:-18px;*left:-40px;top:4px; height:20px;cursor:pointer;opacity:0;filter:alpha(opacity=0)}
#addfilebox{ display:none; height:30px; padding:0 10px 5px; margin:5px 0 0;background:#f6fcff; border:1px solid #ccc; overflow-y:scroll}
#addfilebox span{ float:left; margin-right:5px; margin-top:5px; padding:2px 8px; border:1px solid #ccc; background:#f9f9f9; cursor:pointer}
</style>
<form enctype="multipart/form-data" action="/index.php/Email/submit"  method="post" name="form1" onsubmit="return CheckForm(document.form1.SEND_FLAG.value);">  
    <div id="title" class="clearfix">
      <h2>新建邮件</h2>
      <span class="rtact">全局查询<input type="text" /><button type="button">搜索</button></span>
    </div>
    <div id="active" class="active">
     <span id="mail-send"><a href="#" onclick='javascript:sub();'>发送</a></span> 
     <span id="mail-st"><a href="#" onclick='javascript:save();'>保存</a></span> 
     <span id="mail-cl"><a href="#" onclick='javascript:$("#mail-act").load("/index.php/Email/inbox")'>取消</a></span> 
    </div>
    <div id="mail-centens" class="clearfix" style="padding:0;border:none">
      <div class="mailto"  id="mailtoinfo">  
        <ul class="clearfix">
            <li style="position:relative"><strong id="sjr">收件人：</strong><span style="position:relative; width:300px">
            <input readonly type="text"  value="<?php echo (is_array($ROW)?$ROW["TO_NAME"]:$ROW->TO_NAME); ?>"  onclick="setInput('TO_ID','TO_NAME','right_show1',true);"  id="TO_NAME"/>
            <input type="hidden" value="<?php echo (is_array($ROW)?$ROW["TO_ID"]:$ROW->TO_ID); ?>" name="TO_ID" id="TO_ID"/>
            </span>
             <strong onclick="addS('csb',this)" style="padding-left:5px;">添加抄送</strong>
             <strong onclick="addS('msb',this)" style="padding-left:5px">添加暗送</strong>
            </li>        
           <li id="csb" style='display:none'><strong id="cs">抄送：</strong><span style="position:relative; width:300px">
            <input readonly type="text"  value="<?php echo (is_array($ROW)?$ROW["COPY_TO_NAME"]:$ROW->COPY_TO_NAME); ?>"  onclick="setInput('COPY_TO_ID','COPY_TO_NAME','right_show2',true);"  id="COPY_TO_NAME"/>
            <input type="hidden" value="<?php echo (is_array($ROW)?$ROW["COPY_TO_ID"]:$ROW->COPY_TO_ID); ?>" name="COPY_TO_ID" id="COPY_TO_ID"/>
            </span></li>
           <li id="msb" style='display:none'><strong id="ms">暗送：</strong><span style="position:relative; width:300px">
            <input readonly type="text"  value="<?php echo (is_array($ROW)?$ROW["SECRET_TO_NAME"]:$ROW->SECRET_TO_NAME); ?>"  onclick="setInput('SECRET_TO_ID','SECRET_TO_NAME','right_show3',true);"  id="SECRET_TO_NAME"/>
            <input type="hidden" value="<?php echo (is_array($ROW)?$ROW["SECRET_TO_ID"]:$ROW->SECRET_TO_ID); ?>" name="SECRET_TO_ID" id="SECRET_TO_ID"/>
            </span></li>
                        
           <li><strong id="zt">主题：</strong><span>
           <input type="text" value="<?php echo (is_array($ROW)?$ROW["SUBJECT"]:$ROW->SUBJECT); ?>" id="htitle"  name="SUBJECT" onclick="(this.value=='请输入邮件主题'?this.value = '':this.value);this.style.background='#fff'" onblur="(this.value==''?this.value='请输入邮件主题':this.value);if(this.value=='请输入邮件主题')this.style.background='#f6bbbb';this.style.color='#000'"/>
          </span></li>
        </ul>
  
	        <div style="float:left;display:inline;overflow:hidden;width:auto" id="fsUploadProgress"></div>
			<div style="float:left;display:none;" id="divStatus"></div>
			<ul>
	                        <?php if(is_array($listatt)): ?><?php $i = 0;?><?php $__LIST__ = $listatt?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><span  id="a_<?php echo ($vo[attid]); ?>" style="float:left"><img src="/oa/Tpl/default/Public/images/email_atta.gif">
							<a href="/index.php/Attach/view/attid/<?php echo ($vo[attid]); ?>"><?php echo ($vo[filename]); ?></a>&nbsp;&nbsp;(<?php echo ($vo[filesize]); ?>字节)&nbsp;&nbsp;<a href="javascript:delete_attach_uploading('<?php echo ($vo[attid]); ?>','<?php echo ($vo[filename]); ?>');">删除</a>
							<input type="hidden" name="oldattid[]" value="<?php echo ($vo[attid]); ?>">
							<input type="hidden" name="oldattname[]" value="<?php echo ($vo[filename]); ?>"></span><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
	        </ul>  								
	        <ul id="addfile" style="height:35px">
								  	<div style="float:left;width:70px;"><span id="spanButtonPlaceHolder"></span></div> 
									<input type="button" class="upBtn" value="开始上传" onclick="swfu.startUpload();"  />
									<script>
									UploadInit();
									</script>
	        </ul>
         </div>
		    <div id="editorbox">
		      <!--strat Editor-->
		          <textarea name="CONTENT" style="display:none" id="content"><?php echo (is_array($ROW)?$ROW["CONTENT"]:$ROW->CONTENT); ?></textarea>
		          <script>
		          $('#content').css('height','300')
		          </script>
		  <iframe ID="Editor" name="Editor" src="/oa/Tpl/default/Public/oaeditor/htmltool.htm?ID=content" frameBorder="0" marginHeight="0" marginWidth="0" scrolling="no" style="height:200px;width:100%;"></iframe>
		      <!--end editor-->
		    </div>
         
      <p class="mailbutton" style="background:none;border:none;border-top:1px solid #ccc">
          <input type="hidden" name="EMAIL_ID" value="<?php echo ($EMAIL_ID); ?>">
          <input type="hidden" name="BOX_ID" value="<?php echo ($BOX_ID); ?>">
          <input type="hidden" name="REPLAY" value="<?php echo ($REPLAY); ?>">
          <input type="hidden" name="FW" value="<?php echo ($FW); ?>">
          <input type="hidden" name="SEND_FLAG" value="1">
          
          <button id="subs" type="submit">发送</button>
          <button type="button" onclick='javascript:canc()'>取消</button>
      </p>
      <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

    </div>  
		<!--选择用户模块开始-->
          
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
			<ul id="right_show1" style="display:none;">	 
							<?php if(is_array($tolist)): ?><?php $i = 0;?><?php $__LIST__ = $tolist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vod): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if($vod.id): ?><li class="<?php echo (is_array($vod)?$vod["id"]:$vod->id); ?>"><?php echo (is_array($vod)?$vod["name"]:$vod->name); ?></li><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>   	 
		  	</ul>
		  	<ul id="right_show2" style="display:none;">	 
							<?php if(is_array($copylist)): ?><?php $i = 0;?><?php $__LIST__ = $copylist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vod): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if($vod.id): ?><li class="<?php echo (is_array($vod)?$vod["id"]:$vod->id); ?>"><?php echo (is_array($vod)?$vod["name"]:$vod->name); ?></li><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>   
		  	</ul>
		  	<ul id="right_show3" style="display:none;">	 
							<?php if(is_array($secretlist)): ?><?php $i = 0;?><?php $__LIST__ = $secretlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vod): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if($vod.id): ?><li class="<?php echo (is_array($vod)?$vod["id"]:$vod->id); ?>"><?php echo (is_array($vod)?$vod["name"]:$vod->name); ?></li><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>   
		  	</ul> 
        <!--选择用户模块结束-->
    
      <script>
         function addS(id,c){
         	
         	var d=$('#'+id);
         	if(d.css('display')=='none'){ 
         		d.css('display','');
         		if(id=='csb'){
         			$(c).html('删除抄送')
         		}else{
         			$(c).html('删除暗送')
         	    }
         	
         	}
         	else{
         		if(id=='csb'){
         			$(c).html('添加抄送')
         		}else{
         			$(c).html('添加暗送')
         		}
         		d.css('display','none');
         	}
         }
        </script>