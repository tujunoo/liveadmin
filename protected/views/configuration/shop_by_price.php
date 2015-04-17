<?php $this->displayMessage(5); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tbody>
    <tr> 
      <!-- body_text //-->
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tbody>
            <tr>
              <td width="41%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                      <td class="pageHeading">&nbsp;</td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
            <tr>
              <td class="al-top"><table border="0" width="99%" cellspacing="0" cellpadding="0" class="table2 zebra">
                  <tr class="dataTableHeadingRow">
                    <th width="370" class="dataTableHeadingContent"><?php echo Yii::t('shopbyprice',TABLE_HEADING_OPTIONS); ?></th>
                    <th width="19" align="right" class="dataTableHeadingContent"><?php echo Yii::t('shopbyprice', TABLE_HEADING_ACTION); ?>&nbsp;</th>
                  </tr>
                  <?php

  if ($oid == 1) {
    echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . $this->createUrl('Configuration/shopByPrice',array( 'oID'=>'1','action'=>'edit')) . '\'">' . "\n";
  } else {
    echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . $this->createUrl('Configuration/shopByPrice',array( 'oID'=>'1')). '\'">' . "\n";
  }
?>
                  <tr>
                  	<td class="dataTableContent"><?php echo Yii::t('shopbyprice',TEXT_INFO_OPTION_1); ?></td>
                    <td class="dataTableContent" align="right"><?php if ($_GET['oID'] == 1) { echo CHtml::image($this->imageUrl.'icon_arrow_right.gif',''); } else { echo '<a href="' . $this->createUrl('Configuration/shopByPrice',array( 'oID'=>'1')). '">' . CHtml::image($this->imageUrl.'icon_info.gif',  Yii::t('main', IMAGE_ICON_INFO)) . '</a>'; } ?>&nbsp;</td>
                  </tr>
                  <?php
  if ($oid == 2) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . $this->createUrl('Configuration/shopByPrice',array( 'oID'=>'2','action'=>'edit')) . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' .$this->createUrl('Configuration/shopByPrice',array( 'oID'=>'2')) . '\'">' . "\n";
    }
