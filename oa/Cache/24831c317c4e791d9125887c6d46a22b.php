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
<script language="JavaScript">
function detail(BGSB_ID)
{
 URL="/index.php/Bgsb/detail/BGSB_ID/"+BGSB_ID;
 myleft=(screen.availWidth-500)/2;
 window.open(URL,"read_notify","height=400,width=500,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
}
function audit(BGSB_ID,AUDIT){
	URL="/index.php/Bgsb/auditto/BGSB_ID/"+BGSB_ID+"/AUDIT/"+AUDIT;
	window.location=URL;
}
function delete_gdzc(BGSB_ID)
{
 msg='确认要删除该办公设备么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Bgsb/delete/SH/<?php echo ($SH); ?>/BGSB_ID/"+BGSB_ID;
  window.location=URL;
 }
}
</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "固定资产审核",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: <?php if($SH == 2): ?>1<?php endif; ?><?php if($SH == 1): ?>2<?php endif; ?><?php if($SH == 0): ?>3<?php endif; ?>,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "已审核办公设备", Url: "/index.php/Bgsb/audit/SH/2", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "未通过审核办公设备", Url: "/index.php/Bgsb/audit/SH/1", Cls: "", IconCls: "ico ico-list" },
            { Title: "待审核办公设备", Url: "/index.php/Bgsb/audit/SH/0", Cls: "", IconCls: "ico ico-list" }
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
	<col />
	<col width="80px" />
	<col width="110px" />
	<col />
	<col width="80px" />
	<col width="80px" />
	<col width="120px" />
			<caption></caption>
  <thead>
      <tr>
      <th>办公设备名称</th>
      <th>数量</th>
      <th>登计日期</th>
      <th>使用部门</th>
      <th>管理人</th>
      <th>资产录入员</th>
      <th></th>
      </tr>
  </thead>
  
  <tbody>
  <?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["MC"]:$vo->MC); ?></td>
      <td><?php echo (is_array($vo)?$vo["SL"]:$vo->SL); ?></td>
      <td><?php echo (is_array($vo)?$vo["DJSJ"]:$vo->DJSJ); ?></td>
      <td><?php if($vo[SYBM_ID] != '0'): ?><?php echo (getDeptname(is_array($vo)?$vo["SYBM_ID"]:$vo->SYBM_ID)); ?><?php else: ?><?php echo (is_array($vo)?$vo["SZD"]:$vo->SZD); ?><?php endif; ?></td>
      <td><?php if($vo[GLR_ID] != ''): ?><?php echo (getUsername(is_array($vo)?$vo["GLR_ID"]:$vo->GLR_ID)); ?><?php else: ?><?php echo (is_array($vo)?$vo["GLR_NAME"]:$vo->GLR_NAME); ?><?php endif; ?></td>
      <td><?php if($vo[ZCLRY_ID] != ''): ?><?php echo (getUsername(is_array($vo)?$vo["ZCLRY_ID"]:$vo->ZCLRY_ID)); ?><?php else: ?><?php echo (is_array($vo)?$vo["ZCLRY_NAME"]:$vo->ZCLRY_NAME); ?><?php endif; ?></td>
      <td>
        <a href="javascript:detail(<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>)"> 详细</a>&nbsp;
        <?php if($SH == 0): ?><a href="javascript:audit(<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>,2)">批准</a>&nbsp;
		<a href="javascript:audit(<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>,1)">不准</a>&nbsp;
		<?php elseif($SH == 1): ?>
		<a href="javascript:audit(<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>,0)">撤消</a>&nbsp;
		<?php elseif($SH == 2): ?>
		<a href="javascript:audit(<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>,0)">撤消</a>&nbsp;<?php endif; ?>		
        <a href="javascript:delete_gdzc(<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>);"> 删除 </a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
  </tbody>

 <tfoot> 
 <tr><th colspan="7"><?php echo ($page); ?>
 </th>
 </tr>
 </tfoot>
</table>  
</div>

</body>
</html>