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
 
<script language=javascript>
function clear_content(obj)
{
  obj.value="";
}
</script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 30);

		createHeader({
        Title: "会议申请",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("KDMain", 30); 
    
    });

</script>
<body>

<div  class="KDStyle" id="KDMain">	
 <table>
  <form action="/index.php/Meeting/meetingsubmit"  method="post" name="form1" onsubmit="return CheckForm();">
  <caption class="nostyle"> <?php echo ($desc); ?></caption>
				<colgroup>
					<col width="150"></col>
					<col></col>
				</colgroup>
				  
    <tr>
      <th> 会议名称：</th>
      <td>
        <input type="text" name="M_NAME" size="53" maxlength="100"  value="<?php echo (is_array($ROW)?$ROW["M_NAME"]:$ROW->M_NAME); ?>">
      </td>
    </tr>
    <tr>
      <th> 会议主题<span style="color:red"> * </span>：</th>
      <td>
        <input type="text" name="M_TOPIC" size="53" maxlength="100"  value="<?php echo (is_array($ROW)?$ROW["M_TOPIC"]:$ROW->M_TOPIC); ?>">
      </td>
    </tr> 
    <tr>
      <th> 出席人员(外部)：</th>
      <td>
        <textarea name="M_RENYUAN" style="height:50px"></textarea>
      </td>
    </tr>
    <tr>
      <th> 出席人员(内部)：</th>
      <td>
        					<input type="hidden" name="M_RENYUAN_ID" id="M_RENYUAN_ID" value="<?php echo (is_array($ROW)?$ROW["M_RENYUAN_ID"]:$ROW->M_RENYUAN_ID); ?>">
							<textarea cols=20 name="M_RENYUAN_NAME" id="M_RENYUAN_NAME" rows="2" wrap="yes" style="height:50px" readonly><?php echo (is_array($ROW)?$ROW["M_RENYUAN_NAME"]:$ROW->M_RENYUAN_NAME); ?></textarea>
							<button name="Abutton1" type="button" onClick="setInput('M_RENYUAN_ID','M_RENYUAN_NAME')">添加</button> 
      </td>
    </tr>
            
    <tr>
      <th> 会议室：</th>
      <td>
        <select name="M_ROOM">
        <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo (is_array($vo)?$vo["MR_ID"]:$vo->MR_ID); ?>" <?php if($vo[MR_ID] == $ROW[M_ROOM]): ?>selected<?php endif; ?>><?php echo (is_array($vo)?$vo["MR_NAME"]:$vo->MR_NAME); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
        </select>
        <a href="/index.php/Meeting/mrlist">预约情况</a>
      </td>
    </tr>      
    <tr>
      <th> 开始时间：</th>
      <td>
        <input style="WIDTH: 177px; HEIGHT: 22px" maxlength=value= name=M_START value="<?php echo (is_array($ROW)?$ROW["M_START"]:$ROW->M_START); ?>"  onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});">
        <img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onClick="WdatePicker({el:$dp.$('M_START'),dateFmt:'yyyy-MM-dd HH:mm:ss'})"  />
      </td>
    </tr> 
    <tr>
      <th> 结束时间：</th>
      <td>
        <input style="WIDTH: 177px; HEIGHT: 22px" maxlength=value= name=M_END value="<?php echo (is_array($ROW)?$ROW["M_END"]:$ROW->M_END); ?>"  onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});">
        <img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onClick="WdatePicker({el:$dp.$('M_END'),dateFmt:'yyyy-MM-dd HH:mm:ss'})"  />
      </td>
    </tr>
  <tr>
    <th>通知出席人员：</th>
    <td>
       <input class="mailcheck" type="checkbox" name="SMS_REMIND" id="SMS_REMIND" checked><label for="SMS_REMIND">使用内部短信提醒</label>
    </td>
  </tr>    
    <tr>
      <th>会议简介：</th>
      <td>
        <textarea cols=50 name="M_DESC" rows="3" style="height:100px" wrap="on"><?php echo (is_array($ROW)?$ROW["M_DESC"]:$ROW->M_DESC); ?></textarea>
      </td>
    </tr>
    <tfoot>        
    <tr>
      <th colspan="2" nowrap>
        <input type="hidden" name="M_ID" value="<?php echo (is_array($ROW)?$ROW["M_ID"]:$ROW->M_ID); ?>">
        <button type="submit">提交</button>
        <button onClick="location='/index.php/Meeting/meetingform'">重填</button>
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
</body>
</html>