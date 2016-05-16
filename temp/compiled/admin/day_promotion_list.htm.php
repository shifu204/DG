<?php echo $this->fetch('easyui_page_header.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'ajaxfileupload.js')); ?>
<div id="promotion_tool">
    <a href="javascript:add()" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true">添加</a>
    <a href="javascript:del()" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true">删除</a>
    <a href="javascript:save()" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true">保存</a>
    <a href="javascript:reject()" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true">取消更改</a>
    <a href="javascript:doSearch()" class="easyui-linkbutton" plain="true">搜索</a>  
</div>
<table id="promotion"></table>
<div id="promotion_edit"></div>
<div id="add_form"></div>
<script type="text/javascript">

$("#promotion").datagrid({
    title:'天天特价活动列表',
    pagination:true,
    pageSize:20,
    url:'day_promotion.php?act=get_promotion',
    method:'post',
    singleSelect:true,
    toolbar:'#promotion_tool',
    fitColumns:true,
    columns:[[    
        {field:'act_id',title:'编号',width:100,align:'center'},    
        {field:'act_name',title:'名称',width:300,align:'center'},    
        {field:'act_desc',title:'描述',width:200,align:'center'},
        {field:'start_time',title:'开始时间',width:200,align:'center'},    
        {field:'end_time',title:'结束时间',width:200,align:'center'},    
        {field:'is_finished',title:'强制结束',width:200,align:'center',
            formatter:function(value,row,index){
                if(value == 0) {
                    return "否";
                } else if(value == 1){
                    return "是";
                } else if(value == 99){
                    return "正在运行";
                }
            }
        },
        {field:'loop',title:'循环时间',width:200,align:'center',
            formatter:function(value,row,index){
                return value+"天";
            }
        },    
        {field:'sort_order',title:'排序',width:200,align:'center'},
        {field:'last_time',title:'上次活动时间',width:200,align:'center'}
    ]],
    onDblClickRow:function(index,data){
        openEditor(index,data);
    }
});
function add(){
    var time = new Date().getTime();
    $("#add_form").dialog({
        title:'添加天天特价活动',
        width:800,
        maximizable:true,
        minimizable:true,
        resizable:true,
        closable:true,
        modal:true,
        top:50,
        shadow:false,
        href:'day_promotion.php?act=add&time='+time,
        buttons:[
            {
                text:'保存',
                iconCls:'icon-ok',
                handler:function(){
                    $('#add_promotion_form').form('submit', {    
                        url:'day_promotion.php?act=add_do',    
                        onSubmit: function(){    
                            var isValid = $(this).form('validate');
                            if (!isValid){
                                    $.messager.progress('close');	// 如果表单是无效的则隐藏进度条
                            }
                            return isValid;	// 返回false终止表单提交
                        },    
                        success:function(data){    
                            if(data == 1) {
                                $.messager.alert('操作信息','添加成功','info'); 
                                $("#add_form").dialog('close');
                                $("#promotion").datagrid("reload");
                            } else {
                                $.messager.alert('操作信息','添加失败','error'); 
                            }
                        }    
                    }); 
                }
            },
            {
                text:'取消',
                iconCls:'icon-cancel',
                handler:function(){
                    $("#add_form").dialog('close');
                }
            }
        ]
    });
}


function openEditor(index,data){
    $("#promotion_edit").dialog({
        title:"活动："+data.act_name,
        width:1024,
        modal:true,
        top:30,
        maximizable:true,
        minimizable:true,
        shadow:false,
        href:"day_promotion.php?act=promotion_edit&act_id="+data.act_id,
        buttons:[
            {
                text:"保存商品",
                iconCls:"icon-save",
                handler:function(){
                    save_edit();
                }
            },
            {
                text:"保存图片",
                iconCls:"icon-save",
                handler:function(){
                    upload_files();
                }
            },
            {
                text:"取消",
                iconCls:"icon-cancel",
                handler:function(){
                    $("#promotion_edit").dialog("close");
                }
            }
        ]
    });
}

function save_edit(){
    var act_id = $("#act_id").val();
    var act_name = $("#act_name").val();
    var act_desc = $("#act_desc").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
    var loop = $("#loop").combobox("getValue");
    var is_finished = $("#is_finished").combobox("getValue");
    var sort_order = $("#sort_order").val();
    var goods = $("#promotion_goods").datagrid("getChanges");
    var inserted = $("#promotion_goods").datagrid('getChanges','inserted');
    var updated = $("#promotion_goods").datagrid('getChanges','updated');
    var deleted =$("#promotion_goods").datagrid('getChanges','deleted');
    var rows = $("#promotion_goods").datagrid('getRows');
    for ( var i = 0; i < rows.length; i++) {
        $("#promotion_goods").datagrid('endEdit', i);
    }
    var send = {
        act_id:act_id,
        act_name:act_name,
        act_desc:act_desc,
        start_time:start_time,
        end_time:end_time,
        loop:loop,
        is_finished:is_finished,
        sort_order:sort_order,
        inserted:inserted,
        updated:updated,
        deleted:deleted
    }
    $.post('day_promotion.php?act=edit_do',send,function(data){                                       
        if(data == 1) {     
            //更新图片
            //$("#goods_form").submit();
            $.messager.alert('提示消息','操作成功！','info');               
            $("#promotion").datagrid("reload");
            $("#promotion_goods").datagrid("reload");
        } else {
            $.messager.alert('提示消息','网络出错！','error');
        }
    });     
}
var inputs;
var nowinput;

function upload_files(){
    inputs = $("#goods_form input[type='file']");
    nowinput = 0;
    ajax_upload();
}

function ajax_upload(){
    if(nowinput < inputs.length){
        var fileid = $(inputs[nowinput]).attr('id');
        $.ajaxFileUpload
        (
            {
                url: 'day_promotion.php?act=upload_files', //用于文件上传的服务器端请求地址
                secureuri: false, //是否需要安全协议，一般设置为false
                fileElementId:fileid, //文件上传域的ID
                dataType: 'text', //返回值类型 一般设置为json
                success: function (data, status)  //服务器成功响应处理函数
                {                
                   setTimeout("ajax_upload()", 2000);
                },
                error: function (data, status, e)//服务器响应失败处理函数
                {
                    alert(e);
                }
            }
        );
        nowinput++;      
    } else {
        $("#promotion_goods").datagrid("reload");
        $.messager.alert('提示消息','操作完成。','info');
    }
}

function del(){
    var row = $("#promotion").datagrid('getSelected');
    if(row){
        var index = $("#promotion").datagrid("getRowIndex",row);
        $("#promotion").datagrid("deleteRow",index);
    }
}

function save(){
    //获取以删除的行
    var deleted =$("#promotion").datagrid('getChanges','deleted');
    $.post('day_promotion.php?act=delete_do',{deleted:deleted},function(data){                                       
        if(data == 1) {     
            $.messager.alert('提示消息','操作成功！','info');               
            $("#promotion").datagrid("reload");
        } else {
            $.messager.alert('提示消息','网络出错！','error');
        }
    }); 
}

function reject(){
    $("#promotion").datagrid("rejectChanges");
}

</script>

</body>
</html>