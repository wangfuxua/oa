var slec=false;//搜索输入框
var keycut=false;//键盘
$('#qd').click(createArr)//关联收件人输入框
$('#qx').click(function(){$('#sendername').val('');closedPop()})
$('#senderlist li:last').click(setPos)//弹出联系人
$('#sjr').click(setPos)//弹出联系人
$('.closed').click(closedPop)//关闭联系人
$('#zt').click(cutTitle)//切换分类
$('.tl span').click(clearList)//清空联系人
$("#titlebar").draggable();//拖拽
$('#go-sc').click(sous)//鼠标事件搜索
$('#sc-name').focus(function(){keycut=true})
$('#sc-name').blur(function(){keycut=false})
slcTag("bumen");//添加部门
slcTag("juese");//添加角色
slcTag("renyuan");//添加人员
//==========================================================//
function slect(){if(slec==false){$('#sc-name').val('');$('#sc-name').css('color','#666'); return slec=true}else{return}}
function keyDown(e){var e=e || window.event;if(e.keyCode ==13 && keycut==true){sous();}else{return}}//enter键操作
document.onkeydown=keyDown;
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
//add file
var spanlang;
$('#myfile').change(
function(){
var span=document.createElement('span');
var ctn=$('#myfile').val();
var delimg="<img src='images/ico/delete.gif' align='absmiddle' style='padding-left:5px' />";
		$(span).append(ctn);
		$(span).append(delimg);
		$('#Editor').animate({height:'239'});
		$('#addfilebox').append(span);
		$('#addfilebox').slideDown('fast',function(){setTimeout('getLength()',100);$('#myfile').val('')});
    }
)
function getLength(){
spanlang=$('#addfilebox').find('span');
for(var i=0; i<spanlang.length; i++){
           $(spanlang[i]).click(function(){
           $(this).remove();
           spanlang=$('#addfilebox').find('span');
           if(spanlang.length==0){$('#addfilebox').slideUp('fast');$('#Editor').animate({height:'281'})
		   }
      })
  }
}
