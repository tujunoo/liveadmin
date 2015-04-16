<?php

class ConfigurationController extends BaseController
{
        /**
         * Displays a particular model.
         * @param integer $id the ID of the model to be displayed
         */
        public function actionView($id)
        {
                $this->render('view',array(
                        'model'=>$this->loadModel($id),
                ));
        }

        /**
         * Creates a new model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         */
        public function actionCreate()
        {
                $model=new Configuration;

                if(isset($_POST['configuration']))
                {
                        $model->setScenario('insert');
                        $model->setAttributes($_POST['configuration']);
                        $model->attributes=$_POST['configuration'];
						$model->sort_order = (int)$_POST['configuration']['sort_order'];
						$model->created = new CDbExpression('now()');
                        if($model->insert())
                                $this->addFlash('New record insert successfully.....');
                        else
                                $this->addErrorMessage('Insert fail...');
                }
                $this->redirect($this->createUrl('Configuration/index',(($u=$_SESSION['configuration_group_id'])?array('gID'=>$u):array())));
        }

        /**
         * Creates a new model.
         * mehod for update existing records
         */

        public function actionModify()
        {

                $confiGroup = array();
                $configGroup = ConfigurationGroup::model()->getAllGroups();
                        foreach($configGroup as $key => $value){
                                $confiGroup[$value->configuration_group_id] = $value->title;
                }

                if(isset($_GET['cID']))
                {
						$this->setPageTitle('Modify  Configuration');
						$this->breadcrumbs->add('Configuration',$this->createUrl('Configuration/index',(($u=$_SESSION['configuration_group_id'])?array('gID'=>$u):array())));
						$this->breadcrumbs->add('Modify  Configuration');
                        $model=$this->loadModel($_GET['cID']);
                        //Configuration Permisson
                        //conf_all   : allow to update all configuration items
                        //conf_groupx : allow the group
                        //conf_item_x: allow update item key
                        //vincent 20150204
//                        $permission_all = 'conf_all';
//                        $permission_group = 'conf_group'.$model->configuration_group_id;
//                        $permission_item = 'conf_item_'.strtolower($model->key);
                        
//                        $user = Yii::app()->user ;
//                        if(!($user->checkAccess($permission_all)
//                        		|| $user->checkAccess($permission_group)
//                        		|| $user->checkAccess($permission_item))){
//
//                           throw new CHttpException(403,"You are not allow to modify this configuration item .
//                        			To modify this item , you must have one of those permissions \"{$permission_all}\",\"{$permission_group}\",\"{$permission_item}\"");
//                        }
                        if(isset($_POST['configuration']))
                        {
                                $needUpdate = array('title','key','value','description','sort_order', 'configuration_group_id');
                                $model->setAttributes($_POST['configuration']);
								$model->sort_order = (int)$_POST['configuration']['sort_order'];
                                if($model->validate()){
                                        if($model->update($needUpdate)) {
												$this->addFlash('Record "#'.$_GET['cID'].'" Update successful.');
												$this->redirect($this->createUrl('Configuration/index').'?'.tep_get_all_get_params(array('cID')));
										} else {
                                                $this->addMessage('Update fail.',self::MSG_ERROR);
										}
                                }else{
                                        $this->addMessage($model);
                                }
                        }
                        $data = $model->findByPk($_GET['cID']);

                        $this->render('update',array(
                                'model'=>$model,
                                'data' =>$data ,
                                'do_modify' => 'Modify',
                                'confiGroup' =>  $confiGroup
                        ));
                }else{
						$this->setPageTitle('Add Configuration');
						$this->breadcrumbs->add('Configuration',$this->createUrl('Configuration/index',(($u=$_SESSION['configuration_group_id'])?array('gID'=>$u):array())));
						$this->breadcrumbs->add('Add Configuration');
                                $model = new Configuration();
                                $this->render('update',array(
                                        'model'=>$model,
                                        'data' => $m ,
                                        'do_modify' => '/configuration/Create',
                                        'confiGroup' =>  $confiGroup
                                ));
                }
        }

