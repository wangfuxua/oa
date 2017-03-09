/* Override the module code here.
 * This code will be Loaded on Demand.
 */

Ext.override(QoDesk.AccordionWindow, {
	
    createWindow : function(){
    	var desktop = this.app.getDesktop();
        var win = desktop.getWindow('acc-win');
        if(!win){
            win = desktop.createWindow({
                id: 'acc-win',
                title: '内部通讯',
                width:250,
                height:400,
                iconCls: 'acc-icon',
                shim:false,
                animCollapse:false,
                constrainHeader:true,
                maximizable: false,
                taskbuttonTooltip: '<b>IM信息</b>',

                tbar:[{
                    tooltip:'<b>设置</b><br />设置在线状态!',
                    iconCls:'demo-acc-connect'
                },'-',{
                    tooltip:'添加新用户',
                    iconCls:'demo-acc-user-add'
                },' ',{
                    tooltip:'删除选择的用户',
                    iconCls:'demo-acc-user-delete'
                }],

                layout: 'accordion',
                layoutConfig: {
                    animate:false
                },

                items: [
                    new Ext.tree.TreePanel({
                        id:'im-tree',
                        title: '在线用户',
                        loader: new Ext.tree.TreeLoader(),
                        rootVisible:false,
                        lines:false,
                        autoScroll:true,
                        useArrows: true,
                        tools:[{
                            id:'refresh',
                            on:{
                                click: function(){
                                    var tree = Ext.getCmp('im-tree');
                                    tree.body.mask('更新...', 'x-mask-loading');
                                    tree.root.reload();
                                    tree.root.collapse(true, false);
                                    setTimeout(function(){ // mimic a server call
                                        tree.body.unmask();
                                        tree.root.expand(true, true);
                                    }, 1000);
                                }
                            }
                        }],
                        root: new Ext.tree.AsyncTreeNode({
                            text:'在线',
                            children:[{
                                text:'技术部',
                                expanded:true,
                                children:[{
                                    text:'成员一',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'成员二',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'成员三',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'成员四',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'成员五',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'成员六',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'成员七',
                                    iconCls:'user',
                                    leaf:true
                                }]
                            },{
                                text:'其他部门',
                                expanded:false,
                                children:[{
                                    text:'人员一',
                                    iconCls:'user-girl',
                                    leaf:true
                                },{
                                    text:'人员二',
                                    iconCls:'user-girl',
                                    leaf:true
                                },{
                                    text:'人员三',
                                    iconCls:'user-kid',
                                    leaf:true
                                },{
                                    text:'人员四',
                                    iconCls:'user-kid',
                                    leaf:true
                                }]
                            }]
                        })
                    })
                ]
            });
        }
        win.show();
    }
});