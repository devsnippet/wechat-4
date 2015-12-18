<div class="row">
    <?php if ($message == "导入成功") {
        echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>恭喜!</strong> 礼包码导入成功.</div>";
    } ?>
    <form action="<?php echo current_url(); ?>" method="post">
        <input type="hidden" name="wid" value="<?php echo $wid;?>">
        <input type="hidden" name="gid" value="<?php echo $gid;?>">
        <input type="hidden" name="action" value="insert">
        <div class="form-group">
          <label for="code">粘贴所有礼包码，每行一个</label>
          <textarea class="form-control span12" rows="20" id="code" name="giftcode"></textarea>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-small btn-primary">提交</button>
        </div>
    </form>
</div>
