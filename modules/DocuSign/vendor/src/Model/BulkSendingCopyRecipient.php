<?php
/**
 * BulkSendingCopyRecipient
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
 * BulkSendingCopyRecipient Class Doc Comment
 *
 * @category    Class
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class BulkSendingCopyRecipient implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'bulkSendingCopyRecipient';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'access_code' => 'string',
        'client_user_id' => 'string',
        'custom_fields' => 'string[]',
        'delivery_method' => 'string',
        'email' => 'string',
        'email_notification' => '\DocuSign\eSign\Model\RecipientEmailNotification',
        'embedded_recipient_start_url' => 'string',
        'fax_number' => 'string',
        'id_check_configuration_name' => 'string',
        'id_check_information_input' => '\DocuSign\eSign\Model\IdCheckInformationInput',
        'identification_method' => 'string',
        'name' => 'string',
        'note' => 'string',
        'phone_authentication' => '\DocuSign\eSign\Model\RecipientPhoneAuthentication',
        'recipient_id' => 'string',
        'recipient_signature_providers' => '\DocuSign\eSign\Model\RecipientSignatureProvider[]',
        'role_name' => 'string',
        'sms_authentication' => '\DocuSign\eSign\Model\RecipientSMSAuthentication',
        'social_authentications' => '\DocuSign\eSign\Model\SocialAuthentication[]',
        'tabs' => '\DocuSign\eSign\Model\BulkSendingCopyTab[]'
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
        'access_code' => 'accessCode',
        'client_user_id' => 'clientUserId',
        'custom_fields' => 'customFields',
        'delivery_method' => 'deliveryMethod',
        'email' => 'email',
        'email_notification' => 'emailNotification',
        'embedded_recipient_start_url' => 'embeddedRecipientStartURL',
        'fax_number' => 'faxNumber',
        'id_check_configuration_name' => 'idCheckConfigurationName',
        'id_check_information_input' => 'idCheckInformationInput',
        'identification_method' => 'identificationMethod',
        'name' => 'name',
        'note' => 'note',
        'phone_authentication' => 'phoneAuthentication',
        'recipient_id' => 'recipientId',
        'recipient_signature_providers' => 'recipientSignatureProviders',
        'role_name' => 'roleName',
        'sms_authentication' => 'smsAuthentication',
        'social_authentications' => 'socialAuthentications',
        'tabs' => 'tabs'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'access_code' => 'setAccessCode',
        'client_user_id' => 'setClientUserId',
        'custom_fields' => 'setCustomFields',
        'delivery_method' => 'setDeliveryMethod',
        'email' => 'setEmail',
        'email_notification' => 'setEmailNotification',
        'embedded_recipient_start_url' => 'setEmbeddedRecipientStartUrl',
        'fax_number' => 'setFaxNumber',
        'id_check_configuration_name' => 'setIdCheckConfigurationName',
        'id_check_information_input' => 'setIdCheckInformationInput',
        'identification_method' => 'setIdentificationMethod',
        'name' => 'setName',
        'note' => 'setNote',
        'phone_authentication' => 'setPhoneAuthentication',
        'recipient_id' => 'setRecipientId',
        'recipient_signature_providers' => 'setRecipientSignatureProviders',
        'role_name' => 'setRoleName',
        'sms_authentication' => 'setSmsAuthentication',
        'social_authentications' => 'setSocialAuthentications',
        'tabs' => 'setTabs'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'access_code' => 'getAccessCode',
        'client_user_id' => 'getClientUserId',
        'custom_fields' => 'getCustomFields',
        'delivery_method' => 'getDeliveryMethod',
        'email' => 'getEmail',
        'email_notification' => 'getEmailNotification',
        'embedded_recipient_start_url' => 'getEmbeddedRecipientStartUrl',
        'fax_number' => 'getFaxNumber',
        'id_check_configuration_name' => 'getIdCheckConfigurationName',
        'id_check_information_input' => 'getIdCheckInformationInput',
        'identification_method' => 'getIdentificationMethod',
        'name' => 'getName',
        'note' => 'getNote',
        'phone_authentication' => 'getPhoneAuthentication',
        'recipient_id' => 'getRecipientId',
        'recipient_signature_providers' => 'getRecipientSignatureProviders',
        'role_name' => 'getRoleName',
        'sms_authentication' => 'getSmsAuthentication',
        'social_authentications' => 'getSocialAuthentications',
        'tabs' => 'getTabs'
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
        $this->container['access_code'] = isset($data['access_code']) ? $data['access_code'] : null;
        $this->container['client_user_id'] = isset($data['client_user_id']) ? $data['client_user_id'] : null;
        $this->container['custom_fields'] = isset($data['custom_fields']) ? $data['custom_fields'] : null;
        $this->container['delivery_method'] = isset($data['delivery_method']) ? $data['delivery_method'] : null;
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['email_notification'] = isset($data['email_notification']) ? $data['email_notification'] : null;
        $this->container['embedded_recipient_start_url'] = isset($data['embedded_recipient_start_url']) ? $data['embedded_recipient_start_url'] : null;
        $this->container['fax_number'] = isset($data['fax_number']) ? $data['fax_number'] : null;
        $this->container['id_check_configuration_name'] = isset($data['id_check_configuration_name']) ? $data['id_check_configuration_name'] : null;
        $this->container['id_check_information_input'] = isset($data['id_check_information_input']) ? $data['id_check_information_input'] : null;
        $this->container['identification_method'] = isset($data['identification_method']) ? $data['identification_method'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['note'] = isset($data['note']) ? $data['note'] : null;
        $this->container['phone_authentication'] = isset($data['phone_authentication']) ? $data['phone_authentication'] : null;
        $this->container['recipient_id'] = isset($data['recipient_id']) ? $data['recipient_id'] : null;
        $this->container['recipient_signature_providers'] = isset($data['recipient_signature_providers']) ? $data['recipient_signature_providers'] : null;
        $this->container['role_name'] = isset($data['role_name']) ? $data['role_name'] : null;
        $this->container['sms_authentication'] = isset($data['sms_authentication']) ? $data['sms_authentication'] : null;
        $this->container['social_authentications'] = isset($data['social_authentications']) ? $data['social_authentications'] : null;
        $this->container['tabs'] = isset($data['tabs']) ? $data['tabs'] : null;
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
     * Gets access_code
     * @return string
     */
    public function getAccessCode()
    {
        return $this->container['access_code'];
    }

    /**
     * Sets access_code
     * @param string $access_code If a value is provided, the recipient must enter the value as the access code to view and sign the envelope.   Maximum Length: 50 characters and it must conform to the account's access code format setting.  If blank, but the signer `accessCode` property is set in the envelope, then that value is used.  If blank and the signer `accessCode` property is not set, then the access code is not required.
     * @return $this
     */
    public function setAccessCode($access_code)
    {
        $this->container['access_code'] = $access_code;

        return $this;
    }

    /**
     * Gets client_user_id
     * @return string
     */
    public function getClientUserId()
    {
        return $this->container['client_user_id'];
    }

    /**
     * Sets client_user_id
     * @param string $client_user_id Specifies whether the recipient is embedded or remote.   If the `clientUserId` property is not null then the recipient is embedded. Note that if the `ClientUserId` property is set and either `SignerMustHaveAccount` or `SignerMustLoginToSign` property of the account settings is set to  **true**, an error is generated on sending.ng.   Maximum length: 100 characters.
     * @return $this
     */
    public function setClientUserId($client_user_id)
    {
        $this->container['client_user_id'] = $client_user_id;

        return $this;
    }

    /**
     * Gets custom_fields
     * @return string[]
     */
    public function getCustomFields()
    {
        return $this->container['custom_fields'];
    }

    /**
     * Sets custom_fields
     * @param string[] $custom_fields An optional array of strings that allows the sender to provide custom data about the recipient. This information is returned in the envelope status but otherwise not used by DocuSign. Each customField string can be a maximum of 100 characters.
     * @return $this
     */
    public function setCustomFields($custom_fields)
    {
        $this->container['custom_fields'] = $custom_fields;

        return $this;
    }

    /**
     * Gets delivery_method
     * @return string
     */
    public function getDeliveryMethod()
    {
        return $this->container['delivery_method'];
    }

    /**
     * Sets delivery_method
     * @param string $delivery_method Reserved: For DocuSign use only.
     * @return $this
     */
    public function setDeliveryMethod($delivery_method)
    {
        $this->container['delivery_method'] = $delivery_method;

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
     * @param string $email 
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets email_notification
     * @return \DocuSign\eSign\Model\RecipientEmailNotification
     */
    public function getEmailNotification()
    {
        return $this->container['email_notification'];
    }

    /**
     * Sets email_notification
     * @param \DocuSign\eSign\Model\RecipientEmailNotification $email_notification
     * @return $this
     */
    public function setEmailNotification($email_notification)
    {
        $this->container['email_notification'] = $email_notification;

        return $this;
    }

    /**
     * Gets embedded_recipient_start_url
     * @return string
     */
    public function getEmbeddedRecipientStartUrl()
    {
        return $this->container['embedded_recipient_start_url'];
    }

    /**
     * Sets embedded_recipient_start_url
     * @param string $embedded_recipient_start_url Specifies a sender provided valid URL string for redirecting an embedded recipient. When using this option, the embedded recipient still receives an email from DocuSign, just as a remote recipient would. When the document link in the email is clicked the recipient is redirected, through DocuSign, to the supplied URL to complete their actions. When routing to the URL, the sender's system (the server responding to the URL) must request a recipient token to launch a signing session.   If set to `SIGN_AT_DOCUSIGN`, the recipient is directed to an embedded signing or viewing process directly at DocuSign. The signing or viewing action is initiated by the DocuSign system and the transaction activity and Certificate of Completion records will reflect this. In all other ways the process is identical to an embedded signing or viewing operation that is launched by any partner.  It is important to remember that in a typical embedded workflow the authentication of an embedded recipient is the responsibility of the sending application, DocuSign expects that senders will follow their own process for establishing the recipient's identity. In this workflow the recipient goes through the sending application before the embedded signing or viewing process in initiated. However, when the sending application sets `EmbeddedRecipientStartURL=SIGN_AT_DOCUSIGN`, the recipient goes directly to the embedded signing or viewing process bypassing the sending application and any authentication steps the sending application would use. In this case, DocuSign recommends that you use one of the normal DocuSign authentication features (Access Code, Phone Authentication, SMS Authentication, etc.) to verify the identity of the recipient.  If the `clientUserId` property is NOT set, and the `embeddedRecipientStartURL` is set, DocuSign will ignore the redirect URL and launch the standard signing process for the email recipient. Information can be appended to the embedded recipient start URL using merge fields. The available merge fields items are: envelopeId, recipientId, recipientName, recipientEmail, and customFields. The `customFields` property must be set fort the recipient or envelope. The merge fields are enclosed in double brackets.   *Example*:   `http://senderHost/[[mergeField1]]/ beginSigningSession? [[mergeField2]]&[[mergeField3]]`
     * @return $this
     */
    public function setEmbeddedRecipientStartUrl($embedded_recipient_start_url)
    {
        $this->container['embedded_recipient_start_url'] = $embedded_recipient_start_url;

        return $this;
    }

    /**
     * Gets fax_number
     * @return string
     */
    public function getFaxNumber()
    {
        return $this->container['fax_number'];
    }

    /**
     * Sets fax_number
     * @param string $fax_number Reserved:
     * @return $this
     */
    public function setFaxNumber($fax_number)
    {
        $this->container['fax_number'] = $fax_number;

        return $this;
    }

    /**
     * Gets id_check_configuration_name
     * @return string
     */
    public function getIdCheckConfigurationName()
    {
        return $this->container['id_check_configuration_name'];
    }

    /**
     * Sets id_check_configuration_name
     * @param string $id_check_configuration_name Specifies authentication check by name. The names used here must be the same as the authentication type names used by the account (these name can also be found in the web console sending interface in the Identify list for a recipient,) This overrides any default authentication setting.  *Example*: Your account has ID Check and SMS Authentication available and in the web console Identify list these appear as 'ID Check $' and 'SMS Auth $'. To use ID check in an envelope, the idCheckConfigurationName should be 'ID Check '. If you wanted to use SMS, it would be 'SMS Auth $' and you would need to add you would need to add phone number information to the `smsAuthentication` node.
     * @return $this
     */
    public function setIdCheckConfigurationName($id_check_configuration_name)
    {
        $this->container['id_check_configuration_name'] = $id_check_configuration_name;

        return $this;
    }

    /**
     * Gets id_check_information_input
     * @return \DocuSign\eSign\Model\IdCheckInformationInput
     */
    public function getIdCheckInformationInput()
    {
        return $this->container['id_check_information_input'];
    }

    /**
     * Sets id_check_information_input
     * @param \DocuSign\eSign\Model\IdCheckInformationInput $id_check_information_input
     * @return $this
     */
    public function setIdCheckInformationInput($id_check_information_input)
    {
        $this->container['id_check_information_input'] = $id_check_information_input;

        return $this;
    }

    /**
     * Gets identification_method
     * @return string
     */
    public function getIdentificationMethod()
    {
        return $this->container['identification_method'];
    }

    /**
     * Sets identification_method
     * @param string $identification_method 
     * @return $this
     */
    public function setIdentificationMethod($identification_method)
    {
        $this->container['identification_method'] = $identification_method;

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
     * @param string $name 
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets note
     * @return string
     */
    public function getNote()
    {
        return $this->container['note'];
    }

    /**
     * Sets note
     * @param string $note Specifies a note that is unique to this recipient. This note is sent to the recipient via the signing email. The note displays in the signing UI near the upper left corner of the document on the signing screen.  Maximum Length: 1000 characters.
     * @return $this
     */
    public function setNote($note)
    {
        $this->container['note'] = $note;

        return $this;
    }

    /**
     * Gets phone_authentication
     * @return \DocuSign\eSign\Model\RecipientPhoneAuthentication
     */
    public function getPhoneAuthentication()
    {
        return $this->container['phone_authentication'];
    }

    /**
     * Sets phone_authentication
     * @param \DocuSign\eSign\Model\RecipientPhoneAuthentication $phone_authentication
     * @return $this
     */
    public function setPhoneAuthentication($phone_authentication)
    {
        $this->container['phone_authentication'] = $phone_authentication;

        return $this;
    }

    /**
     * Gets recipient_id
     * @return string
     */
    public function getRecipientId()
    {
        return $this->container['recipient_id'];
    }

    /**
     * Sets recipient_id
     * @param string $recipient_id Unique for the recipient. It is used by the tab element to indicate which recipient is to sign the Document.
     * @return $this
     */
    public function setRecipientId($recipient_id)
    {
        $this->container['recipient_id'] = $recipient_id;

        return $this;
    }

    /**
     * Gets recipient_signature_providers
     * @return \DocuSign\eSign\Model\RecipientSignatureProvider[]
     */
    public function getRecipientSignatureProviders()
    {
        return $this->container['recipient_signature_providers'];
    }

    /**
     * Sets recipient_signature_providers
     * @param \DocuSign\eSign\Model\RecipientSignatureProvider[] $recipient_signature_providers 
     * @return $this
     */
    public function setRecipientSignatureProviders($recipient_signature_providers)
    {
        $this->container['recipient_signature_providers'] = $recipient_signature_providers;

        return $this;
    }

    /**
     * Gets role_name
     * @return string
     */
    public function getRoleName()
    {
        return $this->container['role_name'];
    }

    /**
     * Sets role_name
     * @param string $role_name Optional element. Specifies the role name associated with the recipient.<br/><br/>This is required when working with template recipients.
     * @return $this
     */
    public function setRoleName($role_name)
    {
        $this->container['role_name'] = $role_name;

        return $this;
    }

    /**
     * Gets sms_authentication
     * @return \DocuSign\eSign\Model\RecipientSMSAuthentication
     */
    public function getSmsAuthentication()
    {
        return $this->container['sms_authentication'];
    }

    /**
     * Sets sms_authentication
     * @param \DocuSign\eSign\Model\RecipientSMSAuthentication $sms_authentication
     * @return $this
     */
    public function setSmsAuthentication($sms_authentication)
    {
        $this->container['sms_authentication'] = $sms_authentication;

        return $this;
    }

    /**
     * Gets social_authentications
     * @return \DocuSign\eSign\Model\SocialAuthentication[]
     */
    public function getSocialAuthentications()
    {
        return $this->container['social_authentications'];
    }

    /**
     * Sets social_authentications
     * @param \DocuSign\eSign\Model\SocialAuthentication[] $social_authentications Lists the social ID type that can be used for recipient authentication.
     * @return $this
     */
    public function setSocialAuthentications($social_authentications)
    {
        $this->container['social_authentications'] = $social_authentications;

        return $this;
    }

    /**
     * Gets tabs
     * @return \DocuSign\eSign\Model\BulkSendingCopyTab[]
     */
    public function getTabs()
    {
        return $this->container['tabs'];
    }

    /**
     * Sets tabs
     * @param \DocuSign\eSign\Model\BulkSendingCopyTab[] $tabs 
     * @return $this
     */
    public function setTabs($tabs)
    {
        $this->container['tabs'] = $tabs;

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


