// JavaScript Document
var setting={
	visbleinput:'',//显示发件人名称的表单id
	hideinput:'',	//显示收件人id的隐藏表单id
	newinner:false,//是否检测添加内容
	newinnerid:'',
	mbox:''
	}
var postarr=new Array();
var slec=false;//搜索输入框
var keycut=false;//键盘
slcTag("bumen");	
slcTag("juese");
slcTag("renyuan");
$('#qd').bind('click',function(){createArr('#'+setting.visbleinput,'#'+setting.hideinput)})//关联收件人输入框
$('#qx').click(function(){createArr('#'+setting.visbleinput,'#'+setting.hideinput)});
$('.closed').click(function(){createArr('#'+setting.visbleinput,'#'+setting.hideinput)})//关闭联系人
$('.tl span').click(clearList)//清空联系人
$('#go-sc').click(sous)//鼠标事件搜索
$('#sc-name').focus(function(){keycut=true});
$('#sc-name').blur(function(){keycut=false});
document.onkeydown=keyDown;					  
function setInput(hidid,visid,innerid,blean){ 
			 setPos()//弹出联系人
			  setting.hideinput=hidid;
			  setting.visbleinput=visid;
			  setting.newinnerid=innerid;
			  setting.newinner=blean;
			  clearList(); 
			  	if(setting.newinner){
				  newHtml();
				}  
			  checkSelected();
}
function newHtml(){// 
	var liclass=$('#'+setting.newinnerid).find('li');
	for(var i=0; i<liclass.length; i++){
		var v=$(liclass[i]).attr('class');
	    var t=v.replace(/[\/_](\d)+/,'');
		switch(t){
			case 'P':
			setting.mbox='juese'
			break;
			case 'D':
			setting.mbox='bumen'
			break;
			case 'U':
			setting.mbox='renyuan'
			break;
			}
			var newlist=document.createElement('li');
			$(newlist).addClass(v);
			$(newlist).html($(liclass[i]).html());
			
			$("."+setting.mbox).append(newlist); 
			checkLi();
	    }			
	}
function cutGroup(){
var oh=$('#waitselect').find('h4');
for(var k=0; k<oh.length; k++){
oh[k].onclick=function(){
	for(var j=0; j<oh.length;j++){
		oh[j].className=" ";
		$(oh[j]).next().css('display','none')
		}					
		$(this).next().css('display','block');
		$(this).addClass('over')								   
	}
  }
}
cutGroup();
function slect(){if(slec==false){$('#sc-name').val('');$('#sc-name').css('color','#666'); return slec=true}else{return}}
function keyDown(e){var e=e || window.event;if(e.keyCode ==13 && keycut==true){sous();}else{return}}//enter键操作
///////////////////////////
function createArr(visId,hidId){
	   $(hidId).val(postarr);//把id传入表单
		var myarr=new Array();
		var lis=$('#sld-list').find('li');
		for(var i=0; i<lis.length; i++){
			myarr.push($(lis[i]).html());								 
			$(visId).val(myarr);
		}	 
	   closedPop();	 
 }
function createArr2(){
		var myarr=new Array();
		var lis=$('#sld-list').find('li');
		for(var i=0; i<lis.length; i++){
			myarr.push($(lis[i]).html());								 
			$('#'+setting.visbleinput).val(myarr); 
		}
		$('#'+setting.hideinput).val(postarr);	
			 
}	 
//搜索   
function sous(){
	  var cert=$('#sc-name').val();
	  var targets=$('#renyuan').find('li');
      var lis=$('#sld-list').find('li');
	  var oh=$('#waitselect').find('h4');
	  if(cert==''){alert('请输入您要查找的人员名称'); return}
		  for(var i=0; i<targets.length; i++){
			   	  for(var o=0; o<lis.length; o++){
					  if($(lis[o]).html()==cert){						  
						  alert('您已经选择了：'+cert);
						  return						  
						  }		  		  
		               }
			   for(var j=0; j<targets.length; j++){
				$(targets[j]).css({color:'#000',fontWeight:'100'});
				}//清除动画停止前的样式
			   if($(targets[i]).css('color')=='red'){
				   $('#renyuan').stop(); 			  
				   }
			   if($(targets[i]).text()==cert){				  
					  for(var j=0; j<oh.length;j++){
						  oh[j].className=" ";
						  $(oh[j]).next().css('display','none')
						  }						 
					  $('#renyuan').css('display','block');
					 $(targets[i]).css({color:'red',fontWeight:'bold'})										
				     $('#renyuan').prev().addClass('over');
				    $('#renyuan').animate({scrollTop:19*i});					
				   return
			} 
		}
		alert('没有您查找的人员')
	 }
