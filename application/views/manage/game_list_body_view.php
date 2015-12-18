<div class="row">
    <table id="content" class="table table-striped">
        <thead>
            <tr>
                <th>游戏ID</th>
                <th>游戏名称</th>
                <!--th>游戏图标路径</th-->
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($list)){
            foreach($list as $v){ ?>
            <tr <?=empty($v['wid'])?"":"class='success'"?>>
                <td><?=$v['game_id']?></td>
                <td><?=$v['name']?></td>
                <!--td><?=($v['icon_120'] == null)?"无图标":"<img style=\"width:35px;height:35px;\" src=".$v['icon_120'].">";?></td-->
                <td>
                    <a class="btn" href="<?php echo base_url("/manage/wechat/view/{$v['game_id']}"); ?>">选择该游戏</a>
                </td>
            </tr>
            <?php }} ?>
        </tbody>
    </table>
</div>
