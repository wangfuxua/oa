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

<link href="/oa/Tpl/default/Public/css/addcentcater.css" rel="stylesheet" type="text/css" />   
<body>
<!--顶部菜单加容量显示开始-->

<!--顶部菜单加容量显示结束-->

	<!-- leftPanel END-->
	<!--<div id="mainPanel" >-->
	<!--/* Right START ////////////////////////////////////////// */-->
	
	<!--右侧开始-->
  	<div id="ger-info">
  		<!--右侧111111开始-->
	    <div id="files_title" class="clearfix">
			<?php if($sort_id == 0 ): ?><h2>网盘根目录</h2><?php endif; ?>
			<?php if($sort_id != 0 ): ?><h2><?php echo ($myrow[SORT_NAME]); ?>(<?php echo ($SORT_TYPE_DESC); ?>)</h2><?php endif; ?>
			<span>
				<button onClick="javascript:search_file(<?php echo ($file_sort); ?>,0);" title="点击进入全局查询">全局查询</button>
	      	</span>
		</div>
  		<!--右侧111111结束--> 
  		<!--右侧222222开始--> 
    	<div id="active" class="clearfix">
				<?php 	if($new_priv == 1&&$sort_id>0&&$file_sort!=3){ ?>
				  	<span id="createNewFile"><img src="/oa/Tpl/default/Public/newimg/notify_new.gif" width="16" height="16" align="absmiddle" />新建文件</span>
				<?php }
						if(($new_priv==1||$file_sort==2)&&$file_sort!=3){
				?>
					<span id="createNewCildFile"><img src="/oa/Tpl/default/Public/newimg/newfolder.gif" width="16" height="16" align="absmiddle" />新建子文件夹</span>
				<?php }
						if($file_sort==2&&$sort_id!=0){
								if($myrow[TO_USER_ID]==''){
				?>
					<span onClick="setInput('FLD_STR','FLD_STR_NAME','right_show1',true)"><img src="/oa/Tpl/default/Public/newimg/endnode_share.gif" width="16" height="16" align="absmiddle" />共享此文件夹</span>
				<?php 			}else{
				?>
				<span onClick="setInput('FLD_STR','FLD_STR_NAME','right_show1',true)" title="点击更改共享范围"><img src="/oa/Tpl/default/Public/newimg/endnode_share.gif" width="16" height="16" align="absmiddle" />此文件夹已共享</span>
				<?php 			}
						}
						if($manage_priv==1&&$sort_id!=0&&$file_sort!=3){
				?>		<span id="reName"><img src="/oa/Tpl/default/Public/newimg/renamed.gif" width="18" height="18" align="absmiddle" />重命名</span>
						<span ><img src="/oa/Tpl/default/Public/newimg/deleteAll.gif" width="18" height="18" align="absmiddle" /><a href="javascript:delete_sort(<?php echo ($sort_id); ?>);">删除此文件夹</a></span>
						<span id="orderlist"><img src="/oa/Tpl/default/Public/images/bg_11.gif" width="16" height="16" align="absmiddle" />文件夹排序</span> 
						<span><img src="/oa/Tpl/default/Public/images/flow_prev.gif" width="16" height="16" align="absmiddle" />转移此文件夹至：<select name="SORT_ID_FILE" onChange="change_sort('1');" class="SmallSelect"><?php echo ($mysorttree); ?></select></span>
						 
				<?php }
				?>
                <span style="float:right"></span>
    	</div>
    	<!--右侧222222结束-->
    	<!--右侧333333开始-->  
    	<div id="ger-centens"> 
				<?php if($sort_id == 0 ): ?><table  id="tb-date">
						<caption class="nostyle">网盘根目录</caption>
					  	<tr>
					    	<td><?php echo ($msg); ?></td>
					  	</tr>
					</table><?php endif; ?> 
				<?php if($sort_id != 0 ): ?><div class="">
