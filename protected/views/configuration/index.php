<style>
.searchbox {
        padding:5px;
        float:right;
}
</style>
<div class="sub-title-box">
<?php echo CHtml::link('Add New',$this->createUrl('Modify'),array('class'=>'btn3 indent5'));?>
<?php echo CHtml::link('List All',$this->createUrl('index'),array('class'=>'btn3 indent5'));?>

<div class="sub-content searchbox">
<form action="<?php echo $this->createUrl('index')?>" method="get">
Search: <?php echo CHtml::textField('search','',array('size'=>20,'class'=>'itext'))?> <?php echo CHtml::submitButton('Search',array('class'=>'btn'))?>
</form>
</div>
</div>
<h2 class="sub-title" style="clear:both">
<span><?php echo CHtml::encode($this->pageTitle)?></span>
</h2>
<?php $this->displayMessage(5);?>
<div class="sub-content no-folding">
        <table class="table1 zebra row-hl">
                <tr>
                        <th>标题</th>
                        <th>内容</th>
                        <th>操作</th>
                </tr>
                <?php foreach($dataProvider->getData() as $data){?>
                <tr>
                        <td style="white-space:nowrap;border-right:1px solid #b5cfdf;">
                        	<div style="width:390px;" title="<?php echo CHtml::encode($data['title'])?>">
								<p style="clear:both;after-content:'...';">
									<span style="display:block;float:left;width:390px;max-width:365px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
										<?php echo CHtml::encode($data['title'])?>
									</span>
								<p>
							</div>
						</td>
                        <td>
                        	<div style="width:650px;" title="<?php echo CHtml::encode($data['value'])?>">
								<p style="clear:both;after-content:'...';">
									<span style="display:block;float:left;width:650px;max-width:625px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
										<?php echo CHtml::encode($data['value'])?>
									</span>
								<p>
							</div>
						</td>
                        <td style="white-space:nowrap;border-left:1px solid #b5cfdf;">
                                <a href="<?php echo $this->createUrl('Configuration/modify',array('cID'=>$data['configuration_id'])).'&'.tep_get_all_get_params(array('id'))?>">Modify</a>
                        </td>
                </tr>
                <?php } ?>
        </table>
        <div class="pager-box ">
        <?php $this->widget('CLinkPager', array('cssFile'=>false,'header'=>'','pages' => $dataProvider->pagination));?>
        </div>
</div>
