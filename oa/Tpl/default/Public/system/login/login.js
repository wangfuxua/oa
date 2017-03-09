Ext.onReady(function(){
	Ext.EventManager.onWindowResize(centerPanel);
	
	var loginPanel = Ext.get("qo-login-panel");
	
	var loginBtn = Ext.get("submitBtn");
	loginBtn.on({
		'click': { fn: login }
		, 'mouseover': { fn: function(){ loginBtn.addClass('qo-login-submit-over'); } }
		, 'mouseout': { fn: function(){ loginBtn.removeClass('qo-login-submit-over'); } }
	});
	
	Ext.get("field3-label").setDisplayed('none');
	Ext.get("field3").setDisplayed('none');

	centerPanel();
	
	function centerPanel(){
		var xy = loginPanel.getAlignToXY(document, 'c-c');
		positionPanel(loginPanel, xy[0], xy[1]);
	}
	
	function hideLoginFields(){
		Ext.get("field1-label").setDisplayed('none');
		Ext.get("field1").setDisplayed('none');
		Ext.get("field2-label").setDisplayed('none');
		Ext.get("field2").setDisplayed('none');
	}
	
	function loadGroupField(d){
		var combo = Ext.get("field3");
		var comboEl = combo.dom;
		
		while(comboEl.options.length){
			comboEl.options[0] = null;
		}
		
		for(var i = 0, len = d.length; i < len; i++){
			comboEl.options[i] = new Option(d[i][1], d[i][0]);
		}
	}
	
	function login(){		
		var emailField = Ext.get("field1");
		var email = emailField.dom.value;
		var pwdField = Ext.get("field2");
		var pwd = pwdField.dom.value;
		var groupField = Ext.get("field3");
		var group = groupField.dom.value;
		
		if(validate(email) === false){
			alert("请填写你的邮箱地址/用户名");
			return false;
		}
		
		if(validate(pwd) === false){
			alert("请输入你的密码");
			return false;
		}
		
		loginPanel.mask('请等待...', 'x-mask-loading');
		
		Ext.Ajax.request({
			url: '/index.php/Public/checkloginnew'
			, params: {
				module: 'login'
				, user: email
				, pass: pwd
				, group: group
			}
			, success: function(o){
				loginPanel.unmask();
				
				if(typeof o == 'object'){
					var d = Ext.decode(o.responseText);
					
					if(typeof d == 'object'){
						if(d.success == true){
							   //alert(d.success);
                             if(d.sessionId !== ""){
                                
								loginPanel.mask('登录系统...', 'x-mask-loading');
								
								// get the path
								var path = window.location.pathname,
									path = path.substring(0, path.lastIndexOf('/') + 1);
									
								// set the cookie
								set_cookie('uid', d.sessionId, '', path, '', '' );
								
								// redirect the window
								//window.location = path;
								
								window.location = "http://192.168.1.21:8002/index.php/newoa/general";
								
							}
						}else{
							if(d.errors && d.errors[0].msg){
								alert(d.errors[0].msg);
							}else{
								alert('服务器错误.');
							}
						}
					}
				}
			}
			, failure: function(){
				loginPanel.unmask();
				alert('与服务器链接丢失.');
			}
		});
	}
	
	function positionPanel(el, x, y){
        if(x && typeof x[1] == 'number'){
            y = x[1];
            x = x[0];
        }
        el.pageX = x;
        el.pageY = y;
       	
        if(x === undefined || y === undefined){ // cannot translate undefined points
            return;
        }
        
        if(y < 0){ y = 10; }
        
        var p = el.translatePoints(x, y);
        el.setLocation(p.left, p.top);
        return el;
    }
    
    function showGroupField(){
		Ext.get("field3-label").setDisplayed(true);
		Ext.get("field3").setDisplayed(true);
	}
    
    function validate(field){
		if(field === ""){
			//field.markInvalid();
			return false;
		}
		return true;
	}
});