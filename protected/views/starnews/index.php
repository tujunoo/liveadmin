<style>
.searchbox {
        padding:5px;
        float:right;
}
</style>
<div class="sub-title-box">


<div class="sub-content searchbox">
<form action="<?php echo $this->createUrl('index')?>" method="get">
<?php echo "Search By: ".CHtml::textField('search','',array('size'=>20,'class'=>'itext w200'))?> <?php echo CHtml::submitButton('Search',array('class'=>'btn'))?>
</form>
</div>
</div>




<div class="sub-content no-folding">
        <table class="table1 zebra row-hl">
                <tr>
                        <th>id</th>
                        <th>starName</th>
                        <th>title</th>
                        <th>createtime</th>
                        <th>action</th>
                  
                </tr>
                <?php foreach($dataProvider->getData() as $data)
				{

					// by zyme // the title='' is group info
                	if ( $data['title'] == '' ) continue;
               	?>
                <tr>
                        <td><?php echo CHtml::encode($data['id'])?></td>
                        <td><?php echo CHtml::encode($data['star_name'])?></td>
                        <td><?php  echo  CHtml::encode($data['title'])?></td>
                        <td><?php echo CHtml::encode(date('Y-m-d H:i:s',$data['createtime']))?></td>
                        <td>

                         <a href="<?php echo $this->createUrl('Starnews/look',array('id'=>$data['id']))?>">Look</a> |		
                        <a href="javascript:void(0);" onclick="$.popwin.ask('Are you sure you want delete banner [<?php echo addslashes($data['title']);?>]?',function(){location.href='<?php echo $this->createUrl('Starnews/Delete',array('id'=>$data['id'])).'&'.tep_get_all_get_params('id')?>';});">Delete</a></td>
                   
                </tr>
                <?php } ?>
        </table>
        <div class="pager-box ">
        <?php $this->widget('CLinkPager', array('cssFile'=>false,'header'=>'','pages' => $dataProvider->pagination));?>
        </div>

</div>
