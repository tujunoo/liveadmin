<h2 class="sub-title" style="clear:both">
<span><?php echo CHtml::encode($this->pageTitle)?></span>
</h2>
<div class="sub-content no-folding">
        <table class="table1 zebra row-hl">
                <tr>
                        <th><?php echo yii::t('configuration',TABLE_HEADING_MODULES); ?></th>
                        <th><?php echo yii::t('configuration',TABLE_HEADING_SORT_ORDER); ?></th>
                        <th><?php echo yii::t('configuration',TABLE_HEADING_ACTION); ?></th>
                </tr>
                <?php $modules = explode(';', MODULE_PAYMENT_INSTALLED); 
					  sort($modules); 
					  foreach($modules as $mod) { 
		       		  	$file_name = explode('.',$mod);  
						if(file_exists(WEBEEZ_LIB.'/classes/payment_class/'.$mod)){	
							include_once(Yii::getPathOfAlias('webeez.classes.payment_class').DIRECTORY_SEPARATOR.$mod);
							$module = new $file_name[0];
						 
						  ?>
                <tr>
                        <td><?php echo $module->title;?></td>
                        <td><?php echo $module->sort_order;?></td>
                        <td>
                                <a href="<?php echo $this->createUrl('Configuration/PaymentModules',array('file_name'=>$file_name[0]))?>">View</a> |  <a href="<?php echo $this->createUrl('Configuration/PaymentModuleUpdate',array('file_name'=>$file_name[0]))?>">Modify</a>
                        </td>
                </tr>
                <?php }} ?>
        </table>
</div>
