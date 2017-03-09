// JavaScript Document

var setting={
	visbleinput:'',//显示发件人名称的表单id
	hideinput:'',//显示收件人id的隐藏表单id
	loadimgpath:'/oa/Tpl/default/Public/images/loading.gif',//图片的路径根据文件路径进行更改
	tree_imgpath:'/oa/Tpl/default/Public/images/',//树形菜单折叠图标图片路径
	settempid:'',//隐藏数据容器id
	set_hide_date:false//是否对隐藏数据操作
	}
var tempd=document.createElement('ul');
function setInput(hidid,visid,temp,b){
	          setPos();
			  setting.hideinput=hidid;
			  setting.visbleinput=visid;
			  setting.settempid=temp;
			  resetPop();
	   var h=$('#'+hidid).val();
	   var v=$('#'+visid).val();
		if(h!='' || v!='' || b==true){
			setting.set_hide_date=true;
			$(tempd).html('');
			$('#'+temp).find('li').each(function(i){
			$(tempd).append($(this).clone());						  
			var c=getFristWord($(this).attr('class'));
			  switch(c){
						  case 'p':
						  $('.bumen').append($(this));
						  break;
						  case 'c':
						  $('.juese').append($(this));
						  case 'u':
						  $('.renyuan').append($(this));
				      }
				  checkSelected();
				  checkLi();
		  })
	 }

	
}
function closedPop(save){//关闭弹出层并清空postarr数组 参数1：‘是否为确定按钮’；参数2：是否对隐藏数据操作
	$('#addbox').css({display:'none'});
	$('#addDrug').css({display:'none'});
	$('#senderlist').fadeOut('fast');
	$('select').css('visibility','visible');
	if(save){
		$('#'+setting.settempid).html($('#sld-list').find('li'));
	  }
	 else{
		$('#'+setting.settempid).html($(tempd).find('li'));
		 newArr('#'+setting.settempid)
		}
		setting.set_hide_date=false;    
 }
var postarr=new Array();
var namearr=new Array();
var slec=false;//搜索输入框
var keycut=false;//键盘
var ssing=false;//搜索是否正在进行
var readyclearid=null;
var toptagscrh;//是否为顶端首字母搜索 true是；false包含搜索 all搜索全部
slcTag("bumen");	
slcTag("juese");
$('#qd').bind('click',function(){closedPop(true,setting.set_hide_date);})//关联收件人输入框
$('#qx').click(function(){closedPop(false,setting.set_hide_date)});
$('.closed').click(function(){closedPop(false,setting.set_hide_date)})//关闭联系人
$('.tl span').click(clearList)//清空联系人
$('#go-sc').click(function(){createLoader('none',true)})//鼠标事件搜索
$('#sc-name').focus(function(){keycut=true});
$('#sc-name').blur(function(){keycut=false});
document.onkeydown=keyDown;	
function slect(){if(slec==false){$('#sc-name').val('');$('#sc-name').css('color','#666'); return slec=true}else{return}}
function keyDown(e){var e=e || window.event;if(e.keyCode ==13 && keycut==true){createLoader('false',true);}else{return}}//enter键操作
//重设列表框和数组
function resetPop(){
	postarr=[];
	namearr=[];
	$('#rel ul').html('');
	$('#sld-list ul').html('');
	}
//顶部首字母搜索
var d=$('#top-sh').find('a');
var target_length=d.length;
	 for(var i=0; i<target_length; i++){
		$(d[i]).click(function(){
							this.blur();
							var c=$('#top-sh').find('a');
							var c_lth=c.length;
		for(var k=0; k<c_lth; k++){							
				$(c[k]).parent().css({position:'static'});
				$(c[k]).css({position:'static',fontWeight:100,color:'#666'});
				c[0].parentNode.style.position='relative';
				c[0].style.position='absolute';					
				}								   
					         $(this).parent().css({position:'relative'})
					         $(this).css({position:'absolute',left:0,bottom:0,Zindex:9999,fontWeight:900,color:'red'})
							 $('#sc-name').val($(this).text().toLowerCase());
							   if($(this).parent().attr('class')=='all_c'){createLoader('all')}
							   else{
								
								  var wd=getFristWord($('#rel span').attr('class'))
								  
								  if(wd=='p'){
									   
									  createLoader('role')  
									  }
								  else if(wd=='d'){
								   
								   createLoader('part')
								  }else{
									  
									  createLoader('true')
									  
									  }
								   
								   }
						
							   })
		
		
		}

