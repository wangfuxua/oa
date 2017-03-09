/**
* 基于jQuery的KDOA函数库
* @author Jay
* update: 2009-03-13
*
*/

/* input 焦点函数
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