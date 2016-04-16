<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "notify".
 *
 * @property integer $id
 * @property string $name
 * @property string $event_code
 * @property integer $sender_id
 * @property string $recipient_id
 * @property string $title
 * @property string $fulltext
 * @property string $notifications
 *
 * @property User $sender
 */
class Notify extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notify';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'event_code', 'sender_id', 'recipient_id', 'notifications', 'title', 'fulltext'], 'required'],
            [['sender_id'], 'integer'],
            [['fulltext','title'], 'string'],
            [['event_code'], 'in', 'range' => Yii::$app->notiHandler->getEventsRange()],
            [['recipient_id'], 'checkRecipient'],
            [['sender_id'], 'in', 'range' => User::getUserRange()],
            [['name', 'event_code', 'recipient_id'], 'string', 'max' => 255],
            [['sender_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sender_id' => 'id']],
            ['notifications', 'each', 'rule' => ['in', 'range' => array_keys(Yii::$app->notiHandler->getNotiTypes())]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'event_code' => 'Event Code',
            'sender_id' => 'Sender',
            'recipient_id' => 'Recipient Type',
            'title' => 'Title',
            'fulltext' => 'Fulltext',
            'notifications' => 'Notifications',
        ];
    }

    public function checkRecipient($attribute)
    {
        $range = Yii::$app->notiHandler->getRecipientTypes($this->event_code);
        $range = ArrayHelper::getColumn($range, 'id');
        if (!in_array($this->recipient_id, $range)) {
            $this->addError($attribute, 'You entered invalid recipient type');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipient()
    {
        return $this->hasOne(User::className(), ['id' => 'recipient_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->notifications = implode(",", $this->notifications);
            return true;
        }
        return false;
    }

    public function afterFind()
    {
        $this->notifications = explode(",", $this->notifications);
        return true;
    }
}
