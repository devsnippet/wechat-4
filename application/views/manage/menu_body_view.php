<div class="container">
    <div class="row">
            <div class="hide flag"><?= $wid ?></div>
            <p class="text-right">
            <button type="button" id="delRemoteMenu" class="btn btn-danger" onclick="delRemoteMenu()" alt="该操作会删除腾讯服务器上的自定义菜单设置" data-toggle="tooltip" data-placement="top" title="" data-original-title="请注意，该操作会删除腾讯服务器上的自定义菜单设置，该微信号将没有自定义菜单">删除自定义菜单</button>
            <button type="button" id="initmenu" class="btn btn-danger" onclick="initmenu()" alt="初始化菜单会清空当前已存储的菜单设置，但并不会影响微信上的数据" data-toggle="tooltip" data-placement="top" title="" data-original-title="该操作会清空当前已存储的自定义菜单设置">初始化自定义菜单</button>
            <button type="button" id="fetchmenu" class="btn btn-primary" onclick="fetchmenu()"  data-toggle="tooltip" data-placement="top" title="" data-original-title="从腾讯服务器上获取现有的菜单设置，该操作将会覆盖本地已存储的菜单">读取现在使用的菜单</button>
            <button type="button" class="btn" onclick="edit(this, 'add')">新增菜单</button>
            <button type="button" class="btn btn-success" onclick="pushmenu()">推送菜单到微信</button>
            </p>
            <table id="content" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>菜单名称</th>
                        <th>菜单类型</th>
                        <th>菜单对应值</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($bar_arr) && $wid){?>
                    <?php foreach ($bar_arr as $key => $value) {
                    if (is_null($value['pid'])) { ?>
                    <tr class="parent item<?=$value['id'] ?>">
                        <td class="text-left valuename"><?=$value['name'] ?></td>
                        <td class="valuetype"><?= $value['type'] ?></td>
                        <td class="valueurl"><?= $value['code'] ?></td>
                        <td><?php if(is_null($value['code']) && is_null($value['type'])){ ?>
                            <a href="javascript:;" onclick="edit(this,'addchild', 'edit', <?=$value['id'] ?>)"><i class="icon-pencil" title="新建子菜单"></i></a>
                            <?php } ?>
                            <a href="javascript:;" onclick="edit(this,'action','set',<?=$value['id'] ?>)"><i class="icon-wrench" title="编辑响应动作"></i></a>
                            <a href="javascript:;" onclick="edit(this,'name','edit', <?=$value['id'] ?>)"><i class="icon-edit" title="重命名"></i></a>
                            <a href="javascript:;" onclick="delitem(<?=$value['id'] ?>)"><i class="icon-trash" title="删除"></i></a>
                        </td>
                    </tr>
                    <?php }else{?>
                    <tr class="child item<?=$value['id'] ?> parent<?=$value['pid'] ?>">
                        <td style="padding-left:3em" class="valuename"><?=$value['name'] ?></td>
                        <td class="valuetype"><?= $value['type'] ?></td>
                        <td class="valueurl"><?= $value['code'] ?></td>
                        <td>
                            <!-- <a href="javascript:;" onclick="edit(this,'name','edit')"><i class="icon-edit" title="编辑"></i></a> -->
                            <a href="javascript:;" onclick="edit(this,'action','set',<?=$value['id'] ?>)"><i class="icon-wrench" title="编辑响应动作"></i></a>
                            <a href="javascript:;" onclick="edit(this,'name','edit', <?=$value['id'] ?>)"><i class="icon-edit" title="重命名"></i></a>
                            <a href="javascript:;" onclick="delitem(<?=$value['id'] ?>)"><i class="icon-trash" title="删除"></i></a>
                        </td>
                    </tr>
                    <?php } } }else{ ?>
                        <div class="alert alert-block">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <h4>目前数据库中没有自定义菜单数据!</h4>
                        请确认你已经选择正确的微信并且从该微信的菜单操作链接进来，然后尝试从服务器读取旧的自定义菜单内容进行编辑。
                    </div>
                    <?php } ?>
                </tbody>
            </table>
    </div>
