<div class="row">
    <table id="content" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>所属活动</th>
                <th>所属微信</th>
                <th>礼包码</th>
                <th>发放状态</th>
                <th>发放时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($list)){
            foreach($list as $v){ ?>
            <tr>
                <td><?=isset($v['event_name'])?$v['event_name']:$v['gid']?></td>
                <td><?=$v['wid']?></td>
                <td><?=$v['code']?></td>
                <td><?=($v['isUsed'] == 1)?"已发放":"未发放"?></td>
                <td><?=($v['usedTime'] == "")?"未发放":date("Y-m-d",$v['usedTime'])?></td>
                <td>
                    <a href="javascript:;" onclick="<?php echo "del({$v['cid']}, {$v['wid']}); " ?>">删除</a>
                </td>
            </tr>
            <?php }} ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    function del(cid, wid){
        url = "<?php echo current_url(); ?>";
        $.post(url, {
            cid: cid,
            wid: wid,
            action: 'delete'
        }, function(data) {
            location.reload();
        });
    }
</script>
