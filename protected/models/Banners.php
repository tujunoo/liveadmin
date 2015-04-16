<?php

/**
 * This is the model class for table "banners".
 *
 * The followings are the available columns in table 'banners':
 * @property integer $banners_id
 * @property string $banners_title
 * @property string $banners_url
 * @property string $banners_image
 * @property string $banners_group
 * @property integer $expires_impressions
 * @property string $expires_date
 * @property string $date_scheduled
 * @property string $date_added
 * @property string $date_status_change
 * @property integer $status
 * @property integer $banner_sort_order
 */
class Banners extends CActiveRecord
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
                return '{{banner}}';
        }
        /**
         * 根据banner分组获取banner
         * @author Wajda 1347882645@qq.com
         * @param string $group
         * @return mixed
         */
        public function getBannerByGroup($group='Index Page Top850x390'){
            $language = (LANGUAGE_ID == 'cn'||LANGUAGE_ID == 'sc'?'schinese':(LANGUAGE_ID ==
            'tw'?'tchinese':'all'));
            $now      = date('Y-m-d H:i:s');
            $sql  = "SELECT `banner_id`,`title`,`url`,`image`,`html_text` FROM `banner`
                         WHERE `active`=1 AND `group`='$group'
                         AND (`language`='$language' OR `language` = 'all')
                         AND (`scheduled`<='$now'      OR `scheduled`='0000-00-00 00:00:00')
                         AND (`expiration`>='$now'     OR  `expiration`='0000-00-00 00:00:00')
                         ORDER BY `sort_order` DESC
                         ";
            return Yii::app()->db->createCommand($sql)->queryAll();
        }

    /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
                // NOTE: you should only define rules for those attributes that
                // will receive user inputs.
                return array(
                        array('title', 'required'),
                        array('url', 'length', 'max'=>250),
                        array('image, group', 'length', 'max'=>64),
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

        public function getBannersDisplayHomePage(){
                        $criteria = new CDbCriteria;
                        $criteria->select = 'banner_id, title, image, url';
                        $criteria->condition='active = "1" and group = "500x220 Home Page"';
                        $criteria->order = 'sort_order DESC';
                        $data=$this->findAll($criteria);
                        for($i=0;$i<sizeof($data); $i++){
                         $rec[] =array(
                   'image'=>array('src'=>'http://www.tours4fun.com/images/'.$data[$i]['image'],'alt'=>$data[$i]['title']),
                  'link'=>array('url'=>$data[$i]['url'],'htmlOptions'=>array('name'=>$data[$i]['banner_id'],'title'=>$data[$i]['title']))
              );
                        }
                        return $rec;
        }

        public function removeBanner(){
                $transaction = $this->dbConnection->beginTransaction();
                try{
                        $this->delete();
                        $transaction->commit();
                        return true;
                }catch(Exception $e){
                        $transaction->rollBack();
                        return false;
                }
        }

        public function bannerGroup(){
                $rec = array();
                $criteria = new CDbCriteria;
                $criteria->select = 't.group';
                $criteria->condition = '';
                $criteria->group = 't.group';
                $data=Banners::model()->findAll($criteria);
                for($i=0;$i<sizeof($data); $i++){
                 $rec[$data[$i]['group']] = $data[$i]['group'];
                }
                return $rec;
        }

        public function bannerLanguage(){
            $array=array('all'=>'all');
            $language = Language::model()->findAll();
            foreach($language as $a){
                $array[$a['name']]=$a['name'];
            }

            return $array;
        }

        public function bannerIP(){
            $array=array('0'=>'允许所有IP访问', '1'=>'允许国内IP访问');
            return $array;
        }

		public function getBannerUrl($banner_id) {
				  $banner = Yii::app()->db->createCommand("select url from " . Banners::model()->tableName() . " where banner_id = '" . (int)$banner_id . "'")->queryRow();
			  if (!empty($banner) && count($banner) > 0) {
				  BannersHistory::updateBannerClickCount($banner_id);
				  return $banner['url'];
			  }
			       return $banner;
		}
}
