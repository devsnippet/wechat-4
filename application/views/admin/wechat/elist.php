<div class="row">
    <div class="span12" style="margin-top:20px;">
        <p class="text-right"><a class="btn btn-small btn-primary" style="margin-right:20px;" href="<?php echo base_url("/admin/wechat/wlist"); ?>">全部微信</a><a class="btn btn-small btn-success" style="margin-right:20px;" href="<?php echo base_url("/admin/wechat/esetting/$wid/"); ?>">添加新的活动信息</a><a class="btn btn-small btn-warning" href="<?php echo base_url("/admin/account/logout"); ?>">退出登录</a></p>
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
                <tr>
                    <td><?=($v['name'] != "")?$v['name']:$v['game_id']?></td>
                    <td><?=$v['wid']?></td>
                    <td><?=$v['event_name']?></td>
                    <td><?=$v['event_info']?></td>
                    <td>
                        <a href="<?php echo base_url("/admin/wechat/esetting/$wid/{$v['event_name']}"); ?>">修改</a>
                        <a href="<?php echo base_url("/admin/wechat/codelist/$wid/{$v['gid']}"); ?>">本活动下的礼包码</a>
                    </td>
                </tr>
                <?php }} ?>
            </tbody>
        </table>
    </div>
</div>
