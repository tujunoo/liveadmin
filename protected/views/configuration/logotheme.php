<style>
.searchbox {
        padding:5px;
        float:right;
}
</style>
<script type="text/javascript">
/*<![CDATA[*/
$(document).ready(function() {
    $.datepicker.setDefaults($.datepicker.regional['nl']);
    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd 00:00:00' });
    $('.datepicker').datepicker();
});
function chtml_statebutton(obj,url){
        $.get(url ,{},function(data){
                if(data == "1"){
                        $(obj).removeClass();
                        $(obj).addClass("state-active");
                }else if(data == "0"){
                        $(obj).removeClass();
                        $(obj).addClass("state-inactive");
                }
                else alert(data);
        }
        )
}
/*]]>*/
</script>
<div class="sub-title-box">
</div>
<?php
$this->displayMessage(5);
?>
<div class="sub-content no-folding">
                <?php
foreach ($data as $i => $v)
{
?>

  <form  action="<?php
    echo $this->createUrl('Configuration/Logotheme', array('id' => $i))
?>" method="post" enctype="multipart/form-data">
    <div class="tab-box">
      <div class="tab-tit">
        <h3 class="current"><span>Festival <?php
    echo $i;
?></span></h3>
      </div>
      <!-- Basic Tab Start -->
      <div class="tab-con">
        <div class="frmWrap">
          <div class="flist">
            <p class="flabel">Start Time:</p>
            <div class="fcon"> <?php
    echo CHtml::textField('configuration[start]', date('Y-m-d H:i:s',$v['START']), array('size' => 20,'class'=>'datepicker','id'=>'start_'.$i))
?> </div>
            <p class="flabel">End Time:</p>
            <div class="fcon"><?php
    echo CHtml::textField('configuration[end]', date('Y-m-d H:i:s',$v['END']), array('size' => 20,'class'=>'datepicker','id'=>'end_'.$i))
?></div>
          </div>
          <div class="flist">
            <p class="flabel">Priority:</p>
            <div class="fcon"><?php
    echo CHtml::textField('configuration[priority]', $v['PRIORITY'], array('size' => 3))
?></div>
          </div>
          <div class="flist">
            <p class="flabel">Enable（Click to Change）:</p>
            <div class="fcon">
            <a onclick="chtml_statebutton(this,'<?php echo $this->createUrl('Configuration/changeState', array('id' => $i))?>')"
            class="<?php if($v['ENABLE'])echo 'state-active';else echo 'state-inactive' ?>" href="#"> </a>
            </div>
          </div>
           <div class="flist">
            <p class="flabel">1200px Image:</p>
            <div class="fcon"><?php echo CHtml::fileField('logo');?>
            <a href="<?php echo $root.$v['LOGO'];?>" target="_blank"><?php echo $root.$v['LOGO'];?></a></div>
          </div>
           <div class="flist">
            <p class="flabel">1000px Image:</p>
            <div class="fcon"><?php echo CHtml::fileField('img');?>
            <a href="<?php echo $root.$v['IMG'];?>" target="_blank"><?php echo $root.$v['IMG'];?></a></div>
          </div>
        </div>
      </div>
      <!-- Basic Tab End -->
    </div>
    <!-- Submit Bar -->
    <div class="flist">
      <p class="flabel">&nbsp;</p>
      <?php
    echo CHtml::submitButton('Submit', array('class' => 'btn'))
?> <?php
    echo CHtml::button('Go Back', array('class' => 'btn2', 'onclick' => 'javascript:history.back(1)'))
?> </div>
  </form>
      <?php
}
?>
        <div class="pager-box ">
        </div>
</div>