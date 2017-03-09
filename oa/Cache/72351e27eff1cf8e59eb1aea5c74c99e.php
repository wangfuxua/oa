    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />

				<ul class="ul1 ul2" id="c3">
				<?php if($workRow): ?><?php if(is_array($workRow)): ?><?php $i = 0;?><?php $__LIST__ = $workRow?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><a href="/index.php/ZworkFlow/flowView/workId/<?php echo ($vo['zworkId']); ?>"><?php echo ($vo['zworkName']); ?></a>第<?php echo ($vo['state']); ?>步&nbsp;&nbsp;&nbsp;<a href="/index.php/ZworkFlow/execute/workId/<?php echo ($vo['zworkId']); ?>">快速办理</a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				<?php else: ?>
				没有待办工作<?php endif; ?>
				</ul>