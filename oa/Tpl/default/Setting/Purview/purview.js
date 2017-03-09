Ext.override(QoDesk.PurviewWindow, {

	createWindow : function(){
		var desktop = this.app.getDesktop();
		var win = desktop.getWindow('purview-win');
		// ==========| Start for form panel |==========
		var tabForm=new Ext.FormPanel({
			border:false,
			labelWidth: 75,
			//height:'90%',
			items:{
				xtype:'tabpanel',
				activeTab: 0,
				tabPosition :'top',
				autoHeight:true,
				
				//width:'100%',
				autoWidth:true,
				//tabWidth:120,
				//resizeTabs:true,
				//minTabWidth: 100,
				//monitorResize:true,
				enableTabScroll:true,
				
				border:false,
				deferredRender:false,//设为false，提交的时候才会提交所有tab里的field   
				//labelWidth:0,
				items:[
					{title:'我的办公桌',autoHeight:true,layout:'fit', items: formDesktop},
					{title:'公共事务',autoHeight:true,layout:'fit', items: formPublic },
					{title:'项目',autoHeight:true,layout:'fit', items:formProject},
					{title:'ISO9000管理',autoHeight:true, items:formISO9000},
					{title:'交流',autoHeight:true,items:formCommunicate},
					{title:'资产管理',autoHeight:true,items:formAsset},
					{title:'商业',autoHeight:true,items:formBusiness},
					{title:'系统',autoHeight:true,items:formSetting},
					{title:'附件',autoHeight:true,items:formWidgets}
				]
			},
			// 为form添加按钮了，在formpanel的buttons属性中我们加入了一个保存按钮和取消按钮
			buttons:[{
				// 在buttons里定义的按钮默认是Ext.Button，所以按钮的属性定义可以查看Ext.Button的API。在这里两个按钮都没用到其它属性，只是设置了显示文本（text）和单击事件。
				text : '保存',
				handler : function() {
					if (tabForm.form.isValid()) {
						this.disabled = true;
						tabForm.form.doAction('submit', {
							url : '/index.php/purview/submit',
							method : 'post',
							params : '',
							success : function(form, action) {
								Ext.Msg.alert('操作',
									action.result.data);
								this.disabled = false;
							},
							failure : function() {
								Ext.Msg.alert('操作', '保存失败！');
								this.disabled = false;
							}
						});
					}
				}
			},
			{text : '取消',handler : function() {tabForm.form.reset();}}
			]
		});
		// ==========| End |==========
		if(!win){
			var winWidth = desktop.getWinWidth() / 1.1;
			var winHeight = desktop.getWinHeight() / 1.1;
			win= desktop.createWindow({
				id:'purview-win',
				layout:'border',
				title:'权限设置',
				width:750,
				height:500,
				iconCls: 'purview-icon',
				shim:false,
				animCollapse:false,
				constrainHeader:true,
				minimizable:true,
    			maximizable:true,

				items:[{
					region:'west',
					autoScroll:true,
					collapsible:true,
					cmargins:'0 0 0 0',
					margins:'0 0 0 0',
					split:true,
					title:'角色列表',
					html:'<div id="mygrid"></div>',
					width:parseFloat(winWidth*0.3) < 201 ? parseFloat(winWidth*0.3) : 200
				},{
					region:'center',
					layout: 'fit',
					widht:'100%',
					items:tabForm
				}],
				taskbuttonTooltip: '<b>权限设置窗口</b>'
			})
		}
		win.show();
	}
});

/* =====| Start for form items |===== */
/* ----------| 我的办公桌 |----------*/
/* 溢出部分滚动显示: autoScroll:true
 * 默认关闭: collapsed:true,
 * 展开的箭头：collapsible:true, 
 * 展开的checkbox：checkboxToggle:true,
 * columnWidth:0.3
 */

