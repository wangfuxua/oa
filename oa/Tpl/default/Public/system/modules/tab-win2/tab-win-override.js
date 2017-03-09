/* Override the module code here.
 * This code will be Loaded on Demand.
 */

Ext.override(QoDesk.TabWindow, {
	
	createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow(this.moduleId);
        
        if(!win){
        	var winWidth = desktop.getWinWidth() / 1.1;
			var winHeight = desktop.getWinHeight() / 1.1;
			var ajax="<b>aaaaaaaa</b>";
            win = desktop.createWindow({
                id: this.moduleId,
                title: '选项卡窗口',
                width: winWidth,
                height: winHeight,
                iconCls: 'tab-icon',
                shim: false,
                constrainHeader: true,
                layout: 'fit',
                items:
                    new Ext.TabPanel({
                        activeTab:0,
                        items: [{
                        	autoScroll: true,
                            title: '页面 1',
                            header: false,
                            html: ajax,
                			border: false
                        },{
                            title: '页面 2',
                            header:false,
                            html: '<p>内容 2</p>',
                            border: false
                        },{
                            title: '页面 3',
                            header:false,
                            html: '<p>内容 3</p>',
                            border:false
                        }]
                    }),
                    taskbuttonTooltip: '<b>选项卡窗口</b><br />'
            });
        }
        win.show();
    }
});