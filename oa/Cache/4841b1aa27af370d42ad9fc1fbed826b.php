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
function detail(DZYH_ID)
{
 URL="/index.php/Dzyh/detail/DZYH_ID/"+DZYH_ID;
 myleft=(screen.availWidth-500)/2;
 window.open(URL,"read_notify","height=400,width=500,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
}
</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "低值易耗品领用",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "低值易耗品列表", Url: "/index.php/Dzyh/requestlist/GLR_ID/all/SYBM_ID/all", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "低值易耗品查询", Url: "/index.php/Dzyh/request", Cls: "", Icon: "", IconCls: "ico ico-list" }			
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
<col width="" />
<col width="" />
<col width="" />
<col width="" />
<col width="" />
<col width="" />
<col width="110px" />
<col width="" />
<col width="80px" />
<col width="" />
<col width="80px" />
  <thead>
      <tr>
      <th>低值易耗品名称</th>
      <th>编号</th>
      <th>类别</th>
      <th>数量</th>
      <th>规格型号</th>
      <th>单价</th>
      <th>供货单位</th>
      <th>开始使用日期</th>
      <th>使用部门</th>
      <th>管理人</th>
      <th>剩余数量</th>
      <th>操作</th>
      </tr>
  </thead>
  <tbody class="tcenter">
  <?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["MC"]:$vo->MC); ?></td>
      <td><?php echo (is_array($vo)?$vo["BH"]:$vo->BH); ?></td>
      <td><?php echo (is_array($vo)?$vo["LB"]:$vo->LB); ?></td>
      <td><?php echo (is_array($vo)?$vo["SL"]:$vo->SL); ?></td>
      <td><?php echo (is_array($vo)?$vo["GGXH"]:$vo->GGXH); ?></td>
      <td><?php echo (is_array($vo)?$vo["DJ"]:$vo->DJ); ?></td>
      <td><?php echo (is_array($vo)?$vo["JZDW"]:$vo->JZDW); ?></td>
      <td><?php echo (is_array($vo)?$vo["KSSYRQ"]:$vo->KSSYRQ); ?></td>
      <td><?php echo (getDeptname(is_array($vo)?$vo["SYBM_ID"]:$vo->SYBM_ID)); ?></td>
      <td><?php echo (getUsername(is_array($vo)?$vo["GLR_ID"]:$vo->GLR_ID)); ?></td>
      <td><?php echo (getSysl(is_array($vo)?$vo["DZYH_ID"]:$vo->DZYH_ID,$vo[SL])); ?></td>
      <td>
		<a href="/index.php/Dzyh/ly/DZYH_ID/<?php echo (is_array($vo)?$vo["DZYH_ID"]:$vo->DZYH_ID); ?>/SYSL/<?php echo (getSyslNum(is_array($vo)?$vo["DZYH_ID"]:$vo->DZYH_ID,$vo[SL])); ?>"> 领用</a>&nbsp;
        <a href="javascript:detail('<?php echo (is_array($vo)?$vo["DZYH_ID"]:$vo->DZYH_ID); ?>');"> 已领用 </a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
  </tbody>
 <tfoot> 
 <tr><th colspan="12"><?php echo ($page); ?>
 </th>
 </tr>
 </tfoot>
</table>  
</div>
</body>
</html>