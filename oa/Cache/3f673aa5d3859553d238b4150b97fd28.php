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
<script language=JavaScript>
window.setTimeout('this.location.reload();',60000);
</script>
<script>
my_top=50;
my_left=50;

function my_note(CAL_ID)
{
  my_top+=25;
  my_left+=15;

  window.open("/index.php/Calendar/note/CAL_ID/"+CAL_ID,"note_win"+CAL_ID,"height=170,width=180,status=0,toolbar=no,menubar=no,location=no,scrollbars=auto,top="+ my_top +",left="+ my_left +",resizable=no");
}

function delete_cal(CAL_ID)
{
 msg='确认要删除么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Calendar/caldelete/YEAR/<?php echo ($YEAR); ?>/MONTH/<?php echo ($MONTH); ?>/DAY/<?php echo ($DAY); ?>/CAL_ID/" + CAL_ID;
  window.location=URL;
 }
}


</script>
<script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "日程安排",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "日程安排", Url: "/index.php/Calendar/index", Cls: "", Icon: "", IconCls: "ico ico-calendar" },
            { Title: "日常事务", Url: "/index.php/Calendar/affairIndex", Cls: "", IconCls: "ico ico-clock" },
            { Title: "新建日常事务", Url: "/index.php/Calendar/affairform", Cls: "", IconCls: "ico ico-add" }
        ]
    });		   
});
</script>
<body>
	<div class="KDStyle" id="main">
		<div class="mainpanelHead">
		<h2>管理日程安排（<?php echo ($YEAR); ?>年<?php echo ($MONTH); ?>月<?php echo ($DAY); ?>日）</h2>
		<p>
		<button type="button"  value="新建事务" class="btnFnt" onClick="location='/index.php/Calendar/calform/YEAR/<?php echo ($YEAR); ?>/MONTH/<?php echo ($MONTH); ?>/DAY/<?php echo ($DAY); ?>';" title="创建新的事务，以便提醒自己">新建事务（<?php echo ($YEAR); ?>年<?php echo ($MONTH); ?>月<?php echo ($DAY); ?>日）</button>

		</p>
		</div>



  <table>
   <thead>
      <th>开始时间 <img border=0 src="/oa/Tpl/default/Public/images/arrow_up.gif" width="11" height="10"></th>
      <th>结束时间</th>
      <th>事务类型</th>
      <th>事务内容</th>
      <th>状态</th>
      <th>操作</th>
   </thead>
   <tbody>
   <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["CAL_TIME"]:$vo->CAL_TIME); ?></td>
      <td><?php echo (is_array($vo)?$vo["END_TIME"]:$vo->END_TIME); ?></td>
      <td><?php if($vo[CAL_TYPE] == 1): ?>工作事务<?php else: ?>个人事务<?php endif; ?></td>
      <td><a href="javascript:my_note(<?php echo (is_array($vo)?$vo["CAL_ID"]:$vo->CAL_ID); ?>);"><?php echo (csubstr(is_array($vo)?$vo["CONTENT"]:$vo->CONTENT,0,50)); ?></a></td>
      <td><?php if(compare_time($CUR_TIME,$vo[END_TIME]) > 0): ?><font color='#FF0000'><b>过期</span><?php elseif(compare_time($CUR_TIME,$vo[CAL_TIME]) < 0): ?><font color='#0000AA'><b>未至</span><?php else: ?><font color='#00AA00'><b>进行中</span><?php endif; ?></td>
      <td>
          <a href="javascript:my_note(<?php echo (is_array($vo)?$vo["CAL_ID"]:$vo->CAL_ID); ?>);"> 便笺</a>&nbsp;&nbsp;
          <a href="/index.php/Calendar/calform/YEAR/<?php echo ($YEAR); ?>/MONTH/<?php echo ($MONTH); ?>/DAY/<?php echo ($DAY); ?>/CAL_ID/<?php echo (is_array($vo)?$vo["CAL_ID"]:$vo->CAL_ID); ?>"> 修改</a>&nbsp;&nbsp;
          <a href="javascript:delete_cal(<?php echo (is_array($vo)?$vo["CAL_ID"]:$vo->CAL_ID); ?>);"> 删除</a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
   </tbody>
  <tfoot>
  <tr>
  <th colspan="6"><?php echo ($page); ?></th>
  </tr>
  </tfoot>
     
   </table>
   </div>
</body>
</html>