<div class="header">
    <?php if(!empty($game_info)){ ?>
    <nav>
        <ul class="nav nav-pills pull-right">
            <li class=""><a href="<?php echo base_url("admin/wechat/"); ?>">选择游戏</a></li>
            <li class=""><a href="<?php echo base_url("admin/wechat/wlist/"); ?>">全部微信</a></li>
            <li class="<?=$active=='setting'?"active":""?>"><a href="<?php echo base_url("admin/wechat/setting/$wid"); ?>">初始化</a></li>
            <li class="<?=$active=='bar'?"active":""?>"><a href="<?php echo base_url("/admin/wechat/bar/$wid"); ?>">设置菜单</a></li>
            <li class="<?=$active=='reply'?"active":""?>"><a href="<?php echo base_url("/admin/wechat/reply/$wid"); ?>">回复设置</a></li>
            <li class="<?=$active=='elist'?"active":""?>"><a href="<?php echo base_url("/admin/wechat/elist/$wid"); ?>">管理礼包活动</a></li>
            <li class=""><a href="<?php echo base_url("admin/account/logout"); ?>">退出登录</a></li>
        </ul>
    </nav>
    <?php }else{ ?>
    <div class="alert">
        <strong>提示：</strong> 请先选择游戏
    </div>
    <?php } ?>
    <h3 class="lead"><img src="<?=$game_info['icon_120']?>" width="35"> <?=$game_info['name']?></h3>
</div>