<form name="form1">
						<table  id="tb-date">
						  	<thead>
								<tr>
								<?php if($manage_priv == 1&&$file_sort!=3){ ?>
								<th width="10%">选择</th>
								<?php }?>
								<th width="20%">文件名称</th>
								<th width="30%">附件文件</th>
								<th width="30%">发布时间 <img border=0 src="/oa/Tpl/default/Public/images/arrow_down.gif" width="11" height="10"></th>
								<?php if($manage_priv == 1&&$file_sort!=3){ ?>
								<th>操作</th>
								<?php }?>
								</tr>
		   				  	</thead>
		   					<tbody>
		     					<?php if(is_array($file)): ?><?php $k = 0;?><?php $__LIST__ = $file?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
										<?php if($manage_priv == 1&&$file_sort!=3){ ?>
											<td>&nbsp;<input type="checkbox" name="email_select" value="<?php echo (is_array($vo)?$vo["CONTENT_ID"]:$vo->CONTENT_ID); ?>"></td>
										<?php }?>
											<td><a href="javascript:view_file(<?php echo (is_array($vo)?$vo["CONTENT_ID"]:$vo->CONTENT_ID); ?>,<?php echo ($sort_id); ?>);"><?php echo (csubstr(is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT,0,60)); ?></a></td>
											<td> <?php echo (file_att_list(is_array($vo)?$vo["ATTACHMENT_NAME"]:$vo->ATTACHMENT_NAME,$vo[ATTACHMENT_ID],$manager_priv,$down_priv)); ?></td>
											<td><?php echo (is_array($vo)?$vo["SEND_TIME"]:$vo->SEND_TIME); ?></td>
										<?php if($manage_priv == 1&&$file_sort!=3){ ?>
											<td>
											<a href="javascript:edit_file(<?php echo (is_array($vo)?$vo["CONTENT_ID"]:$vo->CONTENT_ID); ?>,<?php echo ($sort_id); ?>,<?php echo ($file_sort); ?>);">编辑</a>&nbsp;
											<a href="javascript:delete_content(<?php echo (is_array($vo)?$vo["CONTENT_ID"]:$vo->CONTENT_ID); ?>);"> 删除</a>
											</td>
										<?php }?>
								    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
		     				</tbody>
							<tfoot>
								<tr> 
										<?php if($manage_priv == 1&&$file_sort!=3){ ?> 
												<th colspan="5">  </th> 
										<?php }else{ ?>
												<th colspan="3">  </th> 
										<?php }?> 
								</tr>
							</tfoot>
		 				</table>
				</div>
      			<div class="tab-ft">
						<?php if($manage_priv == 1 &&$k>0&&$file_sort!=3){ ?>
						      <span id="check-all" style="width:80px;"><label for="cka"><input type="checkbox" name="allbox" id="cka" onClick="check_all()" style="vertical-align:middle" />全选</label></span>
						<?php }?>
						        <div class="btm-act">
						<?php if($manage_priv == 1&&$file_sort!=3){ ?>	
								<span id="deleteFlie"><img src="/oa/Tpl/default/Public/newimg/delete.gif" width="12" height="12" align="absmiddle" /> 删除 </span>
						<?php }?>		
								<span id="scFile"><img src="/oa/Tpl/default/Public/newimg/folder_search.gif" width="18" height="18" align="absmiddle" />查询当前目录下文件</span>
						<?php
						if($manage_priv==1 &&$k>0&&$file_sort!=3){
						?>
						    <span> 文档转移至：<select name="SORT_PARENT" onChange="change_sort('0');" class="SmallSelect"><?php echo ($mysorttree); ?></select></span>
						<?php 
						}
						?>
				</div>
		        <div id="page-Num"><?php echo ($page); ?></div>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		</div>
		<!--右侧333333结束-->
    </div>
	<!--右侧结束-->
	
	
	
	
	
	
	<!--/* Right END ////////////////////////////////////////// */-->





