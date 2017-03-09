    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
				<ul class="ul1" id="c2">
       <?php if($newslist): ?><?php if(is_array($newslist)): ?><?php $i = 0;?><?php $__LIST__ = $newslist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><a href="/index.php/News/article/id/<?php echo (is_array($vo)?$vo["newId"]:$vo->newId); ?>" title=""><?php echo (is_array($vo)?$vo["title"]:$vo->title); ?></a>&nbsp;&nbsp;<?php echo (formatdate(is_array($vo)?$vo["createTime"]:$vo->createTime)); ?>&nbsp;&nbsp;</li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
       <?php else: ?>
       没有新闻<?php endif; ?>

				</ul>