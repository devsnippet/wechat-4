<div class="row">
    <div class="span12" style="margin-top:20px;">
        <p class="text-right"><a class="btn btn-small btn-warning" style="margin-right:20px;"href="<?php echo base_url("/admin/account/logout"); ?>">退出登录</a><a class="btn btn-small btn-primary" href="<?php echo base_url("/admin/wechat/gsetting"); ?>">添加新的游戏信息</a></p>
        <table id="content" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>游戏ID</th>
                    <th>游戏名称</th>
                    <th>游戏图标路径</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(!empty($list)){
                foreach($list as $v){ ?>
                <tr>
                    <td><?=$v['game_id']?></td>
                    <td><?=$v['name']?></td>
                    <td><?=$v['icon_120']?></td>
                    <td>
                        <a href="<?php echo base_url("/admin/wechat/gsetting/{$v['game_id']}"); ?>">修改</a>
                        <a href="<?php echo base_url("/admin/wechat/choose/{$v['game_id']}"); ?>">选择此游戏</a>
                    </td>
                </tr>
                <?php }} ?>
            </tbody>
        </table>
        <script type="text/javascript">
        function generateAccessURL(wid){
            prompt("微信开发者接入地址\n请拷贝并且填写在微信公众号管理平台", "<?php echo base_url("/access/");?>/"+wid);
        }
        </script>
    </div>
</div>
