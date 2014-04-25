<?php
/*
 */
namespace Bogo\Yii\Chat;

/**
 * This is the model class for table "ChatNode".
 *
 * The followings are the available columns in table 'ChatNode':
 * @property integer $id
 * @property integer $masterUserId
 * @property integer $slaveUserId
 * @property integer $messageId
 * @property integer $isIncoming
 * @property integer $isNew
 * @property integer $isTerminal
 *
 * The followings are the available model relations:
 * @property Message $message
 * @property User $masterUser
 * @property User $slaveUser
 */
trait TNodeModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ChatNode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('masterUserId, slaveUserId, isIncoming, isNew, isTerminal', 'required'),
			array('masterUserId, slaveUserId, messageId, isIncoming, isNew, isTerminal', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, masterUserId, slaveUserId, messageId, isIncoming, isNew, isTerminal', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'message' => array(self::BELONGS_TO, 'ChatMessage', 'messageId'),
			'masterUser' => array(self::BELONGS_TO, 'User', 'masterUserId'),
			'slaveUser' => array(self::BELONGS_TO, 'User', 'slaveUserId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Node',
			'masterUserId' => 'Master User',
			'slaveUserId' => 'Slave User',
			'messageId' => 'Message',
			'isIncoming' => 'Is Incoming',
			'isNew' => 'Is New',
			'isTerminal' => 'Is Terminal',
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
		$criteria->compare('masterUserId',$this->masterUserId);
		$criteria->compare('slaveUserId',$this->slaveUserId);
		$criteria->compare('messageId',$this->messageId);
		$criteria->compare('isIncoming',$this->isIncoming);
		$criteria->compare('isNew',$this->isNew);
		$criteria->compare('isTerminal',$this->isTerminal);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your R2SActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Node the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
