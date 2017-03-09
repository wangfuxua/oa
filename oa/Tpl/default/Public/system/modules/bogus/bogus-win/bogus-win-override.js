Ext.override(QoDesk.BogusWindow, {
	
	detailModule : null,
	
	init : function(){
		this.detailModule = new BogusDetailModule();
	},
	
	createWindow : function(){
		var desktop = this.app.getDesktop();
		var win = desktop.getWindow('bogus-win');
		
		if(!win){
            win = desktop.createWindow({
                autoScroll: true,
                id: 'bogus-win',
                title: '窗口',
                width:640,
                height:480,
                iconCls: 'bogus-icon',
                items: new QoDesk.BogusWindow.NavPanel({owner: this, id: 'nav-panel'}),
                shim:false,
                animCollapse:false,
                constrainHeader:true,
                maximizable: false,
                tbar: [{
                	handler: this.showDialog,
                	scope: this,
                	text: '打开新窗口'
                }],
                taskbuttonTooltip: '<b>弹出窗口</b><br />弹出一个新窗口'
            });
        }
        
        win.show();
    },
    
    openDetail : function(id){
		this.detailModule.createWindow(this.app, id);
    },
    
    showDialog : function(){
    	var winManager = this.app.getDesktop().getManager();
    	
    	if(!this.dialog){
            this.dialog = new Ext.Window({
            	bodyStyle:'padding:10px',
                layout:'fit',
                width:500,
                height:300,
                closeAction:'hide',
                plain: true,
                html: '这里是弹出窗口的内容',
                buttons: [{
                    text:'Submit',
                    disabled:true
                },{
                    text: 'Close',
                    handler: function(){
                        this.dialog.hide();
                    },
                    scope: this
                }],
                manager: winManager,
                modal: true
            });
        }
        this.dialog.show();
    }
});



QoDesk.BogusWindow.NavPanel = function(config){
	this.owner = config.owner;
	
	QoDesk.BogusWindow.NavPanel.superclass.constructor.call(this, {
		autoScroll: true,
		bodyStyle: 'padding:15px',
		border: false,
		autoLoad:'http://www.newoa.com/test.php?a=1',
		html: '<ul id="bogus-nav-panel"> \
				<li> \
					<a id="openDetailOne" href="#">链接 1</a><br /> \
					<span>这里是链接1的注释.</span> \
				</li> \
				<li> \
					<a id="openDetailTwo" href="#">链接 2</a><br /> \
					<span>这里是链接2的注释.</span> \
				</li> \
				<li> \
					<a id="openDetailThree" href="#">链接 3</a><br /> \
					<span>这里是链接3的注释.</span> \
				</li> \
			</ul>',
		id: config.id
	});
	
	this.actions = {
		'openDetailOne' : function(owner){
			owner.openDetail(1);
		},
		
		'openDetailTwo' : function(owner){
			owner.openDetail(2);
		},
		
		'openDetailThree' : function(owner){
	   		owner.openDetail(3);
	   	}
	};
};

Ext.extend(QoDesk.BogusWindow.NavPanel, Ext.Panel, {
	afterRender : function(){
		this.body.on({
			'mousedown': {
				fn: this.doAction,
				scope: this,
				delegate: 'a'
			},
			'click': {
				fn: Ext.emptyFn,
				scope: null,
				delegate: 'a',
				preventDefault: true
			}
		});
		
		QoDesk.BogusWindow.NavPanel.superclass.afterRender.call(this); // do sizing calcs last
	},
	
	doAction : function(e, t){
    	e.stopEvent();
    	this.actions[t.id](this.owner);  // pass owner for scope
    }
});



BogusDetailModule = Ext.extend(Ext.app.Module, {

	moduleType : 'demo',
	moduleId : 'demo-bogus-detail',
	
	init : function(){
		this.launcher = {
			handler: this.createWindow,
			iconCls: 'bogus-icon',
			scope: this,
			shortcutIconCls: 'demo-bogus-shortcut',
			text: '弹出窗口',
			tooltip: '<b>弹出的窗口</b><br />弹出的虚拟窗口'
		}
	},

	createWindow : function(app, id){
		this.moduleId = 'demo-bogus-detail-'+id;
		
		var desktop = app.getDesktop();
		var win = desktop.getWindow('bogus-detail'+id);
		
        if(!win){
            win = desktop.createWindow({
                id: 'bogus-detail'+id,
                title: '链接 '+id,
                width: 540,
                height: 380,
                html : '<p>这里是点击链接后的详细内容.</p>',
                iconCls: 'bogus-icon',
                shim:false,
                animCollapse:false,
                constrainHeader:true
            });
        }
        win.show();
    }
});