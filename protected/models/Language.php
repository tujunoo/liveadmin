<?php
/**
 * @property string $language_id
 * @property string $name
 * @property string $code
 * @author vincent.mi@toursforfun.com (2012-5-16)
 */
class Language extends CActiveRecord
{
        /**
         * Returns the static model of the specified AR class.
         * @return Language the static model class
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
                return '{{language}}';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
                // NOTE: you should only define rules for those attributes that
                // will receive user inputs.
                return array(
                        array('name, code', 'required'),
                        array('name', 'length', 'max'=>32),
                        array('code', 'length', 'max'=>2),
                        // The following rule is used by search().
                        // Please remove those attributes that should not be searched.
                        array('language_id, name, code', 'safe', 'on'=>'search'),
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
                        'affiliatePaymentStatusDescriptions' => array(self::HAS_MANY, 'AffiliatePaymentStatusDescription', 'language_id'),
                        'articleDescriptions' => array(self::HAS_MANY, 'ArticleDescription', 'language_id'),
                        'articleReviewDescriptions' => array(self::HAS_MANY, 'ArticleReviewDescription', 'language_id'),
                        'categoryDescriptions' => array(self::HAS_MANY, 'CategoryDescription', 'language_id'),
                        'couponDescriptions' => array(self::HAS_MANY, 'CouponDescription', 'language_id'),
                        'faqs' => array(self::HAS_MANY, 'Faq', 'language_id'),
                        'faqCategoryDescriptions' => array(self::HAS_MANY, 'FaqCategoryDescription', 'language_id'),
                        'landingPages' => array(self::HAS_MANY, 'LandingPage', 'language_id'),
                        'linkCategoryDescriptions' => array(self::HAS_MANY, 'LinkCategoryDescription', 'language_id'),
                        'linkDescriptions' => array(self::HAS_MANY, 'LinkDescription', 'language_id'),
                        'linkStatuses' => array(self::HAS_MANY, 'LinkStatus', 'language_id'),
                        'locales' => array(self::HAS_MANY, 'Locale', 'language_id'),
                        'orderSessionInfos' => array(self::HAS_MANY, 'OrderSessionInfo', 'language_id'),
                        'orderStatuses' => array(self::HAS_MANY, 'OrderStatus', 'language_id'),
                        'productAttributeValueTourProviders' => array(self::HAS_MANY, 'ProductAttributeValueTourProvider', 'language_id'),
                        'productDescriptions' => array(self::HAS_MANY, 'ProductDescription', 'language_id'),
                        'productOptions' => array(self::HAS_MANY, 'ProductOption', 'language_id'),
                        'productOptionValues' => array(self::HAS_MANY, 'ProductOptionValue', 'language_id'),
                        'providers' => array(self::HAS_MANY, 'Provider', 'language_id'),
                        'regionDescriptions' => array(self::HAS_MANY, 'RegionDescription', 'language_id'),
                        'reviewDescriptions' => array(self::HAS_MANY, 'ReviewDescription', 'language_id'),
                        'tourAnswers' => array(self::HAS_MANY, 'TourAnswer', 'language_id'),
                        'tourQuestions' => array(self::HAS_MANY, 'TourQuestion', 'language_id'),
                );
        }
        
        /**
         * Get the site's current lanauge
         * 
         * @return Lanauge
         * @author Gihan <gihanshp@gmail.com> 
         */
        public function getCurrentLauange(){
            // get the current site language id
            $lang = Yii::app()->language;

            // set default to en_US if not specified
            if(!$lang)
                $lang = 'en_US';

            // split for db query
            $langCode = substr($lang, 0, 2);
            $language = Language::model()->findByAttributes(array('code'=>$langCode));
            
            return $language;
        }
        
        
        /**
         * Easy wrapper to get current lanauge id
         * 
         * @return int Language id
         * @author Gihan <gihanshp@gmail.com>
         */
        public function getCurrentLanguageId(){
            return $this->getCurrentLauange()->language_id;
        }
        
        
		public function getLanguageIcon($langId = 1){
			if($langId == 1){
				$code = 'en_us';
			}else{
				$code = 'es_es';
			}
				$icon = CHtml::image($this->imageUrl.strtolower($code).'/flag.jpg', 'English',array('title' =>'English','width'=>'20', 'height' => '13','class'=>'fleft lang_drop_img'));
			return $icon;
		}

}
