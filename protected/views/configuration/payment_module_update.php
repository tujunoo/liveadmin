<div class="sub-title-box">
<h2 class="sub-title"><?php echo CHtml::encode($this->pageTitle)?></h2>
</div>
<?php $this->displayMessage(5);?>
<div class="sub-content">
  <form  action="<?php echo $this->createUrl('PaymentModuleUpdate',array('file_name'=>$_GET['file_name'])); ?>" method="post">
    <div class="tab-box">
      <div class="tab-tit">
        <h3 class="current"><span>Basic Attributes</span></h3>
      </div>
      <!-- Basic Tab Start -->
      <div class="tab-con">
        <div class="frmWrap">
        <?php if(!empty($keys)){
					foreach($keys as $key) { 
					 $data = Yii::app()->db->createCommand("SELECT * FROM `". Configuration::model()->tableName() . "` WHERE  `key`= '".$key."'")->queryRow();
					?>
          <div class="flist">
            <p class="flabel"><?php echo $data['title']; ?></p>
            <div class="fcon"> <?php echo CHtml::textField($data['key'],$data['value'],array('size'=>60))?> </div>
          </div>
          <?php } } ?>
         </div>
      </div>
      <!-- Basic Tab End -->
    </div>
    <!-- Submit Bar -->
    <div class="flist">
      <p class="flabel">&nbsp;</p>
      <?php echo CHtml::hiddenField('file_name',$_GET['file_name']); ?> <?php echo CHtml::submitButton('Submit' ,array('class'=>'btn'))?> <?php echo CHtml::link('Go Back',$this->createUrl('PaymentModules',array('file_name'=>$_GET['file_name'])),array('class'=>'btn3 indent5'));?> </div>
  </form>
</div>