//清空已选
function clearList(){	
			var list=$('#waitselect').find('li');
			var oho=$('#waitselect').find('h5');
			var nlist=$('#sld-list').find('li');
							for(var k=0; k<list.length; k++){
								for(var i=0; i<nlist.length; i++){
								if($(list[k]).attr('id')==$(nlist[i]).attr('class')){
									$(list[k]).css({color:'#000',fontWeight:'100'})
									$(nlist[i]).remove();
									checkLi();
									}
								 } 
								} 
							 for(var m=0; m<oho.length; m++){
								 for(var i=0; i<nlist.length; i++){
									if($(oho[m]).attr('id')==$(nlist[i]).attr('class')){
										$(oho[m]).css({color:'#000',fontWeight:'100'})	
								   		$(nlist[i]).remove();
							   			checkLi();
									}	
									
				 				}								
							}
							$('#'+setting.visbleinput).val(''); 	
							$('#'+setting.hideinput).val(''); 	
			postarr=[];
}
function slcTag(id){
var tags=document.getElementById(id).getElementsByTagName('li');
var titles=document.getElementById(id).getElementsByTagName('h5');
var addTobox=function(tag){
	switch(id){
		case 'bumen':
		$(".bumen").append(tag);
		break;
		case 'juese':
		$(".juese").append(tag);
		break;
		case 'renyuan':		
		$(".renyuan").append(tag);
		break;	
	  }	
	}
for(var j=0; j<titles.length; j++){
	titles[j].onclick=function(){
		if(this.className=='down' || this.className=='up'){
		if(this.className=='down'){this.className='up';$(this).next().toggle();}
		else{this.className='down';$(this).next().toggle();}		
		}
	else{
		var newlist=document.createElement('li');
		var dls=$('#sld-list').find('li');
				for(var d=0; d<dls.length; d++){
					 if($(dls[d]).html()==$(this).html()){						
						       alert("您已经选择了:"+$(this).html());
						        return;
						          }					
				}
		         newlist.innerHTML=$(this).html();		  
		         $(this).css({color:'#ccc',fontWeight:'100'});			
		         var psid=$(this).attr('id');//获取当前标签id
				 $(newlist).addClass(psid);//将id名做为class给新建list
				 postarr.push(psid);//将id添加到数组
				 addTobox(newlist);//分类添加到右侧
	             checkLi();	//重新检测右侧列表
	             newArr();
	             createArr2();
				 $('#FLD_STR').val(postarr);									
	      }
		}	
	}

//子孙级li
for(var i=0; i<tags.length; i++){
 tags[i].onmouseover=function(){
		if(this.className==''){
			this.className='overli';						
			}else{return}		
		}
tags[i].onmouseout=function(){
		if(this.className=='overli'){
			this.className='';			
			}else{return}				
		}
	
tags[i].onclick=function(e){	
	var e=e || window.event;	
	var newlist=document.createElement('li');
	var parentclass=$(this).parent().parent().attr("class");
    if(this.className=='subdown'){	
	     if(document.all){e.cancelBubble=true; }else{e.stopPropagation();}	   
	           $(this).find('ul').eq(0).toggle();
	            return  
	         }						
  else if(parentclass=='subdown' || parentclass=='down' || parentclass=='up'){			
		if(document.all){
				e.cancelBubble=true; 
				var dls=$('#sld-list').find('li');
				for(var d=0; d<dls.length; d++){
					 if($(dls[d]).html()==e.srcElement.innerHTML){						
						       alert("您已经选择了:"+e.srcElement.innerHTML);
						        return;
						          }					
					         }						
					newlist.innerHTML=e.srcElement.innerHTML;			 
					$(e.srcElement).css({color:'#ccc',fontWeight:'100'});
					var psid= $(e.srcElement).attr('id');//获取当前标签id		
					$(newlist).addClass(psid);//将id名做为class给新建list
					postarr.push(psid);//将id添加到数组
					addTobox(newlist);//分类添加到右侧
					checkLi();//重新检测右侧列表 
					newArr();
					 createArr2(); 
				 	$('#FLD_STR').val(postarr);
					return
					}
			else{	
				e.stopPropagation(); 
				var dls=$('#sld-list').find('li');
				for(var d=0; d<dls.length; d++){
					if($(dls[d]).html()==e.target.innerHTML){						
						alert("您已经选择了:"+e.target.innerHTML);
						return;
						}					
					}
				 newlist.innerHTML=e.target.innerHTML;			  			 
				 $(e.target).css({color:'#ccc',fontWeight:'100'});
				 var psid=$(e.target).attr('id');//获取当前标签id
				 postarr.push(psid);//将id添加到数组
				 $(newlist).addClass(psid);//将id名做为class给新建list
				 addTobox(newlist);//分类添加到右侧
				 checkLi();//重新检测右侧列表 
				 newArr();
			 	createArr2(); 
				 $('#FLD_STR').val(postarr);
				 return
				}
										 
			}					
   else{
		var dls=$('#sld-list').find('li');
				for(var d=0; d<dls.length; d++){
					if($(dls[d]).html()==$(this).html()){						
						alert("您已经选择了:"+$(this).html());
						return;
						}					
					}			
				newlist.innerHTML=$(this).html();
				$(this).css({color:'#ccc',fontWeight:'100'});
				var psid=$(this).attr('id');//获取当前标签id
			    postarr.push(psid);//将id添加到数组
				$(newlist).addClass(psid);//将id名做为class给新建list
				addTobox(newlist);//分类添加到右侧
				checkLi();//重新检测右侧列表 
				newArr();
			 createArr2(); 
				 $('#FLD_STR').val(postarr);
			}							
		}								   								   
     }
   }
