    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <script>
        function my_noteaff(AFF_ID) {
            my_left = document.body.scrollLeft + event.clientX - event.offsetX - 50;
            my_top = document.body.scrollTop + event.clientY - event.offsetY + 150;

            window.open("/index.php/Calendar/affairnote/AFF_ID/" + AFF_ID, "note_win" + AFF_ID, "height=170,width=180,status=0,toolbar=no,menubar=no,location=no,scrollbars=auto,top=" + my_top + ",left=" + my_left + ",resizable=no");
        }

    </script>
				<!--<ul class="ul1 ul3">
				<?php if(is_array($calendarlist)): ?><?php $i = 0;?><?php $__LIST__ = $calendarlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><?php echo (getHis(is_array($vo)?$vo["END_TIME"]:$vo->END_TIME)); ?>&nbsp;<a href="#" onclick='javascript:my_notecal("<?php echo (is_array($vo)?$vo["CAL_ID"]:$vo->CAL_ID); ?>");'><?php echo (csubstr(is_array($vo)?$vo["CONTENT"]:$vo->CONTENT,0,27)); ?></a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				</ul>-->
				<ul class="ul1 ul3">
				<?php if(is_array($affairlist)): ?><?php $i = 0;?><?php $__LIST__ = $affairlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><?php echo (is_array($vo)?$vo["REMIND_TIME"]:$vo->REMIND_TIME); ?>&nbsp;<a href="#" onclick='javascript:my_noteaff("<?php echo (is_array($vo)?$vo["AFF_ID"]:$vo->AFF_ID); ?>");'><?php echo (csubstr(is_array($vo)?$vo["CONTENT"]:$vo->CONTENT,0,27)); ?></a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
				</ul>