<!--</div>--><?php endif; ?>
<script type="text/javascript">
//删除
function delete_content(CONTENT_ID)
{
  msg="确定要删除该文件吗？这将不可恢复！";
  if(window.confirm(msg))
  {
    URL="/index.php/File/filedelete/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>/CONTENT_ID/" + CONTENT_ID;
    window.location=URL;
  }
}
function delete_sort(SORT_ID)
{
  msg="确定要删除该文件夹吗？这将删除该文件夹中的所有文件和子文件夹，且不可恢复！";
  if(window.confirm(msg))
  {
    URL="/index.php/File/sortdelete/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>";
    window.location=URL;
  }
}
function CheckForm()
{
   if(document.form1.SUBJECT.value=="")
   { alert("文件名称不能为空！");
     return (false);
   }
   document.form1.OP.value="1"
   return (true);
}
//多选删除
$("#deleteFlie").click(function(){
var gh=$("#tb-date").find("input");
if(confirm("您确定要删所选文件吗？")==1)
	for(var i=0; i<gh.length; i++){
		if(gh[i].checked==true){
			$.ajax({
			   type: "POST",
			   url: "/index.php/File/filedelete/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>/CONTENT_ID/" + gh[i].value,
			   success: function(){
					for(var j=0; j<gh.length; j++){
						if(gh[j].checked==true){
				  			var py=gh[j].parentNode;
				 			if(py.tagName!='tr')
							py=py.parentNode;
				   			$(py).remove();
				 		}	 a=$("#ger-centens").html();
				  	}   return
			   }
			});
	    }
	}
	if(i<1){
	alert("文件为空");
	return
	}
})
function upload_attach(op)
{
  if(CheckForm())
   {
     document.form1.OP.value=op;
     document.form1.submit();
   }
}
function check_all()
{
 	for (i=0;i<document.all("email_select").length;i++){
   		if(document.all("allbox").checked)
      		document.all("email_select").item(i).checked=true;
   		else
      		document.all("email_select").item(i).checked=false;
 	}
 	if(i==0){
   		if(document.all("allbox").checked)
      		document.all("email_select").checked=true;
   		else
      		document.all("email_select").checked=false;
 	}
}
function check_one(el)
{
   	if(!el.checked)
      		document.all("allbox").checked=false;
}
function set_page()
{
 			PAGE_START=(document.form1.PAGE_NUM.value-1)*<?php echo ($PAGE_SIZE); ?>+1;
 			location="folder.php?file_sort=<?php echo ($file_sort); ?>&sort_id=<?php echo ($sort_id); ?>&PAGE_START="+PAGE_START;
}
<?php
if($RELOAD_TREE==1){
?>
parent.file_tree.location.reload();
<?php }?>
//创建新文件
$("#createNewFile").click(function(){
 			$("#ger-centens").css('display','none');
           $("#ger-centens").html("")
         var bb=$("#ger-centens").load( "/index.php/File/filenew/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>");
           $(bb).fadeIn('slow');
/*
url="/index.php/File/filenew/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>";
location=url;
*/
})
//查看文件
function view_file(content_id,sort_id){
		$("#ger-centens").css('display','none');
           $("#ger-centens").html("")
         var bb=$("#ger-centens").load( "/index.php/File/fileread/?sort_id="+sort_id+"&CONTENT_ID="+content_id+"");

           $(bb).fadeIn('slow');

}
//编辑文件
function edit_file(content_id,sort_id,file_sort){
		$("#ger-centens").css('display','none');
           $("#ger-centens").html("")
         var bb=$("#ger-centens").load( "/index.php/File/filenew/file_sort/"+file_sort+"/sort_id/"+sort_id+"/CONTENT_ID/"+content_id+"");

           $(bb).fadeIn('slow');

}
//查询文件（全局）
function search_file(file_sort,sort_id){
$("#ger-centens").css('display','none');
$("#ger-centens").html("")
var bb=$("#ger-centens").load( "/index.php/File/filequery/file_sort/"+file_sort+"/sort_id/"+sort_id+"");
$(bb).fadeIn('slow')
}
//查询文件
$("#scFile").click(function(){
$("#ger-centens").css('display','none');
$("#ger-centens").html("")
var bb=$("#ger-centens").load( "/index.php/File/filequery/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>");
$(bb).fadeIn('slow')
})
//新建子文件
$("#createNewCildFile").click(function(){
$("#ger-centens").css('display','none');
$("#ger-centens").html("")
var bb=$("#ger-centens").load( "/index.php/File/sortnew/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>")
$(bb).fadeIn('slow')

})
//重命名
$("#reName").click(function(){
$("#ger-centens").css('display','none');
$("#ger-centens").html("")
var bb=$("#ger-centens").load( "/index.php/File/sortedit/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>")
$(bb).fadeIn('slow');
})
//文件夹排序
$("#orderlist").click(function(){
$("#ger-centens").css('display','none');
$("#ger-centens").html("")
var bb=$("#ger-centens").load( "/index.php/File/orderlist/file_sort/<?php echo ($file_sort); ?>")
$(bb).fadeIn('slow');
})


