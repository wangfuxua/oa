//selectuser
function oa$() {
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

var ci, _hid, pID, pName, winbox, mask;
_hid        = oa$("hiddenName");      // 隐藏表单，存储用来放置用户name和id的表单前缀
//mask        = oa$("mask");          // 遮罩层
winbox      = oa$("massage_box");     // 弹出窗口

//mask = window.parent.document.getElementById("mask");

function oWin(oo) {
    _hid.value  = oo;
    //mask.style.visibility = 'visible';
    winbox.style.visibility = 'visible';
}

function ch(n, m) {
    pName   =   oa$(_hid.value + "_Name");    // 存储用户name的表单
    pID     =   oa$(_hid.value + "_ID");      // 存储用户id的表单

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

function chclear(sr) {
    if (sr != "") {
        pID             =   oa$(sr + "_ID");
        pName           =   oa$(sr + "_Name");
        pID.value       =   "";
        pName.value     =   "";
    } else {
        if (_hid != "") {
            pID         =   oa$(_hid.value + "_ID");
            pName       =   oa$(_hid.value+"_Name");
            pID.value   =   "";
            pName       =   "";
        }
    }
}

function senddata() {
    winbox.style.visibility = 'hidden';
    //mask.style.visibility = 'hidden';
}