var formDesktop={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px', border:false,
	items:
	[{
		title:'  公共属性',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false,style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'公告通知',	 name:'aa[]',inputValue:'1',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'个人考勤',	 name:'aa[]',inputValue:'2',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'日程安排',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'工作日志',	 name:'aa[]',inputValue:'4',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'通讯薄',	 	 name:'aa[]',inputValue:'5',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'个人文件柜',	 name:'aa[]',inputValue:'6',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'个人设置',	 name:'aa[]',inputValue:'7',style:''}
			]
		}]
	},{
		title:'电子邮件',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'内部邮件',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'Internet邮件',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'短信息',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'内部短信息',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'个人手机短信',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'公费手机短信',name:'',inputValue:'',style:''}
			]
		}]
	}] // End for items
};
/* ----------| formDesktop End |---------- */
/* ----------| 公共事物 |----------*/
var formPublic=	{
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px;clear:both', border:false,
	items:
	[{
		title:'公共属性',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'工作流', name:'bb', inputValue : '1',style:'' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'公告通知管理',name:'bb',inputValue:'2',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'新闻管理',name:'bb',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'考勤管理',name:'',inputValue:'4',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'员工日程安排查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'员工工作日志查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'投票',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'公共通讯薄',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'工作计划',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'工作计划查询', name:'', inputValue : '1',style:'' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'工作计划管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'工作计划类型设置',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'人事档案',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'人事档案管理', name:'', inputValue : '1',style:'' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'人事档案查询',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'工资上报',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'工资上报管理', name:'', inputValue : '1',style:'' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'工资上报录入',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'图书管理',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'图书类别定义', name:'', inputValue : '1',style:'' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'图书信息录入管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'图书查询',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'会议申请与安排',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'会议申请', name:'', inputValue : '1',style:'float:left' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'会议查询',name:'',inputValue:'',style:'float:left'},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'会议管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'会议室设置',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'车辆申请与安排',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'车辆使用申请', name:'', inputValue : '1',style:'float:left' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'车辆使用查询',name:'',inputValue:'',style:'float:left'},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'车辆使用管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'车辆维护管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'车辆信息管理',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'组织机构信息',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'单位信息查询', name:'', inputValue : '1',style:'float:left' },
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'部门信息查询',name:'',inputValue:'',style:'float:left'},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'员工信息查询',name:'',inputValue:'',style:''}
			]
		}]
	}]
};
/* ----------| formPublic End |---------- */
/* ----------| 项目 |----------*/
var formProject={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px', border:false,
	items:
	[{
		title:'  公共属性',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false,style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'项目管理流程',	 name:'aa[]',inputValue:'1',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'项目统计',	 name:'aa[]',inputValue:'2',style:''}
			]
		}]
	},{
		title:'项目申报',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.2,boxLabel:'新建申报',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.2,boxLabel:'申报初审',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.2,boxLabel:'申报终审',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.2,boxLabel:'申报复核',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'项目实施',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'新立项',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'项目维护',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'审核文件',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'项目审批',name:'',inputValue:'',style:''}
			]
		}]
	}] // End for items
};
/* ----------| formProject End |---------- */
/* ----------| ISO9000 |----------*/
var formISO9000={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px 10px 10px 20px', border:false,
	items:[{
		layout:'fit', columnWidth:3, border:false,style:'padding:0 0 0 10',
		items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'新增ISO文件',	 name:'aa[]',inputValue:'1',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'查阅ISO文件',	 name:'aa[]',inputValue:'2',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'审核ISO文件',	 name:'aa[]',inputValue:'3',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'ISO文件分发',	 name:'aa[]',inputValue:'4',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:' 历史ISO文件查询',	 	 name:'aa[]',inputValue:'5',style:''}
		]
	}] // End for items
};
/* ----------| formISO9000 End |---------- */
/* ----------| 交流 |----------*/
var formCommunicate={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px', border:false,
	items:
	[{
		title:'  公共属性',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false,style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'公共文件柜',	 name:'aa[]',inputValue:'1',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'网络硬盘',	 name:'aa[]',inputValue:'2',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'图片管理',	 name:'aa[]',inputValue:'3',style:''}
			]
		}]
	},{
		title:'讨论区',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'简单讨论区',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'超级论坛',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'网络会议',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'文本网络会议管理',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'文本网络会议',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'语音视频会议',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'聊天室',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'文本聊天室',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'语音聊天室',name:'',inputValue:'',style:''}
			]
		}]
	}] // End for items
};
/* ----------| formCommunicate End |---------- */
/* ----------| 资产管理 |----------*/
var formAsset={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px', border:false,
	items:
	[{
		title:'固定资产 ',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false,style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'固定资产管理',	 name:'aa[]',inputValue:'1',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'固定资产查询',	 name:'aa[]',inputValue:'2',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'固定资产审核',	 name:'aa[]',inputValue:'3',style:''}
			]
		}]
	},{
		title:'办公设备 ',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'办公设备管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'办公设备查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'办公设备审核',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'低值易耗品',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'低值易耗品管理',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'低值易耗品查询',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'低值易耗品审核',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'低值易耗品领用',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'低值易耗品领用审核',name:'',inputValue:'',style:''}
			]
		}]
	}] // End for items
};
/* ----------| 商业 |----------*/
var formBusiness={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px', border:false,
	items:
	[{
		title:'客户关系',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false,style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'客户基本信息管理',	 name:'aa[]',inputValue:'1',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'联系人信息管理',	 name:'aa[]',inputValue:'2',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'交往信息管理',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'客户关系综合查询',	 name:'aa[]',inputValue:'3',style:''}
			]
		}]
	},{
		title:'产品销售',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'产品信息管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'产品信息查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'销售记录管理',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'供应商',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'供应商基本信息管理',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'供应商综合查询',name:'',inputValue:'',style:''}
			]
		}]
	}] // End for items
};
/* ----------| formBusiness End |---------- */
/* ----------| 系统 |----------*/
var formSetting={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px', border:false,
	items:
	[{
		title:'公共属性',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false,style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'公费手机短信设置',	 name:'aa[]',inputValue:'1',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'界面设置',	 name:'aa[]',inputValue:'2',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'菜单设置',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'数据库管理',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'系统日志管理',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'系统资源管理',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'系统访问控制',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'系统安全设置',	 name:'aa[]',inputValue:'3',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'系统信息',	 name:'aa[]',inputValue:'3',style:''}
			]
		}]
	},{
		title:'组织机构设置',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'单位管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'部门管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'用户管理',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'角色与权限管理',name:'',inputValue:'',style:''}				
			]
		}]
	},{
		title:'公共事务设置',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'工作流设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'考勤设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'投票管理设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'公共网址设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'公共通讯簿设置',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'项目事务设置',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'项目管理设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'项目实施设置',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'交流设置',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'简单讨论区设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'超级论坛设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'文本聊天室设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'公共文件柜设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'网络硬盘设置',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'图片管理设置',name:'',inputValue:'',style:''}
			]
		}]
	}] // End for items
};
/* ----------| formSetting End |---------- */
/* ----------| 附件 |----------*/
var formWidgets={
	layout:'form',height:390,autoWidth:true, autoScroll:true, style:'padding:10px', border:false,
	items:
	[{
		title:'公共属性',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false,style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'游戏',	 name:'aa[]',inputValue:'1',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'万年历',	 name:'aa[]',inputValue:'2',style:''},
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'世界时间',	 name:'aa[]',inputValue:'3',style:''}
			]
		}]
	},{
		title:'实用信息',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
				{xtype:'checkbox',columnWidth:0.3,boxLabel:'电话区号查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'邮政编码查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'列车时刻查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'公交线路查询',name:'',inputValue:'',style:''},
				{xtype:'checkbox',columnWidth:0.7,boxLabel:'法律法规查询',name:'',inputValue:'',style:''}
			]
		}]
	},{
		title:'供应商',
		xtype:'fieldset', autoHeight:true, collapsible:true, border:false,
		items:[{
			layout:'column', border:false, style:'padding:0 0 0 10',
			items:[
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'供应商基本信息管理',name:'',inputValue:'',style:''},
			{xtype:'checkbox',columnWidth:0.3,boxLabel:'供应商综合查询',name:'',inputValue:'',style:''}
			]
		}]
	}] // End for items
};
/* ----------| formWidgets End |---------- */