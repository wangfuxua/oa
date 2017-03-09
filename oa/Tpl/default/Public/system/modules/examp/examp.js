Ext.onReady(function(){
        var sm = new Ext.grid.CheckboxSelectionModel();
          alert(sm)
        var cm = new Ext.grid.ColumnModel([
        //new Ext.grid.RowNumberer(),
        sm,
        {header:"用户名",dataIndex:"USER_ID"},
        {header:"姓名",dataIndex:"USER_NAME"},
        {header:"性别",dataIndex:"SEX"},
        {header:"生日",dataIndex:"BIRTHDAY"},
        {header:"部门",dataIndex:"DEPT_ID "}
        
        ]);
        
        cm.defaultSortable = true;

        var ds = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({url:"http://www.newoa.com/examp.php"}),
            reader: new Ext.data.JsonReader({
                totalProperty: "totalProperty",
                root: "results"
            }, [
            {name: "USER_ID"},
            {name: "USER_NAME"}
            ])
        });

        var grid = new Ext.grid.GridPanel({
            ds: ds,
            cm: cm,
            sm: sm,
            bbar: new Ext.PagingToolbar({
                pageSize: 10,
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
            //height:500,
            //width:860,
            autoWidth:true,
            autoHeight: true
        });
        ds.load({params:{start:0,limit:10}});
})