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
function delete_gdzc(GDZC_ID)
{
 msg='确认要删除该固定资产么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Gdzc/delete/GDZC_ID/"+GDZC_ID;
  window.location=URL;
 }
}

</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "固定资产管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "固定资产列表", Url: "/index.php/Gdzc/manage", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加固定资产", Url: "/index.php/Gdzc/form", Cls: "", IconCls: "ico ico-view" }
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
  <thead>
      <tr>
      <th>固定资产名称</th>
      <th>数量</th>
      <th>开始使用日期</th>
      <th>使用部门</th>
      <th>管理人</th>
      <th>资产录入员</th>
      <th>审核</th>
      <th>操作</th>
      </tr>
  </thead>
  
  <tbody>
  <?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["MC"]:$vo->MC); ?></td>
      <td><?php echo (is_array($vo)?$vo["SL"]:$vo->SL); ?></td>
      <td><?php echo (is_array($vo)?$vo["KSSYRQ"]:$vo->KSSYRQ); ?></td>
      <td><?php echo (getDeptname(is_array($vo)?$vo["SYBM_ID"]:$vo->SYBM_ID)); ?></td>
      <td><?php echo (getUsername(is_array($vo)?$vo["GLR_ID"]:$vo->GLR_ID)); ?></td>
      <td><?php echo (getUsername(is_array($vo)?$vo["ZCLRY_ID"]:$vo->ZCLRY_ID)); ?></td>
      <td>
      <?php if($vo[SH] == 2): ?>通过审核<?php elseif($vo[SH] == 1): ?>未通过审核<?php else: ?>待审核<?php endif; ?>
      </td>
      <td width="80">
		<a href="/index.php/Gdzc/form/GDZC_ID/<?php echo (is_array($vo)?$vo["GDZC_ID"]:$vo->GDZC_ID); ?>"> 修改</a>&nbsp;&nbsp;
        <a href="javascript:delete_gdzc(<?php echo (is_array($vo)?$vo["GDZC_ID"]:$vo->GDZC_ID); ?>);"> 删除 </a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
  </tbody>

 <tfoot> 
 <tr><th colspan="8"><?php echo ($page); ?>
 </th>
 </tr>
 </tfoot>
</table>  
</div>

</body>
</html>