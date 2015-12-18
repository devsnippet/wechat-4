
<div class="container">
	<div class="row">
		<div class="span12">
			<form class="form-horizontal" method="POST">
				<legend>管理 模板消息使用的模板信息</legend>
                <div class="alert">
                <strong>Warning!</strong> 每个类别的模板最多只能添加一个，如没有可选模板类别，需在数据库中添加
                </div>
				<?php if(!empty($template_arr)){ foreach($template_arr as $k=>$v){?>
				<div class="control-group template">
					<label class="control-label">模板</label>
					<div class="controls template_list">
						<input type="text" name="template_id[]" placeholder="模板ID，在微信官方后台模板信息中显示" value="<?=$v['template_id']?>" class="span4">
						<select name="type[]" id="type" class="span2">
                            <?php if(!empty($template_list)){ foreach($template_list as $key=>$val){
                                    
                            ?>
                            <option value="<?=$val['type']?>" <?php echo $v['type'] == $val['type'] ? 'selected' : ''?>><?=$val['type_info']?></option>

                            <?php } }?>
                        </select>
                        <input type="text" name="desc[]" placeholder="详情url" value="<?=$v['desc']?>" class="span3">
                        <span class='btn add-template-j'>+</span>
					    <span class='btn remove-template-j'>-</span><br/>
					</div>
				</div>
				<?php } }else{?>
                    <div class="control-group template">
                        <label class="control-label">模板</label>
                        <div class="controls template_list">
                            <input type="text" name="template_id[]" placeholder="模板ID，在微信官方后台模板信息中显示" value="" class="span4">
                            <select name="type[]" id="type" class="span2">
                                <?php if(!empty($template_list)){ foreach($template_list as $key=>$val){
                                        
                                ?>
                                <option value="<?=$val['type']?>" ><?=$val['type_info']?></option>

                                <?php } }?>
                            </select>
                            <input type="text" name="desc[]" placeholder="详情url" value="" class="span3">
                            <span class='btn add-template-j'>+</span>
                            <span class='btn remove-template-j'>-</span><br/>
                        </div>
                    </div>
                <?php }?>
				<div class="control-group" id="aciton">
					<div class="controls">
						<input type="hidden" name="act" value="edit">
						<button type="submit" class="btn btn-primary btn-large">提交</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>

$('.add-template-j').on('click',function(){

//            var tp=$(this).parent();
//
//            var tpp=$(this).parent().parent();

            // $(this).parent().append($(this).parent().clone(true));
            $(this).parent().parent().clone(true).insertBefore('#aciton');
			//$(this).parent().append($(this).prev().add($(this).prev().prev()).add($(this).prev().prev().prev()).add($(this).next()).add($(this).next().next()).add($(this)).clone(true))
			//$(this).prev().prev()
			//$(this).prev().prev().prev()
			//$(this)
			//$(this).next()
		})
        
  $('.remove-template-j').on('click',function(){
           // if($(this).parent().parent().find('.template_list').children().length==6){
               if($('.control-group.template').length == 1){
                alert('不能再删了！');
                return;
            }

            $(this).parent().parent().remove();
			//$(this).prev().add($(this).prev().prev()).add($(this).prev().prev().prev()).add($(this).prev().prev().prev().prev()).add($(this).next()).add($(this)).remove();
        })

        
        
</script>