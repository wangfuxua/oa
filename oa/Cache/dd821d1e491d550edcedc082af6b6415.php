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
function delete_gdzc(BGSB_ID, typeid)
{
 msg='确认要删除该办公设备么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Bgsb/delete/BGSB_ID/"+BGSB_ID+"/typeid/"+typeid;
  window.location=URL;
 }
}
</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "办公设备管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "办公设备列表", Url: "/index.php/Bgsb/manage/typeid/<?php echo ($typeid); ?>", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加办公设备", Url: "/index.php/Bgsb/form/typeid/<?php echo ($typeid); ?>", Cls: "", IconCls: "ico ico-add" },
            { Title: "办公设备导入", Url: "/index.php/Bgsb/bgsbimport/typeid/<?php echo ($typeid); ?>", Cls: "", IconCls: "ico ico-view" }
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
<col width="" />
<col width="60px" />
<col width="110px" />
<col />
<col width="80px" />
<col width="80px" />
<col width="60px" />
<col width="80px"	 />
<caption class="nostyle"><?php echo ($rowtype[typename]); ?></caption>
  <thead>
      <tr>
      <th>办公设备名称</th>
      <th>数量</th>
      <th>登计日期</th>
      <th>使用部门</th>
      <th>管理人</th>
      <th>资产录入员</th>
      <th>审核</th>
      <th>操作</th>
      </tr>
  </thead>
  
  <tbody class="tcenter">
  <?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["MC"]:$vo->MC); ?></td>
      <td><?php echo (is_array($vo)?$vo["SL"]:$vo->SL); ?></td>
      <td><?php echo (is_array($vo)?$vo["DJSJ"]:$vo->DJSJ); ?></td>
      <td><?php if($vo[SYBM_ID] != '0'): ?><?php echo (getDeptname(is_array($vo)?$vo["SYBM_ID"]:$vo->SYBM_ID)); ?><?php else: ?><?php echo (is_array($vo)?$vo["SZD"]:$vo->SZD); ?><?php endif; ?></td>
      <td><?php if($vo[GLR_ID] != ''): ?><?php echo (getUsername(is_array($vo)?$vo["GLR_ID"]:$vo->GLR_ID)); ?><?php else: ?><?php echo (is_array($vo)?$vo["GLR_NAME"]:$vo->GLR_NAME); ?><?php endif; ?></td>
      <td><?php if($vo[ZCLRY_ID] != ''): ?><?php echo (getUsername(is_array($vo)?$vo["ZCLRY_ID"]:$vo->ZCLRY_ID)); ?><?php else: ?><?php echo (is_array($vo)?$vo["ZCLRY_NAME"]:$vo->ZCLRY_NAME); ?><?php endif; ?></td>
      <td>
      <?php if($vo[SH] == 2): ?>通过审核<?php elseif($vo[SH] == 1): ?>未通过审核<?php else: ?>待审核<?php endif; ?>
      </td>
      <td width="80">
		<a href="/index.php/Bgsb/form/BGSB_ID/<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>"> 修改</a>&nbsp;&nbsp;
        <a href="javascript:delete_gdzc(<?php echo (is_array($vo)?$vo["BGSB_ID"]:$vo->BGSB_ID); ?>, <?php echo ($typeid); ?>);"> 删除 </a>
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