</div>
<div class="modal hide fade" id="name_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>设置菜单</h3>
    </div>
    <div class="modal-body">
        <div class="control-group">
            <div class="controls">
                <input type="text" class="input-xlarge" id="name_value" placeholder="输入按钮名称，一级4个汉字，二级7个">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" onclick="save(this,'name');" class="btn btn-primary">保存</a>
    </div>
</div>
<script>
$(function(){
    $("#add_type_value").change(selectchange("add_type_value", "add_key_value"));
    $("#add_key_value").change(selectchange("add_key_value", "add_type_value"));
    $("#addchild_type_value").change(selectchange("addchild_type_value", "addchild_key_value"));
    $("#addchild_key_value").change(selectchange("addchild_key_value", "addchild_type_value"));
    $("#action_type").change(selectchange("action_type", "action_key"));
    $("#action_key").change(selectchange("action_key", "action_type"));
    $('#isChild').change(function() {
        if($(this).is(":checked")) {
            $(".inputmethod").attr("disabled",true); 
        }else{
            $(".inputmethod").attr("disabled",false); 
        }
    });
    $('#initmenu').tooltip('hide');
    $('#fetchmenu').tooltip('hide');
    $('#delRemoteMenu').tooltip('hide');
});
function selectchange(bindelement, changeelement){
        $("#"+bindelement).change(function(){
        var typevalue = $("#"+bindelement).val();  //获取Select选择的Value
        var selectedindex = $("#"+bindelement).get(0).selectedIndex;
        $("#"+changeelement).get(0).selectedIndex=selectedindex;//index为索引值
    });
}
var _itemid = 0;
function edit(self,type,action,itemid){
    _itemid = itemid;
    if(type=="name"){
        if (action == "edit"){
            var value_name = $(".item"+itemid+">td.valuename").html();
            $('#'+type+'_value').val(value_name);
        } 
    }
    if(type=="action"){
        if (action == "set") {
            var value_type = $(".item"+itemid+">td.valuetype").html();
            var value_url = $(".item"+itemid+">td.valueurl").html();
            $('#'+type+'_value').val(value_url);
        };
    }
    if (type=="add") {//new item
        if (check_parent()) {}else{return;};
        if (action == "edit") {
            //alert(_itemid);//this _itemid is parent id
        };
    };
    if (type == "addchild") {
        if (check_child(_itemid/*this is parent id*/)) {}else{return;};
    };
    
    $('#'+type+'_form').modal('show');
    return false;
}
function save(self,type,action,itemid){
    if(type=="name"){
        var name = $("#name_value").val();
        var wid = $('.flag').html();
        if (name == "") {alert("菜单名称不能为空");return false;};
        if (name.getBytesLength() >= 12) {alert("菜单名称超出长度,最多5个汉字");return false;}
        $.ajax({
                type: "POST",
                url: '<?php echo base_url('menu_ajax/editname');?>',
                data: 'name='+name+'&name_id='+_itemid+'&wid='+wid,
                dataType: 'json',
                success: function(data){
                    if (data.status == 200) {
                        $('#'+type+'_form').modal('hide');
                        alert("修改成功");
                        reloadpage();
                    }else{
                        alert("修改失败"+data.message);
                    }
                },
                error: function() {
                    alert("修改失败，您未修改任何配置或状态异常");
                }
            });
    }
    if(type=="action"){
        if (action == "edit") {
            var actiontype = $('#action_type').val();
            var actionkeyvalue = $('#action_key').val();
            var actionvalue = $('#'+type+'_value').val();
            var wid = $('.flag').html();
            if (actiontype == 'view') {
                if (!checkUrl(actionvalue)) {
                    $('#'+type+'_value').tooltip('show');
                    return false;
                };
            };
            $.ajax({
                type: "POST",
                url: '<?php echo base_url('menu_ajax/editaction');?>',
                data: 'action_id='+_itemid+'&action_type='+actiontype+'&action_key='+actionkeyvalue+'&action_value='+actionvalue+'&wid='+wid,
                dataType: 'json',
                success: function(data){
                    if (data.status == 200) {
                        $('#'+type+'_form').modal('hide');
                        alert("修改成功");
                        reloadpage();
                    }else{
                        alert("修改失败"+data.message);
                    }
                },
                error: function() {
                    alert("修改失败，您未修改任何配置或状态异常");
                }
            });
        };
        if (action == "add") {
            var actionname = $('#add_name_value').val();
            var actiontype = $('#add_type_value').val();
            var actionkeyvalue = $('#add_key_value').val();
            var actionvalue = $('#add_value').val();
            var wid = $('.flag').html();
            var postStr = "";
            if ($("#isChild").is(":checked")) {
                if (actionname == "") {
                    alert("请填写菜单名称");
                    return false;
                }else{
                    postStr = "action_name=" + actionname+'&wid='+wid;
                }
            }else{
                if (actionname == "" || actiontype == "" || actionkeyvalue == "" || actionvalue == "") {
                    alert("你添加的可能是一级菜单, 所以请完整填写响应的事件或者内容");
                    return false;
                }else{
                    postStr = "action_name="+actionname+"&action_type="+actiontype+"&action_key="+actionkeyvalue+"&action_value="+actionvalue+'&wid='+wid;
                }
            }
            
            if (actionname.getBytesLength() >= 12) {
                alert("你输入的一级菜单名称过长,最多5个汉字");
                return false;
            };

            if (actiontype == 'view') {
                if (!checkUrl(actionvalue)) {
                    $('#add_value').tooltip('show');
                    return false;
                };
            };
            $.ajax({
                type: "POST",
                url: '<?php echo base_url('menu_ajax/addmenu');?>',
                data: postStr,
                dataType: 'json',
                success: function(data){
                    if (data.status == 200) {
                        $('#'+action+'_form').modal('hide');
                        alert(data.message);
                        reloadpage();
                    }else{
                        alert(data.message);
                    }
                },
                error: function() {
                    alert("添加失败");
                }
            });
        };
        if (action == "addchild") {
            var actionname = $('#addchild_name_value').val();
            var actiontype = $('#addchild_type_value').val();
            var actionkeyvalue = $('#addchild_key_value').val();
            var actionvalue = $('#addchild_value').val();
            var wid = $('.flag').html();
            var postStr = "";
            if (actionname == "" || actiontype == "" || actionkeyvalue == "" || actionvalue == "") {
                alert("你正在添加二级菜单, 请填写完整所有内容");
                return false;
            };
            postStr = "action_name="+actionname+"&action_type="+actiontype+"&action_key="+actionkeyvalue+"&action_value="+actionvalue + "&pid="+_itemid+'&wid='+wid;
            if (actiontype == 'view') {
                if (!checkUrl(actionvalue)) {
                    $('#addchild_value').tooltip('show');
                    return false;
                };
            };
            $.ajax({
                type: "POST",
                url: '<?php echo base_url('menu_ajax/addmenu');?>',
                data: postStr,
                dataType: 'json',
                success: function(data){
                    if (data.status == 200) {
                        $('#'+action+'_form').modal('hide');
                        alert(data.message);
                        reloadpage();
                    }else{
                        alert(data.message);
                    }
                },
                error: function() {
                    alert("添加失败");
                }
            });
        };
   }
}
function check_parent(){
    var parent = $("tr.parent");
    if (parent.length >= 3) {
        alert("第一级菜单最多只能添加3个");
        return false;
    };
    return true;
}
function check_child(pid){
    var child = $("tr.parent"+pid);
    var childAll = $("tr.child");
    if (child.length >= 5) {
        alert("二级菜单最多只能添加5个");
        return false;
    };
    if (childAll.length >= 15) {
        alert("二级菜单最多只能添加15个");
        return false;
    };
    return true;
}

