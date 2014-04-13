<?php
/*
 */
namespace Bogo\Yii\Chat;

/**
 * This is the model class for table "ChatMessage".
 *
 * The followings are the available columns in table 'ChatMessage':
 * @property integer $id
 * @property integer $senderUserId
 * @property integer $recipientUserId
 * @property string $body
 * @property string $createdUdatetime
 *
 * The followings are the available model relations:
 * @property User $recipientUser
 * @property User $senderUser
 * @property Node[] $chatNodes
 */
class Message extends \CBActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ChatMessage';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('senderUserId, recipientUserId, body, createdUdatetime', 'required'),
			array('senderUserId, recipientUserId', 'numerical', 'integerOnly'=>true),
			array('body', 'length', 'max'=>2000),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, senderUserId, recipientUserId, body, createdUdatetime', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'recipientUser' => array(self::BELONGS_TO, 'User', 'recipientUserId'),
			'senderUser' => array(self::BELONGS_TO, 'User', 'senderUserId'),
			'chatNodes' => array(self::HAS_MANY, 'ChatNode', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Message',
			'senderUserId' => 'Sender User',
			'recipientUserId' => 'Recipient User',
			'body' => 'Body',
			'createdUdatetime' => 'Created Udatetime',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('senderUserId',$this->senderUserId);
		$criteria->compare('recipientUserId',$this->recipientUserId);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('createdUdatetime',$this->createdUdatetime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your R2SActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Message the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
