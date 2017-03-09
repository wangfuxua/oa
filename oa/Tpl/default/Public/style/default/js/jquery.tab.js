$.fn.Tab = function(opt){
	var cfg={
		items:[{'title':'tab','closed':true,'icon':'','html':'',load:'','callback':function(){}}],//tab的属性
		width:500,
		height:500,
		tabcontentWidth:498,
		tabWidth:66,
		tabHeight:25,
		tabScroll:true,
		tabScrollWidth:19,
		tabClass:'tab',
		tabContentClass:'tab-div-content',
		tabClassOn:'on',
		tabClassOff:'off',
		tabClassClose:'close',
		tabClassInner:'inner',
		tabClassText:'text',
		tabClassScrollLeft:'scroll-left',
		tabClassScrollRight:'scroll-right',
		tabClassDiv:'tab-div',
		tabClassHtmlDiv:'tab-div-html',
		tabHtml:''
	};
	$.extend(cfg,opt);
	//判断是不是有隐藏的tab
	var tW=cfg.tabWidth*cfg.items.length;
	cfg.tabScroll?tW-=cfg.tabScrollWidth*2:null;
	//tabDiv,该div是自动增加的
	var tab=$('<div />').attr('id','jquery_tab_div').height(cfg.tabHeight).addClass(cfg.tabClass).append('<ul />');
	//tab target content
	var tabContent=$('<div />').attr('id','jquery_tab_div_content').width(cfg.tabcontentWidth).height(cfg.height-cfg.tabHeight).addClass(cfg.tabContentClass);
	var ccW=(cfg.items.length*cfg.tabWidth)-cfg.width;
	var tabH=$('<div />').append(cfg.tabHtml).addClass(cfg.tabClassHtmlDiv).height(cfg.tabHeight);
	var scrollTab=function(o,flag){
		var left;
		flag?left=Number(tab.css('left').replace('px',''))+cfg.tabWidth:left=Number(tab.css('left').replace('px',''))-cfg.tabWidth;
		$(o).unbind('click');
		tab.animate({'left':left},function(){
			var tmp_o,tmp_ccW;
			ccW<0?tmp_ccW=0:tmp_ccW=ccW;
			if(left >= 0 || Math.abs(left)>tmp_ccW) {
				return;
			}
			$(o).click(function(){
				scrollTab(this,flag);
			});
			if($(o).hasClass(cfg.tabClassScrollLeft)){
				tmp_o=srcollRight;
				tmp_o.click(function(){
					scrollTab(this,!flag);
				});
			}
			else{
				tmp_o=scrollLeft;
				tmp_o.click(function(){
					scrollTab(this,!flag);
				});
			}
		});
	}
  function addTab(item){
		//close
		var close='';
		if(item.closed){
			close=$('<a class="'+cfg.tabClassClose+'" onclick="return false;" />').click(function(){
				var _self=$(this);
				//only one tab
				if(tab.find('li').length<2){
					_self.remove();
				}
				else{
					//first tab delete
					var prev=_self.parent().prev();
					var next=_self.parent().next();
					_self.parent().remove();
					ccW-=cfg.tabWidth;
					var left=Number(tab.css('left').replace('px',''));
					if(left<0){
						tab.animate({'left':left+cfg.tabWidth});
					}
					else{
						cfg.tabScroll&&scrollLeft.unbind('click');
					}
					if(_self.parent().hasClass('on')){
						if(prev.html()){
							prev.click();
						}
						else{
							next.click();
						}
					}
					if(ccW<=cfg.tabWidth){
						cfg.tabScroll&&srcollRight.unbind('click');
					}
				}
			});
		}
		var inner=$('<a class="'+cfg.tabClassInner+'" onclick="return false;">'+'<span class="'+cfg.tabClassText+'">'+item.title+'</span></a>');
		$('<li></li>').addClass(cfg.tabClassOff).click(function(){
			var _self=$(this);
			if(_self.hasClass(cfg.tabClassOn)) return;
			_self.parent().find('li').removeClass().addClass(cfg.tabClassOff);
			_self.removeClass().addClass(cfg.tabClassOn);
			//判断是显示html代码还是ajax请求内容
			if(item.html){
				tabContent.html(item.html);
			}
			else{
				if(item.load){
					tabContent.load(item.load);
				}
			}
			//回调函数是什么
			if(item.callback) item.callback(_self);
		}).append(close).append(inner).appendTo(tab.find('ul'));
	}
	function newTab(item){
		cfg.items.push(item);
		var nW=cfg.items.length*cfg.tabWidth;
		ccW+=cfg.tabWidth;
		//(ccW>0&&ccW<cfg.tabWidth)?ccW=cfg.tabWidth:null;
		if(nW>cfg.width){
			if(!cfg.tabScroll){
				cfg.tabScroll=true;
				scrollLeft=$('<div class="'+cfg.tabClassScrollLeft+'"></div>').click(function(){
					scrollTab(this,true);
				});
				srcollRight=$('<div class="'+cfg.tabClassScrollRight+'"></div>');
				cW-=cfg.tabScrollWidth*2;
				tabContenter.width(cW);
				scrollLeft.insertBefore(tabContenter);
				srcollRight.insertBefore(tabContenter);
			}
			addTab(item);
			//scrollLeft.click();
			scrollTab(srcollRight,false);
		}
		else{
			addTab(item);
		}
	}
	$.each(cfg.items,function(i,o){
		addTab(o);
	});
	var cW=cfg.width;
	var scrollLeft,srcollRight;
	
	if(cfg.tabScroll){
		scrollLeft=$('<div class="'+cfg.tabClassScrollLeft+'"></div>');
		if(tW>cW){
			srcollRight=$('<div class="'+cfg.tabClassScrollRight+'"></div>').click(function(){
				scrollTab(this,false);
			});
		}
		else{
			srcollRight=$('<div class="'+cfg.tabClassScrollRight+'"></div>');
		}
		cW-=cfg.tabScrollWidth*2;
	}
	var container=$('<div />').css({
		'overflow':'hidden',
		'position':'relative',
		'width':cfg.width,
		'height':cfg.tabHeight
	}).append(scrollLeft).append(srcollRight).addClass(cfg.tabClassDiv);
	var tabContenter=$('<div />').css({
		'overflow':'hidden',
		'width':cW,
		'height':cfg.tabHeight,
		'float':'left'
	}).append(tab);
	var obj=$(this).append(tabH).append(container.append(tabContenter)).append(tabContent);
	//点击第一
	tab.find('li:first').click();
	return obj.extend({'addTab':addTab,'newTab':newTab});
};