        /**
         * Deletes a particular model.
         * If deletion is successful, the browser will be redirected to the 'admin' page.
         * @param integer $id the ID of the model to be deleted
         */
        public function actionDelete()
        {
                if(isset($_GET['id']) && $_GET['id'] != '')
                {
                        if($this->loadModel($_GET['id'])->delete()){
                                $this->addFlash('Record has deleted.');
                        }else{
                                $this->addErrorMessage('Record delete fail.');
                        }
                }
                $this->redirect($this->createUrl('Configuration/index',(($u=$_SESSION['configuration_group_id'])?array('gID'=>$u):array())));
        }

        public function actionIndex()
        {
				if((int)$_GET['gID'] > 0){
					$_SESSION['configuration_group_id'] = $_GET['gID'];
					$article_group = ConfigurationGroup::model()->findByPk($_GET['gID']);
                	$this->setPageTitle($article_group->title);
				}else{
					unset($_SESSION['configuration_group_id']);
                	$this->setPageTitle('Configuration');
				}
				$this->breadcrumbs->add('Configuration',$this->createUrl('Configuration/index',(($u=$_SESSION['configuration_group_id'])?array('gID'=>$u):array())));
				$criteria = new CDbCriteria();
                $criteria->order = 'sort_order';

                if($_GET['search'] != ''){
                        $keyword = trim($_GET['search']);
                        if(is_numeric($keyword)){
                                $criteria->addCondition('configuration_id = '.intval($keyword),'OR');
                        }else {
                                $criteria->addSearchCondition('title', $keyword,true,'OR');
                                $criteria->addSearchCondition('value', $keyword,true,'OR');
                                $criteria->addSearchCondition('`key`', $keyword,true,'OR');
                        }
                }
                if($_GET['gID'] != ''){
                                $criteria->addCondition('configuration_group_id = '.intval($_GET['gID']),'OR');
                }
                $dataProvider=new CActiveDataProvider('Configuration',array(
                 'criteria'=>$criteria,
                 'pagination'=>array(
                        'pageSize'=>30,
                        ),
                ));

                $this->render('index',array(
                        'dataProvider'=>$dataProvider,
                ));
        }
		public function actionShopByPrice()
		{
			$this->setPageTitle(Yii::t('shopbyprice', HEADING_TITLE));
			$this->breadcrumbs->add('Configuration',$this->createUrl('Configuration/index',(($u=$_SESSION['configuration_group_id'])?array('gID'=>$u):array())));
			  $action = (isset($_GET['action']) ? $_GET['action'] : '');
			  $error_message = '';
			  if (!isset($_GET['oID'])) {
				$oid = 1;
			  } else {
				$oid = $_GET['oID'];
			  } 
			  if ($action == 'save') {
				if ($oid == 1) {
				  $sbp_ranges = tep_db_prepare_input($_POST['sbp_ranges']);
				  if (is_numeric($sbp_ranges)) {
					Yii::app()->db->createCommand("update `".Configuration::model()->tableName()."` set `value` = '".(int)$sbp_ranges."' where `key` ='MODULE_SHOPBYPRICE_RANGES'")->execute();
					define('MODULE_SHOPBYPRICE_RANGES', $sbp_ranges);
				  } else {
					$error_message = Yii::t('shopbyprice',TEXT_EDIT_ERROR_RANGES);
					$this->addErrorMessage($error_message);
				  }
				  Yii::app()->db->createCommand("update `".Configuration::model()->tableName()."` set `value` = '".$_POST['configuration_value']."' where `key` ='MODULE_SHOPBYPRICE_OVER'")->execute();	
				  if ($error_message != '') {
					$action = 'edit';
				  }
				} else {
				  $sbp_input_array = $_POST['sbp_range'];
				  $sbp_array[0] = tep_db_prepare_input($sbp_input_array[0]);
				  for ($i = 1, $ii = MODULE_SHOPBYPRICE_RANGES; $i < $ii; $i++) {
					$sbp_array[$i] = tep_db_prepare_input($sbp_input_array[$i]);
					if (! is_numeric($sbp_array[$i])) {
					  $error_message = Yii::t('shopbyprice',TEXT_EDIT_ERROR_NUMERIC);
					  $this->addErrorMessage($error_message);
					} elseif ($sbp_array[$i] <= $sbp_array[$i - 1]) {
					  $error_message = Yii::t('shopbyprice',TEXT_EDIT_ERROR_RANGE);
					  $this->addErrorMessage($error_message);
					}
				  }
				  if ($error_message == '') {
					$serial_array = serialize($sbp_array);
					$text = tep_db_input($serial_array);
					Yii::app()->db->createCommand("update `".Configuration::model()->tableName()."` set `value` = '".$text."' where `key` ='MODULE_SHOPBYPRICE_RANGE'")->execute();
					define('MODULE_SHOPBYPRICE_RANGE', $serial_array);
				  } else {
					$action = 'edit';
				  }
				}
			  }
			  $sbp_array = unserialize(MODULE_SHOPBYPRICE_RANGE);
			$this->render('shop_by_price',array('oid'=>$oid,'error_message'=>$error_message,'sbp_array'=>$sbp_array));	
		}
		public function actionPayment()
		{
			$this->setPageTitle("Payment Modules");
			$this->render('payment');	
		}
		public function actionPaymentModules()
		{
			if(file_exists(WEBEEZ_LIB.'/classes/payment_class/'.$_GET['file_name'].".php")){	
				include_once(Yii::getPathOfAlias('webeez.classes.payment_class').DIRECTORY_SEPARATOR.$_GET['file_name'].".php");
			}
			$class_name = explode('.',$_GET['file_name']);
			$this->setPageTitle(ucfirst($class_name[0]));
			$modules = new $class_name[0];
			$keys = $modules->keys();
			$this->render('payment_modules',array('keys'=>$keys,'modules'=>$modules));	
		}
		public function actionPaymentModuleUpdate()
		{
			if(file_exists(WEBEEZ_LIB.'/classes/payment_class/'.$_GET['file_name'].".php")){	
				include_once(Yii::getPathOfAlias('webeez.classes.payment_class').DIRECTORY_SEPARATOR.$_GET['file_name'].".php");
			}
			$class_name = explode('.',$_GET['file_name']);
			$this->setPageTitle(ucfirst($class_name[0]));
			$modules = new $class_name[0];
			$keys = $modules->keys();
			if(isset($_POST['file_name']))
			{
				foreach($_POST as $key=>$value){
				Yii::app()->db->createCommand("update " . Configuration::model()->tableName() . " set value = '" . $value . "' where  	`key` = '" . $key . "'")->execute();	
				}
				$this->addMessage('Update successful.');
			}
			$this->render('payment_module_update',array('keys'=>$keys));	
		}
        /**
         * Returns the data model based on the primary key given in the GET variable.
         * If the data model is not found, an HTTP exception will be raised.
         * @param integer the ID of the model to be loaded
         */
        public function loadModel($id)
        {
                $model=Configuration::model()->findByPk($id);
                if($model===null)
                        throw new CHttpException(404,'The requested page does not exist.');
                return $model;
        }