//部门和角色标题切换
(function cutGroup(){
var oh=$('#waitselect').find('h4');
var oh_lth=oh.length;
for(var k=0; k<oh_lth; k++){
$(oh[k]).hover(function(){$(this).css('color','#2a7eab')},function(){$(this).css('color','#1f4559')})
oh[k].onclick=function(){
	for(var j=0; j<oh.length;j++){
		oh[j].className=" ";
		$(oh[j]).parent().next().css('display','none')
		}		
		$(this).parent().next().css('display','block');
		$(this).addClass('over')								   
	}
  }
})()

//搜索 
function createLoader(d,g){//创建loading 并设置搜索规则
  var cert=trim($('#sc-name').val());
  var lis=$('#sld-list').find('li');
  var lis_lth=lis.length;
  if(ssing=='breaked'){return ssing=false}
  if(ssing==true){return}
     ssing=true;
	 if(cert=='' || cert=='输入搜索内容汉字或拼音'){alert('请输入您要查找的人员名称');return ssing='breaked'} 
		  for(var o=0; o<lis_lth; o++){
			  var f=trim($(lis[o]).html());
				if(f==cert){						  
						 alert('您已经选择了：'+cert);
						  	 	$('#rel').fadeOut('slow');
								
						      return  
						  }		  		  
		               }
			
	$('#rel ul').html('');
	if(g){
	$('#rel h5').html('全局搜索结果');
	}
    createSbox();
	toptagscrh=d;
	setTimeout('sous()',500);
	}

function sous(){
	  var cert=$('#sc-name').val();
      var lis=$('#sld-list').find('li');
	  if(toptagscrh=='part'){//局部搜索		 
					var d=trim($('#rel h5').html());
					for(var i in person){
						var vv=trim(person[i].part) || trim(person[i].role)
						if(vv.indexOf(d)!=-1){
							if(getFristWord(i)==trim(cert)){
								$('#rel').css('display','block');
				     var relist=document.createElement('li');
				            $(relist).attr('class',person[i].id);
				             relist.innerHTML=person[i].name;
				           $('#rel ul').append(relist);
								
								
								
								}						
							
							}					
						
						}						
					
		addTo();
		checkSelected();
		$('#loaderimg').remove();
        $('#loaderbg').remove();		
	    if($('#rel ul').html()==''){
        alert('没有找到！')
		}		
	 ssing=false;
	 toptagscrh=false;
	 return								
		}
else if(toptagscrh=='role'){
						var d=trim($('#rel h5').html());
					for(var i in person){
						var vv=trim(person[i].role)
						if(vv.indexOf(d)!=-1){
							if(getFristWord(i)==trim(cert)){
								$('#rel').css('display','block');
				     var relist=document.createElement('li');
				            $(relist).attr('class',person[i].id);
				             relist.innerHTML=person[i].name;
				           $('#rel ul').append(relist);
								
								
								
								}						
							
							}					
						
						}						
					
		addTo();
		checkSelected();
		$('#loaderimg').remove();
        $('#loaderbg').remove();		
	    if($('#rel ul').html()==''){
        alert('没有找到！')
		}		
	 ssing=false;
	 toptagscrh=false;
	 return	
	
	
	
	
	}
else{//全局搜索 首字母 、 全部 、包含搜索
	for(var i in person){
		if(toptagscrh=='true'){
			var scrhrole=i.substring(-1,1)==cert;
			}
			else if(toptagscrh=='all'){
				scrhrole=person[i].constructor==Object;}
			
			else{var scrhrole=i.indexOf(cert)!=-1 || person[i].name.indexOf(cert)!=-1 }
		 if(scrhrole){	
				$('#rel').css('display','block');
				var relist=document.createElement('li');
				 $(relist).attr('class',person[i].id);
				  relist.innerHTML=person[i].name;
				  $('#rel ul').append(relist);
						   } 
				   } //end loop
		addTo();
		checkSelected();
		$('#loaderimg').remove();
        $('#loaderbg').remove();		
	    if($('#rel ul').html()==''){
        alert('没有找到！')
		}		
	 ssing=false;
	 toptagscrh=false;
	 }
}
//清空已选
function clearList(){	
			var nlist=$('#sld-list').find('li');
			var sos=$('#rel').find('li');
			var nlist_lth=nlist.length;
			var sos_lth=sos.length;
			for(var i=0; i<nlist_lth; i++){$(nlist[i]).remove();}
			for(var s=0; s<sos_lth; s++){$(sos[s]).css({background:'#f9f9f9',fontWeight:'100'})}
			newArr('#sld-list');
	}
