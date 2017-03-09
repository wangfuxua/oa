// JavaScript Document

function removeC(){
	for(var i=1; i<=3; i++){
		$("#n_1").removeClass("n_1" + i);
		$("#a" + i).removeClass("over1");
		$("#con" + i).slideUp("fast");
	}
}
function changeNav(a){
	switch(a){
		case 1:
			removeC();
			$("#n_1").addClass("n_1");
			$("#a1").addClass("over1");
			$("#con1").slideDown("fast");
			break;
		case 2:
			removeC();
			$("#n_1").addClass("n_12");
			$("#a2").addClass("over1");
			$("#con2").slideDown("fast");
			break;
		case 3:
			removeC();
			$("#n_1").addClass("n_13");
			$("#a3").addClass("over1");
			$("#con3").slideDown("fast");
			break;
	}
}
$("#p1").toggle(function(){
	$("#c1").slideUp("fast");
	$("#p1").addClass("over3");
},function(){
	$("#c1").slideDown("fast");
	$("#p1").removeClass("over3");
});
$("#p2").toggle(function(){
	$("#c2").slideUp("fast");
	$("#p2").addClass("over3");
},function(){
	$("#c2").slideDown("fast");
	$("#p2").removeClass("over3");
});
$("#p3").toggle(function(){
	$("#c3").slideUp("fast");
	$("#p3").addClass("over3");
},function(){
	$("#c3").slideDown("fast");
	$("#p3").removeClass("over3");
});
function changeText(str,b){
	$("#myem").text(str);
	for(var j=1; j<=7; j++){
		$("#main_c" + j).css("display","none");
	}
	$("#main_c" + b).css("display","block");
}