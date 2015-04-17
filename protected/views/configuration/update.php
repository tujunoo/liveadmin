<div class="sub-title-box">
<h2 class="sub-title"><?php echo CHtml::encode($this->pageTitle)?></h2>
</div>
<?php $this->displayMessage(5);?>
<div class="sub-content">
<?php // Modify configuration 
       if($do_modify == 'Modify') { 
	      $do_modify = $do_modify.'?'.tep_get_all_get_params();
	   } ?>
  <form  action="<?php echo $this->createUrl($do_modify); ?>" method="post">
    <div class="tab-box">
      <div class="tab-tit">
        <h3 class="current"><span>Basic Attributes</span></h3>
      </div>
      <!-- Basic Tab Start -->
      <div class="tab-con">
        <div class="frmWrap">
          <div class="flist">
            <p class="flabel">Title:</p>
            <div class="fcon"> <?php echo CHtml::textField('configuration[title]',$data->title,array('size'=>60))?> </div>
          </div>
          <div class="flist">
            <p class="flabel">Key:</p>
            <div class="fcon"><?php echo CHtml::textField('configuration[key]',$data->key,array('size'=>60))?></div>
          </div>
          <div class="flist">
            <p class="flabel">Value:</p>
            <div class="fcon"><?php echo CHtml::textArea('configuration[value]',$data->value);if($data->key == 'CHINA_FROM_ATTRACTION'){?><p class="green">格式如下：北美|20001,20003,20065;欧洲|20253,20287,20079</p><?php }?></div>
          </div>
          <div class="flist">
            <p class="flabel">Description:</p>
            <div class="fcon"><?php echo CHtml::textField('configuration[description]',$data->description,array('size'=>60))?></div>
          </div>
          <div class="flist">
            <p class="flabel">Description Group:</p>
            <div class="fcon"><?php echo CHtml::dropDownList('configuration[configuration_group_id]',$data->configuration_group_id,$confiGroup);?></div>
          </div>
           <div class="flist">
            <p class="flabel">Sort Order:</p>
            <div class="fcon"><?php echo CHtml::textField('configuration[sort_order]',$data->sort_order,array('size'=>20))?></div>
          </div>
        </div>
      </div>
      <!-- Basic Tab End -->
    </div>
    <!-- Submit Bar -->
    <div class="flist">
      <p class="flabel">&nbsp;</p>
      <?php echo CHtml::hiddenField('configuration[configuration_id]' ,$data->configuration_id)?> <?php echo CHtml::submitButton('Submit' ,array('class'=>'btn'))?> <?php echo CHtml::button('Go Back' ,array('class'=>'btn2','onclick'=>'javascript:history.back(1)'))?> </div>
  </form>
</div>

<!-- <zChange> by zyme -->
<div style="text-align:right;"><span style="cursor:pointer;" id="zChange" onclick="zChange()">v</span></div>
<style>
.zChange {color:#999999;}
</style>
<script>
/** 锁定与解锁表单的部分内容的编辑状态 */
function zChange()
{
	if ($('#configuration_configuration_id').val()=='') return;
	$('#zChange').text($('#zChange').text()!='x'?'x':'v');
	var xv = ($('#zChange').text()=='x'?true:false);
	zChange_class($('#configuration_title'),xv);
	zChange_class($('#configuration_key'),xv);
	zChange_class($('#configuration_description'),xv);
	zChange_class($('#configuration_configuration_group_id'),xv);
	zChange_class($('#configuration_sort_order'),xv);
}
function zChange_class(xthis,xv)
{
	if (xv)
	{
//		xthis.attr('readOnly',true);
		xthis.addClass('zChange');
	}
	else
	{
		xthis.attr('readOnly',false);
		xthis.removeClass('zChange');
	}
}
$(document).ready(function(){zChange();});
</script>
<!-- </zChange> -->
