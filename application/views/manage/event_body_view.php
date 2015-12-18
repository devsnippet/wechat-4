<div class="row">
    <table id="content" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>所属游戏</th>
                <th>所属微信ID</th>
                <th>活动名称</th>
                <th>活动介绍</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($list)){
            foreach($list as $v){ ?>
            <tr id="gid_<?=$v['gid']?>">
                <td><?=($game_name != null)?$game_name :$v['game_id']?></td>
                <td><?=$v['wid']?></td>
                <td><?=$v['event_name']?></td>
                <td><?=$v['event_info']?></td>
                <td>
                    <a href="<?php echo base_url("/manage/wechat/event/$gameid/$wxid/{$v['gid']}"); ?>">修改</a>
                    <a href="<?php echo base_url("/manage/wechat/event/$gameid/$wxid/{$v['gid']}/code"); ?>">本活动下的礼包码</a>
                    <a href="#" onclick="del(<?=$v['gid']?>)">删除</a>
                </td>
            </tr>
            <?php }} ?>
        </tbody>
    </table>
    <script type="text/javascript">
    var url = "<?=current_url()?>";
    function del(gid) {
        $.post(url, {
            action: "delete",
            gid: gid,
        }, function(data) {
            $('#gid_' + gid).remove();
        });
    }
    </script>
</div>
