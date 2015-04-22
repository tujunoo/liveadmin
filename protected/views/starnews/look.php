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
            <div class="fcon"> <?php echo CHtml::textField('Starnews[title]',$data->title,array('must' => 'yes','class' => 'itext w500'))?>&nbsp;<font color="#FF0000">* Required</font></div>
          </div>

          <div class="flist">
            <p class="flabel">Star name:</p>
            <div class="fcon"> <?php echo CHtml::textField('Starnews[star_name]',$data->html_text,array('class' => 'itext w500'))?></div>
        </div>

            <div class="flist">
                <p class="flabel">Landing Page URL:</p>
                <div class="fcon">
                    <?php echo CHtml::textField('Banners[url]', $data->url, array('must' => 'no', 'class' => 'itext w500')) ?>
                    <span class="hint">Full url with "<b>http://</b>" or <b><a href="<?php echo $this->createUrl('landingpages/LandingPages')?>" target="_blank">landing page id</a></b> <font color="#FF0000">(for mobile non-responsive page only)</font></span>
                </div>
            </div>
      
  
    
     
         
     
      <!-- Basic Tab End -->
    </div>
 

  </form>
</div>