function delitem(itemid){
    //remove item frontend
    $(".item"+itemid).remove();
    //TODO ajax to delete it
    //
    $.getJSON("<?php echo base_url('menu_ajax/delmenu/');?>"+"/"+itemid,function(result){
        alert(result.message);
    });
}

function fetchmenu(itemid){
    var wid = $('.flag').html();
    $.getJSON("<?php echo base_url('manage/requestMenu/');?>"+"/"+wid,function(result){
        console.log(result);
        if (result.status == 200) {
            reloadpage();
        }else{
            alert(result.message);
        };
    });
}

function initmenu(){
    var wid = $('.flag').html();
    $.ajax({
        type: "POST",
        url: '<?php echo base_url('menu_ajax/initmenu');?>',
        data: "wid="+wid,
        dataType: 'json',
        success: function(data){
            if (data.status == 200) {
                alert(data.message);
                reloadpage();
            }else if(data.status == 404){
                alert(data.message);
            }
        }
    });
}

function pushmenu(){
    var wid = $('.flag').html();
    $.ajax({
        type: "POST",
        url: '<?php echo base_url('menu_ajax/pushmenu');?>',
        data: "wid="+wid,
        dataType: 'json',
        success: function(data){
            alert(data.message);
        },
        error: function() {
            alert("推送过程中发生异常，失败");
        }
    });
}

