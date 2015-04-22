<?php $this->displayMessage(5);?>
<div class="sub-content">
  <form  action="<?php echo $this->createUrl('Starnews/', array('id' => $_GET['id'], 'StarNews_page' => $_GET['StarNews_page']));?>"
         method="post" validate="yes" enctype="multipart/form-data">
    <div class="tab-box">
      <div class="tab-tit">
        <h3 class="current"><span>Basic Attributes</span></h3>
      </div>
      <!-- Basic Tab Start -->
      <div class="tab-con">
        <div class="frmWrap">
          <div class="flist">
            <p class="flabel">Starnews Title:</p>
            <div class="fcon"> <?php echo CHtml::textField('Starnews[title]',$model->title,array('must' => 'yes','class' => 'itext w500'))?>&nbsp;</div>
          </div>

          <div class="flist">
            <p class="flabel">Star name:</p>
            <div class="fcon"> <?php echo CHtml::textField('Starnews[star_name]',$model->star_name,array('class' => 'itext w500'))?></div>
        </div>

            <div class="flist">
                <p class="flabel">Landing Page URL:</p>
                <div class="fcon">
                   <?php echo CHtml::textField('Starnews[content]',$model->content,array('class' => 'itext w500'))?>
                </div>
            </div>
      
  
    
     
         
     
      <!-- Basic Tab End -->
    </div>
 

  </form>
</div>
