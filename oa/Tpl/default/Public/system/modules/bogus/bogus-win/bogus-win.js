/* This code defines the module and will be loaded at start up.
 * 
 * When the user selects to open this module, the override code will
 * be loaded to provide the functionality.
 * 
 * Allows for 'Module on Demand'.
 */

QoDesk.BogusWindow = Ext.extend(Ext.app.Module, {
	moduleType : 'demo',
	moduleId : 'demo-bogus',
	menuPath : 'StartMenu/窗口示例/弹出窗口',
	launcher : {
		iconCls: 'bogus-icon',
		shortcutIconCls: 'demo-bogus-shortcut',
		text: '弹出窗口',
		tooltip: '<b>弹出的新窗口</b><br />这是一个弹出窗口的示例'
	}
});