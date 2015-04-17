<?php $this->displayMessage(5);?>
<div class="sub-content">
  <form  action="<?php echo $this->createUrl('banners/' . $do_action, array('id' => $_GET['id'], 'Banners_page' => $_GET['Banners_page']));?>"
         method="post" validate="yes" enctype="multipart/form-data">
    <div class="tab-box">
      <div class="tab-tit">
        <h3 class="current"><span>Basic Attributes</span></h3>
      </div>
      <!-- Basic Tab Start -->
      <div class="tab-con">
        <div class="frmWrap">
          <div class="flist">
            <p class="flabel">Banner Title:</p>
            <div class="fcon"> <?php echo CHtml::textField('Banners[title]',$data->title,array('must' => 'yes','class' => 'itext w500'))?>&nbsp;<font color="#FF0000">* Required</font></div>
          </div>

          <div class="flist">
            <p class="flabel">Banner Notice Title:</p>
            <div class="fcon"> <?php echo CHtml::textField('Banners[html_text]',$data->html_text,array('class' => 'itext w500'))?></div>
        </div>

            <div class="flist">
                <p class="flabel">Landing Page URL:</p>
                <div class="fcon">
                    <?php echo CHtml::textField('Banners[url]', $data->url, array('must' => 'no', 'class' => 'itext w500')) ?>
                    <span class="hint">Full url with "<b>http://</b>" or <b><a href="<?php echo $this->createUrl('landingpages/LandingPages')?>" target="_blank">landing page id</a></b> <font color="#FF0000">(for mobile non-responsive page only)</font></span>
                </div>
            </div>
            <div class="flist">
                <p class="flabel">Banner Background Color:</p>
                <div class="fcon">
                    <?php echo CHtml::textField('Banners[bgcolor]', $data->bgcolor, array('must' => 'no', 'class' => 'itext w500')) ?>
                    <span class="hint">(For example: #FFFFFF)</span>
                </div>
            </div>
          <div class="flist">
            <p class="flabel">Banner Group:</p>
            <div class="fcon"><?php echo CHtml::dropDownList('Banners[group]',$data->group, Banners::bannerGroup() ,array('must' => 'yes','class' => '')).', or enter a new banner group:';?></div>
            <div class="fcon"><?php echo CHtml::textField('Banners[group_new]','',array('class' => 'itext w180'))?></div>
          </div>
          <div class="flist">
            <p class="flabel">Image:</p>
            <div class="fcon">
            	<div>Upload New Image:<?php echo CHtml::fileField('image',$data->image,array('class' => 'itext w500')); ?></div>
           		<?php if($data->image) : ?>
           		<div>Or File:
            	<?php echo($this->imagesUrl);?><?php echo CHtml::textField('Banners[image_exist]',$data->image,array('class' => 'itext w500','readonly' =>'true'))?>
				<?php echo(CHtml::link('[View]',$this->imagesUrl.$data->image,array('target'=>'_blank')));?>
          </div>
			<?php endif; ?>
			</div>
          </div>
          <div class="flist">
            <p class="flabel">Sort Order:</p>
            <div class="fcon"><?php echo CHtml::textField('Banners[sort_order]',$data->sort_order,array('must' => 'no','class' => 'itext w500'))?></div>
        </div>
          <div class="flist">
            <p class="flabel">Banner Language:</p>
            <div class="fcon"><?php echo CHtml::dropDownList('Banners[language]',$data->language, Banners::bannerLanguage()); ?></div>
      </div>
          <div class="flist">
            <p class="flabel">是否屏蔽某些IP:</p>
            <div class="fcon"><?php echo CHtml::dropDownList('Banners[domestic_ip]',$data->domestic_ip, array('0'=>'允许所有IP访问', '1'=>'禁止中国内IP访问', '2'=>'禁止中国外IP访问')); ?></div>
        </div>
        <div class="flist">
            <p class="flabel">Scheduled At:<br/>(dd/mm/yyyy)</p>
            <div class="fcon"><input type="text" class="itext calendar" name="Banners[scheduled]" id="st_created" value="<?php echo ($data->scheduled?date('Y-m-d',strtotime($data->scheduled)) : date('Y-m-d',time())); ?>" /></div>
        </div>
        <div class="flist">
            <p class="flabel">Expires On:<br/>(dd/mm/yyyy)</p>
            <div class="fcon"><input type="text" class="itext calendar" name="Banners[expiration]" id="st_status_change" value="<?php echo ($data->expiration?date('Y-m-d',strtotime($data->expiration)) : date('Y-m-d',time())); ?>" /></div>
        </div>
        <div class="flist">
            <p class="flabel">Show in Affilate:</p>
            <div class="fcon"><?php echo CHtml::dropDownList('Banners[show_in_affilate]',$data->show_in_affilate, array(1=>'Yes',0=>'No')); ?></div>
        </div>
      </div>
      <!-- Basic Tab End -->
    </div>
    <!-- Submit Bar -->
    <div class="flist">
      <p class="flabel">&nbsp;</p>
      <?php if($do_action == 'modify' )echo CHtml::hiddenField('Banners[banner_id]' ,$data->banner_id)?> <?php echo CHtml::submitButton('Submit' ,array('class'=>'btn'))?> <?php echo CHtml::button('Go Back' ,array('class'=>'btn2','onclick'=>'javascript:history.back(1)'))?> </div>
  </form>
</div>
