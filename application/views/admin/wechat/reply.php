<div class="container">
    <div class="row">
        <div class="span12">
            <div id="legend" class="">
                <legend class="">自定义回复设置</legend>
            </div>
            <div class="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>请注意</strong> 新添加的自定义回复条目默认是启用的状态，如果您还在编辑，请先停用，编辑好后再开启
            </div>
            <p class="text-right">
                <button class="btn btn-success" id="insert" type="button">添加条目</button>
            </p>
                <?php if(!empty($list)){ ?>
                <table id="content" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>识别</th>
                            <th>内容</th>
                            <th>添加时间</th>
                            <th>状态</th>
                            <th>关联</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $v){ ?>
                        <tr id="rid_<?=$v['rid']?>">
                            <td><?=$v['rid']?></td>
                            <td class="target"><?=$v['target']?></td>
                            <td class="reply" style="width:450px;word-wrap:break-word;"><?=$v['reply']?></td>
                            <td><?=mdate("%Y-%m-%d",$v['addtime'])?></td>
                            <td class="status"><?php if ($v['disabled'] == 0) {echo "<span class=\"label label-success\">启用</span>";}else{echo "<span class=\"label label-important\">停用</span>";} ?></td>
                            <td><?=empty($v['link_type'])?"无":"预注册"?></td>
                            <td>
                                <a href="javascript:;" onclick="edit(this,'<?=$v['rid']?>')"><i class="icon-edit" title="修改"></i></a>
                                <a href="javascript:;" onclick="disabled(this,'<?=$v['rid']?>')"><i class="<?php if ($v['disabled'] == 0) {echo "icon-stop";}else{echo "icon-play";} ?>" title="<?php if ($v['disabled'] == 0) {echo "停用该回复";}else{echo "启用该回复";} ?>"></i></a>
                                <a href="javascript:;" onclick="del('<?=$v['rid']?>')"><i class="icon-trash" title="删除"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                <p class="text-center">暂无内容</p>
                <?php } ?>
        </div>
    </div>
    <div class="modal hide fade" id="new">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>设置自定义回复</h3>
        </div>
        <div class="modal-body">
            <form method="post" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="target" class="control-label">目标</label>
                        <div class="controls">
                            <input type="text" name="target" value="<?=!empty($wechat_info['target'])?$wechat_info['target']:""?>" id="target" placeholder="欲匹配的用户发送的关键词" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="系统会自动进行模糊识别如：用户发送关键词'礼包', 将会匹配包含礼包的条目,如'我要礼包'等,以最后添加/修改为准">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">回复内容</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply" placeholder="" value="<?=!empty($wechat_info['reply'])?$wechat_info['reply']:""?>" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="匹配到关键词时回复给用户的内容"></textarea>
                        </div>
                        <div class="controls">
                            <span class="help-block"> 如果准备使用预注册，用<span class="label label-info">%w</span> 作为标记，可以自动填入发码内容，换行请使用输入<span class="label label-info">%nl</span>或者直接<span class="label label-info">另起一行</span>插入文字链接直接使用<pre>&lt;a href=&quot;http://www.g.cn&quot;&gt;名称&lt;/a&gt;</pre></span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="link_register" class="control-label">关联预注册发码</label>
                        <div class="controls">
                            <select name="link_register" id="link_register" class="form-control">
                                <option value="0">无</option>
                                <?php
                                if(!empty($event_list)){
                                foreach($event_list as $v){ ?>
                                <option value="<?=$v['gid']?>"><?=$v['event_name']?></option>
                                <?php }} ?>
                            </select>
                            <span class="help-block">* <small>如有重复匹配，按照最晚添加/修改的目标为准</small></span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="isApibox" class="control-label">是否通过Apibox发码</label>
                        <div class="controls">
                            <label class="checkbox" id="isApiboxShow" data-toggle="tooltip" data-placement="top" data-original-title="勾选将不通过Apibox接口发码。请注意该区别">
                                <input name="isApibox" id="isApibox" type="checkbox" value="noApibox"> 不通过Apibox发码
                            </label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="link_register" class="control-label">礼包码适用类型</label>
                        <div class="controls">
                            <label class="radio">
                                <input type="radio" name="link_language" id="simplified" value="simplified" checked>简体中文
                            </label>
                            <label class="radio">
                                <input type="radio" name="link_language" id="traditional" value="traditional">繁体中文
                            </label>
                        </div>
                        <input type="hidden" name="action" id="action" value="insert">
                        <input type="hidden" name="rid" id="rid" value="">
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="submit()" class="btn btn-primary">保存</a>
        </div>
    </div>
    <script>
    var url = "<?=current_url()?>";
    $(document).ready(function() {
        $('#insert').click(function() {
            $('#action').val("insert");
            $('#new').modal('show');
        })
        $('#target').tooltip('hide');
        $('#reply').tooltip('hide');
        $('#isApiboxShow').tooltip('hide');
    });

    function submit() {
        var target = $('#target').val();
        var reply = $('#reply').val();
        var action = $('#action').val();
        var rid = $('#rid').val();
        var link_register = $('#link_register').val();
        var language = $('input[type="radio"][name="link_language"]:checked').val();
        var isApibox = $('input[type="checkbox"][name="isApibox"]:checked').val();
        $.post(url, {
            target: target,
            reply: reply,
            action: action,
            rid: rid,
            isApibox: isApibox,
            link_register: link_register,
            link_language: language
        }, function(data) {
            location.reload();
        });
    }

    function edit(self, rid) {
        var target = $(self).closest('tr').find('.target').text();
        var reply = $(self).closest('tr').find('.reply').text();
        $('#action').val("update");
        $('#target').val(target);
        $('#reply').val(reply);
        $('#rid').val(rid);
        $('#new').modal('show');
        return false;
    }

    function disabled(self, rid){
        var target = $(self).closest('tr').find('.status').text();
        var disabledStatus = 0;
        switch(target){
            case '启用':
                disabledStatus = 1;
                break;
            case '停用':
                disabledStatus = 0;
                break;
            default:
                disabledStatus = 0;
                break;
        }
        $.post(url, {
            action: "update",
            rid: rid,
            disabled: disabledStatus
        }, function(data) {
            location.reload();
        });
    }

    function del(rid) {
        $.post(url, {
            action: "delete",
            rid: rid,
        }, function(data) {
            $('#rid_' + rid).remove();
        });
    }


    </script>