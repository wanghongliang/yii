<?php

class Order extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'user':
	 * @var integer $user_id 
	 */
	const STATUS_DRAFT=1;
	const STATUS_PUBLISHED=2;
	const STATUS_ARCHIVED=3;

	private $_oldTags;

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
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
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_name, pass_word, user_email', 'required'), 
		);
	}
 
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'Id',
			'user_name' => '用户名',
			'user_email' => 'E-mail',
			'user_regtime' => '注册时间',
			'login_time' => '会员登陆时间', 
		);
	}

	/**
	 * @return string the URL that shows the detail of the post
	 */
	public function getUrl()
	{
		return Yii::app()->createUrl('order/view', array(
			'id'=>$this->user_id,
			'title'=>$this->user_name,
		));
	}

	/**
	 * Retrieves the list of posts based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the needed posts.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('user_name',$this->user_name,true); 

		return new CActiveDataProvider('Order', array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'user_id DESC',
			),
		));
	}
}