        /**
         * Performs the AJAX validation.
         * @param CModel the model to be validated
         */
        protected function performAjaxValidation($model)
        {
                if(isset($_POST['ajax']) && $_POST['ajax']==='configuration-form')
                {
                        echo CActiveForm::validate($model);
                        Yii::app()->end();
                }
        }

        public function actionLogotheme()
        {
            $id = intval($_GET['id']);
            $this->breadcrumbs->add('Modify Logo Theme');
            if ($_POST['configuration'])
            {
                $conf = $_POST['configuration'];
                foreach ($conf as $k => $v)
                {
                    if ($v || $v === '0')
                    {
                        if($k=='start' || $k=='end'){
                            $v = strtotime($v);
}
                        Configuration::model()->updateAll(array('value' => $v), '`'.Configuration::model()->tableName().'`.key="FESTIVAL_THEME'.$id.'_'.strtoupper($k).'"');
                    }
                }
                Yii::import('webeez.extensions.Uploader');
                $subThemePath = Yii::app()->params['storeId'] == 4 ? 'image/':'landing/theme/';
                $save_path = rtrim(Yii::app()->params['mainImagesabsuPath'], '/') . '/image/';
                if(Yii::app()->params['storeId'] != 4){
                    $save_path = Yii::app()->params['mainImagesabsuPath'].$subThemePath;
                }
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                }
                if($_FILES['img']['name']){
                    $up = Uploader::create($save_path)->renameFormat('[uid][extension]')->existFile(Uploader::EXISTS_RENAME);
                    $up->process('img');
                    $file = $save_path . $_POST['img'];
                    if (file_exists($file)){
                        //upload to cdn folder start
                        if(Yii::app()->params['storeId'] != 4){
                            Yii::app()->rackspaceConnect->upload($file, array('dir' => '/images/'.$subThemePath));
                        }else{
                            Yii::app()->rackspaceConnect->upload($file);
                        }
                        //upload to cdn folder end
                    }
                    $file = '/images/'. $subThemePath . $_POST['img'];
                    Configuration::model()->updateAll(array('value' => $file), '`'.Configuration::model()->tableName().'`.key="FESTIVAL_THEME'.$id.'_IMG"');
                }
                if($_FILES['logo']['name']){
                    $up = Uploader::create($save_path)->renameFormat('[uid][extension]')->existFile(Uploader::EXISTS_RENAME);
                    $up->process('logo');
                    $file = $save_path . $_POST['logo'];
                    if (file_exists($file)){
                        //upload to cdn folder start
                        if(Yii::app()->params['storeId'] != 4){
                            Yii::app()->rackspaceConnect->upload($file, array('dir' => '/images/'.$subThemePath));
                        }else{
                            Yii::app()->rackspaceConnect->upload($file);
                        }
                        //upload to cdn folder end
                    }
                    $file = '/images/'. $subThemePath . $_POST['logo'];
                    Configuration::model()->updateAll(array('value' => $file), '`'.Configuration::model()->tableName().'`.key="FESTIVAL_THEME'.$id.'_LOGO"');
                }

            }
            $criteria = new CDbCriteria();
            $criteria->order = 't.key';
            $criteria->addSearchCondition('t.key', 'FESTIVAL_THEME%', false);
            $criteria->addCondition('configuration_group_id = 1'); //本来打算用999的，发现新数据库里面没有999，为了保险，改放入group 1了
            $tmp = Configuration::model()->findAll($criteria);
            $data = array();
            foreach ($tmp as $i => $v)
            {
                $key = substr($v->key, 14, 1);
                $data[$key][substr($v->key, 16)] = $v->value;
            }
            $this->render('logotheme', array('data' => $data, 'root'=>Yii::app()->params['catalogUrl']));
        }

        public function actionChangeState(){
            $id = intval($_GET['id']);
            $model = new Configuration();
            $conf = $model->findByAttributes(array('key'=>'FESTIVAL_THEME'.$id.'_ENABLE'));
            if($conf->value){
                echo '0';
                Configuration::model()->updateAll(array('value' => '0'), '`'.Configuration::model()->tableName().'`.key="FESTIVAL_THEME'.$id.'_ENABLE"');
            }else{
                echo '1';
                Configuration::model()->updateAll(array('value' => '1'), '`'.Configuration::model()->tableName().'`.key="FESTIVAL_THEME'.$id.'_ENABLE"');
            }
        }


    /*
        配置美洲高校学生惠活动首页的配置项
    */
    public function actionStudentPromotion()
    {
        $this->breadcrumbs->add('Configuration', $this->createUrl('Configuration/Index'));
        $this->breadcrumbs->add('TFF Student Promotion');
        $this->setPageTitle('TFF Student Promotion');

        $cfg = CJSON::decode(Configuration::model()->getVal('TFF_STUDENT_PROMOTION_SETTING'));

        if (Yii::app()->request->isPostRequest) {
            $cfg = Yii::app()->request->getParam('cfg');
            $cfg['position_a_products'] = implode(',', array_map('intval', (array) explode(',', $cfg['position_a_products'])));
            $cfg['position_b_products'] = implode(',', array_map('intval', (array) explode(',', $cfg['position_b_products'])));
            for ($i = 0; $i < 4; $i++) {
                $cfg['coupon'][$i]['id'] = intval($cfg['coupon'][$i]['id']);
                $cfg['coupon'][$i]['desc'] = urlencode($cfg['coupon'][$i]['desc']);
            }

            Configuration::model()->setValueByKey('TFF_STUDENT_PROMOTION_SETTING', CJSON::encode($cfg));
            $this->addFlash('Saved Successful.');
        }
        for ($i = 0; $i < 4; $i++) {
            $cfg['coupon'][$i]['desc'] = urldecode($cfg['coupon'][$i]['desc']);
        }

        $this->render('student_promotion', array(
            'cfg'           => $cfg,
        ));
    }
}
