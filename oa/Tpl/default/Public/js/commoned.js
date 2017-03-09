// JavaScript Document
//sdsd
var list=document.getElementById("active").getElementsByTagName("span");
for(var i=0; i<list.length; i++){
list[i].onmouseover=function (){
 this.style.background="#fff";
 this.style.border='1px solid #f9f9f9';
 this.style.padding="0px 5px"
}
list[i].onmouseout=function (){
 this.style.background="";
 this.style.border='';
 this.style.padding="1px 6px"
}
}
//ddddddddddddddddddddddddddddddddddddddddddddddddd
var n;
var endnums;//接受一个参数
function addNums(){
endnums=parseInt(document.getElementById("ce").innerHTML);
n=parseInt(document.getElementById("cb").innerHTML);

if(n==endnums){
clearInterval(h);
}
n+=1;
document.getElementById("cb").innerHTML=n;
$('#percent').html(endnums+"%");
$("#proportion").width(n+'px');
}
h=setInterval("addNums()",50)



////////////////////////////////////////////////////////////////////////////////////////顶部选项卡
/*
function getId(id){return document.getElementById(id)}
var boxs=[getId('ger-box'),getId('pub-box')];
var fileTypes=document.getElementById("tags").getElementsByTagName("li");
for(var i=0; i<fileTypes.length; i++){
	(function(i){
			fileTypes[i].onclick=function(){
				  for(var j=0; j<fileTypes.length; j++){
					  boxs[j].style.display='none';
					  fileTypes[j].className='';
					  }
				      fileTypes[i].className='current';
				       boxs[i].style.display='';
				  }
			  })(i)
	}
	*/
//////////////////////////////////////////////////////////////////////////////////添加隐藏操作菜单
function addDown(){
var dh=$(".fuj");
	for(var i=0; i<dh.length; i++){
         var  pw=$(dh[i]).width();
$(dh[i]).append("<span><a href="+"'"+"url"+"'"+">下载</a></span>");//设置参数url
$(dh[i]).find('span').css({display:'none',left:pw,width:"40px",padding:"2px 5px"});
$(dh[i]).hover(function(){$(this).find('span').show("fast")},function(){$(this).find('span').hide("fast")});
}
}
addDown();
///////////////////////////////////////////////////////dfasdfasdf/////////////////////////////////////
///////////////////////////////////////////////////////dfasdfasdf/////////////////////////////////////

var moveFile=new Array();;//转发文件
var a=$("#ger-centens").html();//储存容器原始值
var copy=new Array();//拷贝、剪切、粘贴


//删除全部
$("#deleteAll").click(function(){
if(confirm("您确定要删除全部文件（永久性不可恢复）！")==1){
if(document.all){$("#tb-date").find('tbody').remove();}
else{$("#tb-date").find('tr').remove();}

$("#page-Num").html("")
a=$("#ger-centens").html();
}
else return
})
//addRows
function addRows(name,url,days){
var fName=name;
var fujian=url;
if(url==""){var fujian="无";}
if(name==""){var fName="无";}

var tds="<tr><td align='center'><input type='checkbox' name='' /></td><td>"+fName+"</td><td><span class='fuj'>"+url+"</span></td>"
          +"<td align='center'>"+days+"</td>"
          +"<td align='center'>编辑</td</tr>";

		  if(document.all){

			  $($("#tb-date").find("tbody:last").append(tds));
			  }
		  else{

			  $($("#tb-date").find("tbody:last-child").append(tds));
			  }

addDown();
}
//选取或释放全部选项

//拷贝操作
function copyCtn(){
  var gh=$("#tb-date").find("input");
	for(var j=0; j<gh.length; j++){
			if(gh[j].checked==true){
		      var py=gh[j].parentNode;
		       if(py.tagName!='tr')
			      py=py.parentNode;
				  if(copy==$(py).html()){return }//对比当前2次拷贝内容是否为同一份 文件名对比
				  else{
			      copy.push($(py).html());
				  }

			 }

		  }

}
/**
function Glue(){
 var gh=$("#tb-date").find("input");
 	  for(var k=0;k<gh.length; k++){
					var pk=gh[k].parentNode;
		                if(pk.tagName!='tr')
			               pk=pk.parentNode;
					     if($($(pk).html()).text()==$(copy[k]).text()){

							 alert("复制的内容已存在！");
							 return copy=[];
							 }
						else{
for(var i=0; i<copy.length; i++){
	var newTr=document.createElement('tr');
	 $(newTr).append(copy[i]);
	  $(newTr).appendTo($("#tb-date").find("tbody:last-child"));

	  }
	 return copy=[];//每个复制动作只能执行一次粘贴


								 }

					  }

	}
**/
//粘贴操作
function Glue(){
 var gh=$("#tb-date").find("input");
for(var i=0; i<copy.length; i++){
	var newTr=document.createElement('tr');
	 $(newTr).append(copy[i]);
	  $(newTr).appendTo($("#tb-date").find("tbody:last"));//IE
	  }
	  a=$("#ger-centens").html();
	 copy=[];//每个复制动作只能执行一次粘贴
	}
//剪切操作
function cut(){

 var gh=$("#tb-date").find("input");
 //内容拷贝
	for(var j=0; j<gh.length; j++){
			if(gh[j].checked==true){
		      var py=gh[j].parentNode;
		       if(py.tagName!='tr')
			      py=py.parentNode;
				  if(copy==$(py).html()){return }//对比当前2次拷贝内容是否为同一份
				  else{
			      copy.push($(py).html());
				  }

			 }

		  }
//移除剪切内容
for(var i=0; i<gh.length; i++){
   if(gh[i].checked==true){
		for(var j=0; j<gh.length; j++){
			if(gh[j].checked==true){
		      var py=gh[j].parentNode;
		     if(py.tagName!='tr')
			    py=py.parentNode;
			   $(py).remove();
			 }
			 a=$("#ger-centens").html();
		  }
		  return
	    }
	  }
	if(i<1){
	alert("文件为空");
	return
	}
	alert("请选择你要剪切的文件")
	}

function moveToFolder(){
	 var gh=$("#tb-date").find("input");
	for(var j=0; j<gh.length; j++){
			if(gh[j].checked==true){
		      var py=gh[j].parentNode;
		       if(py.tagName!='tr')
			      py=py.parentNode;
				  if(moveFile==$(py).html() && moveFile.length!=0){return alert("请选择目标文件") }//对比当前2次拷贝内容是否为同一份
				  else{
			      moveFile.push($(py).html());
				  }
				  alert(moveFile)
				 return
			 }

			 		if(i<1){
	alert("文件为空");
	return
		  }
	    }
	alert("您至少要选择一个文件")

 }
	function newNamed(name){
	$(name).appendTo($("#title h2"));
	}