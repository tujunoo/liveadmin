<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"></meta>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl; ?>/css/base.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl; ?>/js/datepicker/jquery.datepicker.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl; ?>/js/popwin/popwin.css" />
    <?php Yii::app()->clientScript->registerCoreScript('jquery') ?>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/sub.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/base.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/popwin/jquery.popwin.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/datepicker/jquery.datepicker.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl.'/js/jquery.tablesorter.js'?>" ></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/underscore-min.js"></script>
</head>
<body>
<div id="admin-contents">
    <?php echo $content; ?>
</div>
</body>
</html>
