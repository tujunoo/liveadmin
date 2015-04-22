<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"  />
    <link rel="shortcut icon" href="<?php echo $this->baseUrl; ?>/img/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl; ?>/css/base.css" />
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/base.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/general.js"></script>
    <script type="text/javascript">
        if (top.location !== self.location) {
            top.location=self.location;
        }
    </script>
</head>
<body id="admin-body">
<div id="admin-top">
    <a href="<?php echo $this->createUrl('site/index'); ?>"><img class="admin-logo" src="<?php echo $this->baseUrl; ?>/img/admin_logo.png" alt="" border="0" /></a>
    <h1 class="admin-title">Store Admin</h1>
    <div class="welcome">
        Welcome,
        <?php echo Yii::app()->user->name;?>
        |<a href="<?php echo $this->createUrl('site/logout')?>">Log off</a>
    </div>
</div>
<div id="admin-top-toggle" class="admin-top-toggle"></div>
<div id="admin-side-toggle" class="admin-side-toggle"></div>
<div id="admin-nav-sub-wrap"></div>
<div id="admin-side">
    <h2 class="admin-side-title">
        <span class="titl"></span>
        <span class="titm">Main Menu</span>
        <span class="titr"></span>
    </h2>
    <div id="admin-side-con">
        <div class="admin-nav-box">
        <ul id="admin-nav" class="admin-nav">
        <li class="navtit"> <a href="<?php echo $this->createUrl('configuration/index')?>" target="conframe">meunu1</a>
            <ul class="navsub">
                <li><a href="<?php echo $this->createUrl('site/action1')?>" target="conframe">Product Listings</a></li>
                <li><a href="<?php echo $this->createUrl('site/action1')?>" target="conframe">Product Type</a></li>
                <li><a href="<?php echo $this->createUrl('site/action1')?>" target="conframe">Provider</a></li>
                <li><a href="<?php echo $this->createUrl('site/action1')?>" target="conframe">Attributes</a></li>
            </ul>
        </li>
        <li class="navtit"> <a href="<?php echo $this->createUrl('banners/index'); ?>" target="conframe">banners</a></li>
        <li class="navtit"> <a href="<?php echo $this->createUrl('configuration/index')?>" target="conframe">configuration</a></li>
        <li class="navtit"> <a href="<?php echo $this->createUrl('starnews/index'); ?>" target="conframe">starnews</a></li>
        </ul>
        </div>
    </div>
</div>

<div id="admin-main">
    <div id="admin-main-con">
        <iframe name="conframe" src="<?php echo $this->createUrl('configuration/index')?>" frameborder="0" width="100%" height="100%" scrolling="auto"></iframe>
    </div>
</div>
<div id="admin-foot">Copyright &copy; 2006-2012 Tours4Fun.com</div>
</body>
</html>
