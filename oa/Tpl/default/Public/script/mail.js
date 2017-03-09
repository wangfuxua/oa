// JavaScript Document
//写信
$("#rt-mail").click(function(){
$("#mail-act").html('');
var b=$(this).find('em').attr('class')
if(b=="nb"){
   $("#mail-act").load("/index.php/Email/add/");	
}else{
   $("#mail-act").load("/index.php/WebMail/writeMail/setid/"+b);
}
})
//收信
$("#re-mail").click(function(){
$("#mail-act").html('');
var b=$(this).find('em').attr('class')

if(b=="nb"){
   $("#mail-act").load("/index.php/Email/inbox");	
}else{
   $("#mail-act").load("/index.php/WebMail/receivelist/setids/"+b);
}

})

//邮箱管理
$("#create-folder").click(function(){
$("#mail-act").html('');
var b=$(this).find('em').attr('class')
if(b=="nb"){
   $("#mail-act").load("/index.php/Email/mailbox");	
}else{
   $("#mail-act").load("/index.php/WebMailSet/index");
}

})



//内部邮件
//列表收件箱
$("#received").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/inbox/load/1");
})
//列表发件箱
$("#sending").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/outbox/folder/outbox");
})
//列表已发邮件
$("#sended").click(function(){
$("#mail-act").html('');
  $("#mail-act").load("/index.php/Email/sentbox/folder/sentbox");
})
//列表已删除
$("#deleted").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/deletebox//folder/deletebox");
})
//邮件搜索
$("#mail-sc").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/searchform");
})

//邮件全局搜索
$("#searchsub").click(function(){
var keyword=$('#keyword').val();
var BOX_ID=$('#BOX_ID').val();
var READ_FLAG=$('#READ_FLAG').val();
$("#mail-act").html('');
$("#mail-act").load("/index.php/Email/search/keyword/"+keyword+"/BOX_ID/"+BOX_ID+"/READ_FLAG/"+READ_FLAG);
})

//内部邮件END


//外部邮件begin
//外部邮箱设置
$("#mailset").click(function(){
$("#mail-act").html('');
$("#mail-act").load("/index.php/WebMailSet/index");
})

//外部邮件end


//内部邮件
/*var cc=true;
$("#nb-mail h3").click(function(){
$("#nb-mailbox-list").slideToggle("fast",function(){
if(cc==true){
$("#nb-mail h3").removeClass('uplist');
$("#nb-mail h3").addClass('downlist');

 return cc=false;
 }
else{
$("#nb-mail h3").removeClass('downlist');
$("#nb-mail h3").addClass('uplist');
return cc=true;
}

}); 
})
*/
//外部邮件
//var bb=true;
var titlec=$('.mailtype');
for(var i=0; i<titlec.length;i++){

	$(titlec[i]).click(function(){
		//
	$('#rt-mail').find('em').attr('class',$(this).find('h3').attr('id'));
	$('#re-mail').find('em').attr('class',$(this).find('h3').attr('id'));
	$('#create-folder').find('em').attr('class',$(this).find('h3').attr('id'));
	
	$(".foldername").hide();
	$(this).next().show();
	$(".mailtype").find('h3').attr('class','downlist');
    $(this).find('h3').addClass('uplist');
	})
	

}
/*$(".mailtype  h3").click(function(){
$(".foldername").slideToggle("fast",function(){
if(bb==true){
$("#wb-mail h3").removeClass('uplist');
$("#wb-mail h3").addClass('downlist');
 return bb=false;
 }
else{
$("#wb-mail h3").removeClass('downlist');
$("#wb-mail h3").addClass('uplist');
return bb=true;
}

}); 
})
*/

	  

