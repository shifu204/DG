/* easyui 输入的两次密码一致*/
// extend the 'equals' rule   
$.extend($.fn.validatebox.defaults.rules, {   
    equals: {   
        validator: function(value,param){   
            return value == $(param[0]).val();   
        },   
        message: '两次输入不匹配。'  
    }   
});

 /*<input id="pwd" name="pwd" type="password" class="easyui-validatebox" data-options="required:true" />  
<input id="rpwd" name="rpwd" type="password" class="easyui-validatebox"    
 required="required" validType="equals['#pwd']" />  */
/*长度最少验证规则*/
$.extend($.fn.validatebox.defaults.rules, {   
    minLength: {   
        validator: function(value, param){   
            return value.length >= param[0];   
        },   
        message: '长度最少为{0}个字符。'  
    }   
}); 

/*easyui常用的验证规则*/
$.extend($.fn.validatebox.defaults.rules, {
    CHS: {
        validator: function (value, param) {
            return /^[\u0391-\uFFE5]+$/.test(value);
        },
        message: '请输入汉字'
    },
    ZIP: {
        validator: function (value, param) {
            return /^[1-9]\d{5}$/.test(value);
        },
        message: '邮政编码不存在'
    },
    QQ: {
        validator: function (value, param) {
            return /^[1-9]\d{4,10}$/.test(value);
        },
        message: 'QQ号码不正确'
    },
    mobile: {
        validator: function (value, param) {
            return /^((\(\d{2,3}\))|(\d{3}\-))?13\d{9}$/.test(value);
        },
        message: '手机号码不正确'
    },
    loginName: {
        validator: function (value, param) {
            return /^[\u0391-\uFFE5\w]+$/.test(value);
        },
        message: '登录名称只允许汉字、英文字母、数字及下划线。'
    },
    safepass: {
        validator: function (value, param) {
            return safePassword(value);
        },
        message: '密码由字母和数字组成，至少6位'
    },
    equalTo: {
        validator: function (value, param) {
            return value == $(param[0]).val();
        },
        message: '两次输入的字符不一至'
    },
    number: {
        validator: function (value, param) {
            return /^\d+$/.test(value);
        },
        message: '请输入数字'
    },
    idcard: {
        validator: function (value, param) {
            return idCard(value);
        },
        message:'请输入正确的身份证号码'
    }
});

