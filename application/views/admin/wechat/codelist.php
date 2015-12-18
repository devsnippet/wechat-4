<div class="row">
    <div class="span12" style="margin-top:20px;">
        <p class="text-right"><a class="btn btn-small btn-primary" style="margin-right:20px;" href="<?php echo base_url("/admin/wechat/elist/$wid"); ?>">全部活动信息</a><a class="btn btn-small btn-success" style="margin-right:20px;" href="<?php echo base_url("/admin/wechat/csetting/add/$wid/$gid"); ?>">导入礼包码</a><a class="btn btn-small btn-warning" href="<?php echo base_url("/admin/account/logout"); ?>">退出登录</a></p>
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
                    <td><?=($v['usedTime'] == "")?"未发放":$v['usedTime']?></td>
                    <td>
                        <a href="javascript:;" onclick="<?php echo "del({$v['cid']}); " ?>">删除</a>
                    </td>
                </tr>
                <?php }} ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    function del(cid){
        url = "<?php echo base_url("/admin/wechat/csetting/delete/"); ?>";
        $.post(url, {
            cid: cid
        }, function(data) {
            location.reload();
        });
    }
</script>
