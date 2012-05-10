<?php

class Orderfrc extends CActiveRecord
{
	/**
	 * 用于判断订单的状态
	 * @var integer $status 
	 */
	const STATUS_DRAFT=1;
	const STATUS_PUBLISHED=2;
	const STATUS_ARCHIVED=3;

	private $_oldTags;


	public $user_name;

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
		return '{{recharge_order}}';
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'order_no' => '订单号',
			'amount' => '金额',
			'integral' => '积分',
			'mobile' => '电话号码',
			'created' => '订单创建时间', 
			'status'=>'订单状态',
			'user_name'=>'用户名',
			'id'=>'ID',
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
			'user' => array(self::BELONGS_TO, 'user', 'uid'),
			'lottery_share_num'=>array(self::BELONGS_TO, 'ls', 'lsn_id'),
		);
	}

	public function afterFind(){
		$this->user_name = $this->user->user_name;
	}
	
	
	/**
	 * @return string the URL that shows the detail of the post
	 */
	public function getUrl()
	{
		return Yii::app()->createUrl('orderfrc/view', array(
			'id'=>$this->id, 
		));
	}

	/**
	 * Retrieves the list of posts based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the needed posts.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		
		$order_no_value = iconv("UTF-8","GB2312",$_GET[__CLASS__]['order_no']);
		$user_name_value =  iconv("UTF-8","GB2312",$_GET[__CLASS__]['user_name']);
		
  		$criteria->compare('order_no',$order_no_value,true);
		$criteria->compare('user.user_name',$user_name_value,true);
		$criteria->with=array('user'=>array('select'=>'user_name'));
		$criteria->select=array('id','order_no' ,'amount','integral','mobile','created');
		 
		if( $_GET['sd']!='' && $_GET['ed']!='' ){
			$sd = explode('-',$_GET['sd']);
			$sd_time = mktime(0,0,0,$sd[1],$sd[2],$sd[0]);
 
			$ed =  explode('-',$_GET['ed']);
			$ed_time = mktime(0,0,0,$ed[1],$ed[2],$ed[0]); 
 

			$criteria->addCondition('t.created between '.$sd_time.' and '.$ed_time );
		}

		 return new CActiveDataProvider('Orderfrc', array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'id DESC',
			),
		));







	 
		$condition = '';
        if(isset($this->user_name))
        {
            $condition = 'user_name='.($this->user_name);
        }
		
		//print_r( $this );echo $condition;exit;
		  $model=new CActiveDataProvider('Orderfrc', array(
            'criteria'=>array(
                'condition'=>$condition,
                'order'=>'id DESC', 
				'with'=>array('user'=>array('user_name')),
            ),
        ));
		
		return $model;


		return new CActiveDataProvider('Orderfrc', array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'id DESC',
			),
		));


	}
}