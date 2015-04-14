<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"  />
    <link rel="shortcut icon" href="<?php echo $this->baseUrl; ?>/img/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl; ?>/css/base.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl; ?>/css/stylesheet.css" />
    <script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/jquery-1.7.1.min.js"></script>
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
    <a href="<?php echo $this->createUrl('site/index'); ?>">logo</a>
    <h1 class="admin-title">管理中心</h1>
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

                <li class="navtit">
                    <a href="<?php echo $this->createUrl('site/action1')?>" target="conframe">action1</a>
                </li>
                <li class="navtit">
                    <a href="<?php echo $this->createUrl('site/action2')?>" target="conframe">action2</a>
                </li>
                <li class="navtit">
                    <a href="<?php echo $this->createUrl('site/action3')?>" target="conframe">action3</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div id="admin-main">
    <div id="admin-main-con">
        <iframe name="conframe" src="<?php echo $this->createUrl('site/order')?>" frameborder="0" width="100%" height="100%" scrolling="auto"></iframe>
    </div>
</div>
<div id="admin-foot">Copyright &copy; 2006-2012 Tours4Fun.com</div>
</body>
</html>
