<?php

class 	StarnewsController extends BaseController
{
		 public function actionIndex()
        {

     		$criteria = new CDbCriteria(); 
			$criteria->order = 'createtime desc'; 
			//$criteria->addCondition('status=1');      //根据条件查询 
			//$criteria->addCondition('exchange_status=0'); 
			if(isset($_GET['search']) && $_GET['search'] != ''){
                $keyword = trim($_GET['search']);
                $criteria->addSearchCondition('star_name', $keyword,true,'OR');
                $criteria->addSearchCondition('title', $keyword,true,'OR');    
            }
            $dataProvider=new CActiveDataProvider('StarNews',array(
                'criteria'=>$criteria,
                 'pagination'=>array(
                        'pageSize'=>1,
                        ),
            ));
		
			$starnews = new StarNews; 
			       
            $this->render('index',array(
                        'dataProvider'=>$dataProvider,'starnews' =>$starnews
            ));
        }
        
        public function actionLook($id)
        {
        	if(isset($_GET['id']))
            {
            	$model=$this->loadModel($_GET['id']);

            	if($model==null)
            	{
                     throw new CException('Banner #'.$_REQUEST['id'].' Not Exists');
                }
                 $this->render('look',array(
                                     
                                        'model'=>$model,
                                        
                                ));
            }

        }	


        public function actionDelete()
        {
        	StarNews::model()->findByPk($_GET['id'])->delete();
        	$this->redirect($this->createUrl('Starnews/index'));
        }
        public function loadModel($id)
        {
                $model=StarNews::model()->findByPk($id);
                if($model===null)
                        throw new CHttpException(404,'The requested page does not exist.');
                return $model;
        }

}