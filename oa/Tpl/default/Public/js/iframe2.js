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

// 解决高度自适应：IE6+、FF 均可
/*
function iframeResize() {
    var ifr = ["ifrMsgTips", "userOnline", "ifruser", "ifrQuick", "ifrMsg", "ifrFav", "icontent"];
    var ifrArray = new Array();
    for (i = 0; i < ifr.length; i++) {
        var ifrID = ifr[i];
        var ifrObj = document.getElementById(ifrID);
        //alert(ifrID);
        try {
            var aH = ifrObj.contentWindow.document.body.scrollHeight;
            var bH = ifrObj.contentWindow.document.documentElement.scrollHeight;
            var oH = Math.max(aH, bH);
            ifrObj.height = oH;
        }catch(ex){}
    }
}
*/
window.setInterval("reinitIframe()", 200);


function reinitIframe() {
    var iframe = document.getElementById("icontent");
    try {
        var bHeight = iframe.contentWindow.document.body.scrollHeight;
        var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
        var height = Math.max(bHeight, dHeight);

        iframe.height = height;
    } catch (ex) { }
}

