<?php

/**
 * This is the model class for table "configuration_group".
 *
 * The followings are the available columns in table 'configuration_group':
 *
 *
 */
class ConfigurationGroup extends CActiveRecord
{
        /**
         * Returns the static model of the specified AR class.
         * @return SimpleData the static model class
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
                return '{{configuration_group}}';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
                // NOTE: you should only define rules for those attributes that
                // will receive user inputs.
                return array();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
                // NOTE: you may need to adjust the relation name and the related
                // class name for the relations automatically generated below.
                return array();
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
                return array();
        }
        /**
         * Get All Configuration groups.
         * @todo add cache support.
         */
        public function getAllGroups(){
                return $this->findAll("`active` = :active order by sort_order" , array(':active'=>1));
        }
}
