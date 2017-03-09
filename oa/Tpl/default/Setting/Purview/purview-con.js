QoDesk.PurviewWindow = Ext.extend(Ext.app.Module, {
	moduleType : 'sett',
    moduleId : 'set-purview',
    menuPath : 'StartMenu',
	launcher : {
        iconCls: 'purview-icon',
        shortcutIconCls: 'purview-shortcut',
        text: '权限设置',
        tooltip: '<b>设置用户的权限</b>'
    }
});