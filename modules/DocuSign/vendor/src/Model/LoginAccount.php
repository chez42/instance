<?php
/**
 * LoginAccount
 *
 * PHP version 5
 *
 * @category Class
 * @package  DocuSign\eSign
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * DocuSign REST API
 *
 * The DocuSign REST API provides you with a powerful, convenient, and simple Web services API for interacting with DocuSign.
 *
 * OpenAPI spec version: v2.1
 * Contact: devcenter@docusign.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace DocuSign\eSign\Model;

use \ArrayAccess;

/**
 * LoginAccount Class Doc Comment
 *
 * @category    Class
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class LoginAccount implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'loginAccount';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'account_id' => 'string',
        'account_id_guid' => 'string',
        'base_url' => 'string',
        'email' => 'string',
        'is_default' => 'string',
        'login_account_settings' => '\DocuSign\eSign\Model\NameValue[]',
        'login_user_settings' => '\DocuSign\eSign\Model\NameValue[]',
        'name' => 'string',
        'site_description' => 'string',
        'user_id' => 'string',
        'user_name' => 'string'
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'account_id' => 'accountId',
        'account_id_guid' => 'accountIdGuid',
        'base_url' => 'baseUrl',
        'email' => 'email',
        'is_default' => 'isDefault',
        'login_account_settings' => 'loginAccountSettings',
        'login_user_settings' => 'loginUserSettings',
        'name' => 'name',
        'site_description' => 'siteDescription',
        'user_id' => 'userId',
        'user_name' => 'userName'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'account_id' => 'setAccountId',
        'account_id_guid' => 'setAccountIdGuid',
        'base_url' => 'setBaseUrl',
        'email' => 'setEmail',
        'is_default' => 'setIsDefault',
        'login_account_settings' => 'setLoginAccountSettings',
        'login_user_settings' => 'setLoginUserSettings',
        'name' => 'setName',
        'site_description' => 'setSiteDescription',
        'user_id' => 'setUserId',
        'user_name' => 'setUserName'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'account_id' => 'getAccountId',
        'account_id_guid' => 'getAccountIdGuid',
        'base_url' => 'getBaseUrl',
        'email' => 'getEmail',
        'is_default' => 'getIsDefault',
        'login_account_settings' => 'getLoginAccountSettings',
        'login_user_settings' => 'getLoginUserSettings',
        'name' => 'getName',
        'site_description' => 'getSiteDescription',
        'user_id' => 'getUserId',
        'user_name' => 'getUserName'
    ];

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    public static function setters()
    {
        return self::$setters;
    }

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['account_id'] = isset($data['account_id']) ? $data['account_id'] : null;
        $this->container['account_id_guid'] = isset($data['account_id_guid']) ? $data['account_id_guid'] : null;
        $this->container['base_url'] = isset($data['base_url']) ? $data['base_url'] : null;
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['is_default'] = isset($data['is_default']) ? $data['is_default'] : null;
        $this->container['login_account_settings'] = isset($data['login_account_settings']) ? $data['login_account_settings'] : null;
        $this->container['login_user_settings'] = isset($data['login_user_settings']) ? $data['login_user_settings'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['site_description'] = isset($data['site_description']) ? $data['site_description'] : null;
        $this->container['user_id'] = isset($data['user_id']) ? $data['user_id'] : null;
        $this->container['user_name'] = isset($data['user_name']) ? $data['user_name'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];
        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properteis are valid
     */
    public function valid()
    {
        return true;
    }


    /**
     * Gets account_id
     * @return string
     */
    public function getAccountId()
    {
        return $this->container['account_id'];
    }

    /**
     * Sets account_id
     * @param string $account_id The account ID associated with the envelope.
     * @return $this
     */
    public function setAccountId($account_id)
    {
        $this->container['account_id'] = $account_id;

        return $this;
    }

    /**
     * Gets account_id_guid
     * @return string
     */
    public function getAccountIdGuid()
    {
        return $this->container['account_id_guid'];
    }

    /**
     * Sets account_id_guid
     * @param string $account_id_guid The GUID associated with the account ID.
     * @return $this
     */
    public function setAccountIdGuid($account_id_guid)
    {
        $this->container['account_id_guid'] = $account_id_guid;

        return $this;
    }

    /**
     * Gets base_url
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->container['base_url'];
    }

    /**
     * Sets base_url
     * @param string $base_url The URL that should be used for successive calls to this account. It includes the protocal (https), the DocuSign server where the account is located, and the account number. Use this Url to make API calls against this account. Many of the API calls provide Uri's that are relative to this baseUrl.
     * @return $this
     */
    public function setBaseUrl($base_url)
    {
        $this->container['base_url'] = $base_url;

        return $this;
    }

    /**
     * Gets email
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     * @param string $email The email address for the user.
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets is_default
     * @return string
     */
    public function getIsDefault()
    {
        return $this->container['is_default'];
    }

    /**
     * Sets is_default
     * @param string $is_default This value is true if this is the default account for the user, otherwise false is returned.
     * @return $this
     */
    public function setIsDefault($is_default)
    {
        $this->container['is_default'] = $is_default;

        return $this;
    }

    /**
     * Gets login_account_settings
     * @return \DocuSign\eSign\Model\NameValue[]
     */
    public function getLoginAccountSettings()
    {
        return $this->container['login_account_settings'];
    }

    /**
     * Sets login_account_settings
     * @param \DocuSign\eSign\Model\NameValue[] $login_account_settings A list of settings on the acccount that indicate what features are available.
     * @return $this
     */
    public function setLoginAccountSettings($login_account_settings)
    {
        $this->container['login_account_settings'] = $login_account_settings;

        return $this;
    }

    /**
     * Gets login_user_settings
     * @return \DocuSign\eSign\Model\NameValue[]
     */
    public function getLoginUserSettings()
    {
        return $this->container['login_user_settings'];
    }

    /**
     * Sets login_user_settings
     * @param \DocuSign\eSign\Model\NameValue[] $login_user_settings A list of user-level settings that indicate what user-specific features are available.
     * @return $this
     */
    public function setLoginUserSettings($login_user_settings)
    {
        $this->container['login_user_settings'] = $login_user_settings;

        return $this;
    }

    /**
     * Gets name
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     * @param string $name The name associated with the account.
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets site_description
     * @return string
     */
    public function getSiteDescription()
    {
        return $this->container['site_description'];
    }

    /**
     * Sets site_description
     * @param string $site_description An optional descirption of the site that hosts the account.
     * @return $this
     */
    public function setSiteDescription($site_description)
    {
        $this->container['site_description'] = $site_description;

        return $this;
    }

    /**
     * Gets user_id
     * @return string
     */
    public function getUserId()
    {
        return $this->container['user_id'];
    }

    /**
     * Sets user_id
     * @param string $user_id 
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->container['user_id'] = $user_id;

        return $this;
    }

    /**
     * Gets user_name
     * @return string
     */
    public function getUserName()
    {
        return $this->container['user_name'];
    }

    /**
     * Sets user_name
     * @param string $user_name The name of this user as defined by the account.
     * @return $this
     */
    public function setUserName($user_name)
    {
        $this->container['user_name'] = $user_name;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\DocuSign\eSign\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\DocuSign\eSign\ObjectSerializer::sanitizeForSerialization($this));
    }
}


