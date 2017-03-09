/* Override the module code here.
 * This code will be Loaded on Demand.
 */

Ext.override(QoDesk.LayoutWindow, {
	
	createWindow : function(){
		var desktop = this.app.getDesktop();
		var win = desktop.getWindow('layout-win');
		if(!win){
			var winWidth = desktop.getWinWidth() / 1.1;
			var winHeight = desktop.getWinHeight() / 1.1;
			
			win = desktop.createWindow({
				id: 'layout-win',
				title:'框架窗口',
				width:winWidth,
				height:winHeight,
				x:desktop.getWinX(winWidth),
				y:desktop.getWinY(winHeight),
				iconCls: 'layout-icon',
				shim:false,
				animCollapse:false,
				constrainHeader:true,
				minimizable:true,
    			maximizable:true,

				layout: 'border',
				tbar:[{
					text: '菜单1'
				},{
					text: '菜单2'
				}],
				items:[/*{
					region:'north',
					border:false,
					elements:'body',
					height:30
				},*/{
					region:'west',
					autoScroll:true,
					collapsible:true,
					cmargins:'0 0 0 0',
					margins:'0 0 0 0',
					split:true,
					title:'左侧列表',
					width:parseFloat(winWidth*0.3) < 201 ? parseFloat(winWidth*0.3) : 200
				},{
					region:'center',
					border:false,
					layout:'border',
					margins:'0 0 0 0',
					items:[{
						region:'north',
						elements:'body',
						title:'右上列表',
						height:winHeight*0.3,
						split:true
					},{
						autoScroll:true,
						elements:'body',
						region:'center',
						id:'Details',
						title:'内容'
					}]
				}/*,{
					region:'south',
					border:false,
					elements:'body',
					height:25
				}*/],
				taskbuttonTooltip: '<b>框架窗口</b><br />可做内部邮件模块'
			});
		}
		win.show();
	}
});