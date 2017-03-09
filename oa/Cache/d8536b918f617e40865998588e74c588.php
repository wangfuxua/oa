<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Jay" />
<title>工资上报管理</title>
<link href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js"></script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "工资上报管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "工资项目定义", Url: "/index.php/salary/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "工资上报流程管理", Url: "/index.php/salary/flowIndex", Cls: "", IconCls: "ico ico-list2" },
            { Title: "工资报表", Url: "/index.php/salary/flowIndex", Cls: "", IconCls: "ico ico-view" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });

</script>
</head>
<body>

	<div class="KDStyle" id="main">
		<table class="layout_auto">
			<col width="50px" />
			<col width="60px" />
			<col width="60px" />
			<thead>
				<tr>
					<th colspan="21" >工资报表 </th>
				</tr>

			</thead>
			<tbody>
				<tr class="tcenter">
					<th>部门</th><th>姓名</th><th>职务</th><?php if(is_array($salItemList)): ?><?php $i = 0;?><?php $__LIST__ = $salItemList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><th><?php echo (is_array($vo)?$vo["ITEM_NAME"]:$vo->ITEM_NAME); ?></th><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				</tr>
			    <?php if(is_array($userModel)): ?><?php $i = 0;?><?php $__LIST__ = $userModel?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
						<td><?php echo (is_array($vo)?$vo["DEPT_NAME"]:$vo->DEPT_NAME); ?></td><td><?php echo (is_array($vo)?$vo["USER_NAME"]:$vo->USER_NAME); ?></td><td><?php echo (is_array($vo)?$vo["PRIV_NAME"]:$vo->PRIV_NAME); ?></td>
						<?php if(is_array($vo[sub])): ?><?php $i = 0;?><?php $__LIST__ = $vo[sub]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vodata): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><td>
						<?php echo ($vodata); ?>
						</td><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
					</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				
				<tr>
					<th colspan="3">合计</th>
					<?php if(is_array($listsum)): ?><?php $i = 0;?><?php $__LIST__ = $listsum?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vosum): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><th><?php echo ($vosum); ?></th><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				</tr>
			</tfoot>
		</table>


</div>
</body>
</html>