//ssssssssssssssssssssssssssssssssssssssssssssssssssssssssss
function addTo(){//备选（绑定事件 添加数组）
	var list=$('#rel').find('li');
	var list_lth=list.length;
	for(var i=0; i<list_lth; i++){
		$(list[i]).click(function(){
					var lis=$('#sld-list').find('li');
					var lis_lth=lis.length;
//检测已选是否包含备选顶端标题： true删除已选部门或角色 ==>并将删除的角色或部门包含的人员添加至已选(绑定事件，重写数组) ==> 删除当前点击的人员 ==>return				
					for(var k=0;k<lis_lth; k++){	
						if($(lis[k]).attr('class')=='all'){return alert('删除\"'+$(lis[k]).html()+'\"后才能选取')}
							
							if($(lis[k]).attr('class')==$('#rel span').attr('class')){
								 $(lis[k]).remove();
								  for(var d=0; d<list_lth; d++){	
										 var newlist=document.createElement('li');
								              var _class=$(list[d]).attr('class');
								                  $(newlist).attr('class',_class);														  
								                  newlist.innerHTML=$(list[d]).html();
								                  $(list[d]).css('background','#bbddfe');
								                  $('.renyuan').append(newlist);          																		
								                }//end loop
												
								 var newlis=$('#sld-list').find('li');
								 var newlis_lth=newlis.length;
								 var d=trim($(this).html());
								 for(var f=0;f<newlis_lth; f++){									
								   									
                                     var s=trim($(newlis[f]).html());
									
								      if(s==d){
										     $(this).css('background','#f9f9f9');
										     $(newlis[f]).remove();
										         }	
											 }//end loop
										    checkLi();
								            newArr('#sld-list');
								return
								}
						//	判断是否相等 true==>删除等同已选==>return  ||  false==>添加至已选
							if(trim($(lis[k]).html())==trim($(this).html())){								
										  $(this).css('background','#f9f9f9');
										  $(lis[k]).remove();
										   newArr('#sld-list');
										  return
										  }								  
									  }
								  var newlist=document.createElement('li');
								  var _class=$(this).attr('class');
								  $(newlist).attr('class',_class);															  
								   newlist.innerHTML=$(this).html();
								   $(this).css('background','#bbddfe');								
								   if($(this).attr('class')=='all'){									  
									   $('#sld-list ul').html('');
									    $('.bumen').append(newlist);
										 checkLi();
								          newArr('#sld-list');
									       return
									   }
									  else{
								            $('.renyuan').append(newlist);
									   }
								   checkLi();
								   newArr('#sld-list');
			})		
		}
	}
//获取首字母 转换小写
function getFristWord(wrod){
		return wrod.substring(-1,1).toLowerCase();
		}
//去掉字符串空格
function trim(string){
	var re=/ /g;
	return string.replace(re,'')	
	}
function selectAll(b){//选取全部（绑定事件 添加数组）
	var list=$('#rel').find('li');
	var list_lth=list.length;
	for(var i=0; i<list_lth; i++){
		var lis=$('#sld-list').find('li');
		var lis_lth=lis.length;
			for(var k=0;k<lis_lth; k++){
				if($(lis[k]).attr('class')==$('#rel span').attr('class') || $(lis[k]).attr('class')=='all'){return}
					if(trim($(lis[k]).html())==trim($(list[i]).html())){
										 $(lis[k]).remove();//删除部门 和部门包含的人员
										  }								  
									  }//end inner loop
					$(list[i]).css('background','#bbddfe');		
		
		 }//end warp loop
		   var newlist=document.createElement('li');
		   var _class=$(b).attr('class');
		   $(newlist).attr('class',_class);
			newlist.innerHTML=$('#rel h5').html();
			var kind=$('#rel span').attr('class');
			var d=getFristWord(kind);
			switch(d){
				case 'p':
				$('.bumen').append(newlist);
				break;
				case 'c':
				$('.juese').append(newlist);
				}
	
		checkLi();
		newArr('#sld-list');
		}
