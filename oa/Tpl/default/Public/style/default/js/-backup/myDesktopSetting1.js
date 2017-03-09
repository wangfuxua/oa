//var columnData = [[["news", "公司新闻"], ["email", "内部邮件"]], [["Calendar", "日程安排"], ["notify", "公告通知"]], [["flow", "代办事宜"]]];
var colValue = {
    id: "",
    style: "",
    desktop: [[["news", "公司新闻"], ["email", "内部邮件"]], [["Calendar", "日程安排"], ["notify", "公告通知"]], [["flow", "代办事宜"]]]
};

var sv = [];
$.post("/index.php/Mytable/getSet", "", function(d) {
    setCookie("oaDesk", d, null, null, null, false);
}, "string");


var aa = getCookie("oaDesk") || "|/news,公司新闻/email,内部邮件|/Calendar,日程安排/notify,公告通知|/flow,代办事宜/url,常用网址";
var cc = aa.split("|");

for (var i = 1; i < cc.length; i++) {
    var tv = [];
    var dd = cc[i].split("/");
    for (var j = 1; j < dd.length; j++) {
        var cv = [];
        var ee = dd[j].split(",");
        for (var k = 0; k < ee.length; k++) {
            cv.push(ee[k]);
        }
        tv.push(dd[j]);
    }
    sv.push(tv);
}

if (sv != "") {
    var deskSetting = sv;  
} else {
    var deskSetting = colValue.desktop;
}

var container;
var columns = [];
$(document).ready(function() {
    var layerTit = $(".layer-tit");

    // 自定义按钮的点击事件
    $("#custombtn").click(function() {
        customToggle();
    })
    // 点击自定义box中按钮打开funbar对应模块
    $("#custombox li").each(function(i) {
        $(this).find("a").click(function() {
            funbarShow(i);
        });
    }); ;
    $("#funbar li a").each(function(i) {
        $(this).click(function() {
            funShow(i);
        })

    });
    // 点击保存按钮执行的函数
    $("#funbtn :button[name='funbtn-save']").click(function() {
        customSave();
        funboxHide();
    });
    $("#funbtn :button[name='funbtn-cancel']").click(function() {
        customCancel();
    });

    // 鼠标点击其他地方执行的函数
    $("body").contents().click(function() {
        var a = $(this).attr("id");
        // 自定义菜单部分
        if (a != "titbar" && a != "custombox" && a != "custombtn") {
            customHide();
        }
        // 模块设置部分
        if (a != "funbar" && a != "funbox") {
            funboxHide();
        }
    });

    // 鼠标移入box头部，显示功能按钮：折叠、关闭
    /*
    layerTit.mousemove(function() {
        $(this).parent().find(".layer-btn").show();
    }).mouseout(function() {
        var btn = $(this).parent().find(".layer-btn");
        btn.mouseover(function() {
            $(this).show()
        }).mouseout(function() {
            $(this).hide();
        });
    });
    */
    
    // 关闭box


    // 设置column高度
    columnHeight();

    // 设置布局:点击funbox-layout对应的选项
    /*
    $(".funbox-layout").find("li").each(function(i) {
        $(this).click(function() {
            layout(i + 1);
        });
    });
    */

    // 初始化设置布局的checkbox与显示的栏目相对应
    $(".funbox-module").find("input[type='checkbox']").each(function() {
        var box = $("#column-" + $(this).val());
        if (box.css("display") == "none") {
            $(this).attr("checked", "")
        } else {
            $(this).attr("checked", "checked");
        }
    });
    // 检测 布局设置input的点击事件
    $(".funbox-module").find("input").click(function() {
        var type = $(this).attr("name");
        module(type);
    });

    ///// 读取桌面配置并加载 /////

    for (var i = 0; i < deskSetting.length; i++) {
        var col = deskSetting[i];
        var w = (100 / deskSetting.length) - 2; ;
        $("#columns").append('<div class="column" id="column' + i + '" style="width:' + w + '%"></div>');
        for (var j = 0; j < col.length; j++) {
            var colbox = col[j];
            var item = colbox.split(",");
            creatItem(item[0], "column" + i, item[1]);
        }
    }

    ///// 加载内容 /////
    loadCon("column-news", "news");
    loadCon("column-email", "email");
    loadCon("column-Calendar", "Calendar");
    loadCon("column-notify", "notify");
    loadCon("column-flow", "flow");
    loadCon("column-url", "url");

    // 初始化Drag的布局
    setTimeout("initDrag()", 100);

    // 初始化模块与增减模块表单相对应
    checkModule();
});