function change_sort(sort)
{
  delete_str="";
  if(sort=="0"){
     for(i=0;i<document.all("email_select").length;i++){ 
         el=document.all("email_select").item(i);
         if(el.checked){
         	val=el.value;
            delete_str+=val + ",";
         }
     }
     if(i==0){ 
         el=document.all("email_select");
         if(el.checked)
         {  
         	val=el.value;
            delete_str+=val + ",";
         }
     }
     if(delete_str==""){
        alert("要转移文件，请至少选择其中一个。");
        document.form1.reset();
        return;
     }
  }
  if(sort=="0"){
     sort_parent=document.all("SORT_PARENT").value;
     if(sort_parent=="0"){
     	alert("根目录下不允许存放文件！");return;
     }  
  }else{
     sort_parent=document.all("SORT_ID_FILE").value;  
  }
  url="/index.php/File/changesort/file_sort/<?php echo ($file_sort); ?>/sort_id/<?php echo ($sort_id); ?>/?FILE_STR="+ delete_str +"&SORT_PARENT="+sort_parent+"&SORT="+sort;
  location=url;
} 
</script>

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
			        <span onClick="selectAll(this)" style="position:absolute; right:10px; top:8px; color:#f60; cursor:pointer">全选</span>
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
						
			  			<ul id="right_show1" style="display:none;">
							<?php if(is_array($listall)): ?><?php $i = 0;?><?php $__LIST__ = $listall?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vod): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if($vod.id): ?><li class="<?php echo (is_array($vod)?$vod["id"]:$vod->id); ?>"><?php echo (is_array($vod)?$vod["name"]:$vod->name); ?></li><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>  	      
						</ul>     
			  	</div>
			</div>
		</div> 
		<p class="subotton"><button type="button" id="qd" onClick="mysubmit();">确定</button><button type="button" id="qx">取消</button></p>
</div>
<script type="text/javascript">
var person={ 
	<?php echo ($js_list); ?>
   }   
function mysubmit()
{     
	var a=$("#FLD_STR").val();
    $.ajax({
		type: "POST",
		url: "/index.php/File/sharesubmit/SORT_ID/<?php echo ($sort_id); ?>/FLD_STR/"+a,
		success: function(){
		alert("文件夹共享成功！"); 
		}
	});
}


</script>
<script src="/oa/Tpl/default/Public/js/writeAndaddcter.js" type="text/javascript"></script>
<!--弹出框结束-->  
<input type="hidden" name="FLD_STR" id="FLD_STR"> 
<input type="hidden" name="FLD_STR_NAME" id="FLD_STR_NAME">  
</body>
</html>