function delRemoteMenu(){
    var wid = $('.flag').html();
    $.ajax({
        type: "POST",
        url: '<?php echo base_url('menu_ajax/delRemoteMenu');?>',
        data: "wid="+wid,
        dataType: 'json',
        success: function(data){
            alert(data.message);
        },
        error: function() {
            alert("提交过程中发生异常，失败");
        }
    });
}

function reloadpage(){
    window.location.reload();
}

function checkUrl(str) { 
    var RegUrl = new RegExp(); 
    RegUrl.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
    if (!RegUrl.test(str)) { 
        return false; 
    } 
    return true; 
} 

String.prototype.getBytesLength = function() { 
    return this.replace(/[^\x00-\xff]/gi, "--").length; 
} 

</script>
<div class="modal hide fade" id="action_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>设置动作</h3>
    </div>
    <div class="modal-body">
        <div class="control-group">
            <div class="controls">
                <select name="action_type" id="action_type">
                    <option value="click">点击</option>
                    <option value="view">跳转</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <select name="action_value" id="action_key">
                    <option value="key">响应的动作名</option>
                    <option value="url">跳转目的链接</option>
                </select>
                <input type="text" class="input-xlarge" id="action_value" placeholder="内容"  data-toggle="tooltip" title="请输入正确的url">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" onclick="save(this,'action', 'edit');">保存</a>
    </div>
</div>
<div class="modal hide fade" id="add_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>添加一级菜单</h3>
    </div>
    <div class="modal-body">
        <div class="control-group">
            <div class="controls">
                <label>菜单名称</label>
                <input type="text" class="input-xlarge" id="add_name_value" placeholder="输入按钮名称，一级4个汉字，二级7个">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox" id="isChild"> 本菜单包含二级菜单
                </label>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <select name="action_type" id="add_type_value" class="inputmethod">
                    <option value="click">点击</option>
                    <option value="view">跳转</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <select name="action_value" id="add_key_value" class="inputmethod">
                    <option value="key">响应的动作名</option>
                    <option value="url">跳转目的链接</option>
                </select>
                <input type="text" class="input-xlarge inputmethod" id="add_value" placeholder="内容"  data-toggle="tooltip" title="请输入正确的url">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" onclick="save(this, 'action', 'add')">保存</a>
    </div>
</div>
<div class="modal hide fade" id="addchild_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>添加二级菜单</h3>
    </div>
    <div class="modal-body">
        <div class="control-group">
            <div class="controls">
                <label>菜单名称</label>
                <input type="text" class="input-xlarge" id="addchild_name_value" placeholder="输入按钮名称，一级4个汉字，二级7个">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <select name="action_type" id="addchild_type_value" class="inputmethod">
                    <option value="click">点击</option>
                    <option value="view">跳转</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <select name="action_value" id="addchild_key_value" class="inputmethod">
                    <option value="key">响应的动作名</option>
                    <option value="url">跳转目的链接</option>
                </select>
                <input type="text" class="input-xlarge inputmethod" id="addchild_value" placeholder="内容" data-toggle="tooltip" title="请输入正确的url">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" onclick="save(this, 'action', 'addchild')">保存</a><!-- set action to mod -->
    </div>
</div>