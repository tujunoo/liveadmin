<?php
/**
 * This is the model class for table "banners".
 *
 */
class BannersHistory extends CActiveRecord
{
        /**
         * Returns the static model of the specified AR class.
         * @return Banners the static model class
         */
        public static function model($className=__CLASS__)
        {
                return parent::model($className);
        }
        /**
         * @return string the associated database table name
         */
        public function tableName()
        {
                return '{{banner_history}}';
        }
        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
                // NOTE: you should only define rules for those attributes that
                // will receive user inputs.
                return array(
                        );
        }
        /**
         * @return array relational rules.
         */
        public function relations()
        {
                // NOTE: you may need to adjust the relation name and the related
                // class name for the relations automatically generated below.
                return array(
                );
        }
        /*
        * Total count for home page banner shown and clicked by users
        */
        public function getClickShown($id){
                        $criteria = new CDbCriteria;
                        $criteria->select = 'sum(shown) as shown, sum(clicked) as clicked';
                        $criteria->condition='banner_id=:ID';
                        $criteria->params=array(':ID'=>$id);
                        $data=$this->find($criteria);
                        return $data->shown.'/'.$data->clicked;
        }
		public function updateBannerClickCount($banner_id) {
			 $banner = Yii::app()->db->createCommand("select banner_id from " . BannersHistory::model()->tableName() . " where banner_id = '" . (int)$banner_id . "'")->queryRow();
	     	if(!isset($banner['banner_id']) && (int)$banner_id > 0){
			  Yii::app()->db->createCommand("insert into ".BannersHistory::model()->tableName()." (banner_id, clicked) values('".(int)$banner_id."', 1)")->execute();
			} else {
			  Yii::app()->db->createCommand("update ".BannersHistory::model()->tableName()." set clicked = clicked + 1 where banner_id = '" . (int)$banner_id . "'")->execute();				
			}
		}
}
