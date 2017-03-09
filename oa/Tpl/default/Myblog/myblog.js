var myblog,absform,ds;
Ext.override(QoDesk.MyblogWindow, {
	////////////////////////////////
	//var initWin,showWin,closeWin,save,edit,reset,remove,create,createWin,createForm;
	initWin:function(width,height,title){
		var winManager = this.app.getDesktop().getManager();
		if(!win){
			var win=new Ext.Window({
				bodyStyle:'padding:10px',
				layout:'fit',
				title:title,
				width:width, height:height, closeAction:'hide',
				plain: true, autoScroll:1, resizable:false, frame: true, header :false,
				iconCls: 'icon-addblog', cls: 'email-form',
			
				buttons:[
					{text:"保存", scope:this, handler:this.save},
					{text:"清空", scope:this, handler:this.reset},
					{text:"关闭", scope:this, handler:function(){win.hide();	}}
				],
				items:[this.fp],
				manager: winManager
			})
		};
		return win;
	},
	showWin:function(){
		if(!this.win){
			if(!this.fp){
				this.fp=this.createForm();
			}
		this.win=this.createWin();
		//this.win.on("close",function(){this.win=null;this.fp=null;this.store.reload();},this);
		}
		//窗口关闭时，数据重新加载
		this.win.show();
	},
	create:function(){this.showWin();this.reset();},
	closeWin:function(){
		if(this.win)this.win.close();
		this.win=null;
		this.fp=null;
		//this.store.reload();  
	},
	
	save:function(){
		var id=this.fp.form.findField("DIA_ID").getValue();		
		this.fp.form.submit({
				waitMsg:'正在保存。。。',
				url:'/index.php/myblog/insert',
	            
	            method:'POST',
	            success:function(form_instance_create, action){
				Ext.MessageBox.alert('提示', '保存成功');
	           	this.closeWin();
	           	ds.reload();          	
	            },
				failure:function(form_instance_create, action){
				Ext.MessageBox.alert('提示', '保存失败');  	
	            },
	            scope:this
		});	
	},
	edit:function(){
		if(myblog.selModel.hasSelection()){   
				var records = myblog.selModel.getSelections();//得到被选择的行的数组
				var recordsLen = records.length;//得到行数组的长度
				if( recordsLen>1){
				Ext.Msg.alert("系统提示信息","请选择其中一项进行编辑！");
				}//一次只给编辑一行
				else{
                        var record=myblog.getSelectionModel().getSelected();//获取选择的记录集
	                    var id=record.get("id");
						//alert(record);
	                    this.showWin();
	                    this.fp.form.loadRecord(record); //往表单（fp.form）加载数据
	                }
			    } else {   
                 Ext.Msg.alert("提示","请先选择要编辑的行!");
				 
                }
	},
	reset:function(){if(this.win)this.fp.form.reset();},
	remove:function(){
		var selectedKeys = myblog.selModel.selections.keys; //returns array of selected rows ids only
		if(selectedKeys.length > 0){
			Ext.MessageBox.confirm('提示','您确实要删除选定的记录吗？'+selectedKeys,myblog.onRemoveQuestion,myblog);
		}else{
			Ext.MessageBox.alert('提示','请至少选择一条记录！');
		}//end
	},
	

	createWin:function(){return this.initWin(600,450,"日志管理");},
	createForm:function(){
		var formPanel=new Ext.form.FormPanel({
			frame:true,
			labelWidth:40,
			border: true,
			defaultType: 'textfield',
			items:[{
				xtype:'fieldset',
				//title:'基本信息',
				autoHeight:true,
				border:false,
				items:[{
					layout:'column',
	                border:false,
	                defaults:{border:false},
	                items:[{
							columnWidth:1,
							layout:'form',
							defaultType:'textfield',
							items:[{
								fieldLabel:'标题',
								name:'DIA_ID',
								width:480
							}]
					},{
	                    	columnWidth:.5,
	                        layout:'form',
	                        defaultType:'textfield',
	                        defaults:{width:120},
							
	                        items:new Ext.form.DateField({name: 'DIA_DATE',fieldLabel:'日期',width:190,allowBlank:false,format:'Y-m-d H:i:s'})
					},{
	                    	columnWidth:.5,
	                        layout:'form',
	                        defaultType:'textfield',
	                        defaults:{width:104},
	                        items:new Ext.form.ComboBox({
									fieldLabel: '类型',
									hiddenName:'DIA_TYPE',
									store: new Ext.data.SimpleStore({
										fields: ['abbr', 'DIA_TYPE'],
										data : 	[	
											['1', '工作日志', '工作日志注释'],
											['2', '个人日志', '个人日志注释']
										]
									}),
									valueField:'abbr',
									displayField:'DIA_TYPE',
									typeAhead: true,
									mode: 'local',
									triggerAction: 'all',
									emptyText:'请选择日志类型',
									selectOnFocus:true,
									width:50,

								})
	                }]
				}]
			},{
				xtype:'fieldset',
				title:'内容',
				autoHeight:true,
				items:
				new Ext.form.HtmlEditor({
					id:'CONTENT',
					//deferHeight:true,
					createLinkText:'添加您的URL地址',
					width:530,
					height:220,
					hideLabel:true,
					buttonTips:{
						bold : {
							title: 'Bold (Ctrl+B)',
							text: '文字加粗',
							cls: 'x-html-editor-tip'
						},
						italic : {
							title: 'Italic (Ctrl+I)',
							text: '斜体字',
							cls: 'x-html-editor-tip'
						},
						underline:{
							title:'Underline (Ctrl+U)',
							text:'给字体加下划线'
						}
					}
				})
			}] 
		});
		return formPanel;
	},
	////////////////////////////////
    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('myblog-win');
    		ds =new Ext.data.Store({
				proxy: new Ext.data.HttpProxy({url:'/index.php/myblog/index'}),
				reader: new Ext.data.JsonReader({
					totalProperty: "totalProperty",
                    root: "results"
				}, [
				    {name: 'DIA_ID', mapping: 'DIA_ID'},
					{name: 'DIA_DATE', mapping: 'DIA_DATE'},
					{name: 'DIA_TYPE'},
					{name: 'CONTENT'}
				])
		});
		if(!win){

			ds.load({params:{start:0,limit:15}});
				
		    var change = function (val){
		        if(val == 1){
		            return '<span style="color:green;">' + '工作日志' + '</span>';
		        }else if(val == 2){
		            return '<span style="color:red;">' + '个人日志' + '</span>';
		        }
		        return val;
		    };
			var titlink = function(val){
				return '<a href="">'+val+'</a>';
			};

			/* ===== Start ===== */  
			/* ===== End ===== */
/*--------------添加日志---------------*/
	    
    var NewBlog=function(){
		var winManager = this.app.getDesktop().getManager();
		var absform = new Ext.form.FormPanel({
			baseCls: 'x-plain',
			layout:'absolute',
			url:'/index.php/myblog/insert',
			border: true,
			defaultType: 'textfield',
			items: [
				{x: 0,y: 5,xtype: 'label',text: '日期选择'},
			    new Ext.form.DateField({name: 'DIA_DATE',width:190,x:55,y:0,allowBlank:false,format:'Y-m-d'}),
				{x:280,y:5,xtype:'label',text:'日志分类'},
				new Ext.form.ComboBox({
					fieldLabel: 'State',
					hiddenName:'DIA_TYPE',
					store: new Ext.data.SimpleStore({
						fields: ['DIA_TYPE', 'State'],
						data : 	[	
							['1', '工作日志'],
							['2', '个人日志']
						]
					}),
					valueField:'DIA_TYPE',
					displayField:'State',
					typeAhead: true,
					mode: 'local',
					triggerAction: 'all',
					emptyText:'请选择日志类型',
					selectOnFocus:true,
					width:190,
					x:335,
					y:0
				}),
				{x: 0, y: 32,	xtype: 'label',  text: '日志标题:'},
				{x: 55,y: 27,	name: 'SUBJECT', anchor: '100%' },
				new Ext.form.HtmlEditor({
					id:'CONTENT',
					x:0,
					y:54,
					deferHeight:true,
					createLinkText:'添加您的URL地址',
					width:664,
					height:335,
					buttonTips:{
						bold : {
							title: 'Bold (Ctrl+B)',
							text: '文字加粗',
							cls: 'x-html-editor-tip'
						},
						italic : {
							title: 'Italic (Ctrl+I)',
							text: '斜体字',
							cls: 'x-html-editor-tip'
						},
						underline:{
							title:'Underline (Ctrl+U)',
							text:'给字体加下划线'
						}
					}
				})
			]
		});
		var formwin;
        
		if(!formwin){
			formwin = new Ext.Window({
            	bodyStyle:'padding:10px',
                layout:'fit',
                width:700,
                height:500,
                closeAction:'hide',//'hide'视觉上通过偏移到零下(negative)的区域的手法来隐藏,这样使得 window可通过#show 的方法再显示. 
                plain: true,//True 表示为渲染window body的背景为透明的背景,这样看来window body 与边框元素(framing elements)融为一体,
				autoScroll :1,
				resizable:false,//True 表示为允许用户从window的四边和四角改变window的大小(默认为 true). 
				iconCls: 'icon-addblog',
				title:'添加日志',
				cls: 'email-form',
				frame: true,
				header :false,
				items: absform,
				bbar:[{
					text:'提交',
					iconCls: 'icon-save',
					handler:function(){
								
		                  if (absform.form.isValid()) {
		                      absform.form.submit( {
		                                params : {
		                                    action:'submit',
		                                },
		                                success:function(){
		                                	alert("添加成功");
		                                	//saveComplete('完成', '保存成功.');
				                            ds.load( {
				                                params : {
				                                    start : 0,
				                                    limit : 15
				                                }
				                            });
		                                },
		                                failure:function(){
		                                	
		                                	alert("添加失败");
		                                },
		                        });

		                }else{
		                	Ext.MessageBox.alert('信息', '请填写完成再提交!');
		                }
		                
		                //ds.load(); 
                        formwin.hide();
					}
				},'-',{
					text:'取消',
					handler  : function(){
                        			formwin.hide();
                    			}
				}],
				tbar: [{
					text: '保存',
					iconCls: 'icon-save'
				},'-','->',{
					text: '上传附件',
					iconCls: 'icon-attach'
				}],
                manager: winManager,
                modal: true//True 表示为当window显示时对其后面的一切内容进行遮罩,false表示为限制 对其它UI元素的语法 (默认为 false). 
			}) //end for this.forwin= new Ext.Window

		}	// end for if(!this.formwin)
		
		formwin.show()
};
/*------------------显示列表-----------------*/
			var sm = new Ext.grid.CheckboxSelectionModel();
			var viewConfig=Ext.apply({forceFit:true},this.gridViewConfig);  
        	myblog = new Ext.grid.GridPanel({
        		
        		conn:new Ext.data.Connection(),
        		 
				id: 'company-form',
				border:false,
				ds: ds, 
               	//sm:sm, 
				cm: new Ext.grid.ColumnModel([
					new Ext.grid.RowNumberer(),
					sm,
					{header: "ID", width: 30, sortable: true, dataIndex: 'DIA_ID'},
					{header: "日期", width: 120, sortable: true, dataIndex: 'DIA_DATE'},
					{header: "日志类型", width: 70, sortable: true, renderer: change,dataIndex: 'DIA_TYPE'},
					{header: "内容", width:500,dataIndex: 'CONTENT',renderer:titlink}
					
				]),
				//selModel:true,
				shadow: false,
				//shadowOffset: 0,
			    bbar: new Ext.PagingToolbar({
	                pageSize: 15,
	                store: ds,
	                displayInfo: true,
	                displayMsg: "显示第 {0} 条到 {1} 条记录，一共 {2} 条",
	                emptyMsg: "没有记录",
	                beforePageText:'第',
	                afterPageText:'页 共{0}页',
	                firstText:'首页',
	                prevText:'上一页',
	                nextText:'下一页',
	                lastText:'尾页',
	                refreshText:'刷新'
	            }),
            
				tbar: [{
					text:'添加日志',
					tooltip:'添加一个新的日志',
					iconCls:'demo-myblog-add',
					scope: this,
					handler: this.create
					}, '-', {
					text:'修改日志',
					tooltip:'修改选定的日志',
					iconCls:'demo-myblog-option',
					scope: this,
					handler:this.edit
					
					},'-',{
					text:'删除日志',
					tooltip:'删除选中的日志',
					iconCls:'demo-myblog-remove',
					handler:this.remove
				}],
				viewConfig: viewConfig,
			    onRemoveQuestion: function(_btn){
					if (_btn == "yes") {
						var _rs = myblog.getSelectionModel().getSelected();
						myblog.getStore().remove(_rs);
						if (_rs.get("DIA_ID") != "") {
							myblog.conn.un("requestcomplete", myblog.onSaveInsertData, myblog);
							myblog.conn.request({
								url: "/index.php/myblog/delete",
								params: {
									id: _rs.get("DIA_ID")
								}
							});
						}
						else {
							myblog.inserted.remove(_rs);
							myblog.getStore().modified.remove(_rs);
						}
					}
				}
				
			});
			//sm.on('rowselect',function(){}, this);
			//myblog.on("celldblclick",this.edit,this);
			myblog.addListener('celldblclick', this.edit,this);
            win = desktop.createWindow({
                id: 'myblog-win',
                title:'日志列表',
                width:840,
                height:500,
                iconCls: 'myblog-icon',
                shim:false,
                animCollapse:false,
                constrainHeader:true,
				layout: 'fit',
                items: myblog,
                taskbuttonTooltip: '<b>日志列表窗口</b><br />这里是日志的列表'
            })
        };
        win.show()
    }
})
