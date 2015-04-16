<?php

class BannersController extends BaseController
{
        private $redirectUrl;
        function __construct($id,$module){
                parent::__construct($id,$module);
                $this->breadcrumbs->add('Banner List' , $this->createUrl('Banners/index'));
                $this->redirectUrl = $this->createUrl('banners/index');
				$this->setPageTitle('Banner');
        }

        public function actionCreate()
        {
                $model=new Banners;

                if(isset($_POST['Banners']))
                {
                        if($_POST['Banners']['group_new'] != ''){
                                $_POST['Banners']['group'] = $_POST['Banners']['group_new'];
                        }
                        Yii::import('application.extensions.Uploader');
                        $up = Uploader::create($this->mainImagesabsuPath)->renameFormat('[name][extension]')->existFile(Uploader::EXISTS_RENAME);
                        $up->process('image');

                        $model->image=$_POST['image'];
                        $model->setScenario('insert');
                        $model->setAttributes($_POST['Banners']);
                        $model->url = $_POST['Banners']['url'];
						$model->sort_order = empty($_POST['Banners']['sort_order'])?'0':$_POST['Banners']['sort_order'];
                        $model->language = $_POST['Banners']['language'];
                        $model->html_text = $_POST['Banners']['html_text'];
                        $model->created = date('Y-m-d H:i:s', time());
                        $model->scheduled = $_POST['Banners']['scheduled'];
                        $model->expiration = $_POST['Banners']['expiration'];
						$model->show_in_affilate = $_POST['Banners']['show_in_affilate'];
                        $model->bgcolor=$_POST['Banners']['bgcolor'];
                        $model->attributes=$_POST['Banners'];

                        if($model->insert()){
                                $this->addFlash('Banner insert successfully.....');
								//upload to cdn folder start
//								Yii::app()->rackspaceConnect->upload($this->mainImagesabsuPath.$_POST['image']);
								//upload to cdn folder end
                        }else{
                                $this->addErrorMessage('Insert fail.');
                        }
                }
                $this->redirect($this->createUrl('Banners/index'));
        }

        public function actionModify()
        {
                if(isset($_GET['id']))
                {
						$this->setPageTitle('Edit Banner');
						$this->breadcrumbs->add('Edit Banner');
                        $model=$this->loadModel($_GET['id']);
                        if($model==null){
                                throw new CException('Banner #'.$_REQUEST['id'].' Not Exists');
                        }
                        if(isset($_POST['Banners']))
                        {
                                Yii::import('webeez.extensions.Uploader');
                                $up = Uploader::create($this->mainImagesabsuPath)->renameFormat('[name][extension]')->existFile(Uploader::EXISTS_RENAME);
                                $up->process('image');
                                /* use webeez.extensions.Uploader upload file, Because CUploadedFile can't upload file*/

                                $model->title = $_POST['Banners']['title'];
                                $model->url = $_POST['Banners']['url'];
								$model->sort_order=$_POST['Banners']['sort_order'];
                                $model->language = $_POST['Banners']['language'];
                                $model->domestic_ip = $_POST['Banners']['domestic_ip'];
                                $model->html_text = $_POST['Banners']['html_text'];
                                $model->created = date('Y-m-d H:i:s', time());
                                $model->scheduled = $_POST['Banners']['scheduled'];
                                $model->expiration = $_POST['Banners']['expiration'];
                                $model->show_in_affilate = $_POST['Banners']['show_in_affilate'];
                                $model->bgcolor = $_POST['Banners']['bgcolor'];
                                if($_POST['Banners']['group_new'] != ''){
                                        $model->group = $_POST['Banners']['group_new'];
                                }else{
                                        $model->group = $_POST['Banners']['group'];
                                }

                                if($model->save()){
                                        if($_POST['image'] != ''){
                                                if($model->image != ''){
                                                        @unlink($this->mainImagesabsuPath.$model->image);
														//delete to cdn folder start
														Yii::app()->rackspaceConnect->delete('/images/'.$model->image);
														//delete to cdn folder end
                                                }
                                                $model->image = $_POST['image'];
                                                $model->save();
												//upload to cdn folder start
												Yii::app()->rackspaceConnect->upload($this->mainImagesabsuPath.$_POST['image']);
												//upload to cdn folder end
                                        }
									  $this->addFlash('Banner Updated successful. "#'.$_GET['id'].'"');
									  $this->redirect($this->createUrl('Banners/index',array('Banners_page'=>$_GET['Banners_page'])));exit;
                                }else{
                                        $this->addErrorMessage('Update fail.');
                                }
                         }
                         $m = $model->findByPk($_REQUEST['id']);
                                $this->render('update',array(
                                        'data' => $m,
                                        'model'=>$model,
                                        'do_action'=>'modify',
                                ));
                        }else{
							$this->setPageTitle('Add Banner');
							$this->breadcrumbs->add('Add Banner');
                                        $model=new Banners;
                                        $this->render('update',array(
                                        'model'=>$model,
                                        'do_action'=>'Create',
                                ));
                        }
        }

        public function actionDelete()
        {

                $banner = Banners::model()->findByPk($_GET['id']);
                $rev = BannersHistory::model()->findAll('banner_id=:id', array(':id'=>$_GET['id']));
                foreach($rev as $row){
                        BannersHistory::model()->findByPk($row['banner_history_id'])->delete();
                }
                if($banner->image != ''){
                        @unlink($this->mainImagesabsuPath.$banner->image);
						//delete to cdn folder start
						Yii::app()->rackspaceConnect->delete('/images/'.$banner->image);
						//delete to cdn folder end
                }
                if($banner == null){
                        $this->addFlash('User "#'.$_GET['id'].'" not exists.');
						$this->redirect($this->createUrl('Banners/index').'?'.tep_get_all_get_params(array('id')));
                }
                if($banner->delete()){
                        $this->addFlash('Banner "#'.$banner->title.'" has deleted.');
                }else{
                        $this->addErrorMessage('Banner "#'.$banner->title.'" delete fail.');
                }
				$this->redirect($this->createUrl('Banners/index').'?'.tep_get_all_get_params(array('id')));
        }

        public function actionIndex()
        {
                $data = array();
                $this->setPageTitle('Banner List');
                $criteria = new CDbCriteria();
                $criteria->order = 'active desc, `group` ASC, sort_order desc, title';

                if(isset($_GET['search']) && $_GET['search'] != ''){
                        $keyword = trim($_GET['search']);
                        if(is_numeric($keyword)){
                                $criteria->addCondition('banner_id = '.intval($keyword),'OR');
                        }else {
                                $criteria->addSearchCondition('title', $keyword,true,'OR');
                                $criteria->addSearchCondition('url', $keyword,true,'OR');
                                $criteria->addSearchCondition('`group`', $keyword,true,'OR');
                        }
                }

                $dataProvider=new CActiveDataProvider('Banners',array(
                'criteria'=>$criteria,
                 'pagination'=>array(
                        'pageSize'=>30,
                        ),
                ));
                $bh = new BannersHistory;

                $this->render('index',array(
                        'dataProvider'=>$dataProvider,
                        'show' =>  $data,
                        'bh' => $bh,
                        'allGroup' =>Banners::model()->bannerGroup(),
                ));
        }

        public function loadModel($id)
        {
                $model=Banners::model()->findByPk($id);
                if($model===null)
                        throw new CHttpException(404,'The requested page does not exist.');
                return $model;
        }
		
		function actionChangeState(){
                $this->changeModelState('Banners', $_GET['banner_id'], 'active');
        }
}