/*function deleteAll(){
	var list=$('#rel').find('li');
	for(var i=0; i<list.length; i++){
		var lis=$('#sld-list').find('li');
			for(var k=0;k<lis.length; k++){
					if($(lis[k]).html()==$(list[i]).html()){
										 $(lis[k]).remove();
										  }								  
									  }
	
			$(list[i]).css('background','#f9f9f9');
				
		}
	
	
	}
	*/
function slcTag(id){//左侧树型节点菜单
switch(id){
		case 'bumen':
		var tags=$('#bumen').find('li');
		var tags_lth=tags.length;
		break;
		case 'juese':
		var tags=$('#juese').find('li');
		var tags_lth=tags.length;
		break;	
	  }	
for(var i=0; i<tags_lth; i++){
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
	var target=e.target || e.srcElement;
    var s=trim(target.innerHTML)
    if(this.className=='subdown'){	
	     if(document.all){e.cancelBubble=true;}else{e.stopPropagation();}			
			if(target.tagName=='SPAN'){
			      $(this).find('ul').eq(0).show();
			      $(target).prev().attr('src',setting.tree_imgpath+'bg_9_1.gif');
				  $('#rel ul').html('');	
				  $('#rel').show();
				  $('#rel h5').html(target.innerHTML);
				  $('#rel span').attr('class',$(target).attr('id'));
					   for(var i in person){
						  if(id=='bumen'){var pattr=person[i].part}else{var pattr=person[i].role}
					       var p=trim(pattr);			 
						   if(p.indexOf(s)!=-1){
							   var li=document.createElement('li');
							   li.innerHTML=person[i].name;
							   $(li).appendTo('#rel ul');
							   $(li).attr('class',person[i].id);
							   
							   }						   					   
						   }
				   $('#'+readyclearid).css({color:'#000',fontWeight:'100'});
				   $(target).css({color:'#036',fontWeight:'900'});
				   readyclearid= $(target).attr('id');
	               addTo()
				   checkSelected()
			      }
			 if(target.tagName=='IMG'){				   
				   $(this).find('ul').eq(0).toggle();
				   if($(target).attr('src').indexOf('bg_9_1.gif')!=-1){					   
					   $(target).attr('src',setting.tree_imgpath+'bg_9.gif')
					   }else{$(target).attr('src',setting.tree_imgpath+'bg_9_1.gif')}				   
				   }else{return}
	         }				
   else{			
				 $('#rel ul').html('');
				 $('#rel').show();
				 $('#rel h5').html(target.innerHTML);
				  $('#rel span').attr('class',$(target).attr('id'));
				     for(var i in person){						
				 if(id=='bumen'){var pattr=person[i].part;}else{var pattr=person[i].role;}               
					   var p=trim(pattr);		 
						   if(p.indexOf(s)!=-1){
							   var li=document.createElement('li');
							   li.innerHTML=person[i].name;
							   $(li).appendTo('#rel ul');
							   $(li).attr('class',person[i].id);							 
							   }						   					   
						   }
						  
				   $('#'+readyclearid).css({color:'#000',fontWeight:'100'});
				   $(this).css({color:'#036',fontWeight:'900'});
				   readyclearid= $(this).attr('id');				  
				   addTo();
				   checkSelected()
			return
			}							
		}								   								   
     }
   }
//selected
function checkLi(){//删除选择项并检测当前列表长
var lis=$('#sld-list').find('li');
var lis_lth=lis.length;
	for(var i=0; i<lis_lth; i++){	
    $(lis[i]).dblclick(function(){
			
			var srl=$('#rel').find('li');
			var srl_lth=srl.length;
				 for(var n=0; n<srl_lth; n++){
					 if($(this).attr('class')==''){
						 srl.each(function(){
										    $(this).css({background:'#f9f9f9',fontWeight:'100'})	
										   
										   })
				      
					      
				        $(this).remove();
					     newArr('#sld-list');
						 return
				       }
					if($(srl[n]).attr('class')==$(this).attr('class')){
						$(srl[n]).css({background:'#f9f9f9',fontWeight:'100'})								
						}	
						
						
					}//end loop
			for(var u in person){
				var d=trim(person[u].part);
				var f=trim(person[u].role);	
				var g=trim($(this).html())
							if(d.indexOf(g)!=-1 || f.indexOf(g)!=-1){	
							srl.each(function(i){
								if($(this).attr('class')==person[u].id){
							                   $(this).css({background:'#f9f9f9',fontWeight:'100'});
								          }
							         })
					  }
									
									
				
				}//end loop
					
					$(this).remove();
					 newArr('#sld-list');							 		
				   });	                      
              }
        }
