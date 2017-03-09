QoDesk.QoPreferences = Ext.extend(Ext.app.Module, {
	moduleType : 'system/preferences',
	moduleId : 'qo-preferences',
	menuPath : 'ToolMenu',
	launcher : {
        iconCls: 'pref-icon',
        shortcutIconCls: 'pref-shortcut-icon',
        text: '桌面设置',
        tooltip: '<b>桌面设置</b><br />在这里更改你的桌面设置'
    }
});