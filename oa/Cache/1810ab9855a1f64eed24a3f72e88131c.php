    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
				<ul class="ul1 ul4">
			   <li class="li2">个人网址</li>
			   <?php if(is_array($perurl)): ?><?php $i = 0;?><?php $__LIST__ = $perurl?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><a href="<?php echo (is_array($vo)?$vo["URL"]:$vo->URL); ?>" target="_blank"><?php echo (is_array($vo)?$vo["URL_DESC"]:$vo->URL_DESC); ?></a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			   <li class="li2">公共网址</li>
			   <?php if(is_array($comurl)): ?><?php $i = 0;?><?php $__LIST__ = $comurl?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><a href="<?php echo (is_array($vo)?$vo["URL"]:$vo->URL); ?>" target="_blank"><?php echo (is_array($vo)?$vo["URL_DESC"]:$vo->URL_DESC); ?></a><br></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				</ul>