?>
                  <tr>
                  	<td class="dataTableContent"><?php echo Yii::t('shopbyprice',TEXT_INFO_OPTION_2); ?></td>
                    <td class="dataTableContent" align="right"><?php if ($_GET['oID'] == 2) { echo CHtml::image($this->imageUrl.'icon_arrow_right.gif', ''); } else { echo '<a href="' . $this->createUrl('Configuration/shopByPrice',array( 'oID'=>'2')) . '">' . CHtml::image($this->imageUrl.'icon_info.gif',  Yii::t('main', IMAGE_ICON_INFO)) . '</a>'; } ?>&nbsp;</td>
                  </tr>
              </table></td>
              <?php
                switch ($_GET['action']) {
				case 'edit':
				  if ($oid == 1) { ?>
              <td width="7%" valign="top" style="padding-left:10px;"><table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                    <tr class="infoBoxHeading">
                      <td class="infoBoxHeading"><b><?php echo Yii::t('shopbyprice', TEXT_EDIT_HEADING_OPTIONS); ?></b></td>
                    </tr>
                  </tbody>
                </table>
                <?php echo CHtml::beginForm($this->createUrl('Configuration/shopByPrice',array('oID'=>'1','action'=>'save')),'post',array('name' => 'sbp_options' ));  ?>
                <table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                    <tr>
                      <td class="infoBoxContent"><?php 
							if ($error_message != '') {
							 echo '<font color="red">' . $error_message . '</font>';
							} ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo Yii::t('shopbyprice', TEXT_EDIT_OPTIONS_INTRO); ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo '<br>' . Yii::t('shopbyprice', TEXT_INFO_RANGES) . '<br>' . CHtml::textField('sbp_ranges', MODULE_SHOPBYPRICE_RANGES); ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo '<br>' . Yii::t('shopbyprice', TEXT_INFO_OVER) . '<br>' . CHtml::radioButtonList('configuration_value', MODULE_SHOPBYPRICE_OVER,array('True'=>'True','False'=>'False')); ?></td>
                    </tr>
                    <tr>
                      <td align="center" class="infoBoxContent"><br>
                        <?php echo CHtml::imageButton($this->imageUrl.$this->language.'/button_update.gif', array('style'=>'border:0px; padding:0px;')) . '&nbsp;' .'<a href="'.$this->createUrl('Configuration/shopByPrice').'">'.CHtml::image($this->imageUrl.$this->language.'/button_cancel.gif','Cancel').'</a>'; ?></td>
                    </tr>
                  </tbody>
                </table>
                <?php echo CHtml::endForm(); ?></td>
              <?php
				  }elseif (MODULE_SHOPBYPRICE_RANGES > 0) { ?>
				<td width="23%" valign="top" style="padding-left:10px;"><table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                    <tr class="infoBoxHeading">
                      <td class="infoBoxHeading"><b><?php echo Yii::t('shopbyprice', TEXT_EDIT_HEADING_RANGE); ?></b></td>
                    </tr>
                  </tbody>
                </table>
                <?php echo CHtml::beginForm($this->createUrl('Configuration/shopByPrice',array('oID'=>'2','action'=>'save')),'post',array('name' => 'sbp_options' ));  ?>
                <table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                  	<tr>
                      <td class="infoBoxContent"><?php 
							if ($error_message != '') {
							 echo '<font color="red">' . $error_message . '</font>';
							} ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo Yii::t('shopbyprice', TEXT_EDIT_RANGE_INTRO); ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo '<br>' . Yii::t('shopbyprice', TEXT_INFO_UNDER) . '<br>' . CHtml::textField('sbp_range[0]', $sbp_array[0]); ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php 
					  for ($i = 1, $ii = MODULE_SHOPBYPRICE_RANGES; $i < $ii; $i++) {
					  echo '<br>' . Yii::t('shopbyprice', TEXT_INFO_TO) . '<br>' . CHtml::textField('sbp_range['.$i.']', $sbp_array[$i]); } ?></td>
                    </tr>
                    <tr>
                      <td align="center" class="infoBoxContent"><br>
                        <?php echo CHtml::imageButton($this->imageUrl.$this->language.'/button_update.gif', array('style'=>'border:0px; padding:0px;')) . '&nbsp;' .'<a href="'.$this->createUrl('Configuration/shopByPrice',array('oID'=>'1')).'">'.CHtml::image($this->imageUrl.$this->language.'/button_cancel.gif','Cancel').'</a>'; ?></td>
                    </tr>
                  </tbody>
                </table>
                <?php echo CHtml::endForm(); ?></td>	  
			<?php }
                 break;
                 default:
                 if ($oid == 1) { ?>
              <td width="16%" valign="top" style="padding-left:10px;"><table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                    <tr class="infoBoxHeading">
                      <td class="infoBoxHeading"><b><?php echo Yii::t('shopbyprice', TEXT_EDIT_HEADING_OPTIONS); ?></b></td>
                    </tr>
                  </tbody>
                </table>
                <table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                  	<tr>
                      <td class="infoBoxContent" align="center"><?php echo '<a href="'.$this->createUrl('Configuration/shopByPrice', array('oID'=>'1','action'=>'edit')) . '">' . CHtml::image($this->imageUrl.$this->language.'/button_edit.gif', Yii::t('main',IMAGE_EDIT)) . '</a>'; ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo Yii::t('shopbyprice', TEXT_INFO_RANGES). ' ' . MODULE_SHOPBYPRICE_RANGES; ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo ' ' . Yii::t('shopbyprice', TEXT_INFO_OVER) . ' ' . MODULE_SHOPBYPRICE_OVER; ?></td>
                    </tr>
                    <tr>
                      <td class="infoBoxContent"><?php echo '<br>' . Yii::t('shopbyprice', TEXT_INFO_OPTIONS_DESCRIPTION) . '<br>' . $tcInfo->tax_class_description; ?></td>
                    </tr>
                  </tbody>
                </table>
                </td>
              <?php
              	} else { ?>
				<td width="13%" valign="top" style="padding-left:10px;"><table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                    <tr class="infoBoxHeading">
                      <td class="infoBoxHeading"><b><?php echo '<b>' . Yii::t('shopbyprice',TEXT_EDIT_HEADING_RANGE).'</b>'; ?></b></td>
                    </tr>
                  </tbody>
                </table>
                <table cellspacing="0" cellpadding="2" border="0" width="100%">
                  <tbody>
                  	<?php if (! MODULE_SHOPBYPRICE_RANGES > 0) { ?>
                  	<tr>
                      <td class="infoBoxContent"><?php echo Yii::t('shopbyprice',TEXT_INFO_ZERORANGE); ?></td>
                    </tr>
                    <?php } elseif (MODULE_SHOPBYPRICE_RANGE == '') { ?>
					<tr>
                       <td class="infoBoxContent" align="center"><?php echo '<a href="' . $this->createUrl('Configuration/shopByPrice', array('oID'=>'2','action'=>'edit')) . '">' .  CHtml::image($this->imageUrl.$this->language.'/button_edit.gif', Yii::t('main',IMAGE_EDIT)). '</a></td></tr>';
					   echo '<tr><td class="infoBoxContent">'.Yii::t('shopbyprice',TEXT_INFO_NORANGE).'</td></tr>';
                     }  else { ?>
                     <tr>
                       <td class="infoBoxContent" align="center"><?php echo '<a href="' . $this->createUrl('Configuration/shopByPrice', array('oID'=>'2','action'=>'edit')) . '">' .  CHtml::image($this->imageUrl.$this->language.'/button_edit.gif', Yii::t('main',IMAGE_EDIT)). '</a></td></tr>'; ?> 
                    <tr>
                      <td class="infoBoxContent"><?php echo '<br>'.Yii::t('shopbyprice',TEXT_INFO_UNDER). $sbp_array[0]; ?></td>
                    </tr>
                    <?php for ($i = 1, $ii = count($sbp_array); $i < $ii; $i++) { ?>
                    <tr>
                      <td class="infoBoxContent"><?php echo '<br>'.Yii::t('shopbyprice',TEXT_INFO_TO). $sbp_array[$i]; ?></td>
                    </tr>
                    <?php } ?>
				<?php } if (MODULE_SHOPBYPRICE_OVER) { ?>
                	<tr>
                      <td class="infoBoxContent"><?php echo '<br>'.$sbp_array[$i-1]. Yii::t('shopbyprice',TEXT_INFO_ABOVE); ?></td>
                    </tr>
                <?php } ?>
                  </tbody>
                </table>
                </td>	
			<?php	
			    }
                break;
              } ?>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
</td>
<!-- body_text_eof //-->
</tr>
</tbody>
</table>
