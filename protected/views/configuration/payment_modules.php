<div class="sub-title-box">
<?php echo CHtml::link('Go Back',$this->createUrl('Payment'),array('class'=>'btn3 indent5'));?>
</div>
<div class="sub-content no-folding">
        <table class="table1 zebra row-hl">
                <tr>
                        <th><?php echo yii::t('configuration',TABLE_HEADING_CONFIGURATION_TITLE); ?></th>
                        <th><?php echo yii::t('configuration',TABLE_HEADING_CONFIGURATION_VALUE); ?></th>
                </tr>
                <tr>
                	<td colspan="2"><?php echo $modules->description; ?></td>
                </tr>
                <?php 
					if(!empty($keys)){
					  foreach($keys as $key) {
						 $data = Yii::app()->db->createCommand("SELECT * FROM `". Configuration::model()->tableName() . "` WHERE  `key`= '".$key."'")->queryRow();
						  ?>
                <tr>
                        <td><?php echo CHtml::encode($data['title'])?></td>
                        <td><?php echo $data['value'];?></td>
               </tr>
                <?php } } else { ?>
                <tr><td colspan="3">No any record found</td></tr>
                <?php } ?>
        </table>
</div>
