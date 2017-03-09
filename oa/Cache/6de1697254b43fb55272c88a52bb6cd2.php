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
<body>
<div class="KDStyle" id="KDMain">
<table>
<col width="120px" />
   <tr>
    <th>办公设备名称：</th>
    <td><?php echo (is_array($row)?$row["MC"]:$row->MC); ?></td>
   </tr> 
   <tr>
    <th>编号：</th>
    <td><?php echo (is_array($row)?$row["BH"]:$row->BH); ?></td>
   </tr>
   <tr>
    <th>资产类别：</th>
    <td><?php echo (is_array($row)?$row["LB"]:$row->LB); ?></td>
   </tr>
   <tr>
    <th>数量：</th>
    <td><?php echo (is_array($row)?$row["SL"]:$row->SL); ?></td>
   </tr>
   <tr>
    <th>规格型号：</th>
    <td><?php echo (is_array($row)?$row["GGXH"]:$row->GGXH); ?></td>
   </tr>
   <tr>
    <th>单价：</th>
    <td><?php echo (is_array($row)?$row["DJ"]:$row->DJ); ?></td>
   </tr>
   <tr>
    <th>供货单位：</th>
    <td><?php echo (is_array($row)?$row["JZDW"]:$row->JZDW); ?></td>
   </tr>
   <tr>
    <th>开始使用日期：</th>
    <td><?php echo (is_array($row)?$row["KSSYRQ"]:$row->KSSYRQ); ?></td>
   </tr>
   <tr>
    <th>预计使用年限：</th>
    <td><?php echo (is_array($row)?$row["YJSYNX"]:$row->YJSYNX); ?></td>
   </tr>
   <tr>
    <th>使用部门：</th>
    <td><?php echo (getDeptname(is_array($row)?$row["SYBM_ID"]:$row->SYBM_ID)); ?></select>
    </td>
   </tr>
   <tr>
    <th>所在地：</th>
    <td><?php echo (is_array($row)?$row["SZD"]:$row->SZD); ?></td>
   </tr>
   <tr>
    <th>管理人：</th>
    <td>
    <?php if($row[GLR_ID] != ''): ?><?php echo (getUsername(is_array($row)?$row["GLR_ID"]:$row->GLR_ID)); ?><?php else: ?><?php echo (is_array($row)?$row["GLR_NAME"]:$row->GLR_NAME); ?><?php endif; ?>
    
    </td>
   </tr>
   <tr>
    <th>资产录入员：</th>
    <td>
        <?php if($row[ZCLRY_ID] != ''): ?><?php echo (getUsername(is_array($row)?$row["ZCLRY_ID"]:$row->ZCLRY_ID)); ?><?php else: ?><?php echo (is_array($row)?$row["ZCLRY_NAME"]:$row->ZCLRY_NAME); ?><?php endif; ?>
    </td>
   </tr>
   <tr>
    <th>审核：</th>
    <td>
    
        <?php if($row[SH] == 2): ?>通过审核<?php elseif($row[SH] == 1): ?>未通过审核<?php else: ?>待审核<?php endif; ?>
    </td>
   <tfoot> 
   </tr>
    <th colspan="2">
        <button type="button" value="关闭" class="btnFnt" onClick="javascript:window.close();">关闭</button>
    </th>
   </tr>
   </tfoot> 
   
</table>
</div>

</body>
</html>