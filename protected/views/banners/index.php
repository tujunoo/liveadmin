<style>
.searchbox {
        padding:5px;
        float:right;
}
</style>
<div class="sub-title-box">
<?php echo CHtml::link('Add New Banner',$this->createUrl('Modify'),array('class'=>'btn3 indent5'));?>
<?php echo CHtml::link('List All Banners',$this->createUrl('index'),array('class'=>'btn3 indent5'));?>

<div class="sub-content searchbox">
<form action="<?php echo $this->createUrl('index')?>" method="get">
<?php echo "Search By: ".CHtml::textField('search','',array('size'=>20,'class'=>'itext w200','placeholder'=>'Banner Id/Title/Url/Group'))?> <?php echo CHtml::submitButton('Search',array('class'=>'btn'))?>
</form>
</div>
</div>

<div class="sub-title-box">
	<?php foreach( $allGroup as $oneGroup) : ?>
		<?php echo CHtml::link($oneGroup,$this->createUrl('index',array('search'=>$oneGroup)),array('class'=>'btn3 indent5'));?>
	<?php endforeach; ?>
</div>

<?php $this->displayMessage(5);?>
<div class="sub-content no-folding">
        <table class="table1 zebra row-hl">
                <tr>
                        <th>Banners</th>
                        <th>Groups</th>
                        <th>Displays / Clicks</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                </tr>
                <?php foreach($dataProvider->getData() as $data)
				{
					// by zyme // the title='' is group info
                	if ( $data['title'] == '' ) continue;
               	?>
                <tr>
                        <td><?php echo CHtml::encode($data['title'])?></td>
                        <td><?php echo CHtml::encode($data['group'])?></td>
                        <td><?php echo $bh->getClickShown($data['banner_id']); ?></td>
                        <td><?php echo CHtml::encode($data['sort_order'])?></td>
                        <td><?php echo CHtml::stateButton($data['active'] , $this->createUrl('changeState',array('banner_id'=>$data['banner_id'],'field'=>'active')),array('title'=>'Active/Inactive'))?></td>
                        <td>
                                <a href="<?php echo $this->createUrl('Banners/modify',array('id'=>$data['banner_id'],'Banners_page'=>$_GET['Banners_page']))?>">Modify</a> |
                <a href="javascript:void(0);" onclick="$.popwin.ask('Are you sure you want delete banner [<?php echo addslashes($data['title']);?>]?',function(){location.href='<?php echo $this->createUrl('Banners/Delete',array('id'=>$data['banner_id'])).'&'.tep_get_all_get_params('id')?>';});">Delete</a>
                        </td>
                </tr>
                <?php } ?>
        </table>
        <div class="pager-box ">
        <?php $this->widget('CLinkPager', array('cssFile'=>false,'header'=>'','pages' => $dataProvider->pagination));?>
        </div>

</div>
