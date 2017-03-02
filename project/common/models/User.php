<?php
namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $nickname
 * @property string $password_hash
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DISABLED = 0;
    const STATUS_ACTIVE = 10;

    private static $_statusList;
    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['username'], 'match', 'pattern' => '/^[A-Za-z_-][A-Za-z0-9_-]+$/'],
            [['username'], 'string', 'max' => 32],
            [['username'], 'unique'],

            [['nickname'], 'required'],
            [['nickname'], 'string', 'max' => 255],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DISABLED]],

            [['password'], 'required', 'on' => ['creation']],
            [['password'], 'trim'],
            [['password'], 'match', 'pattern' => '/^\S+$/'],
            [['password'], 'string', 'length' => [6, 32]],
            [['password'], 'default'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'User ID',
            'username' => '用户名',
            'nickname' => '昵称',
            'password' => '密码',
            'password_hash' => '密码hash',
            'access_token' => 'Access Token',
            'auth_key' => '记住密码key',
            'status' => '状态',
        ];
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['sAccessToken' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates access token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes access token
     */
    public function removeAccessToken()
    {
        $this->sAccessToken = null;
    }

    /**
     * Change status
     */
    public function enable()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function disable()
    {
        $this->status = self::STATUS_DISABLED;
    }

    /**
     * Get status
     */
    public static function getStatusList()
    {
        if (self::$_statusList === null) {
            self::$_statusList = [
                STATUS_ENABLED => '正常',
                STATUS_DISABLED => '禁用'
            ];
        }

        return self::$_statusList;
    }

    public function getStatusMsg()
    {
        $list = getStatusList();

        return $list[$this->status] ?? null;
    }

}
