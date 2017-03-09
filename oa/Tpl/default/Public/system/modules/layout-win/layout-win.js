/* This code will be loaded at start up.
 * 
 * When the user selects to open this module, it's override code will
 * be loaded to provide the functionality.
 * 
 * Allows for 'Module on Demand'.
 */

QoDesk.LayoutWindow = Ext.extend(Ext.app.Module, {
	moduleType : 'demo',
	moduleId : 'demo-layout',
	menuPath : 'StartMenu/窗口示例',
	launcher : {
		iconCls: 'layout-icon',
		shortcutIconCls: 'demo-layout-shortcut',
		text: '框架窗口',
		tooltip: '<b>框架窗口</b><br />可做内部邮件模块'
	}
});