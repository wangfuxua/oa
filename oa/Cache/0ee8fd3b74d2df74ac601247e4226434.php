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
<script>
function delete_affair(AFF_ID)
{
 msg='确认要删除该事务么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Calendar/affairdelete/AFF_ID/" + AFF_ID;
  window.location=URL;
 }
}

function my_note(AFF_ID)
{
  my_left=document.body.scrollLeft+event.clientX-event.offsetX-50;
  my_top=document.body.scrollTop+event.clientY-event.offsetY+150;

  window.open("/index.php/Calendar/affairnote/AFF_ID/"+AFF_ID,"note_win"+AFF_ID,"height=170,width=180,status=0,toolbar=no,menubar=no,location=no,scrollbars=auto,top="+ my_top +",left="+ my_left +",resizable=no");
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
        Active: 2,
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
		<h2>日常事务管理</h2>
		<p>
<button type="button" onClick="location='/index.php/Calendar/affairform';">新建日常事务</button>
		</p>
<!--<input type="button" value="" class="btnFnt" title="新建日常事务">-->
</div>

    <table>

   <thead>
   <tr>
      <th width="150">起始时间 <img border=0 src="/oa/Tpl/default/Public/images/arrow_down.gif" width="11" height="10"></th>
      <th width="80">提醒类型</th>
      <th width="80">提醒日期</th>
      <th width="80">提醒时间</th>
      <th>日志内容</td>
      <th width="80">操作</th>
    </tr>  
   </thead>
   <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr class="TableLine1">
      <td><?php echo (is_array($vo)?$vo["BEGIN_TIME"]:$vo->BEGIN_TIME); ?></td>
      <td>
      <?php switch($vo[TYPE]): ?><?php case "2":  ?>按日提醒<?php break;?>
      <?php case "3":  ?>按周提醒
      <?php if($vo[REMIND_DATE] == 1): ?><?php $vo[REMIND_DATE]="周一";?>
      <?php elseif($vo[REMIND_DATE] == 2): ?>
      <?php $vo[REMIND_DATE]="周二";?>
      <?php elseif($vo[REMIND_DATE] == 3): ?>
      <?php $vo[REMIND_DATE]="周三";?>
      <?php elseif($vo[REMIND_DATE] == 4): ?>
      <?php $vo[REMIND_DATE]="周四";?>
      <?php elseif($vo[REMIND_DATE] == 5): ?>
      <?php $vo[REMIND_DATE]="周五";?>
      <?php elseif($vo[REMIND_DATE] == 6): ?>
      <?php $vo[REMIND_DATE]="周六";?>
      <?php elseif($vo[REMIND_DATE] == 7): ?>
      <?php $vo[REMIND_DATE]="周日";?><?php endif; ?><?php break;?>
      <?php case "4":  ?>按月提醒
      <?php $vo[REMIND_DATE].="日";?><?php break;?>
      <?php case "5":  ?>按年提醒
      <?php $vo[REMIND_DATE]=str_replace("-","月",$vo[REMIND_DATE])."日";?><?php break;?><?php endswitch;?>
      </td>
      <td><?php echo ($vo[REMIND_DATE]); ?></td>
      <td><?php echo (is_array($vo)?$vo["REMIND_TIME"]:$vo->REMIND_TIME); ?></td>
      <td><a href="#" onclick="my_note(<?php echo (is_array($vo)?$vo["AFF_ID"]:$vo->AFF_ID); ?>);">
      <?php echo (csubstr(strip_tags(is_array($vo)?$vo["CONTENT"]:$vo->CONTENT),0,80)); ?></td>
      <td>
          <a href="/index.php/Calendar/affairform/AFF_ID/<?php echo (is_array($vo)?$vo["AFF_ID"]:$vo->AFF_ID); ?>"> 修改</a>&nbsp;
          <a href="javascript:delete_affair(<?php echo (is_array($vo)?$vo["AFF_ID"]:$vo->AFF_ID); ?>);"> 删除</a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
  <tfoot>
  <tr>
  <th colspan="6"><?php echo ($page); ?></th>
  </tr>
  </tfoot>
 </table>
</div>


</body>
</html>