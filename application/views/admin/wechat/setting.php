<div class="container">
    <div class="row">
        <div class="span12">
            <?php if($error!=0){ ?>
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>警告!</strong> 请仔细填写每一项内容
            </div>
            <?php } ?>
            <form method="post" class="form-horizontal">
                <div id="legend" class="">
                    <legend class="">配置微信公众号</legend>
                </div>
                <fieldset>
                    <div class="control-group">
                        <label class="control-label"  for="name">微信号名称</label>
                        <div class="controls"><input type="text" name="name" value="<?=!empty($wechat_info['name'])?$wechat_info['name']:""?>" id="name" placeholder="微信管理页右上角显示"></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="type">帐号类型</label>
                        <div class="controls">
                            <select name="type" id="type">
                                <option <?=(!empty($wechat_info['type'])&&$wechat_info['type']==1)?$wechat_info['type']:""?> value="1">订阅号</option>
                                <option <?=(!empty($wechat_info['type'])&&$wechat_info['type']==1)?$wechat_info['type']:""?> value="2">服务号</option>
                            </select></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="token">令牌</label>
                            <div class="controls"><input type="text" name="token" id="token" placeholder="微信开发者中心显示" value="<?=!empty($wechat_info['token'])?$wechat_info['token']:""?>" data-toggle="tooltip" data-placement="right" data-original-title="该令牌用于验证微信开发者接口验证"></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="appid">AppID(应用ID)</label>
                            <div class="controls"><input type="text" value="<?=!empty($wechat_info['appid'])?$wechat_info['appid']:""?>" name="appid" id="appid" placeholder="微信开发者中心显示" data-toggle="tooltip" data-placement="right" data-original-title="请准确核实该appid"></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="appsecret">AppSecret(应用密钥)</label>
                            <div class="controls"><input type="text" value="<?=!empty($wechat_info['appsecret'])?$wechat_info['appsecret']:""?>" name="appsecret" id="appsecret" placeholder="微信开发者中心显示"  data-toggle="tooltip" data-placement="right" data-original-title="请准确核实该appsecret"></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="EncodingAESKey">EncodingAESKey</label>
                            <div class="controls"><input type="text" value="<?=!empty($wechat_info['EncodingAESKey'])?$wechat_info['EncodingAESKey']:""?>" name="EncodingAESKey" id="EncodingAESKey" placeholder="微信开发者中心显示"></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="checkinvalue">签到所得积分</label>
                            <div class="controls"><input type="text" value="<?=!empty($wechat_info['checkinvalue'])?$wechat_info['checkinvalue']:""?>" name="checkinvalue" id="checkinvalue" placeholder="每次签到所得积分 , 整数" data-toggle="tooltip" data-placement="right" data-original-title="设置该微信号的签到功能的每次签到积分"></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="packageAppid">礼包接口Appid</label>
                            <div class="controls"><input type="text" value="<?=!empty($wechat_info['packageAppid'])?$wechat_info['packageAppid']:""?>" name="packageAppid" id="packageAppid"></div>
                        </div>
                        <div class="control-group">
                            <span class="help-block"><i class="icon-eye-open"></i> 上述所有设置，都能在微信管理后台看到，请仔细。</span>
                            <span class="help-block"><i class="icon-ok"></i> 下一步：完成设置后，微信公众平台后台，开发者中心，配置项中提交</span>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <label class="checkbox">
                                    <input type="hidden" name="action" value="<?=$action?>">
                                    <input type="hidden" name="game_id" value="<?=$game_info['game_id']?>">
                                    <input type="hidden" name="flag" value="<?=$wechat_info['flag']?>">
                                    <input name="pass" id="pass" type="checkbox"> 微信号码已经通过认证
                                </label>
                                <button type="submit" onclick="return check()" class="btn btn-primary">设置</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>


<script>
$(function(){
    $('#token').tooltip('hide');
    $('#appid').tooltip('hide');
    $('#appsecret').tooltip('hide');
    $('#checkinvalue').tooltip('hide');

});
function check(){
    if($("#pass").is(":checked")){
        return true;
    }else{
        alert("请确认该微信号码已经通过认证");
        return false;
    }
}
</script>
