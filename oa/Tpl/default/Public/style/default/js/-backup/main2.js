/* 设置宽度、高度 ////////////////////////////////// */

function setDomHeight(id, h) {
    var _H = $(window).height() - h;
    $("#" + id).height(_H);
}
function setDomWidth(id, w) {
    var _W = $(window).width() - w;
    $("#" + id).width(_W);
}

/* input 焦点函数 //////////////////////////////////
当名称为uName的文本框获得焦点时,将文本框内的提示文字去除。并当失去焦点时，判断如果用户没有输入，则继续显示提示文字
name: input表单的名称
val    : input表单默认提示的value值    
example: inputFocus("uName","请输入姓名...");
*/
function inputFocus(name, val) {
    $("input[@name='" + name + "']").focus(  //表示name为uName的input标签获得焦点
  function() { if (this.value == '') this.value = val; }).blur(  //表示失去焦点
  function() { if (this.value == '') this.value = val }
 );
}
/* Cookie ////////////////////////////////// */
function setCookie(sName, sValue, oExpires, sPath, sDomain, bSecure) {
    var sCookie = sName + "=" + encodeURIComponent(sValue);
    if (oExpires)
        sCookie += "; expires=" + oExpires.toGMTString();
    if (sPath)
        sCookie += "; path=" + sPath;
    if (sDomain)
        sCookie += "; domain=" + sDomain;
    if (bSecure)
        sCookie += "; secure";

    document.cookie = sCookie;
}

function getCookie(sName) {
    var sRE = "(?:; )?" + sName + "=([^;]*);?";
    var oRE = new RegExp(sRE);
    if (oRE.test(document.cookie))
        return decodeURIComponent(RegExp["$1"]);
    else
        return null;
}

/* 内页增加头部  ////////////////////////////////// 

Example:
   createHeader({
        Title: "工作流",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Help:"http://",
        Active:1,
        Toolbar:[
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls:"", Cls:"", Target:"_blank",TextHide:true }
        ],
        Items: [
            { Title: "待办工作", Url: "__URL__/index", Action: "", Type: "", Cls: "on",Icon:"clock,png", IconCls: "ico ico-list" },
            { title: "新建工作", url: "news", action: "", type: "", cls: "", iconCls: "ico ico-add" },
            { title: "工作监控", url: "workControl", action: "", type: "", cls: "", iconCls: "ico ico-list2" }
        ]
        });
*/

function createHeader(obj) {
    ///	<summary>
    ///		1: Title:"", Icon:"", IconCls:"", Cls:"", Active:0, Toolbar:{}, Items:{}. 
    ///		2: Items:{ Title: "", Url: "", Action: "", Type: "", Cls: "",Icon:"", IconCls: ""}.
    ///	</summary>
    ///	<param name="obj" type="Json">
    ///		1: json格式的配置选项。
    ///		2: Title -
    ///		3: Icon - 
    ///		4: IconCls -
    ///	</param>
    ///	<returns type="obj" />
    
    var _Html = '';
    _Html += '<div id="titbar"><h2><b class="' + obj.IconCls + '"></b>' + obj.Title + '</h2>'
             + addToolbar(obj.Toolbar)
             + '</div>'
             + addTabs(obj.Items, obj.Active);
             
    $("body").prepend(_Html);
}

// 添加顶部右侧的btn功能组
function addToolbar(items) {
    var _Html = '',_icon='',_Text='';
    if (items != "") {
        _Html += '<div class="toolbar"><ul>';
        $(items).each(function(i) {
            _icon = (items[i].Icon != "" && items[i].Icon != null) ? '<img src="' + items[i].Icon + '" width="" />' : '<b class="' + items[i].IconCls + '"></b>';
            _Text = (items[i].TextHide) ? "" : '<span>' + items[i].Title + '</span>';
            _Html += '<li class="' + items[i].Cls + '"><a href="' + items[i].Url + '" title="' + items[i].Title + '" target="'+items[i].Target+'">' + _icon + _Text + '</a></li>';
        });
        _Html += '</ul></div>';
        
    }
    return _Html;
}

// 添加tab标签
function addTabs(items,active) {
    var _Html = '', _act = '',_icon='';
    if (items != "" && items != null) {
        _Html += '<div id="tabbar">';
        if (typeof items == "string") {
            _Html += '<h3>' + items.Title + '</h3>';
        } else {
        _Html += '<ul>';
        $(items).each(function(i) {
            // 判断当前激活的标签
            _act = ((i + 1) == active) ? ' class="on"' : '';  
            // 判断ico是默认方式显示还是img方式显示
            _icon = (items[i].Icon != "" && items[i].Icon != null) ? '<img src="' + items[i].Icon + '" width="" />' : '<b class="' + items[i].IconCls + '"></b>';
            _Html += '<li class="' + items[i].Cls + '"><a href="' + items[i].Url + '" title="" ' + _act + '>'+_icon + items[i].Title + '</a></li>';
        });
            _Html += '</ul>';
        }
        _Html += '</div>';
    } else {
        _Html = '';
    }
    return _Html;
}

/* createHead End ////////////////////////////////// */