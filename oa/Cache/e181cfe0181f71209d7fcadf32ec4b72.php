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
function detail(BOOK_ID)
{
 URL="/index.php/book/bookDetail/?BOOK_ID="+BOOK_ID;
 myleft=(screen.availWidth-500)/2;
 window.open(URL,"read_notify","height=400,width=500,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
}
</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "图书查询",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
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
<body>
<div class="KDStyle" id="KDMain">
<table>
<caption class="nostyle">图书列表</caption>
<thead>
  <tr>
      <th>部门</th>
      <th>书名</th>
      <th>类别</th>
      <th>作者</th>
      <th>出版社</th>
      <th>存放地点</th>
      <th>借阅状态</th>
      <th>操作</th>
  </tr>
  </thead>
  <tbody>
  <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
      <td><?php echo (getDeptname(is_array($vo)?$vo["DEPT_ID"]:$vo->DEPT_ID)); ?></td>
      <td><?php echo (is_array($vo)?$vo["BOOK_NAME"]:$vo->BOOK_NAME); ?></td>
      <td><?php echo (getTypename(is_array($vo)?$vo["TYPE_ID"]:$vo->TYPE_ID)); ?></td>
      <td><?php echo (is_array($vo)?$vo["AUTHOR"]:$vo->AUTHOR); ?></td>
      <td><?php echo (is_array($vo)?$vo["PUB_HOUSE"]:$vo->PUB_HOUSE); ?></td>
      <td><?php echo (is_array($vo)?$vo["AREA"]:$vo->AREA); ?></td>
      <td><?php echo (is_array($vo)?$vo["LEND_DESC"]:$vo->LEND_DESC); ?></td>
      <td>
        <a href="javascript:detail('<?php echo (is_array($vo)?$vo["BOOK_ID"]:$vo->BOOK_ID); ?>');">详情 </a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?> 
    </tbody>
  <tfoot>  
 <tr>
  <th colspan="8" align="right"><?php echo ($page); ?>
  </th>
  </tr>
  </tfoot>
</table>
</div>
</body>
</html>