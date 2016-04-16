<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property string $title
 * @property string $fulltext
 * @property integer $author
 * @property string $url
 * @property string $created_at
 *
 * @property User $author0
 */
class Post extends ActiveRecord
{

    const NEVENT_NEWPOST  = "post_new";

    const NATTACH_NEWPOST  = ['target' => true, 'attach' => ['app\models\Post']];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * Slug behavior -  so that we will refer by slug instead
     * of id which is better for SEO and UX.
     *
     * Blammable behavior - we can get author
     *
     * PS - Timestamp was used in DB so that here I will skip it.
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'url',
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'author',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'fulltext'], 'required'],
            [['fulltext'], 'string'],
            [['author'], 'integer'],
            [['created_at'], 'safe'],
            ['title', 'string', 'max' => 50],
            ['url', 'string', 'max' => 255],
            [['url'], 'unique'],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'fulltext' => 'Fulltext',
            'author' => 'Author',
            'url' => 'Url',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author']);
    }

}
