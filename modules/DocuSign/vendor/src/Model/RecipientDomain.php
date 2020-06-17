<?php
/**
 * RecipientDomain
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
 * RecipientDomain Class Doc Comment
 *
 * @category    Class
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class RecipientDomain implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'recipientDomain';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'active' => 'string',
        'domain_code' => 'string',
        'domain_name' => 'string',
        'recipient_domain_id' => 'string'
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
        'active' => 'active',
        'domain_code' => 'domainCode',
        'domain_name' => 'domainName',
        'recipient_domain_id' => 'recipientDomainId'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'active' => 'setActive',
        'domain_code' => 'setDomainCode',
        'domain_name' => 'setDomainName',
        'recipient_domain_id' => 'setRecipientDomainId'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'active' => 'getActive',
        'domain_code' => 'getDomainCode',
        'domain_name' => 'getDomainName',
        'recipient_domain_id' => 'getRecipientDomainId'
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
        $this->container['active'] = isset($data['active']) ? $data['active'] : null;
        $this->container['domain_code'] = isset($data['domain_code']) ? $data['domain_code'] : null;
        $this->container['domain_name'] = isset($data['domain_name']) ? $data['domain_name'] : null;
        $this->container['recipient_domain_id'] = isset($data['recipient_domain_id']) ? $data['recipient_domain_id'] : null;
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
     * Gets active
     * @return string
     */
    public function getActive()
    {
        return $this->container['active'];
    }

    /**
     * Sets active
     * @param string $active 
     * @return $this
     */
    public function setActive($active)
    {
        $this->container['active'] = $active;

        return $this;
    }

    /**
     * Gets domain_code
     * @return string
     */
    public function getDomainCode()
    {
        return $this->container['domain_code'];
    }

    /**
     * Sets domain_code
     * @param string $domain_code 
     * @return $this
     */
    public function setDomainCode($domain_code)
    {
        $this->container['domain_code'] = $domain_code;

        return $this;
    }

    /**
     * Gets domain_name
     * @return string
     */
    public function getDomainName()
    {
        return $this->container['domain_name'];
    }

    /**
     * Sets domain_name
     * @param string $domain_name 
     * @return $this
     */
    public function setDomainName($domain_name)
    {
        $this->container['domain_name'] = $domain_name;

        return $this;
    }

    /**
     * Gets recipient_domain_id
     * @return string
     */
    public function getRecipientDomainId()
    {
        return $this->container['recipient_domain_id'];
    }

    /**
     * Sets recipient_domain_id
     * @param string $recipient_domain_id 
     * @return $this
     */
    public function setRecipientDomainId($recipient_domain_id)
    {
        $this->container['recipient_domain_id'] = $recipient_domain_id;

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