//selected

function checkLi(){//删除选择项并检测当前列表长
var lis=$('#sld-list').find('li');
	for(var i=0; i<lis.length; i++){	
    $(lis[i]).click(function(){							 															
			var list=$('#waitselect').find('li');
			var oho=$('#waitselect').find('h5');			
							for(var k=0; k<list.length; k++){																
								if($(list[k]).attr('id')==$(this).attr('class')){
									$(list[k]).css({color:'#000',fontWeight:'100'});									
									}															
								} 
							 for(var m=0; m<oho.length; m++){
								if($(oho[m]).attr('id')==$(this).attr('class')){
									$(oho[m]).css({color:'#000',fontWeight:'100'})								
									}								
								}																											   								
							 $(this).remove();
							 newArr();
							
							  createArr2();
							 
							 ( function(){
							  	var lis=$('#sld-list').find('li');
							  	if(lis.length==0){
							  		
							  		$('#'+setting.visbleinput).val(''); 
							  	} 
							  })()
							  $('#FLD_STR').val(postarr); 
							 
				   });	                      
               }
        }
function checkSelected(){//检测已经添加的人员或部门
	var lis=$('#sld-list').find('li');
	var list=$('#waitselect').find('li');
	var oho=$('#waitselect').find('h5');
	for(var i=0; i<lis.length; i++){							 																		
						for(var k=0; k<list.length; k++){																
								if($(list[k]).attr('id')==$(lis[i]).attr('class')){
									$(list[k]).css({color:'#ccc',fontWeight:'100'});									
									}															
								} 
							 for(var m=0; m<oho.length; m++){
								if($(oho[m]).attr('id')==$(lis[i]).attr('class')){
									$(oho[m]).css({color:'#ccc',fontWeight:'100'})								
									}								
								}																											   								
					newArr();              
               }	
	}
function newArr(){
	var lis=$('#sld-list').find('li');
	postarr=[];
	for(var k=0; k<lis.length; k++){		
			postarr.push($(lis[k]).attr('class'));//将当前class做为id重新添加到数组										
					}			
	}
function closedPop(){
	$('#addbox').css({display:'none'});
	$('#addDrug').css({display:'none'});
	$('#senderlist').fadeOut('fast'); 
}
function setPos(){
	var cw=document.body.clientWidth || document.documentElement.clientWidth;
	var oh=document.body.offsetHeight || document.documentElement.offsetHeight;
	if(oh<$('#addbox').height()){
			var tp=30+'px';
	}else{ 
		var tp=oh/2-$('#addbox').height()/2+'px';}
	  	var lt=cw/2-$('#addbox').width()/2+'px';
	var blt=parseInt(lt)-8;
	var btp=parseInt(tp)-8;
	 $('#addbox').css({display:'block',left:lt,top:tp});
	 $('#addDrug').css({display:'block',left:blt,top:btp});	
	 checkSelected();
	 checkLi();
	}
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
$("#titlebar").draggable();//拖拽