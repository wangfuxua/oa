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
				file_size_limit : "10 MB",
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
   var sendto=$("#to").val();
   var subj=$("#htitle").val();
   //alert("dd");
   if(sendto==""){
   	 alert("请添加收件人！");
     return (false);
   }
   if (subj=="")
   { alert("请填写邮件主题！");
     return (false);
   }
   if(valu==1)
      document.form1.savemail.value="1";
   else 
      document.form1.savemail.value="0";
      
 return (true);
}

function save()
{
  if(CheckForm())
  {
   document.form1.savemail.value="1";
   document.getElementById("subs").click();
   //document.form1.submit();
  }
}
function sub()
{
  if(CheckForm())
  {
   document.form1.savemail.value="0";
   document.getElementById("subs").click();
   //document.form1.submit();
  }
}

function canc(){
  url="/index.php/WebMail/inbox";
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
		url: "/index.php/WebMail/deleteattach/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>/CONTENT_ID/<?php echo ($_REQUEST[CONTENT_ID]); ?>/ATTACHMENT_ID/"+ATTACHMENT_ID+"/ATTACHMENT_NAME/"+ATTACHMENT_NAME,
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
    <div id="title" class="clearfix">
      <h2>新建邮件</h2>
      <span class="rtact">全局查询<input type="text" /><button type="button">搜索</button></span>
    </div>
    <div id="active" class="active">
     <span id="mail-send"><a href="#" onclick='javascript:sub();'>发送</a></span> 
     <span id="mail-st"><a href="#" onclick='javascript:save();'>保存</a></span> 
     <span id="mail-cl"><a href="#" onclick='javascript:$("#mail-act").load("/index.php/WebMail/inbox/setid/<?php echo (is_array($set)?$set["setid"]:$set->setid); ?>")'>取消</a></span> 
    </div>
    <div id="mail-centens" class="clearfix" style="padding:0;border:none">
      <div class="mailto"  id="mailtoinfo">  
      <form enctype="multipart/form-data" action="/index.php/WebMail/sendMail"  method="post" name="form1" onsubmit="return CheckForm(document.form1.savemail.value);">  
        <ul class="clearfix">
	          <li style="position:relative"><strong id="fjr">发件人：</strong><span id="selectdown" style="position:relative; width:300px">
	            <input type="text" name="WP_send[from]" value="<?php echo (is_array($set)?$set["email"]:$set->email); ?>"  onclick="(this.value=='请输入收件人邮箱'?this.value = '':this.value)" onblur="(this.value==''?this.value='请输入收件人邮箱':this.value)"  id="sendername"/>
	            <input type="hidden" value="<?php echo (is_array($set)?$set["setid"]:$set->setid); ?>" name="setid" id="setid"/>
	            <input type="hidden" value="<?php echo (is_array($set)?$set["setid"]:$set->setid); ?>" name="WP_send[from_profile]" id="idarr"/>
	         <b style="position:absolute; right:-3px; _right:-7px; top:-2px">
	         <img src="/oa/Tpl/default/Public/newimg/selected.gif" style="display:block; background:#fff; border:1px solid #B8C9DD" /></b> </span> 
	         <ul id="senderlist"  class="clearfix" style="display:none">
	          <li>shulin2008cn@yahoo.com.cn</li>
	          <li>nicolas@yahoo.com.cn</li>
	         </ul> 
	       </li>
	         

             
            <li style="position:relative"><strong id="sjr">收件人：</strong><span style="position:relative; width:300px">
            <input type="text"  value="<?php echo (is_array($row)?$row["hto"]:$row->hto); ?>" name="WP_send[to]" id="to"/>
            
            </span>
            <strong onclick="addS('csb',this)" style="padding-left:5px;">添加抄送</strong>
             <strong onclick="addS('msb',this)" style="padding-left:5px">添加暗送</strong> 
            </li>   
           <li style="display:none" id="csb"><strong id="cs">抄送：</strong><span style="position:relative; width:300px">
            <input type="text" value="<?php echo (is_array($row)?$row["hcc"]:$row->hcc); ?>" id="hcc" name="WP_send[cc]"/>
            </span></li>
           <li style="display:none" id="msb"><strong id="ms">暗送：</strong><span style="position:relative; width:300px">
            <input type="text" value="<?php echo (is_array($row)?$row["hbcc"]:$row->hbcc); ?>" id="hbcc" name="WP_send[bcc]"/>
            </span></li>
                        
           <li><strong id="zt">主题：</strong><span>
           <input type="text" value="<?php echo (is_array($row)?$row["hsubject"]:$row->hsubject); ?>" id="htitle"  name="WP_send[subj]" onclick="(this.value=='请输入邮件主题'?this.value = '':this.value);this.style.background='#fff'" onblur="(this.value==''?this.value='请输入邮件主题':this.value);if(this.value=='请输入邮件主题')this.style.background='#f6bbbb';this.style.color='#000'"/>
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
          <textarea name="WP_send[body]" style="display:none" id="CONTENT"><?php echo ($CONTENT); ?></textarea>
		  <iframe ID="Editor" name="Editor" src="/oa/Tpl/default/Public/oaeditor/htmltool.htm?ID=CONTENT" frameBorder="0" marginHeight="0" marginWidth="0" scrolling="no" style="height:200px;width:100%;"></iframe>
		  <input type="hidden" name="WP_send[bodytype]" value="text/html">
      <!--end editor-->
    </div>
      <p class="mailbutton" style="background:none;border:none;border-top:1px solid #ccc">
          <input type="hidden" name="savemail" value="0">
          <button id="subs" type="submit">发送</button>
          <button type="button" onclick='javascript:canc();'>取消</button>
      </p>
      <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
      
    </div>  
      <script>
         function addS(id,c){
         	//alert(c);
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

		  	         
        <!--选择用户模块结束-->