/**
 * @author Jay
 * 符合标准的iframe调用
 * 调用方法：
 * <script type="text/javascript">
 * 	   // <![CDATA[
 * 	   iframe('con1.html','100%','','icontent');
 * 	   // ]]>
 * </script>
 * 
 * url:目标url
 * w：宽度
 * h：高度
 * target:iframe的name值
 */

function iframe(url, w, h, iframeID) { 
	document.write('<iframe id="' + iframeID + '" name="' + iframeID + '" allowtransparency="true" align="top" marginwidth="0" marginheight="0" width="' + w + '" height="' + h + '" border="0" frameborder="0" scrolling="yes" src="' + url + '" onload="" style="overflow-x:hidden"></iframe>');
}

