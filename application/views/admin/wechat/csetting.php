<div class="row">
    <div class="span12" style="margin-top:20px;">
        <p class="text-right"><a class="btn btn-small btn-primary" style="margin-right:20px;" href="<?php echo base_url("/admin/wechat/elist/$wid"); ?>">全部活动信息</a><a class="btn btn-small  btn-primary" style="margin-right:20px;" href="<?php echo base_url("/admin/wechat/codelist/$wid/$gid"); ?>">查看礼包码</a><a class="btn btn-small btn-warning" href="<?php echo base_url("/admin/account/logout"); ?>">退出登录</a></p>
        <?php if ($message == "导入成功") {
            echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>恭喜!</strong> 礼包码导入成功.</div>";
        } ?>
        <form action="<?php echo base_url("/admin/wechat/csetting/add/$wid/$gid"); ?>" method="post">
            <input type="hidden" name="wid" value="<?php echo $wid;?>">
            <input type="hidden" name="gid" value="<?php echo $gid;?>">
            <div class="form-group">
              <label for="code">粘贴所有礼包码，每行一个</label>
              <textarea class="form-control span12" rows="20" id="code" name="giftcode"></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-small btn-primary">提交</button>
            </div>
        </form>
    </div>
</div>
