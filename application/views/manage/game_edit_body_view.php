<div class="container">
    <form method="post" class="form-horizontal">
        <div id="legend" class="">
            <legend class="">配置游戏条目信息</legend>
        </div>
        <fieldset>
            <div class="control-group">
                <label class="control-label"  for="game_id">游戏ID</label>
                <div class="controls"><input type="text" name="game_id" value="<?=!empty($game_info['game_id'])?$game_info['game_id']:""?>" id="game_id" placeholder="游戏ID信息"></div>
            </div>

            <div class="control-group">
                <label class="control-label" for="name">游戏名称</label>
                <div class="controls"><input type="text" name="name" id="name" placeholder="游戏名称" value="<?=!empty($game_info['name'])?$game_info['name']:""?>" data-toggle="tooltip" data-placement="right" data-original-title="要显示的游戏名称"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="icon">游戏图标地址(icon)</label>
                <div class="controls"><input type="text" value="<?=!empty($game_info['icon_120'])?$game_info['icon_120']:""?>" name="icon" id="icon" placeholder="http://app1.mobage.cn/image/12000113/69a3c5039339e54ad1149b8e68e58b5a.png" data-toggle="tooltip" data-placement="right" data-original-title="游戏图标地址连接"></div>
            </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="hidden" name="action" value="<?=!empty($action)?$action:"insert"?>">
                        <button type="submit" onclick="return check()" class="btn btn-primary">设置</button>
                    </div>
                </div>
            </fieldset>
    </form>
</div>


<script>
$(function(){
    $('#name').tooltip('hide');
    $('#icon').tooltip('hide');

});

</script>
