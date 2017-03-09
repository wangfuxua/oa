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

<body>
<div class="KDStyle" id="">
  <table style="width:90%">
  <caption>员工日程安排查询结果</caption>
  	<col width="100px" />
    <thead>
    <tr>
      <th>姓名</th>
      <?php if(is_array($headlist)): ?><?php $i = 0;?><?php $__LIST__ = $headlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><th>
          <?php echo ($YEAR); ?>-<?php echo ($MONTH); ?>-<?php echo (is_array($vo)?$vo["DAY"]:$vo->DAY); ?>(周<?php echo (is_array($vo)?$vo["WEEK_DESC"]:$vo->WEEK_DESC); ?>)
      </th><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
    </tr>
    </thead>
    
     <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr class="tcenter">
	   <td><?php echo (is_array($vo)?$vo["USER_NAME"]:$vo->USER_NAME); ?></td>
	    <?php if(is_array($vo[day])): ?><?php $i = 0;?><?php $__LIST__ = $vo[day]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$days): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><td>
	       <?php if(is_array($days[sub])): ?><?php $i = 0;?><?php $__LIST__ = $days[sub]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vos): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if($vos[CAL_TYPE] == 1): ?><?php echo (getHi(is_array($vos)?$vos["CAL_TIME"]:$vos->CAL_TIME)); ?>-<?php echo (getHi(is_array($vos)?$vos["END_TIME"]:$vos->END_TIME)); ?><br><?php echo (is_array($vos)?$vos["CONTENT"]:$vos->CONTENT); ?><br><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
	    </td><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
	  </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</table>
</div>
</body>
</html>