<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property integer $role
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN   = 2;
    const ROLE_USER    = 1;
    const ROLE_BLOCKED = 0;

    const NEVENT_RAUAN  = "user_rauan";
    const NEVENT_ALO    = "user_alo";
    const NEVENT_MAL    = "user_mal";

	const NATTACH_RAUAN = ['target' => true, 'attach' => ['app\models\Post']];
	const NATTACH_ALO   = ['target' => false, 'attach' => ['app\models\Post','app\models\User']];
	const NATTACH_MAL   = ['target' => false, 'attach' => ['app\models\Post']];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required'],
            [['role'], 'integer'],
            [['username', 'email', 'password', 'auth_key'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'role' => 'Role',
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @param $password - password that we want to examine
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->generateAuthKey();
            }
            return true;
        }
        return false;
    }

    public static function getUserList()
    {
	    $users = User::find()
		    ->where(['role' => [self::ROLE_ADMIN, self::ROLE_USER]])
		    ->all();
	    return ArrayHelper::map($users,'id','username');
    }

	public static function  getUserListWNull()
	{
		$ans = self::getUserList();
		$ans['Spam'] = [null => "Everyone"];
		$ans['Certain User'] = [ -1 => "Certain user"];
		return $ans;
	}

	public static function  getUserRange()
	{
		$users = User::find()
			->where(['role' => [self::ROLE_ADMIN, self::ROLE_USER]])
			->all();
		$ans = [];
		foreach($users as $user)
		{
			$ans [] = $user->id;
		}
		return $ans;
	}

	public static  function  getUserRangeWNull()
	{
		$ans = self::getUserRange();
		$ans[] = null;
		return $ans;
	}
}
