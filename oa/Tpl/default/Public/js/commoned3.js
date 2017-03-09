// JavaScript Document

////////////////////////////////////////////////////////////////////////////////////////顶部选项卡
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
//////////////////////////////////////////////////////////////////////////////////添加隐藏操作菜单
