/* This code defines the module and will be loaded at start up.
 * 
 * When the user selects to open this module, the override code will
 * be loaded to provide the functionality.
 * 
 * Allows for 'Module on Demand'.
 */

QoDesk.MyblogWindow = Ext.extend(Ext.app.Module, {
	moduleType : 'a111',
    moduleId : 'demo-myblog',
    menuPath : 'StartMenu/我的办公桌',
	launcher : {
        iconCls: 'myblog-icon',
        shortcutIconCls: 'demo-myblog-shortcut',
        text: '工作日志',
        tooltip: '<b>这里是我的工作日志</b>'
    }
});