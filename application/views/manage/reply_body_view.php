<div class="container">
    <div class="row">
        <div class="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>请注意</strong> 新添加的自定义回复条目默认是启用的状态，如果您还在编辑，请先停用，编辑好后再开启
        </div>
        <p class="text-right">
            <button class="btn btn-success" id="addText" type="button">添加文本型条目</button>
            <button class="btn btn-success" id="addNews" type="button">添加图文消息</button>
            <button class="btn btn-success" id="addEvent" type="button">添加签到条目</button>
            <button class="btn btn-success" id="addCode" type="button">添加发码条目</button>
            <button class="btn btn-success" id="addLottery" type="button">添加抽奖条目</button>
        </p>
            <?php if(!empty($list)){ ?>
            <table id="content" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>匹配类别</th>
                        <th>识别序列1</th>
                        <th>识别序列2</th>
                        <th>响应内容</th>
                        <th>添加时间</th>
                        <th>状态</th>
                        <th>类型</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($list as $v){ ?>
                    <tr id="rid_<?=$v['rid']?>">
                        <td class="cat_name"><?php switch($v['cat_name']) {
                            case 'vague_match':
                                echo "模糊";
                                break;
                            case 'exact_match':
                                echo "精确";
                                break;
                            case 'phone_num_match':
                                echo "手机号";
                                break;
                            default:
                                echo "???";
                                break;
                        }?></td>
                        <td class="alias1"><?=$v['alias1']?></td>
                        <td class="alias2"><?=$v['alias2']?></td>
                        <td class="reply" style="width:450px;word-break:break-all;"><?=($v['reply'] != "")?$v['reply']:$v['extra']?></td>
                        <td><?=date("Y-m-d",$v['addtime'])?></td>
                        <td class="status"><?php if ($v['disabled'] == 0) {echo "<span class=\"label label-success\">启用</span>";}else{echo "<span class=\"label label-important\">停用</span>";} ?></td>
                        <td class="reply_type"><?php switch($v['reply_type']) {
                            case 'text':
                                echo "普通文本";
                                break;
                            case 'news':
                                echo "图文消息";
                                break;
                            case 'event':
                                echo "<span id=\"signCount".$v['alias1']."\" data-toggle=\"tooltip\" title=\"今日".$signCount."人签到\">签到</span>";
                                echo "<script>$('#signCount".$v['alias1']."').tooltip('hide');</script>";
                                break;
                            case 'code':
                                echo "发礼包码";
                                break;
                            case 'lottery':
                                echo "抽奖";
                                break;
                            default:
                                echo "未定义";
                                break;
                        }?></td>
                        <td>
                            <a href="javascript:;" onclick="<?php switch($v['reply_type']) {
                            case 'text':
                                $raw = sprintf("edit(this, %s, 'text')",$v['rid']);
                                echo $raw;
                                break;
                            case 'news':
                                $raw = sprintf("edit(this, %s, 'news')",$v['rid']);
                                echo $raw;
                                break;
                            case 'event':
                                $raw = sprintf("edit(this, %s, 'event')",$v['rid']);
                                echo $raw;
                                break;
                            case 'code':
                                $raw = sprintf("edit(this, %s, 'code')",$v['rid']);
                                echo $raw;
                                break;
                            case 'lottery':
                                $raw = sprintf("edit(this, %s, 'lottery')",$v['rid']);
                                echo $raw;
                                break;
                            default:
                                $raw = sprintf("edit(this, %s, 'text')",$v['rid']);
                                echo $raw;
                                break;
                        }?>"><i class="icon-edit" title="修改"></i></a>
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
    <!-- 文本型消息开始 -->
    <div class="modal hide fade" id="new_text">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>设置文本型自定义回复</h3>
        </div>
        <div class="modal-body">
            <form method="post" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="cat_name" class="control-label">匹配类型</label>
                        <div class="controls">
                            <select id="cat_name_text" name="cat_name" class="form-control">
                                <option value="exact_match">精确匹配</option>
                                <option value="vague_match">模糊匹配</option>
                                <option value="phone_num_match">手机号匹配</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="alias1" class="control-label">匹配关键词1</label>
                        <div class="controls">
                            <input type="text" name="alias1" value="<?=!empty($wechat_info['alias1'])?$wechat_info['alias1']:""?>" id="alias1_text" placeholder="欲匹配的关键词1 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="此处可填写匹配序号 诸如1, 2, 3">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="alias2" class="control-label">匹配关键词2</label>
                        <div class="controls">
                            <input type="text" name="alias2" value="<?=!empty($wechat_info['alias2'])?$wechat_info['alias2']:""?>" id="alias2_text" placeholder="欲匹配的关键词2 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="匹配的文字 诸如“我要礼包” “联系客服”">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">回复内容</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_text" placeholder="返回给用户的内容" value="<?=!empty($wechat_info['reply'])?$wechat_info['reply']:""?>" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="匹配到关键词时回复给用户的内容"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="remarks" class="control-label">备注</label>
                        <div class="controls">
                            <span class="help-block form-control"> 制表符(tab)键请输入<span class="label label-inverse">%tab</span>, 换行请使用输入<span class="label label-info">%nl</span>或者直接<span class="label label-info">另起一行</span>插入文字链接直接使用<pre>&lt;a href=&quot;http://www.g.cn&quot;&gt;名称&lt;/a&gt;</pre></span>
                        <span class="help-block form-control"> 匹配顺序为‘手机号匹配’->'精确匹配'->'模糊匹配'，手机号匹配时不需要填写匹配关键词</span>
                        </div>
                    </div>

                    <input type="hidden" name="action" id="action_text" value="insert">
                    <input type="hidden" name="rid" id="rid_text" value="">
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="submit('text')" class="btn btn-primary">保存</a>
        </div>
    </div>
    <!-- 文本型消息结束 -->
    <!-- 图文消息开始 -->
    <div class="modal hide fade" id="new_news">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>设置图文消息回复</h3>
        </div>
        <div class="modal-body">
            <form method="post" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="alias1" class="control-label">匹配关键词1</label>
                        <div class="controls">
                            <input type="text" name="alias1" value="<?=!empty($wechat_info['alias1'])?$wechat_info['alias1']:""?>" id="alias1_news" placeholder="欲匹配的关键词1 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="此处可填写匹配序号 诸如1, 2, 3">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="alias2" class="control-label">匹配关键词2</label>
                        <div class="controls">
                            <input type="text" name="alias2" value="<?=!empty($wechat_info['alias2'])?$wechat_info['alias2']:""?>" id="alias2_news" placeholder="欲匹配的关键词2 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="匹配的文字 诸如“我要礼包” “联系客服”">
                        </div>
                    </div>
                    <div class="news_container" id="news_container">
                        <div class="control-group news"><label class="control-label">标题</label><div class="controls"><input type="text" name="news_title_1" value="" id="news_title_1" placeholder="标题"></div><label class="control-label">详情</label><div class="controls"><input type="text" name="news_details_1" value="" id="news_details_1" placeholder="详情"></div><label class="control-label">标题图片地址</label><div class="controls"><input type="text" name="news_img_url_1" value="" id="news_img_url_1" placeholder="标题图片地址"></div><label class="control-label">标题链接地址</label><div class="controls"><input type="text" name="news_title_url_1" value="" id="news_title_url_1" placeholder="标题链接地址"></div></div>
                    </div>
                    <a href="javascript:;" onclick="add_news_item()" class="btn btn-primary">添加一组图文消息</a>
                    <a href="javascript:;" onclick="remove_news_item()" class="btn btn-danger">删除一组图文消息</a>
                    <input type="hidden" name="action" id="action_news" value="insert">
                    <input type="hidden" name="extra" id="extra_news" value="">
                    <input type="hidden" name="rid" id="rid_news" value="">
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="submit_news()" class="btn btn-primary">保存</a>
        </div>
    </div>
    <!-- 图文消息结束 -->
    <!-- 事件型消息开始 -->
    <div class="modal hide fade" id="new_event">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>添加签到信息</h3>
        </div>
        <div class="modal-body">
            <form method="post" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="alias1" class="control-label">匹配关键词/事件名1</label>
                        <div class="controls">
                            <input type="text" name="alias1" value="<?=!empty($wechat_info['alias1'])?$wechat_info['alias1']:""?>" id="alias1_event" placeholder="匹配关键词/事件名1" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="此处可填写匹配序号 诸如1, 2, 3 或者newplayer 必须为英文或者数字">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="alias2" class="control-label">匹配关键词/事件名2</label>
                        <div class="controls">
                            <input type="text" name="alias2" value="<?=!empty($wechat_info['alias2'])?$wechat_info['alias2']:""?>" id="alias2_event" placeholder="匹配关键词/事件名22 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="通常填写中文的匹配指令">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">签到成功响应</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_event_success" placeholder="填写签到成功后返回的信息" value="<?=!empty($wechat_info['reply'])?$wechat_info['reply']:""?>" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="签到成功后返回的信息"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="remarks" class="control-label">备注</label>
                        <div class="controls">
                            <span class="help-block form-control">嵌入累计签到<span class="label label-inverse">%scount</span>嵌入连续签到<span class="label label-inverse">%lcount</span>嵌入总积分<span class="label label-inverse">%smark</span>嵌入本次所得<span class="label label-inverse">%cmark</span> 制表符(tab)键请输入<span class="label label-inverse">%tab</span>, 换行请使用输入<span class="label label-info">%nl</span>或者直接<span class="label label-info">另起一行</span>插入文字链接直接使用<pre>&lt;a href=&quot;http://www.g.cn&quot;&gt;名称&lt;/a&gt;</pre></span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">签到失败响应</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_event_failed" placeholder="签到时候后返回的信息" value="<?=!empty($wechat_info['reply'])?$wechat_info['reply']:""?>" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="签到失败后返回的信息(通常是当天已经签到了)"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="action" id="action_event" value="insert">
                    <input type="hidden" name="rid" id="rid_event" value="">
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="submit_event()" class="btn btn-primary">保存</a>
        </div>
    </div>
    <!-- 事件型消息结束 -->
    <!-- 发礼包码回复开始 -->
    <div class="modal hide fade" id="new_code">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>设置发码回复信息</h3>
        </div>
        <div class="modal-body">
            <form method="post" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="alias1" class="control-label">匹配关键词1</label>
                        <div class="controls">
                            <input type="text" name="alias1" value="<?=!empty($wechat_info['alias1'])?$wechat_info['alias1']:""?>" id="alias1_code" placeholder="欲匹配的关键词1 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="此处可填写匹配序号 诸如1, 2, 3">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="alias2" class="control-label">匹配关键词2</label>
                        <div class="controls">
                            <input type="text" name="alias2" value="<?=!empty($wechat_info['alias2'])?$wechat_info['alias2']:""?>" id="alias2_code" placeholder="欲匹配的关键词2 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="匹配的文字 诸如“我要礼包” “新人报道”">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">发码成功返回信息</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_code_success" placeholder="返回给用户的内容" value="" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="发码成功后回复给用户的文字信息 嵌入码使用%code"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="remarks" class="control-label">提示</label>
                        <div class="controls">
                            <span class="help-block form-control">嵌入礼包码请输入<span class="label label-inverse">%code</span>制表符(tab)键请输入<span class="label label-inverse">%tab</span>, 换行请使用输入<span class="label label-info">%nl</span>或者直接<span class="label label-info">另起一行</span>插入文字链接直接使用<pre>&lt;a href=&quot;http://www.g.cn&quot;&gt;名称&lt;/a&gt;</pre></span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">发码失败返回信息</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_code_failed" placeholder="返回给用户的内容" value="" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="发码失败后回复给用户的文字信息"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">选择发码活动名称</label>
                        <div class="controls">
                            <select name="reply_code_event" id="reply_code_event" class="form-control" data-toggle="tooltip" data-placement="top" data-original-title="用于关联发码的批次(ApiBox方式与导入数据发码均需要该活动名)">
                                <?php if(!empty($event_list)){foreach($event_list as $el){ ?>
                                    <option value="<?=$el['event_name']?>"><?= $el['event_name'].' - '.$el['event_info']?></option>
                                <?php }}else{?>
                                    <option value="">未找到礼包活动,请前往添加</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">发码方式</label>
                        <div class="controls">
                            <label class="radio">
                              <input type="radio" name="code_method" id="code_method_apibox" value="apibox" checked>ApiBox发码
                            </label>
                            <label class="radio">
                              <input type="radio" name="code_method" id="code_method_direct" value="direct">导入数据发码
                            </label>
                        </div>
                    </div>
                    <div class="control-group" id="code_language">
                        <label for="reply" class="control-label">ApiBox发码语言类别</label>
                        <div class="controls">
                            <label class="radio">
                              <input type="radio" name="code_language" id="code_language_simple" value="simple" checked>简体中文
                            </label>
                            <label class="radio">
                              <input type="radio" name="code_language" id="code_language_trad" value="trad">繁体中文
                            </label>
                        </div>
                    </div>
                    <input type="hidden" name="action" id="action_code" value="insert">
                    <input type="hidden" name="rid" id="rid_code" value="">
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="submit_code()" class="btn btn-primary">保存</a>
        </div>
    </div>
    <!-- 发礼包码回复结束 -->
    <!-- 抽奖活动回复开始 -->
    <div class="modal hide fade" id="new_lottery">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>设置抽奖条目</h3>
        </div>
        <div class="modal-body">
            <form method="post" class="form-horizontal">
                <fieldset>
                    <div class="control-group">
                        <label for="alias1" class="control-label">匹配关键词1</label>
                        <div class="controls">
                            <input type="text" name="alias1" value="<?=!empty($wechat_info['alias1'])?$wechat_info['alias1']:""?>" id="alias1_lottery" placeholder="欲匹配的关键词1 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="此处可填写匹配序号 诸如1, 2, 3">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="alias2" class="control-label">匹配关键词2</label>
                        <div class="controls">
                            <input type="text" name="alias2" value="<?=!empty($wechat_info['alias2'])?$wechat_info['alias2']:""?>" id="alias2_lottery" placeholder="欲匹配的关键词2 精准匹配" class="form-control" data-toggle="tooltip" data-placement="bottom" data-original-title="匹配的文字 诸如“抽奖”">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="startTime" class="control-label">开始时间</label>
                        <div class="controls">
                            <div id="startTimePicker" class="input-append" name="startTime">
                                <input id="startTime" data-format="yyyy-MM-dd hh:mm:ss" type="text"></input>
                                <span class="add-on">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="endTime" class="control-label">结束时间</label>
                        <div class="controls">
                            <div id="endTimePicker" class="input-append" name="endTime">
                                <input id="endTime" data-format="yyyy-MM-dd hh:mm:ss" type="text"></input>
                                <span class="add-on">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">不在游戏规定时间内</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_lottery_pending" placeholder="抽奖活动还没开始 或者 活动已经过期了~" value="" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">抽奖方式</label>
                        <div class="controls">
                            <label class="radio">
                              <input type="radio" name="lottery_method" id="lottery_method_marks" value="marks">消耗积分
                            </label>
                            <label class="radio">
                              <input type="radio" name="lottery_method" id="lottery_method_lottery" value="lottery">消耗抽奖机会
                            </label>
                        </div>
                    </div>
                    <div class="control-group" id="extend_rule">
                        <label for="counts" class="control-label">额外抽奖条件</label>
                        <div class="controls">
                            当总积分满足<input type="text" name="counts" value="" id="rules_lottery" placeholder="多少分?" class="form-control">时
                        </div>
                    </div>
                    <div class="control-group" id="cost_marks">
                        <label for="counts" class="control-label">消耗积分数量</label>
                        <div class="controls">
                            抽奖一次扣除<input type="text" name="counts" value="" id="cost_marks_num" placeholder="多少分?" class="form-control">分
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">抽奖成功返回信息</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_lottery_success" placeholder="返回给用户的内容" value="" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="抽奖成功后回复给用户的文字信息 嵌入奖品使用%code"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="remarks" class="control-label">提示</label>
                        <div class="controls">
                            <span class="help-block form-control">嵌入奖品请输入<span class="label label-inverse">%code</span>制表符(tab)键请输入<span class="label label-inverse">%tab</span>, 换行请使用输入<span class="label label-info">%nl</span>或者直接<span class="label label-info">另起一行</span>插入文字链接直接使用<pre>&lt;a href=&quot;http://www.g.cn&quot;&gt;名称&lt;/a&gt;</pre></span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">抽奖失败返回信息</label>
                        <div class="controls">
                            <textarea rows="5" type="text" name="reply" id="reply_lottery_failed" placeholder="用户无抽奖机会或不满足额外抽奖条件" value="" class="form-control" data-toggle="tooltip" data-placement="right" data-original-title="通常是没有抽奖机会了"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">选择一个含有奖池的活动</label>
                        <div class="controls">
                            <select name="reply_lottery_event" id="reply_lottery_event" class="form-control" data-toggle="tooltip" data-placement="top" data-original-title="用于关联奖品列表的活动名">
                                <?php if(!empty($event_list)){foreach($event_list as $el){ ?>
                                    <option value="<?=$el['event_name']?>"><?= $el['event_name'].' - '.$el['event_info']?></option>
                                <?php }}else{?>
                                    <option value="">未找到活动，请前往礼包活动添加</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="reply" class="control-label">奖池方式</label>
                        <div class="controls">
                            <label class="radio">
                              <input type="radio" name="lottery_from" id="lottery_from_local" value="local" checked="true">本地奖池
                            </label>
                        </div>
                    </div>
                    <input type="hidden" name="action" id="action_lottery" value="insert">
                    <input type="hidden" name="rid" id="rid_lottery" value="">
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" onclick="submit_lottery()" class="btn btn-primary">保存</a>
        </div>
    </div>
    <!-- 抽奖活动回复结束 -->
    <script>
    var url = "<?=current_url()?>";
    $(document).ready(function() {
        $('#addText').click(function() {
            $('#action_text').val("insert");
            $('#new_text').modal('show');
        })
        $('#addNews').click(function() {
            $('#action_news').val("insert");
            $('#new_news').modal('show');
        })
        $('#addEvent').click(function() {
            $('#action_event').val("insert");
            $('#new_event').modal('show');
        })
        $('#addCode').click(function() {
            $('#action_code').val("insert");
            $('#new_code').modal('show');
        })
        $('#addLottery').click(function() {
            $('#action_lottery').val("insert");
            $('#new_lottery').modal('show');
        })
        $('#alias1_text').tooltip('hide');
        $('#alias2_text').tooltip('hide');
        $('#reply_text').tooltip('hide');
        $('#alias1_news').tooltip('hide');
        $('#alias2_news').tooltip('hide');
        $('#alias1_event').tooltip('hide');
        $('#alias2_event').tooltip('hide');
        $('#reply_event').tooltip('hide');
        $('#alias1_code').tooltip('hide');
        $('#alias2_code').tooltip('hide');
        $('#reply_code_success').tooltip('hide');
        $('#reply_code_failed').tooltip('hide');
        $('#reply_code_event').tooltip('hide');
        $('#alias1_lottery').tooltip('hide');
        $('#alias2_lottery').tooltip('hide');
        $('#reply_lottery_success').tooltip('hide');
        $('#reply_lottery_failed').tooltip('hide');
        $('#reply_lottery_event').tooltip('hide');

        //监听发码选择方式的radio事件
        $("#code_method_direct").change(function() {
            $("#code_language").hide();
        });
        $("#code_method_apibox").change(function() {
            $("#code_language").show();
        });

        $("#lottery_method_lottery").attr("checked", true);//默认选中消耗抽奖机会
        $("#cost_marks").hide();//跟随上面隐藏消耗积分
        $("#lottery_method_marks").change(function() {
            $("#extend_rule").hide();
            $("#cost_marks").show();
        });
        $("#lottery_method_lottery").change(function() {
            $("#cost_marks").hide();
            $("#extend_rule").show();

        });

        $('#startTimePicker').datetimepicker({
          language: 'pt-BR',
          pick12HourFormat: false
        });
        $('#endTimePicker').datetimepicker({
          language: 'pt-BR',
          pick12HourFormat: false
        });

    });

    function submit(type) {
        var cat_name = $('#cat_name_'+type).val();
        var alias1 = $('#alias1_'+type).val();
        var alias2 = $('#alias2_'+type).val();
        var reply = $('#reply_'+type).val();
        var action = $('#action_'+type).val();
        var rid = $('#rid_'+type).val();
        if (cat_name == "phone_num_match") {
            if (reply == "") {
                alert("回复内容不能为空");
                return false;
            }
        } else {
            if (alias1=="" || alias2=="" || reply=="") {
                alert("数据填写有空白 请检查填写");
                return false;
            }
        }
        // var language = $('input[type="radio"][name="link_language"]:checked').val();
        // var isApibox = $('input[type="checkbox"][name="isApibox"]:checked').val();
        $.post(url, {
            cat_name: cat_name,
            alias1: alias1,
            alias2: alias2,
            reply: reply,
            action: action,
            reply_type: type,
            rid: rid,
        }, function(data) {
            location.reload();
        });
    }

    function edit(self, rid, type) {
        var reply_type = $(self).closest('tr').find('.reply_type').text();
        switch(reply_type){
            case '普通文本':
                reply_type = 'text';
                break;
            case '图文消息':
                reply_type = 'news';
                break;
            case '签到':
                reply_type = 'event';
                break;
            case '发礼包码':
                reply_type = 'code';
                break;
            case '抽奖':
                reply_type = 'lottery';
                break;
            default:
                reply_type = "";
                break;
        }

        if (type == 'text') {
            var cat_name = $(self).closest('tr').find('.cat_name').text();
            var alias1 = $(self).closest('tr').find('.alias1').text();
            var alias2 = $(self).closest('tr').find('.alias2').text();
            var reply_text = $(self).closest('tr').find('.reply').text();
            
            if (cat_name == '模糊')
            {
                $('#cat_name_text').val("vague_match");
            } else if (cat_name == '手机号') {
                $('#cat_name_text').val("phone_num_match");
            } else if (cat_name == '精确') {
                $('#cat_name_text').val("exact_match");
            }
            $('#action_text').val("update");
            $('#alias1_text').val(alias1);
            $('#alias2_text').val(alias2);
            $('#reply_text').val(reply_text);
            $('#rid_text').val(rid);
            $('#new_text').modal('show');
        }

        if (type == 'news') {
            var alias1 = $(self).closest('tr').find('.alias1').text();
            var alias2 = $(self).closest('tr').find('.alias2').text();
            var reply_text = $(self).closest('tr').find('.reply').text();

            $('#action_news').val("update");
            $('#alias1_news').val(alias1);
            $('#alias2_news').val(alias2);
            $('#reply_text').val(reply_text);
            format_old_news(reply_text);
            $('#rid_news').val(rid);
            $('#new_news').modal('show');
            console.log("触发了news");
        }

        if (type == 'event') {
            var alias1 = $(self).closest('tr').find('.alias1').text();
            var alias2 = $(self).closest('tr').find('.alias2').text();
            var reply_text = $(self).closest('tr').find('.reply').text();

            $('#action_event').val("update");
            $('#alias1_event').val(alias1);
            $('#alias2_event').val(alias2);
            format_old_event(reply_text);
            $('#rid_event').val(rid);
            $('#new_event').modal('show');
        }

        if (type == 'code') {
            var alias1 = $(self).closest('tr').find('.alias1').text();
            var alias2 = $(self).closest('tr').find('.alias2').text();
            var reply_text = $(self).closest('tr').find('.reply').text();

            $('#action_code').val("update");
            $('#alias1_code').val(alias1);
            $('#alias2_code').val(alias2);
            $('#reply_text').val(reply_text);
            format_old_code(reply_text);
            $('#rid_code').val(rid);
            $('#new_code').modal('show');
            console.log("触发了code");
        };

        if (type == 'lottery') {
            var alias1 = $(self).closest('tr').find('.alias1').text();
            var alias2 = $(self).closest('tr').find('.alias2').text();
            var reply_text = $(self).closest('tr').find('.reply').text();

            $('#action_lottery').val("update");
            $('#alias1_lottery').val(alias1);
            $('#alias2_lottery').val(alias2);
            $('#reply_text').val(reply_text);
            format_old_lottery(reply_text);
            $('#rid_lottery').val(rid);
            $('#new_lottery').modal('show');
            console.log("lottery");
        };

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
            disabled: disabledStatus,
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
    // 图文消息
    function get_current_news_num(){
        var num = $("#news_container >div").length;
        console.log(num);
        return num;
    }

    function add_news_item(){
        var current_num = get_current_news_num();
        current_num++;
        if (current_num > 10) {
            alert("图文消息最多10条");
            return false;
        };
        $("#news_container >div").last().after('<div class="control-group news"><label class="control-label">标题</label><div class="controls"><input type="text" name="news_title_'+current_num+'" value="" id="news_title_'+current_num+'" placeholder="标题"></div><label class="control-label">详情</label><div class="controls"><input type="text" name="news_details_'+current_num+'" value="" id="news_details_'+current_num+'" placeholder="详情"></div><label class="control-label">标题图片地址</label><div class="controls"><input type="text" name="news_img_url_'+current_num+'" value="" id="news_img_url_'+current_num+'" placeholder="标题图片地址"></div><label class="control-label">标题链接地址</label><div class="controls"><input type="text" name="news_title_url_'+current_num+'" value="" id="news_title_url_'+current_num+'" placeholder="标题链接地址"></div></div>');
    }

    function remove_news_item(){
        var current_num = get_current_news_num();
        current_num++;
        if (current_num > 2) {
            $("#news_container >div").last().remove();
        }else{
            alert("至少有一个图文项目");
        }
    }

    function submit_news(){
        //取值
        var alias1 = $('#alias1_news').val();
        var alias2 = $('#alias2_news').val();
        var action = $('#action_news').val();

        var rid = $('#rid_news').val();
        //定义全局变量
        var db_data = [];
        //判断是否有空白
        var current_num = get_current_news_num();
        if (alias1 == "" || alias2 == "") {
            alert("匹配关键词没有填写完整");
            return false;
        };
        for (var i = 1; i <= current_num; i++) {
            if($("#news_title_"+i).val()=="" || $("#news_details_"+i).val()=="" || $("#news_img_url_"+i).val()=="" || $("#news_title_url_"+i).val()==""){
                alert("第" + i + "项中有条目没有填写完整");
                return false;
            }
            var childData = {};
            childData['Title'] = $("#news_title_"+i).val();
            childData['Description'] = $("#news_details_"+i).val();
            childData['PicUrl'] = $("#news_img_url_"+i).val();
            childData['Url'] = $("#news_title_url_"+i).val();
            db_data.push(childData);
        }
        if (db_data.length != 0) {
            $('#extra_news').val(JSON.stringify(db_data));
        }else{
            alert("数据异常 请检查你的填写");
        };

        var extra = $('#extra_news').val();
        $.post(url, {
            alias1: alias1,
            alias2: alias2,
            action: action,
            extra: extra,
            reply_type: 'news',
            rid: rid,
        }, function(data) {
            location.reload();
        });
    }

    /**
     * 格式化旧json数据到表单
     * @param  {string} str json字符串
     * @return {}     无返回值
     */
    function format_old_news(str){
        var jsonObj = $.parseJSON(str);
        for (var i = 0; i < jsonObj.length; i++) {
            if (i == 0) {
                $("#news_title_1").val(jsonObj[i]['Title']);
                $("#news_details_1").val(jsonObj[i]['Description']);
                $("#news_img_url_1").val(jsonObj[i]['PicUrl']);
                $("#news_title_url_1").val(jsonObj[i]['Url']);
            }else{
                var num = i+1;
                $("#news_container >div").last().after('<div class="control-group news"><label class="control-label">标题</label><div class="controls"><input type="text" name="news_title_'+num+'" value="'+jsonObj[i]['Title']+'" id="news_title_'+num+'" placeholder="标题"></div><label class="control-label">详情</label><div class="controls"><input type="text" name="news_details_'+num+'" value="'+jsonObj[i]['Description']+'" id="news_details_'+num+'" placeholder="详情"></div><label class="control-label">标题图片地址</label><div class="controls"><input type="text" name="news_img_url_'+num+'" value="'+jsonObj[i]['PicUrl']+'" id="news_img_url_'+num+'" placeholder="标题图片地址"></div><label class="control-label">标题链接地址</label><div class="controls"><input type="text" name="news_title_url_'+num+'" value="'+jsonObj[i]['Url']+'" id="news_title_url_'+num+'" placeholder="标题链接地址"></div></div>');
            }
        };
    }

    //发码相关信息
    function submit_code(){
        var alias1 = $('#alias1_code').val();
        var alias2 = $('#alias2_code').val();
        var reply_success = $('#reply_code_success').val();
        var reply_failed = $('#reply_code_failed').val();
        var reply_event = $('#reply_code_event').val();//获取option选的值

        var action = $('#action_code').val();
        var rid = $('#rid_code').val();
        if(alias1=="" || alias2=="" || reply_success=="" || reply_failed==""){
            alert("数据填写有空白 请检查填写");
            return false;
        }
        var method = $('input[type="radio"][name="code_method"]:checked').val();//apibox or direct
        var language = $('input[type="radio"][name="code_language"]:checked').val();//simple or trad
        var db_data = {};
        db_data['reply_success'] = reply_success;
        db_data['reply_failed'] = reply_failed;
        db_data['reply_event'] = reply_event;
        db_data['method'] = method;
        if (method == 'apibox') {
            db_data['language'] = language;
        }
        var extra = JSON.stringify(db_data);
        $.post(url, {
            alias1: alias1,
            alias2: alias2,
            action: action,
            extra: extra,
            reply_type: 'code',
            rid: rid,
        }, function(data) {
            location.reload();
        });
    }

    function format_old_code(str){
        var jsonObj = $.parseJSON(str);
        var reply_success = (jsonObj['reply_success'] != undefined) ? jsonObj['reply_success'] : '';
        var reply_failed = (jsonObj['reply_failed'] != undefined) ? jsonObj['reply_failed'] : '';
        var reply_event = (jsonObj['reply_event'] != undefined) ? jsonObj['reply_event'] : '';
        var method = (jsonObj['method'] != undefined) ? jsonObj['method'] : '';
        var language = (jsonObj['language'] != undefined) ? jsonObj['language'] : '';
        $("#reply_code_success").val(reply_success);
        $("#reply_code_failed").val(reply_failed);
        $('#reply_code_event').val(reply_event);
        if (method == 'apibox') {
            $("input[name=code_method][value=apibox]").attr("checked",true);
            $("input[name=code_language][value="+language+"]").attr("checked",true);
        }else{
            $("input[name=code_method][value=direct]").attr("checked",true);
            $("#code_language").hide();
        }
    }

    //签到功能区域
    function submit_event(){
        var alias1 = $('#alias1_event').val();
        var alias2 = $('#alias2_event').val();
        var reply_success = $('#reply_event_success').val();
        var reply_failed = $('#reply_event_failed').val();

        var action = $('#action_event').val();
        var rid = $('#rid_event').val();
        if(alias1=="" || alias2=="" || reply_success=="" || reply_failed==""){
            alert("数据填写有空白 请检查填写");
            return false;
        }
        var db_data = {};
        db_data['reply_success'] = reply_success;
        db_data['reply_failed'] = reply_failed;
        var extra = JSON.stringify(db_data);
        $.post(url, {
            alias1: alias1,
            alias2: alias2,
            action: action,
            extra: extra,
            reply_type: 'event',
            rid: rid,
        }, function(data) {
            location.reload();
        });
    }

    function format_old_event(str){
        var jsonObj = $.parseJSON(str);
        var reply_success = (jsonObj['reply_success'] != undefined) ? jsonObj['reply_success'] : '';
        var reply_failed = (jsonObj['reply_failed'] != undefined) ? jsonObj['reply_failed'] : '';
        $('#reply_event_success').val(reply_success);
        $('#reply_event_failed').val(reply_failed);

    }

    //抽奖功能区域
    function submit_lottery(){
        var alias1 = $('#alias1_lottery').val();
        var alias2 = $('#alias2_lottery').val();
        var startTime = $('#startTime').val();//开始时间
        var endTime = $('#endTime').val();//结束时间
        var reply_pending = $('#reply_lottery_pending').val();//活动尚未开始
        var reply_success = $('#reply_lottery_success').val();//抽奖成功
        var reply_failed = $('#reply_lottery_failed').val();//抽奖失败
        var reply_event = $('#reply_lottery_event').val();//获取option选的值
        var local = 'local';//本地奖池

        //抽奖方式
        var method = $('input[type="radio"][name="lottery_method"]:checked').val();//marks or lottery
        var rules = $('#rules_lottery').val();//抽奖条件
        var costmarks = $('#cost_marks_num').val();//消耗积分


        var action = $('#action_lottery').val();
        var rid = $('#rid_lottery').val();
        if(alias1=="" || alias2=="" || reply_success=="" || reply_failed=="" || startTime == "" || endTime == "" || reply_pending == ""){
            alert("数据填写有空白 请检查填写");
            return false;
        }
        var db_data = {};
        db_data['start_time'] = Date.parse(startTime);
        db_data['end_time'] = Date.parse(endTime);
        db_data['lmethod'] = method;
        db_data['marks'] = costmarks;
        db_data['rules'] = rules;
        db_data['reply_pending'] = reply_pending;
        db_data['reply_success'] = reply_success;
        db_data['reply_failed'] = reply_failed;
        db_data['lottery_event'] = reply_event;
        db_data['method'] = local;


        var extra = JSON.stringify(db_data);
        $.post(url, {
            alias1: alias1,
            alias2: alias2,
            action: action,
            extra: extra,
            reply_type: 'lottery',
            rid: rid,
        }, function(data) {
            location.reload();
        });
    }

    function format_old_lottery(str){
        var jsonObj = $.parseJSON(str);
        var start_time = (jsonObj['start_time'] != undefined) ? jsonObj['start_time'] : '';
        var end_time = (jsonObj['end_time'] != undefined) ? jsonObj['end_time'] : '';
        var lmethod = (jsonObj['lmethod'] != undefined) ? jsonObj['lmethod'] : '';
        var rules = (jsonObj['rules'] != undefined) ? jsonObj['rules'] : 0;
        var marks = (jsonObj['marks'] != undefined) ? jsonObj['marks'] : 0;
        var counts = (jsonObj['counts'] != undefined) ? jsonObj['counts'] : '';
        var reply_pending = (jsonObj['reply_pending'] != undefined) ? jsonObj['reply_pending'] : '';
        var reply_success = (jsonObj['reply_success'] != undefined) ? jsonObj['reply_success'] : '';
        var reply_failed = (jsonObj['reply_failed'] != undefined) ? jsonObj['reply_failed'] : '';
        var lottery_event = (jsonObj['lottery_event'] != undefined) ? jsonObj['lottery_event'] : 'none';

        if (lmethod == 'marks') {
            $("input[name=lottery_method][value=marks]").attr("checked",true);
            $("#extend_rule").hide();
            $("#cost_marks").show();
        }else{
            $("input[name=lottery_method][value=lottery]").attr("checked",true);
            $("#extend_rule").show();
            $("#cost_marks").hide();
        }

        $('#reply_lottery_event').val(lottery_event);
        $('#startTime').val(formatTime(start_time));//开始时间
        $('#endTime').val(formatTime(end_time));//结束时间
        $('#reply_lottery_pending').val(reply_pending);//活动尚未开始
        $('#reply_lottery_success').val(reply_success);//抽奖成功
        $('#reply_lottery_failed').val(reply_failed);//抽奖失败
        $('#rules_lottery').val(rules);//抽奖条件
        $('#cost_marks_num').val(marks);//抽奖一次消耗积分
    }

    //格式化时间代码
    var formatTime=function(e){var a=new Date(e);e=a.getFullYear();var b=a.getMonth(),f=a.getDate(),c=a.getHours(),d=a.getMinutes(),a=a.getSeconds(),b=b+1;10>b&&(b="0"+b);10>c&&(c="0"+c);10>d&&(d="0"+d);10>a&&(a="0"+a);return e+"-"+b+"-"+f+" "+c+":"+d+":"+a};

    </script>

