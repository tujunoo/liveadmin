<?php

class SiteController extends BaseController
{
    function __construct($id,$module){
        parent::__construct($id,$module);
        $this->layout = 'blank_layout';
    }
    public function actionIndex()
    {
        if (Yii::app()->user->isGuest) {
            $this->forward('site/login');
        } else {
            $this->render('frame');
        }
    }

    public function actionAction1()
    {
        echo 'action1';
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        if (!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH)
            throw new CHttpException(500, "This application requires that PHP was compiled with Blowfish support for crypt().");

        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (IS_DEV_SITE) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        }
    }
}
