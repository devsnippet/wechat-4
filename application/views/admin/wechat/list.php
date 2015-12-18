<div class="row">
    <div class="span12" style="margin-top:20px;">
        <p class="text-right"><a class="btn btn-small btn-warning" style="margin-right:20px;"href="<?php echo base_url("/admin/wechat"); ?>">重新选择游戏</a><a class="btn btn-small btn-primary" href="<?php echo base_url("/admin/wechat/setting"); ?>">添加新的微信帐号</a></p>
        <table id="content" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>名称</th>
                    <th>类型</th>
                    <th>AppId</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(!empty($list)){
                foreach($list as $v){ ?>
                <tr>
                    <td><?=$v['name']?></td>
                    <td><?=$v['type']==2?"订阅号":"服务号"?></td>
                    <td><?=$v['appid']?></td>
                    <td><?=mdate("%Y-%m-%d",$v['addtime'])?></td>
                    <td>
                        <a href="<?php echo base_url("/admin/wechat/setting/{$v['wid']}"); ?>">修改</a>
                        <a href="<?php echo base_url("/admin/wechat/bar/{$v['wid']}"); ?>">菜单</a>
                        <a href="<?php echo base_url("/admin/wechat/reply/{$v['wid']}"); ?>">设置回复</a>
                        <a href="<?php echo base_url("/admin/wechat/elist/{$v['wid']}"); ?>">管理礼包活动</a>
                        <a href="javascript:;" onclick="generateAccessURL('<?=$v['wid']?>')">生成接入链接</a>
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
