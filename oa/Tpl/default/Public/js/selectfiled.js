function $() {
    var elements = new Array();
    for (var i = 0; i < arguments.length; i++) {
        var element = arguments[i];
        if (typeof element == 'string')
            element = document.getElementById(element);
        if (arguments.length == 1)
            return element;
        elements.push(element);
    }
    return elements;
}

var ci, _hid, pID, pName, winbox2, mask;
_hid        = $("hiddenName2");      // 隐藏表单，存储用来放置用户name和id的表单前缀
//mask        = $("mask");          // 遮罩层
winbox2      = $("massage_filed");     // 弹出窗口

//mask = window.parent.document.getElementById("mask");

function oWin2(oo) {
    _hid.value  = oo;
    //mask.style.visibility = 'visible';
    winbox2.style.visibility = 'visible';
}

function ch2(n, m) {
    pName   =   $(_hid.value + "_Name");    // 存储用户name的表单
    pID     =   $(_hid.value + "_ID");      // 存储用户id的表单

    if (pName.value.indexOf("," + n + ",") < 0 && pName.value.indexOf(n + ",") != 0) {
        pName.value += n + ",";
    } else {
        pName.value = pName.value.replace(n + ",", "");
    }

    if (pID.value.indexOf("," + m + ",") < 0 && pID.value.indexOf(m + ",") != 0) {
        pID.value += m + ",";
    } else {
        pID.value = pID.value.replace(m + ",", "");
    }
    
    
}

function chclear2(sr) {
    if (sr != "") {
        pID             =   $(sr + "_ID");
        pName           =   $(sr + "_Name");
        pID.value       =   "";
        pName.value     =   "";
    } else {
        if (_hid != "") {
            pID         =   $(_hid.value + "_ID");
            pName       =   $(_hid.value+"_Name");
            pID.value   =   "";
            pName       =   "";
        }
    }
}

function senddata2() {
    winbox2.style.visibility = 'hidden';
    //mask.style.visibility = 'hidden';
}