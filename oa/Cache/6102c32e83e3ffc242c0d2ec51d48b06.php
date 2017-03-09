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
	</head>
<link href="/oa/Tpl/default/Public/css/addcentcater.css" rel="stylesheet" type="text/css" />
<!--<script>
function clear_user()
{
  document.form1.TO_NAME.value="";
  document.form1.TO_ID.value="";
}

</script>-->
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "内部短信息",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 4,
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
			<col width="100px" />
		  <form action="/index.php/Sms/search" name="form1" method="POST">
		  <thead><tr><th colspan="2"></th></tr></thead>
			<tr>
				<td>
         <select name="TYPE">
            <option value="FROM_ID">发送人</option>
            <option value="TO_ID">收信人</option>
         </select>
         
				</td>
				<td>
				    <input type="hidden" name="TO_ID" id="TO_ID">
					<input name="TO_NAME" id="TO_NAME" type="text"  class="dm_inputsen"  readonly />
					<button onClick="setInput('TO_ID','TO_NAME')" type="button"/>添加</button> 
				</td>
			</tr>
			<tr>
				<td>关键字：</td>
				<td class="dm_datetd">
                <input type="text" name="CONTENT" size="30" maxlength="30">
       			
				</td>
			</tr>			
			<tr>
				<td>起始日期：</td>
				<td class="dm_datetd">
        <input type="text" name="BEGIN_DATE" size="30" maxlength="30" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})">
        <img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="" style="cursor:hand" onClick="WdatePicker({el:$dp.$('BEGIN_DATE'),dateFmt:'yyyy-MM-dd HH:mm:ss'})"  />
        				
					
				</td>
			</tr>
			<tr>
				<td>截止日期：</td>
				<td class="dm_datetd">
        <input type="text" name="END_DATE" size="30" maxlength="30" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})">
        <img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="" style="cursor:hand" onClick="WdatePicker({el:$dp.$('END_DATE'),dateFmt:'yyyy-MM-dd HH:mm:ss'})"  />
					
				</td>
			</tr>
			<tr>
				<td>排序字段：</td>
				<td>
        <select name="ORDER_BY">
            <option value="SMS_TYPE">类型</option>
            <option value="FROM_TO">发送人/收信人</option>
            <option value="CONTENT">内容</option>
            <option value="SEND_TIME" selected>发送时间</option>
         </select>
         <select name="SEQ">
            <option value="DESC">降序</option>
            <option value="ASC">升序</option>
         </select>
				</td>
			</tr>
			<tfoot>
			<tr>
				<th colspan="2">
				   <button type="submit"  title="进行查询" >查询</button>
					
				</th>
			</tr>
			</tfoot>
			  <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		</table>
</div> 

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