$(window).resize(function() {
    columnHeight();
});

$(window).unload(function() {
    delete Drag;
});
// 设置colum容器高度
function columnHeight() {
    var h = $(window).height() - 92;
    $("#columns .column").height(h);
}

// 展开“自定义”菜单  show custombox
function customToggle() {
    var btn = $("#custombtn a");
    var box = $("#custombox");

    btn.toggleClass("on");
    box.slideToggle("fast");
}
function customHide() {
    var btn = $("#custombtn a");
    var box = $("#custombox");
    btn.removeClass("on");
    box.hide();
}

// 展开“funbar”对应的box show funbox

// 展开funbar之后加载对应的box
function funbarShow(num) {
    var bar = $("#funbar");
    bar.slideDown("fast", function(i) {
        funShow(num);
    });
}

// 展开对应的模块设置：funbar / funbox, 传入num（所对应的eq值）
function funShow(num) {
    var btn = $("#funbar li a");
    var box = $("#funbox .funbox");
    // 按钮样式更换
    btn.removeClass("on").eq(num).addClass("on");
    // 展开box
    box.hide().eq(num).slideToggle("normal");
}
// 隐藏 模块设置
function funboxHide() {
    $("#funbar li a").removeClass("on");
    $("#funbox .funbox").hide();
}

// 初始化增减模块与实际模块对应
function checkModule() {
    var setBtn = $(".funbox-module").find("input");
    setBtn.each(function() {
        var v = $("#column-" + $(this).attr("name"));
        if (v.length > 0) {
            $(this).attr("checked", "checked");
        } else {
            $(this).attr("checked", "");
        }
    })
}
// 保存处理桌面数据     savelayout
function customSave() {
    var funbar = $("#funbar");
    funbar.slideUp("fast");

    //------------------------------------

    var col = $("#columns .column:visible");
    var str = "";
    col.each(function(i) {
        var items = $(this).find(".item:visible");
        str += "|";
        items.each(function(j) {
            var ID = $(this).attr("id").replace("column-", "");
            var tit = $(this).find("h3").text();

            str += "/" + ID + "," + tit;
        });
    });

    setCookie("oaDesk", str, null, null, null, false);
    $.post("/index.php/Mytable/saveSet", { jsonstr: str }, function(d) {});
}
function customCancel() {
    var funbar = $("#funbar");
    funbar.slideUp("fast");
    funboxHide();
    location.reload();
}
// 显示box头部的功能菜单        showhead

// 关闭box     close
function layerboxClose(type) {
    var checkbox = $(".funbox-module").find("input[name='" + type + "']");
    checkbox.attr("checked", "");
    module(type);
}

// 折叠box     fold
function layerboxToggle(type) {
    var id = "column-" + type;

    $("#" + id).find(".layer-body").slideToggle("fast");
}

/* 布局 layout //////////////////////////////////// */
function layout(n) {
//debugger
    var c = $("#columns").find(".column");
    var w = (100 / n) - 2;
    var v = n - c.length;

    c.hide();
    // 判断栏数与现在的区别：>则追加，< 则删除多余的，并将删除的内容复制到调整后的最后一列中
    if (v>0) {
        for (var i = c.length; i < n; i++) {
            $("#columns").append("<div class='column' id='column" + (i + 1) + "' style='width:" + w + "%'></div>");
        }
    } else if (v==0) {
    } else {
        c.slice(n).find(".item").appendTo(c.eq(n - 1));
        c.slice(n).remove();
    }
    c.show();
    columnHeight();
    columnWidth();

    // 更新Drag布局结构
    initDrag();

}
function columnWidth() {

    var __b = $("#columns").find(".column");
    var n = __b.length;
    var w = (100 / n) - 2;
    __b.width(w + "%");
    var o = __b.width();     // 返回实际宽度，!= css("width")
    return o;
}

