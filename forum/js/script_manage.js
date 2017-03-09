//删除回帖
function post_delete(pid, result) {
	if(result) {
		var obj = $('post_'+ pid +'_li');
		if(obj){
			obj.style.display = "none";
		}
		if($('post_replynum')) {
			var a = parseInt($('post_replynum').innerHTML);
			var b = a - 1;
			$('post_replynum').innerHTML = b + '';
		}
	}
}

//添加回帖
function post_add(pid, result) {
	if(result) {
		var obj = $('post_ul');
		var newli = document.createElement("div");
		var x = new Ajax();
		x.get('forum_ajax.php?act=post', function(s){
			newli.innerHTML = s;
		});
		obj.appendChild(newli);
		if($('message')) {
			$('message').value= '';
			newnode = $('quickpostimg').rows[0].cloneNode(true);
			tags = newnode.getElementsByTagName('input');
			for(i in tags) {
				if(tags[i].name == 'pics[]') {
					tags[i].value = 'http://';
				}
			}
			var allRows = $('quickpostimg').rows;
			while(allRows.length) {
				$('quickpostimg').removeChild(allRows[0]);
			}
			$('quickpostimg').appendChild(newnode);
		}
		if($('post_replynum')) {
			var a = parseInt($('post_replynum').innerHTML);
			var b = a + 1;
			$('post_replynum').innerHTML = b + '';
		}
	}
}
//编辑回帖
function post_edit(pid, result) {
	if(result) {
		var obj = $('post_'+ pid +'_li');
		var x = new Ajax();
		x.get('forum_ajax.php?act=post&pid='+ pid, function(s){
			obj.innerHTML = s;
		});
	}
}
//引用回复

function post_reply(pid, result) {

	if(result){
		var obj = $('reply_'+ pid +'_li');
		var x = new Ajax();
		x.get('forum_ajax.php?act=reply&pid='+ pid, function(s){
			obj.innerHTML = s;
		});
	}

}
//选择图片
function picView(albumid) {
	if(albumid == 'none') {
		$('albumpic_body').innerHTML = '';
	} else {
		ajaxget('forum_ajax.php?act=album&id='+albumid+'&ajaxdiv=albumpic_body', 'albumpic_body');
	}
}