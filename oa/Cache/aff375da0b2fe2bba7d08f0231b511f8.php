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
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "办公设备查询",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "办公设备查询", Url: "/index.php/Bgsb/search", Cls: "", Icon: "", IconCls: "ico ico-query" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("KDMain", 56); 
    
    });

</script>
<body>

<div class="KDStyle" id="KDMain">	
<table>
<col width="120px" />
  <form action="/index.php/Bgsb/searchlist" method="post" name="form1">

   <tr>
    <th>设备类别：</th>
    <td>
    	<select name="typeid" id="vo">
    	<option value="<?php echo ($vo[typeid]); ?>" ></option>
    	<?php if(is_array($typelist)): ?><?php $i = 0;?><?php $__LIST__ = $typelist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo[typeid]); ?>" <?php if($vo[typeid] == $row[typeid]): ?>selected<?php endif; ?>><?php echo ($vo[typename]); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
    	</select>
    </td>
   </tr>
   <tr>
    <th>办公设备名称：</th>
    <td>
    	<input type="text" name="MC" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["MC"]:$row->MC); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>编号：</th>
    <td>
        <input type="text" name="BH" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["BH"]:$row->BH); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>所在地：</th>
    <td>
		<input type="text" name="SZD" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["SZD"]:$row->SZD); ?>">&nbsp;
    </td>
   </tr>
   
   <tr>
    <th>使用部门：</th>
    <td>
		<select name="SYBM_ID" class="BigSelect">
		<?php if($user[POST_PRIV] == 1): ?><option value="all">所有部门</option><?php endif; ?>
		<?php echo ($my_dept_tree); ?>
        </select>
    </td>
   </tr>
   <tr>
    <th>管理人：</th>
    <td>
       <select name="GLR_ID" class="BigSelect">
       <option value="all">所有人</option>
       <?php echo ($my_user_list); ?>
        </select>
    </td>
   </tr>
   <tr>
    <th>开始使用日期：</th>
    <td>
        <input type="text" name="KSSYRQ" size="30" maxlength="30" value="<?php echo (is_array($row)?$row["KSSYRQ"]:$row->KSSYRQ); ?>" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'});">&nbsp;
    </td>
   </tr>
   
   <tfoot>
   <tr>
    <th colspan="2">
        条件关系：
   		<select name="RELATION">
   			<option value="AND">与</option>
   			<option value="OR">或</option>
   		</select>
   		
        <button type="submit" value="查询" name="button"  class="btnFnt">查询</button>
        
    </th>
   </tr>
   </tfoot>
  <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</table>
</div>

</body>
</html>