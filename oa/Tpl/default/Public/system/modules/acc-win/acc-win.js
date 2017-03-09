/* This code defines the module and will be loaded at start up.
 * 
 * When the user selects to open this module, the override code will
 * be loaded to provide the functionality.
 * 
 * Allows for 'Module on Demand'.
 */

QoDesk.AccordionWindow = Ext.extend(Ext.app.Module, {
	moduleType : 'demo',
	moduleId : 'demo-acc',
	menuPath : 'StartMenu',		// 	在开始菜单中的位置
	launcher : {
		iconCls: 'acc-icon',
		shortcutIconCls: 'demo-acc-shortcut',
		text: '内部通讯',
		tooltip: '<b>内部通讯</b><br />这是内部通讯窗口的演示'	// 工具栏中的提示
	}
});