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

<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "图书查询",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 2,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "图书列表", Url: "/index.php/book/bookList", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "图书查询", Url: "/index.php/book/queryIndex", Cls: "", IconCls: "ico ico-query" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("KDMain", 56); 
    
    });

</script>
<body onLoad="document.form1.BOOK_NAME.focus();">

<div class="KDStyle" id="KDMain">
	
<form action="/index.php/book/queryList"  method="post" name="form1" >  
<table>
<col width="120px" />
  <tbody>
   <tr>
    <th>部门：</th>
    <td>
        <select name="DEPT_ID" class="BigSelect">
          <option value="all">所有</option>
           <?php echo ($select); ?>
        </select>
    </td>
   </tr>
  
   <tr>
    <th>图书类别：</th>
    <td>
        <select name="TYPE_ID" class="BigSelect">
        <option value="all">所有</option>
         <?php if(is_array($bookList)): ?><?php $i = 0;?><?php $__LIST__ = $bookList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo (is_array($vo)?$vo["TYPE_ID"]:$vo->TYPE_ID); ?>" ><?php echo (is_array($vo)?$vo["TYPE_NAME"]:$vo->TYPE_NAME); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
        </select>
    </td>
   </tr>
	<tr>
		<th>借阅状态：</th>
		<td>
			<select name="LEND">
				<option value="-1">不限</option>
				<option value="0">未借出</option>
				<option value="1">已借出</option>
			</select>
		</td>
	</tr>   
   <tr>
    <th>书名： </th>
    <td>
        <input type="text" name="BOOK_NAME" class="BigInput" size="25" maxlength="25">&nbsp;
    </td>
   </tr>
   <tr>
    <th>作者： </th>
    <td>
        <input type="text" name="AUTHOR" class="BigInput" size="25" maxlength="25">&nbsp;
    </td>
   </tr>
   <tr>
    <th>ISBN号： </th>
    <td>
        <input type="text" name="ISBN" class="BigInput" size="25" maxlength="25">&nbsp;
    </td>
   </tr>
   <tr>
    <th>出版社： </th>
    <td>
        <input type="text" name="PUB_HOUSE" class="BigInput" size="25" maxlength="25">&nbsp;
    </td>
   </tr>
   <tr>
    <th>存放地点： </th>
    <td>
        <input type="text" name="AREA" class="BigInput" size="25" maxlength="25">&nbsp;
    </td>
   </tr>
   <tr>
    <th>排序字段：</th>
    <td>
        <select name="ORDER_FIELD">
          <option value="DEPT_ID">部门 </option>
          <option value="TYPE_ID">类别 </option>
          <option value="BOOK_NAME">书名 </option>
          <option value="AUTHOR">作者 </option>
          <option value="PUB_HOUSE">出版社 </option>
        </select>
    </td>
   </tr>
   </tbody>
   <tfoot>
   <tr>
    <th colspan="2">
        <button type="submit" value="查询" title="模糊查询" name="button">查询</button>
    </th>
   </tr>
   </tfoot>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>
</body>
</html>