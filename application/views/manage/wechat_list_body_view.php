<div class="row">
    <table id="content" class="table table-striped">
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
            if(!empty($list) && $flag){
            foreach($list as $v){ ?>
            <tr>
                <td><?=$v['name']?></td>
                <td><?=$v['type']==2?"服务号":"订阅号"?></td>
                <td><?=$v['appid']?></td>
                <td><?=date("Y-m-d",$v['addtime'])?></td>
                <td>
                    <a href="<?php echo base_url("/manage/wechat/wxedit/$gameid/{$v['wid']}"); ?>">修改</a>
                    <a href="<?php echo base_url("/manage/wechat/menu/$gameid/{$v['wid']}"); ?>">菜单</a>
                    <a href="<?php echo base_url("/manage/wechat/template/$gameid/{$v['wid']}"); ?>">模板消息</a>
                    <a href="<?php echo base_url("/manage/wechat/reply/$gameid/{$v['wid']}"); ?>">设置回复</a>
                    <a href="<?php echo base_url("/manage/wechat/event/$gameid/{$v['wid']}"); ?>">管理礼包活动</a>
                    <a href="javascript:;" onclick="generateAccessURL('<?=$v['flag']?>')">生成接入链接</a>
                </td>
            </tr>
            <?php }}else{ ?>
                <div class="alert alert-block">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>没找到该游戏下的微信号!</h4>
                    似乎你还没有添加任何微信号到该游戏下, 请添加微信号
                </div>
            <?php } ?>
        </tbody>
    </table>
    <script type="text/javascript">
    function generateAccessURL(flag){
        prompt("微信开发者接入地址\n请拷贝并且填写在微信公众号管理平台", "<?php echo base_url("/entrance/index/");?>/"+flag);
    }
    </script>
</div>
