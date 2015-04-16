<?php

class Configuration extends CActiveRecord
{
        /**
         * Returns the static model of the specified AR class.
         * @return Configuration the static model class
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
                return 'configuration';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
                return array(
                        array('title,key,value,description,sort_order,configuration_group_id', 'required'),
                        array('created', 'safe'),
						/*array('title', 'length', 'max'=>150),
                        array('key', 'length', 'max'=>64),
                        array('description', 'length', 'max'=>255),
                        array('last_modified, date_added', 'safe'),
                        array('configuration_id, title, key, value, description, last_modified, date_added', 'safe', 'on'=>'search'),*/
                );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
                return array(
                        'ConfigurationGroup' => array(self::BELONGS_TO, 'ConfigurationGroup', 'configuration_group_id'),
                );
        }

        public function getDropList($array){
                $list = explode(',',$array);
                for($i=0; $i<sizeof($list); $i++){
                                $listv[strtolower($list[$i])] = $list[$i];
                }
                return $listv;
        }

        public function tep_cfg_select_option($select_array, $key_value, $key = '') {
				$string = '';
				for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
						$name = ((sizeof($key)) ? 'configuration[' . $key . ']' : 'configuration_value');
						$string .= '<br><input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';
						if ($key_value == $select_array[$i]) $string .= ' CHECKED';
						$string .= '> ' . $select_array[$i];
				}
				return $string;
        }

        /**
         * Get saved configuration value
         *
         * @param string $key Key string of the configuration
         * @return mix If configuration found, it will return a string else returns NULL
         * @author Gihan <gihanshp@gmail.com>
         */
        public static function getVal($key, $default = null){
            $config = Configuration::model()->findByAttributes(array('key'=>$key));
            if($config)
                return $config->value;
            else
                return $default;
        }

        public function tep_get_config_value($key){
                $config_value = Configuration::model()->findByAttributes(array('key'=>$key));
                return $config_value->value;
        }

        public function get_departure_form_china_configure($key='HOTEL_TOP10_SPECIAL_TAB1_TAB2_GROUP'){
            $config_data = array();
            $config = Configuration::model()->findByAttributes(array('key'=>$key));
            $config = $config->value;
            if(!$config) return NULL;
            $config = explode('|',$config);
            foreach($config as $cf){
                $config_data[] = explode(',',$cf);
            }
            return $config_data;
        }

		/**
		 * load constants from cache if they were cahced
 		 */
		public static function loadConstants() {
			if(!defined('APP_INIT_CONFIGURATION')) {
				if(Yii::app()->hasComponent('cache')) {
					$configuration = Yii::app()->cache->get('public_constants');
					if($configuration === false){
						$configuration = self::model()->findAll(array('select'=>' t.key, t.value '));
						Yii::app()->cache->set('public_constants', $configuration);
					}
				} else $configuration = self::model()->findAll(array('select'=>' t.key, t.value '));

				for($i =0,$max=count($configuration);$i<$max;$i++){
					if(!defined($configuration[$i]['key'])) define($configuration[$i]['key'],$configuration[$i]['value']);
				}
				define('APP_INIT_CONFIGURATION',true);
			}
		}

		/**
		 * Clear the constans cache in memory
 		 */
		public function afterSave() {
			if(Yii::app()->hasComponent('cache')) {
				Yii::app()->cache->delete('public_constants');
			}
			parent::afterSave();
		}

		/**
		 * Clear the constans cache in memory
 		 */
		public function afterDelete() {
			if(Yii::app()->hasComponent('cache')) {
				Yii::app()->cache->delete('public_constants');
			}
			parent::afterDelete();
		}
    
    /**
     * Get evaluated value of a config value
     * 
     * @param string $key Key
     * 
     * @return mix
     * 
     * @author Gihan S <gihanshp@gmail.com>
     */
    public static function getEval($key)  {
        $val = self::getVal($key);
        return @eval('return ' . $val . ';');
    }

    /**
     * Create new key
     *
     *
     */
    public function createKey($key,$value,$title,$description,$group_id,$sort_order){
        $model=new Configuration;
        $model->setScenario('insert');
        $model->key = $key;
        $model->title = $title;
        $model->value = $value;
        $model->description = $description;
        $model->configuration_group_id = $group_id;
        $model->sort_order = (int)$sort_order;
        $model->created = new CDbExpression('now()');
        if($model->insert()){
            return true;
        }else{
            return false;
        }
    }

    public function setValueByKey($key,$value){
        if(Yii::app()->db->createCommand("update ".$this->tableName()." set `value`='".$value."' where `key`='".$key."'")->execute()){
            return true;
        }else{
            return false;
        }
    }

}
