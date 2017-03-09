// JavaScript Document
//顶部工具条按钮样式

var sp=document.getElementById("active").getElementsByTagName("span");
for(var i=0; i<sp.length; i++){
sp[i].onmouseover=function(){
this.className="down";
}
sp[i].onmouseout=function(){
this.className="";
   }
  }

//删除邮件

$("#mail-del").click(function(){
var gh=$("#mail-list").find("input");
var dlstr="";
for(var i=0; i<gh.length; i++){
   if(gh[i].checked==true){
	   if(confirm("您确定要删所选文件吗？")==1){
		for(var j=0; j<gh.length; j++){
			if(gh[j].checked==true){
		      var py=gh[j].parentNode;
		     if(py.tagName!='tr')
			    py=py.parentNode;				
			    $(py).remove();
			    dlstr += gh[j].value + ','  	
			 }
		  }
		        //alert(dlstr);
		         
		  		$.ajax({
			   	type:'post',
			   	url:'/index.php/Email/delete/DELETE_STR/'+dlstr 
			   })
	   }
		  return 
	    }
	  }
	if(i<1){
	alert("文件为空");
	return
	}
	alert("请选择你要删除的文件")
})


//表格hover效果

var trs=document.getElementById("mail-date").getElementsByTagName("tr");
	  for(var i=0; i<trs.length; i++){
	    trs[i].onmouseover=function(){			
		    this.style.background="#fef8de";
			
		}
	    trs[i].onmouseout=function(){		
		    this.style.background="";
			
		}	  
	  }
//邮件选项判断

var _cleckList=document.getElementById('mail-list').getElementsByTagName('input');
for(var i=0; i<_cleckList.length; i++){
	  _cleckList[i].onclick=function(){
		   for(var j=0; j<_cleckList.length; j++){			   
			   if(_cleckList[j].checked!=true){				   
	                 document.getElementById('topcheck').checked=false;
	                  document.getElementById('btmcheck').checked=false;
					  return;
				   }			   
			  else{
				  for(var k=0; k<_cleckList.length; k++){				  
				   if(_cleckList[k].checked==true){
				    document.getElementById('topcheck').checked=true;
	                  document.getElementById('btmcheck').checked=true;
					  }
					  else{document.getElementById('topcheck').checked=false;
	                       document.getElementById('btmcheck').checked=false;
						   }
					  					  
				      }
				     }
				    }
				   }
				  }
			
		
function checkAll(id,tag,top,tbm){
var _cleckList=document.getElementById('mail-list').getElementsByTagName('input');
if(this.checked==true){	
reAll()	;
	this.checked=false;
	document.getElementById('topcheck').checked=false;
	document.getElementById('btmcheck').checked=false;
		}	
	else{
	
		disAll();
		this.checked=true;
		document.getElementById('topcheck').checked=true;
	document.getElementById('btmcheck').checked=true;
		}
function disAll(){	
   for(var i=0; i<_cleckList.length; i++){
         _cleckList[i].checked="checked";
		 var curt=_cleckList[i].parentNode;
		      if(curt.tagName!='tr'){
			      curt=curt.parentNode;
				  $(curt).css('background','#fef8de');
               }
           }
		}
function reAll(){
	this.checked=false;
		for(var i=0; i<_cleckList.length; i++){	
_cleckList[i].checked="";
 var curt=_cleckList[i].parentNode;
		      if(curt.tagName!='tr'){
			      curt=curt.parentNode;
				  $(curt).css('background','none');
    }
   }				
  }
}
/*
//列表已删除
$(".til").click(function(){
$("#mail-act").html('');
$("#mail-act").load("Mailcentents.html");
})
*/