function checkSelected(){//检测已经添加的人员或部门
	var lis=$('#sld-list').find('li');
	var sos=$('#rel').find('li');
	var lis_lth=lis.length;
	var sos_lth=sos.length;
	var title=$('#rel h5').html();
	for(var i=0; i<lis_lth; i++){							 																		
			for(var s=0; s<sos_lth; s++){																
					if($(sos[s]).attr('class')==$(lis[i]).attr('class')){						
					       $(sos[s]).css({background:'#bbddfe',fontWeight:'100'});									
									}
									
						else if(trim(title)==trim($(lis[i]).html())){										
										$(sos[s]).css({background:'#bbddfe',fontWeight:'100'});
										
										
										
										}
										else if($(lis[i]).attr('class')=='all'){
											
											$(sos[s]).css({background:'#bbddfe',fontWeight:'100'});
											}
								}//end inner loop
								
			for(var u in person){
				var d=trim(person[u].part);
				var f=trim(person[u].role);
				var g=trim($(lis[i]).html());
				if(d.indexOf(g)!=-1 || f.indexOf(g)!=-1){
					sos.each(function(i){
								var p=trim(person[u].name);
								var c=trim($(this).html());
									if(p==c || f==c){										
										$(this).css({background:'#bbddfe',fontWeight:'100'});
										}
									
									})
					$(sos[s]).css({background:'#bbddfe',fontWeight:'100'});	
					
					}
				
				
				
				}//end loop
				
				
				
               }//end warp loop
			   
			   
			   
	    newArr('#sld-list');
	}
function newArr(temp){//清空并重写postarr数组
    postarr=[];
	namearr=[];
	var lis=$(temp).find('li');	
	var lis_lth=lis.length;
	for(var k=0; k<lis_lth; k++){		
			postarr.push($(lis[k]).attr('class'));
			namearr.push($(lis[k]).text());
			
		}
	$('#'+ setting.hideinput).val(postarr);
	$('#'+setting.visbleinput).val(namearr);
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
	$('#addDrug').height($('#addbox').height()+16);
    $('#addDrug').width($('#addbox').width()+16);
	$('#addDrug').css({display:'block',left:blt,top:btp,backGround:'#000',opacity:.6});
	$('select').css('visibility','hidden')
	}
function createSbox(){
	var h=$('.selectboxbder3').height();
	var w=$('.selectboxbder3').width();
	var myloder=document.createElement('img');
	var myloderbox=document.createElement('div');
	var myloderboxbg=document.createElement('div');
	var lodertext='正在查找';
	$(myloder).attr('src',setting.loadimgpath);
	$(myloderbox).attr('id','loaderimg');
	$('#rel').append(myloderboxbg);
	$(myloderbox).append(myloder);
	$(myloderbox).append(lodertext);
	$('#rel').append(myloderbox);
	$(myloderboxbg).attr('id','loaderbg')
	$(myloderboxbg).height(310);
	$(myloderboxbg).width(217);
	$(myloderboxbg).css({position:'absolute',top:30,left:0,opacity:.8,background:'#fff'})	
	var nh=h/2;
	var nw=w/2;
	$(myloderbox).css({position:'absolute',top:nh-10,left:nw-60,height:20,width:120,color:'red'});	
	}
$("#selecte-all-part").click(function(){
    $('#rel ul').html('');
	$('#rel h5').html('全部部门');
	$('#rel span').attr('class','all');
	$('#rel ul').html("<li class='all'>全部部门</li>");
    addTo();
	checkSelected();
	})
$("#selecte-all-role").click(function(){
	$('#rel ul').html('');
	$('#rel h5').html('全部角色');
	$('#rel span').attr('class','all');
	$('#rel ul').html("<li class='all'>全部角色</li>");
    addTo();
	checkSelected();
	})
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
    }

$("#titlebar").draggable();//拖拽
