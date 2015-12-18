<div class="container">
    <div class="row">
        <div class="span12">
            <form method="post" class="form-horizontal">
                <div id="legend" class="">
                    <legend class="">配置礼包码活动信息</legend>
                </div>
                <fieldset>
                    <div class="control-group">
                        <label class="control-label"  for="event_name">活动名称</label>
                        <div class="controls"><input type="text" name="event_name" id="event_name" placeholder="形如:nbasina" value="<?=!empty($event_info['event_name'])?$event_info['event_name']:""?>" data-toggle="tooltip" data-placement="right" data-original-title="活动代名请勿使用中文"></div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="event_info">活动介绍</label>
                        <div class="controls"><input type="text" name="event_info" id="event_info" placeholder="用于新浪推广的xxx" value="<?=!empty($event_info['event_info'])?$event_info['event_info']:""?>" data-toggle="tooltip" data-placement="right" data-original-title="简单输入活动介绍"></div>
                    </div>
                        <div class="control-group">
                            <div class="controls">
                                <input type="hidden" name="game_id" value="<?=!empty($gameid)?$gameid:""?>">
                                <input type="hidden" name="wid" value="<?=!empty($wid)?$wid:""?>">
                                <input type="hidden" name="gid" value="<?=!empty($gid)?$gid:""?>">
                                <input type="hidden" name="action" value="<?=!empty($action)?$action:"insert"?>">
                                <button type="submit" class="btn btn-primary"><?=($tag == 1)?"新增":"更新"?></button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>


<script>
$(function(){
    $('#event_name').tooltip('hide');
    $('#event_info').tooltip('hide');

});

</script>
