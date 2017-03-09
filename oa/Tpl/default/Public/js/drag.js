// JavaScript Document
(function($) {
    $.fn.draggable = function(opts) {
        var defaultSettings = {
            parent: document,                //父级容器
            target: this.parent(),            //拖拽时移动的对象
			movebg:$('#addDrug'),            //拖拽时的背景没参数直接这里设置
            onmove: function(e) {            //拖拽处理函数
                $(settings.target).css({
                    left: e.clientX - dx,
                    top: e.clientY - dy
                });
				 $(settings.movebg).css({
                    left: e.clientX - dx-8,
                    top: e.clientY - dy-8
                });
            },
            onfinish: function(){}
        };
        var settings = $.extend({}, defaultSettings, opts);
        var dx, dy, moveout;
        this.bind("selectstart", function(){return false;});

        this.mousedown(function(e) {
           var t = $(settings.target);
            dx = e.clientX - parseInt(t.css("left"));
            dy = e.clientY - parseInt(t.css("top"));
            $(settings.parent).mousemove(move).mouseout(out);
            $().mouseup(up);
        });
        function move(e) {
            moveout = false;
            settings.onmove(e);
        }
        function out(e) {
            moveout = true;
            setTimeout(function(){checkout(e);}, 100); 
        }
        function up(e) {
            $(settings.parent).unbind("mousemove", move).unbind("mouseout", out);
            $().unbind("mouseup", up);
            settings.onfinish(e);
        }
        function checkout(e) {
            moveout && up(e);
        }
    };
})(jQuery);