//拖拽

//------------------------Utility------------------------
function findPosX(obj) {  
    var curleft = 0;
    if (obj && obj.offsetParent) {
        while (obj.offsetParent) {
            curleft += obj.offsetLeft;
            obj = obj.offsetParent;
        }
    } else if (obj && obj.x) curleft += obj.x;
    return curleft; 
}

function findPosY(obj) {  
    var curtop = 0;
    if (obj && obj.offsetParent) {
        while (obj.offsetParent) {
            curtop += obj.offsetTop;
            obj = obj.offsetParent;
        }
    } else if (obj && obj.y) curtop += obj.y;
    return curtop; 
}


var dragGhost = document.createElement("div");
dragGhost.style.border = "dashed 1px #CCCCCC";
dragGhost.style.background = "white";
dragGhost.style.display = "none";
dragGhost.style.margin = "0 0 10px 0";

var Drag = {
    "obj": null,
    "init": function(handle, dragBody, e) {
        if (e == null) {
            handle.onmousedown = Drag.start;
        }
        handle.root = dragBody;

        if (isNaN(parseInt(handle.root.style.left))) handle.root.style.left = "0px";
        if (isNaN(parseInt(handle.root.style.top))) handle.root.style.top = "0px";
        handle.root.onDragStart = new Function();
        handle.root.onDragEnd = new Function();
        handle.root.onDrag = new Function();
        if (e != null) {
            var handle = Drag.obj = handle;
            e = Drag.fixe(e);
            var top = parseInt(handle.root.style.top);
            var left = parseInt(handle.root.style.left);
            handle.root.onDragStart(left, top, e.pageX, e.pageY);
            handle.lastMouseX = e.pageX;
            handle.lastMouseY = e.pageY;
            document.onmousemove = Drag.drag;
            document.onmouseup = Drag.end;
        }
    },
    "start": function(e) {
        var handle = Drag.obj = this;
        e = Drag.fixEvent(e);
        var top = parseInt(handle.root.style.top);
        var left = parseInt(handle.root.style.left);
        handle.root.onDragStart(left, top, e.pageX, e.pageY);
        handle.lastMouseX = e.pageX;
        handle.lastMouseY = e.pageY;
        document.onmousemove = Drag.drag;
        document.onmouseup = Drag.end;
        return false;
    },
    "drag": function(e) {
        e = Drag.fixEvent(e);
        var handle = Drag.obj;
        var mouseY = e.pageY;
        var mouseX = e.pageX;
        var top = parseInt(handle.root.style.top);
        var left = parseInt(handle.root.style.left);

        var currentLeft, currentTop;
        currentLeft = left + mouseX - handle.lastMouseX;
        currentTop = top + (mouseY - handle.lastMouseY);
        handle.root.style.left = currentLeft + "px";
        handle.root.style.top = currentTop + "px";
        handle.lastMouseX = mouseX;
        handle.lastMouseY = mouseY;

        handle.root.onDrag(currentLeft, currentTop, e.pageX, e.pageY);
        return false;
    },
    "end": function() {
        document.onmousemove = null;
        document.onmouseup = null;
        Drag.obj.root.onDragEnd(parseInt(Drag.obj.root.style.left), parseInt(Drag.obj.root.style.top));
        Drag.obj = null;
    },
    "fixEvent": function(e) {
        if (typeof e == "undefined") e = window.event;
        if (typeof e.layerX == "undefined") e.layerX = e.offsetX;
        if (typeof e.layerY == "undefined") e.layerY = e.offsetY;
        if (typeof e.pageX == "undefined") e.pageX = e.clientX + document.body.scrollLeft - document.body.clientLeft;
        if (typeof e.pageY == "undefined") e.pageY = e.clientY + document.body.scrollTop - document.body.clientTop;
        return e;
    }
};

//------------------------Start------------------------
// 初始化 Drag
function initDrag() {

    columns = [];
    var cols = $("#columns .column:visible"); 
    for (var i = 0; i < cols.length; i++) {
        if (cols[i].className == "column") { 
            columns.push(cols[i]);
        }
    }
    for (var i = 0; i < columns.length; i++) {
        var column = columns[i];
        for (var j = 0; j < column.childNodes.length; j++) {
            var item = column.childNodes[j];
            if (item.className == "item") {
                item.column = column; 

                new dragItem(item);
            }
        }
    }
}