/* 密码由字母和数字组成，至少6位 */
var safePassword = function (value) {
    return !(/^(([A-Z]*|[a-z]*|\d*|[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\\\/\'\"]*)|.{0,5})$|\s/.test(value));
}

var idCard = function (value) {
    if (value.length == 18 && 18 != value.length) return false;
    var number = value.toLowerCase();
    var d, sum = 0, v = '10x98765432', w = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2], a = '11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65,71,81,82,91';
    var re = number.match(/^(\d{2})\d{4}(((\d{2})(\d{2})(\d{2})(\d{3}))|((\d{4})(\d{2})(\d{2})(\d{3}[x\d])))$/);
    if (re == null || a.indexOf(re[1]) < 0) return false;
    if (re[2].length == 9) {
        number = number.substr(0, 6) + '19' + number.substr(6);
        d = ['19' + re[4], re[5], re[6]].join('-');
    } else d = [re[9], re[10], re[11]].join('-');
    if (!isDateTime.call(d, 'yyyy-MM-dd')) return false;
    for (var i = 0; i < 17; i++) sum += number.charAt(i) * w[i];
    return (re[2].length == 9 || number.charAt(17) == v.charAt(sum % 11));
}

var isDateTime = function (format, reObj) {
    format = format || 'yyyy-MM-dd';
    var input = this, o = {}, d = new Date();
    var f1 = format.split(/[^a-z]+/gi), f2 = input.split(/\D+/g), f3 = format.split(/[a-z]+/gi), f4 = input.split(/\d+/g);
    var len = f1.length, len1 = f3.length;
    if (len != f2.length || len1 != f4.length) return false;
    for (var i = 0; i < len1; i++) if (f3[i] != f4[i]) return false;
    for (var i = 0; i < len; i++) o[f1[i]] = f2[i];
    o.yyyy = s(o.yyyy, o.yy, d.getFullYear(), 9999, 4);
    o.MM = s(o.MM, o.M, d.getMonth() + 1, 12);
    o.dd = s(o.dd, o.d, d.getDate(), 31);
    o.hh = s(o.hh, o.h, d.getHours(), 24);
    o.mm = s(o.mm, o.m, d.getMinutes());
    o.ss = s(o.ss, o.s, d.getSeconds());
    o.ms = s(o.ms, o.ms, d.getMilliseconds(), 999, 3);
    if (o.yyyy + o.MM + o.dd + o.hh + o.mm + o.ss + o.ms < 0) return false;
    if (o.yyyy < 100) o.yyyy += (o.yyyy > 30 ? 1900 : 2000);
    d = new Date(o.yyyy, o.MM - 1, o.dd, o.hh, o.mm, o.ss, o.ms);
    var reVal = d.getFullYear() == o.yyyy && d.getMonth() + 1 == o.MM && d.getDate() == o.dd && d.getHours() == o.hh && d.getMinutes() == o.mm && d.getSeconds() == o.ss && d.getMilliseconds() == o.ms;
    return reVal && reObj ? d : reVal;
    function s(s1, s2, s3, s4, s5) {
        s4 = s4 || 60, s5 = s5 || 2;
        var reVal = s3;
        if (s1 != undefined && s1 != '' || !isNaN(s1)) reVal = s1 * 1;
        if (s2 != undefined && s2 != '' && !isNaN(s2)) reVal = s2 * 1;
        return (reVal == s1 && s1.length != s5 || reVal > s4) ? -10000 : reVal;
    }
};

//为datagrid扩展combogrid方法
$.extend($.fn.datagrid.defaults.editors, {
    combogrid: {
        init: function (container, options) {
            var input = $('<input type="text" class="datagrid-editable-input" />').appendTo(container);
            input.combogrid(options);
            return input;
        },
        destroy: function (target) {
            $(target).combogrid('destroy');
        },
        getValue: function (target) {
            return $(target).combogrid('getValue');
        },
        setValue: function (target, value) {
            $(target).combogrid('setValue', value);
        },
        resize: function (target, width) {
            $(target).combogrid('resize', width);
        }
    }
});

//扩展datagrid selectitem方法
$.extend($.fn.datagrid.defaults.editors, {
    selectitem: {
        init: function (container, options) {
            var input = $('<input type="text" class="datagrid-editable-input" onclick="'+options.functionName+'()" style="width:100%"/>').appendTo(container);
            return input;
        },
        getValue: function (target) {
            return $(target).val();
        },
        setValue: function (target, value) {
            $(target).val(value);
        }
    }
});
//扩展datagrid filebox方法
$.extend($.fn.datagrid.defaults.editors, {
    filebox: {
        init: function (container, options) {
            //options.tableid要获取数据的datagrid id， options.column 是datagrid的列名称
            var row = $(options.tableid).datagrid("getSelected");
            var column = options.column;
            var name = "files_"+row[column];
            var input = $('<input type="file" name="'+name+'"  id="'+name+'" />').appendTo(container);
            return input;
        },
        getValue: function (target) {
            return $(target).val();
        },
        setValue: function (target, value) {
            $(target).val(value);
        }
    }
});

function get_table_elements(formid) {
    formid = '#'+formid;
    var values = {};
    //获取所有input数据
    $.each($(formid + " :input"),function(){
        if(typeof($(this).attr("name"))!="undefined")  {
            //values +=$(this).attr("name")+"="+$(this).val()+"&";
            values[$(this).attr("name")] = $(this).val();
        }
    });
    return values;
}

//判断对象的长度
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

//调整iframe的高度，应该在iframe加载完成的时候调用
function reinitIframe(frameid){
    var iframe = document.getElementById(frameid);
    try{
        var bHeight = iframe.contentWindow.document.body.scrollHeight;
        var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
        var height = Math.max(bHeight, dHeight);
        iframe.height =  height;
        //alert(height);
    }catch (ex){

    }
}

//修改订单状态返回，根据返回结果提示客户端
function order_edit_status_alert(data) {
    if(data == 1) {
        $.messager.alert('更新提示','订单更新成功。','info');        
    } else if(data == -1) {
        $.messager.alert('更新提示','此订单已完成，请勿重复提交完成，以免导致数据错误。','error');
    } else if (data == -2) {
        $.messager.alert('更新提示','此订单已完成，请勿将其状态退回，以免导致数据错误。','error');                                       
     } else if (data == -3) {
        $.messager.alert('更新提示','库存不足。','error');
    } else if (data == -5){
        $.messager.alert('更新提示','不能修改别人的订单。','error');
    } else if (data == -6) {
        $.messager.alert('更新提示','这是您自己的订单哦，不要修改成别人的。','error');
    } else if (data == -7){
        $.messager.alert('更新提示','此订单已取消，不能更改。','error');
    } else if (data == -8){
        $.messager.alert('更新提示','此订单已出/入库，不能更改订单状态。','error');
    } else if (data == -9){
        $.messager.alert('更新提示','销售员不能将订单修改成已出库。','error');
    } else if (data == -10){
        $.messager.alert('更新提示','此订单不是已出库/已入库状态，不能修改为完成状态。','error');
    } else if (data == -11){
        $.messager.alert('更新提示','仓库员只能将销售订单修改为已出库状态、添加物流单号和运费。','error');
    } else if (data == -12){
        $.messager.alert('更新提示','已入库/出库，不能取消订单。','error');
    } else if (data == -13){
        $.messager.alert('库存提示','有部分产品库存不足，请检查库存。','error');
    } else {
        $.messager.alert('更新提示','修改失败。','error');
    }
}

(function ($) {
    $.extend($.fn.datagrid.methods, {
        beginEditCell: function (target, options) {
            return target.each(function () {
                beginEditCell(this, options);
            });
        },
        endEditCell: function (target, options) {
            return target.each(function () {
                endEditCell(this, options);
            });
        }
    });
})(jQuery);

(function ($) {

    //开启编辑单元格状态
    function beginEditCell(target, options) {

        var opts = $.data(target, "datagrid").options;
        var tr = opts.finder.getTr(target, options.index);
        var row = opts.finder.getRow(target, options.index);

//        //暂时还不知道该代码的含义,忽略使用
//        if (tr.hasClass("datagrid-row-editing")) {
//            return;
//        }
//        tr.addClass("datagrid-row-editing");

        _initCellEditor(target, options.index, options.field);
        _outerWidthOfEditable(target);
        //$.validateRow(target, options.index);暂时先不使用,不知道该方法作用
    }

    function _initCellEditor(target, _index, _field) {
        var opts = $.data(target, "datagrid").options;
        var tr = opts.finder.getTr(target, _index);
        var row = opts.finder.getRow(target, _index);

        tr.children("td").each(function () {
            var cell = $(this).find("div.datagrid-cell");
            var field = $(this).attr("field");

            if (field == _field) {//找到与传递参数相同field的单元格
                var col = $(target).datagrid("getColumnOption", field);
                if (col && col.editor) {
                    var editorType, editorOp;
                    if (typeof col.editor == "string") {
                        editorType = col.editor;
                    } else {
                        editorType = col.editor.type;
                        editorOp = col.editor.options;
                    }
                    var editor = opts.editors[editorType];
                    if (editor) {
                        var html = cell.html();
                        var outerWidth = cell._outerWidth();
                        cell.addClass("datagrid-editable");
                        cell._outerWidth(outerWidth);
                        cell.html("<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td></td></tr></table>");
                        cell.children("table").bind(
                            "click dblclick contextmenu",
                            function (e) {
                                e.stopPropagation();
                            });
                        $.data(cell[0], "datagrid.editor", {
                            actions: editor,
                            target: editor.init(cell.find("td"),
                                editorOp),
                            field: field,
                            type: editorType,
                            oldHtml: html
                        });
                    }
                }

                tr.find("div.datagrid-editable").each(function () {
                    var field = $(this).parent().attr("field");
                    var ed = $.data(this, "datagrid.editor");
                    ed.actions.setValue(ed.target, row[field]);
                });
            }
        });
    }

    //为可编辑的单元格设置外边框
    //来自jquery.easyui.1.8.0.js的 function _4d8()方法
    function _outerWidthOfEditable(target) {
        var dc = $.data(target, "datagrid").dc;
        dc.view.find("div.datagrid-editable").each(function () {
            var _this = $(this);
            var field = _this.parent().attr("field");
            var col = $(target).datagrid("getColumnOption", field);
            _this._outerWidth(col.width);
            var ed = $.data(this, "datagrid.editor");
            if (ed.actions.resize) {
                ed.actions.resize(ed.target, _this.width());
            }
        });
    }

    //关闭编辑单元格状态
    function endEditCell(target, options) {
        var opts = $.data(target, "datagrid").options;

        var updatedRows = $.data(target, "datagrid").updatedRows;
        var insertedRows = $.data(target, "datagrid").insertedRows;

        var tr = opts.finder.getTr(target, options.index);
        var row = opts.finder.getRow(target, options.index);

//        //与beginEditCell相呼应,暂时取消
//        if (!tr.hasClass("datagrid-row-editing")) {//行不能编辑时,返回
//            return;
//        }
//        tr.removeClass("datagrid-row-editing");

        var _535 = false;
        var _536 = {};
        tr.find("div.datagrid-editable").each(function () {
            var _537 = $(this).parent().attr("field");
            var ed = $.data(this, "datagrid.editor");
            var _538 = ed.actions.getValue(ed.target);
            if (row[_537] != _538) {
                row[_537] = _538;
                _535 = true;
                _536[_537] = _538;
            }
        });
        if (_535) {
            if (_45f(insertedRows, row) == -1) {
                if (_45f(insertedRows, row) == -1) {
                    updatedRows.push(row);
                }
            }
        }

        _destroyCellEditor(target, options);
        $(target).datagrid("refreshRow", options.index);
        opts.onAfterEdit.call(target, options.index, row, _536);
    }

    function _45f(a, o) {
        for (var i = 0, len = a.length; i < len; i++) {
            if (a[i] == o) {
                return i;
            }
        }
        return -1;
    }

    //销毁单元格编辑器
    function _destroyCellEditor(target, options) {

        var opts = $.data(target, "datagrid").options;
        var tr = opts.finder.getTr(target, options.index);

        tr.children("td").each(function () {
            var field = $(this).attr("field");

            if (field == options.field) {//找到与传递参数相同field的单元格

                var cell = $(this).find("div.datagrid-editable");
                if (cell.length) {
                    var ed = $.data(cell[0], "datagrid.editor");
                    if (ed.actions.destroy) {
                        ed.actions.destroy(ed.target);
                    }
                    cell.html(ed.oldHtml);
                    $.removeData(cell[0], "datagrid.editor");
                    cell.removeClass("datagrid-editable");
                    cell.css("width", "");
                }
            }
        });
    }

    $.extend($.fn.datagrid.methods, {
        beginEditCell: function (target, options) {
            return target.each(function () {
                beginEditCell(this, options);
            });
        },
        endEditCell: function (target, options) {
            return target.each(function () {
                endEditCell(this, options);
            });
        }
    });
})(jQuery);