var isIE = document.all;

//------------------------Item------------------------
function dragItem(item) {
    var handle;
    for (var i = 0; i < item.childNodes.length; i++) {
        if (item.childNodes[i].nodeName.toLowerCase() == "h3") {
            handle = item.childNodes[i];
            break;
        }
    }
    if (!handle) return;
    Drag.init(handle, item);
    item.onDragStart = function(left, top, mouseX, mouseY) {
        this.style.opacity = "0.5";
        this.style.filter = "alpha(opacity=50)";
        dragGhost.style.height = (isIE ? this.offsetHeight : this.offsetHeight - 2) + "px";
        //this指的是item

        this.style.width = this.offsetWidth + "px";
        this.style.left = (findPosX(this) - 5) + "px";
        this.style.top = (findPosY(this) - 5) + "px";
        this.style.position = "absolute";
        this.style.zIndex = "1001";

        dragGhost.style.display = "block";
        this.column.insertBefore(dragGhost, this);

        this.columnsX = [];
        for (var i = 0; i < columns.length; i++) {
            this.columnsX.push(findPosX(columns[i]));
        }

    }
    item.onDrag = function(left, top, mouseX, mouseY) {

        //先要判断在哪一列移动
        var columnIndex = 0;

        for (var i = 0; i < this.columnsX.length; i++) {
            if ((left + this.offsetWidth / 2) > this.columnsX[i]) {
                columnIndex = i;
            }
        }
        //如果columnIndex在循环中没有被赋值 则表示当前拖动对象在第一列的左边
        //此时也把它放到第一列

        var column = columns[columnIndex];

        if (this.column != column) {
            //之前拖动对象不在这个列

            column.appendChild(dragGhost);
            this.column = column;
        }

        //然后在判断放在这一列的什么位置

        var currentNode = null;
        for (var i = 0; i < this.column.childNodes.length; i++) {
            if (this.column.childNodes[i].className == "item"
			&& this.column.childNodes[i] != this    
			&& top < findPosY(this.column.childNodes[i])) {   

                currentNode = this.column.childNodes[i];
                break;
            }
        }
        if (currentNode)
            this.column.insertBefore(dragGhost, currentNode);
        else   

            this.column.appendChild(dragGhost);
    }
    item.onDragEnd = function(left, top, mouseX, mouseY) {
        this.style.opacity = "1";
        this.style.filter = "alpha(opacity=100)";

        this.column.insertBefore(this, dragGhost);

        this.style.position = "static";
        this.style.display = "block";
        this.style.width = "auto";
        dragGhost.style.display = "none";
    }
}
/* 增减模块 //////////////////////////////// */
function module(type) {
    var box = $("#column-" + type);
    if (box.length > 0) {
        box.slideToggle("fast");
    } else {
        var col = $("#columns .column").eq(0).attr("id");
        var tit = $(".funbox-module").find("input[name='" + type + "']").parent().text();
        creatItem(type, col, tit);
        initDrag();
    }
}

/////// 加载内容
function loadCon(box, url) {
    var box = $("#" + box).find(".layer-body");
    //box.load(url);
    $.get(url, function(data) {
        box.html("");
        box.append(data);
    });
}

///// 添加 Item 并加载内容
function creatItem(id, box, title) {
    var Html = '<div class="item" id="column-' + id + '">'
                        + '         <h3 class="layer-tit"><span>' + title + '</span></h3>'
                        + '         <div class="layer-btn">'
                        + '                 <a href="javascript:" title="缩小" onclick="layerboxToggle(\'' + id + '\')"><b class="ico-btn ico-arrow-bottom"></b></a>'
                        + '                 <a href="javascript:" title="关闭" onclick="layerboxClose(\'' + id + '\')"><b class="ico-btn ico-close"></b></a><a href="javascript:" title="更多"></a>'
                        + '         </div>'
                        + '         <div class="layer-body"><span class="loading">Loading...</span></div>'
                        + '</div>';
    $("#" + box).append(Html);

    var url = "" + id;
    loadCon("column-" + id, url);
}

