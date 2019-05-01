<?

class ContactItemType extends ItemType {
    public $FileAs; // string
    public $FileAsMapping; // FileAsMappingType
    public $DisplayName; // string
    public $GivenName; // string
    public $Initials; // string
    public $MiddleName; // string
    public $Nickname; // string
    public $CompleteName; // CompleteNameType
    public $CompanyName; // string
    public $EmailAddresses; // EmailAddressDictionaryType
    public $PhysicalAddresses; // PhysicalAddressDictionaryType
    public $PhoneNumbers; // PhoneNumberDictionaryType
    public $AssistantName; // string
    public $Birthday; // dateTime
    public $BusinessHomePage; // anyURI
    public $Children; // ArrayOfStringsType
    public $Companies; // ArrayOfStringsType
    public $ContactSource; // ContactSourceType
    public $Department; // string
    public $Generation; // string
    public $ImAddresses; // ImAddressDictionaryType
    public $JobTitle; // string
    public $Manager; // string
    public $Mileage; // string
    public $OfficeLocation; // string
    public $PostalAddressIndex; // PhysicalAddressIndexType
    public $Profession; // string
    public $SpouseName; // string
    public $Surname; // string
    public $WeddingAnniversary; // dateTime

    public static function getFieldURIScope() { return 'contacts'; }

    public static function getFieldType($field) { 
        $types = array (
            'FileAs' => 'string',
            'FileAsMapping' => 'FileAsMappingType',
            'DisplayName' => 'string',
            'GivenName' => 'string',
            'Initials' => 'string',
            'MiddleName' => 'string',
            'Nickname' => 'string',
            'CompleteName' => 'CompleteNameType',
            'CompanyName' => 'string',
            'EmailAddresses' => 'EmailAddressDictionaryType',
            'PhysicalAddresses' => 'PhysicalAddressDictionaryType',
            'PhoneNumbers' => 'PhoneNumberDictionaryType',
            'AssistantName' => 'string',
            'Birthday' => 'dateTime',
            'BusinessHomePage' => 'anyURI',
            'Children' => 'ArrayOfStringsType',
            'Companies' => 'ArrayOfStringsType',
            'ContactSource' => 'ContactSourceType',
            'Department' => 'string',
            'Generation' => 'string',
            'ImAddresses' => 'ImAddressDictionaryType',
            'JobTitle' => 'string',
            'Manager' => 'string',
            'Mileage' => 'string',
            'OfficeLocation' => 'string',
            'PostalAddressIndex' => 'PhysicalAddressIndexType',
            'Profession' => 'string',
            'SpouseName' => 'string',
            'Surname' => 'string',
            'WeddingAnniversary' => 'dateTime',
        );
        return array_key_exists($field,$types) ? $types[$field] : parent::getFieldType($field);
    }
}

class TaskType extends ItemType {
    public $ActualWork; // int
    public $AssignedTime; // dateTime
    public $BillingInformation; // string
    public $ChangeCount; // int
    public $Companies; // ArrayOfStringsType
    public $CompleteDate; // dateTime
    public $Contacts; // ArrayOfStringsType
    public $DelegationState; // TaskDelegateStateType
    public $Delegator; // string
    public $DueDate; // dateTime
    public $IsAssignmentEditable; // int
    public $IsComplete; // boolean
    public $IsRecurring; // boolean
    public $IsTeamTask; // boolean
    public $Mileage; // string
    public $Owner; // string
    public $PercentComplete; // double
    public $Recurrence; // TaskRecurrenceType
    public $StartDate; // dateTime
    public $Status; // TaskStatusType
    public $StatusDescription; // string
    public $TotalWork; // int

    public static function getFieldURIScope() { return 'task'; }
    public static function getFieldType($field) { 
        $types = array (
            'ActualWork' => 'int',
            'AssignedTime' => 'dateTime',
            'BillingInformation' => 'string',
            'ChangeCount' => 'int',
            'Companies' => 'ArrayOfStringsType',
            'CompleteDate' => 'dateTime',
            'Contacts' => 'ArrayOfStringsType',
            'DelegationState' => 'TaskDelegateStateType',
            'Delegator' => 'string',
            'DueDate' => 'dateTime',
            'IsAssignmentEditable' => 'int',
            'IsComplete' => 'boolean',
            'IsRecurring' => 'boolean',
            'IsTeamTask' => 'boolean',
            'Mileage' => 'string',
            'Owner' => 'string',
            'PercentComplete' => 'double',
            'Recurrence' => 'TaskRecurrenceType',
            'StartDate' => 'dateTime',
            'Status' => 'TaskStatusType',
            'StatusDescription' => 'string',
            'TotalWork' => 'int',
        );
        return array_key_exists($field,$types) ? $types[$field] : parent::getFieldType($field);
    }

}

class CalendarItemType extends ItemType {
    public $Start; // dateTime
    public $End; // dateTime
    public $OriginalStart; // dateTime
    public $IsAllDayEvent; // boolean
    public $LegacyFreeBusyStatus; // LegacyFreeBusyType
    public $Location; // string
    public $When; // string
    public $IsMeeting; // boolean
    public $IsCancelled; // boolean
    public $IsRecurring; // boolean
    public $MeetingRequestWasSent; // boolean
    public $IsResponseRequested; // boolean
    public $CalendarItemType; // CalendarItemTypeType
    public $MyResponseType; // ResponseTypeType
    public $Organizer; // SingleRecipientType
    public $RequiredAttendees; // NonEmptyArrayOfAttendeesType
    public $OptionalAttendees; // NonEmptyArrayOfAttendeesType
    public $Resources; // NonEmptyArrayOfAttendeesType
    public $ConflictingMeetingCount; // int
    public $AdjacentMeetingCount; // int
    public $ConflictingMeetings; // NonEmptyArrayOfAllItemsType
    public $AdjacentMeetings; // NonEmptyArrayOfAllItemsType
    public $Duration; // string
    public $TimeZone; // string
    public $AppointmentReplyTime; // dateTime
    public $AppointmentSequenceNumber; // int
    public $AppointmentState; // int
    public $Recurrence; // RecurrenceType
    public $FirstOccurrence; // OccurrenceInfoType
    public $LastOccurrence; // OccurrenceInfoType
    public $ModifiedOccurrences; // NonEmptyArrayOfOccurrenceInfoType
    public $DeletedOccurrences; // NonEmptyArrayOfDeletedOccurrencesType
    public $MeetingTimeZone; // TimeZoneType
    public $ConferenceType; // int
    public $AllowNewTimeProposal; // boolean
    public $IsOnlineMeeting; // boolean
    public $MeetingWorkspaceUrl; // string
    public $NetShowUrl; // string

    public static function getFieldURIScope() { return 'calendar'; }
    public static function getFieldType($field) { 
        $types = array (
                'Start' => 'dateTime',
                'End' => 'dateTime',
                'OriginalStart' => 'dateTime',
                'IsAllDayEvent' => 'boolean',
                'LegacyFreeBusyStatus' => 'LegacyFreeBusyType',
                'Location' => 'string',
                'When' => 'string',
                'IsMeeting' => 'boolean',
                'IsCancelled' => 'boolean',
                'IsRecurring' => 'boolean',
                'MeetingRequestWasSent' => 'boolean',
                'IsResponseRequested' => 'boolean',
                'CalendarItemType' => 'CalendarItemTypeType',
                'MyResponseType' => 'ResponseTypeType',
                'Organizer' => 'SingleRecipientType',
                'RequiredAttendees' => 'NonEmptyArrayOfAttendeesType',
                'OptionalAttendees' => 'NonEmptyArrayOfAttendeesType',
                'Resources' => 'NonEmptyArrayOfAttendeesType',
                'ConflictingMeetingCount' => 'int',
                'AdjacentMeetingCount' => 'int',
                'ConflictingMeetings' => 'NonEmptyArrayOfAllItemsType',
                'AdjacentMeetings' => 'NonEmptyArrayOfAllItemsType',
                'Duration' => 'string',
                'TimeZone' => 'string',
                'AppointmentReplyTime' => 'dateTime',
                'AppointmentSequenceNumber' => 'int',
                'AppointmentState' => 'int',
                'Recurrence' => 'RecurrenceType',
                'FirstOccurrence' => 'OccurrenceInfoType',
                'LastOccurrence' => 'OccurrenceInfoType',
                'ModifiedOccurrences' => 'NonEmptyArrayOfOccurrenceInfoType',
                'DeletedOccurrences' => 'NonEmptyArrayOfDeletedOccurrencesType',
                'MeetingTimeZone' => 'TimeZoneType',
                'ConferenceType' => 'int',
                'AllowNewTimeProposal' => 'boolean',
                'IsOnlineMeeting' => 'boolean',
                'MeetingWorkspaceUrl' => 'string',
                'NetShowUrl' => 'string',
        );
        return array_key_exists($field,$types) ? $types[$field] : parent::getFieldType($field);
    }
}



class UpdateItemType extends Type {
    public $SavedItemFolderId; // TargetFolderIdType
    public $ItemChanges; // NonEmptyArrayOfItemChangesType
    public $ConflictResolution; // ConflictResolutionType
    public $MessageDisposition; // MessageDispositionType
    public $SendMeetingInvitationsOrCancellations; // CalendarItemUpdateOperationType


    public function __construct($MessageDisposition = "SaveOnly", $ConflictResolution = "AutoResolve",$itemId = NULL, $item = NULL) {
        $this->MessageDisposition = $MessageDisposition;
        $this->ConflictResolution = $ConflictResolution;
        $this->ItemChanges = new NonEmptyArrayOfItemChangesType();
        if ($itemId && $item) 
            $this->ItemChanges->addItemChange($itemId,$item);
    }

    public function addItemChange($itemId,$item) {
        return $this->ItemChanges->addItemChange($itemId,$item);
    }

}

class NonEmptyArrayOfItemChangeDescriptionsType extends Type {
    public $AppendToItemField; // AppendToItemFieldType
    public $SetItemField; // SetItemFieldType
    public $DeleteItemField; // DeleteItemFieldType

    public function __construct() {
        $this->AppendToItemField = array();
        $this->SetItemField = array();
        $this->DeleteItemField = array();
    }

    public function addChange ($item = NULL, $field = NULL, $value = NULL, $fieldIndex = NULL, $fieldKey = NULL) {
        if ($item !== NULL) {
            if ($value === NULL || strlen($value) == 0) { //unset field
                //$this->DeleteItemField[] = new DeleteItemFieldType($item,$field,$fieldIndex,$fieldKey);
            }
            else {
                $this->SetItemField[] = new SetItemFieldType($item,$field,$value,$fieldIndex,$fieldKey);
            }
        }
    }

}

class NonEmptyArrayOfFolderChangeDescriptionsType extends Type {
  public $AppendToFolderField; // AppendToFolderFieldType
  public $SetFolderField; // SetFolderFieldType
  public $DeleteFolderField; // DeleteFolderFieldType
}

class ItemChangeType extends Type {
    public $ItemId; // ItemIdType
    public $OccurrenceItemId; // OccurrenceItemIdType
    public $RecurringMasterItemId; // RecurringMasterItemIdType
    public $Updates; // NonEmptyArrayOfItemChangeDescriptionsType

    public function __construct($itemId,$item) {
        $this->Updates = new NonEmptyArrayOfItemChangeDescriptionsType();
        $this->addUpdate($itemId,$item);

    }
    public function addUpdate($itemId,$item) {
        if ($itemId != NULL) 
            $this->ItemId = $itemId;
        if ($item != NULL) {
            foreach ($item as $field => $value) {
                if ($value instanceOf DictionaryType) {
                    foreach ($value->getKeys() as $fieldIndex) {
                        $entry = $value->getEntry($fieldIndex);
                        foreach ($entry as $fieldKey => $entryValue) {
                            if ($fieldKey != 'Key') 
                                $this->Updates->addChange($item,$field,$entryValue,$fieldIndex,$fieldKey);
                                #$this->Updates[] = new NonEmptyArrayOfItemChangeDescriptionsType($item,$field,$entryValue,$fieldIndex,$fieldKey);
                        }
                    }
                }
                else {
                    $this->Updates->addChange($item,$field,$value);
                }
            }
        }
    }
}

class NonEmptyArrayOfItemChangesType extends Type {
    public $ItemChange; // ItemChangeType

    public function __construct($itemChange = NULL) {
        $this->ItemChange = array();
        if ($itemChange !== NULL)
            $this->addItemChange($itemChange);
    }

    public function addItemChange($itemId,$item) {
        $this->ItemChange[] = new ItemChangeType($itemId,$item);
    }
}

class ItemFieldType extends Type {
    public function __construct($item = NULL,$field = NULL,$fieldIndex = NULL, $fieldKey = NULL) {
        if ($item instanceof Type) {
            $this->setIndex($item,$field,$fieldIndex, $fieldKey);
        }
    }

    public function setIndex($item,$field,$fieldIndex = NULL, $fieldKey = NULL) {
        $fieldURI = $item->getFieldURI($field);

        if ($fieldIndex === NULL) 
            $this->FieldURI->FieldURI = $fieldURI;
        else {
            $fieldURI = preg_replace('/(es|s)$/','',$fieldURI);
            #$fieldURI = "$itemName:$field";
            if ($fieldKey !== NULL && $fieldKey != '_')
                $fieldURI .= ":$fieldKey";

            $this->IndexedFieldURI->FieldURI = $fieldURI;
            $this->IndexedFieldURI->FieldIndex = $fieldIndex;
        }
    }
}

class SetItemFieldType extends ItemFieldType {
    //public $Item; // ItemType
    //public $Message; // MessageType
    //public $CalendarItem; // CalendarItemType
    //public $Contact; // ContactItemType
    //public $DistributionList; // DistributionListType
    //public $MeetingMessage; // MeetingMessageType
    //public $MeetingRequest; // MeetingRequestMessageType
    //public $MeetingResponse; // MeetingResponseMessageType
    //public $MeetingCancellation; // MeetingCancellationMessageType
    //public $Task; // TaskType

    
    public function __construct($item = NULL,$field = NULL, $value = NULL, $fieldIndex = NULL, $fieldKey = NULL) {
        parent::__construct($item,$field,$fieldIndex,$fieldKey);

        $itemClass = get_class($item);

        $obj = new $itemClass; //create a new empty copy of the inbound object
        
        if ($value !== NULL) { //We have a value to set
            if ($fieldIndex !== NULL) {
                $obj->$field = new DictionaryType();
                $obj->$field->setEntry($fieldIndex,array($fieldKey=>$value));
            }
            else
                $obj->$field = $value;

        }

        //Set the attr we need based on the type of the incoming item
        switch ($itemClass) {
            case 'ContactItemType':
                $this->Contact = $obj;
                break;
            case 'TaskType':
                $this->Task = $obj;
                break;
            case 'CalendarItemType':
                $this->CalendarItem = $obj;
                break;
            default:
                $this->Item = $obj;
        }
    }


}

class DeleteItemFieldType extends ItemFieldType {
}






class ImAddressDictionaryEntryType extends DictionaryEntryType {
    public $_;
}

class EmailAddressDictionaryEntryType extends DictionaryEntryType {
    public $_;
}

class PhoneNumberDictionaryEntryType extends DictionaryEntryType {
    public $_;
}

class PhysicalAddressDictionaryEntryType extends DictionaryEntryType {
    
    public $Street; // string
    public $City; // string
    public $State; // string
    public $CountryOrRegion; // string
    public $PostalCode; // string
}


class EmailAddressDictionaryType    extends DictionaryType { 
    public function newEntry() { return new EmailAddressDictionaryEntryType; }

}
class PhoneNumberDictionaryType     extends DictionaryType { 
    public function newEntry() { return new PhoneNumberDictionaryEntryType; }

}
class PhysicalAddressDictionaryType extends DictionaryType { 
    public function newEntry() { return new PhysicalAddressDictionaryEntryType; }

}
class ImAddressDictionaryType extends DictionaryType { 
    public function newEntry() { return new ImAddressDictionaryEntryType; }

}






class SidAndAttributesType extends Type {
  public $SecurityIdentifier; // string
  public $Attributes; // unsignedInt
}

class NonEmptyArrayOfGroupIdentifiersType extends Type {
  public $GroupIdentifier; // SidAndAttributesType
}

class NonEmptyArrayOfRestrictedGroupIdentifiersType extends Type {
  public $RestrictedGroupIdentifier; // SidAndAttributesType
}

class SerializedSecurityContextType extends Type {
  public $UserSid; // string
  public $GroupSids; // NonEmptyArrayOfGroupIdentifiersType
  public $RestrictedGroupSids; // NonEmptyArrayOfRestrictedGroupIdentifiersType
  public $PrimarySmtpAddress; // string
}

class ConnectingSIDType extends Type {
  public $PrincipalName; // string
  public $SID; // string
  public $PrimarySmtpAddress; // string
}

class ExchangeImpersonationType extends Type {
  public $ConnectingSID; // ConnectingSIDType
}

class ServerVersionInfo {
  public $MajorVersion; // int
  public $MinorVersion; // int
  public $MajorBuildNumber; // int
  public $MinorBuildNumber; // int
}

class NonEmptyStringType extends Type {
}

class BaseEmailAddressType extends Type {
}

class MailboxTypeType extends Type {
}

class EmailAddressType extends Type {
  public $Name; // string
  public $EmailAddress; // NonEmptyStringType
  public $RoutingType; // NonEmptyStringType
  public $MailboxType; // MailboxTypeType
  public $ItemId; // ItemIdType
}

class SingleRecipientType extends Type {
  public $Mailbox; // EmailAddressType
}

class UnindexedFieldURIType extends Type {
}

class DictionaryURIType extends Type {
}

class ExceptionPropertyURIType extends Type {
}

class GuidType extends Type {
}

class DistinguishedPropertySetType extends Type {
}

class MapiPropertyTypeType extends Type {
}

class BasePathToElementType extends Type {
}

class PathToUnindexedFieldType extends Type {
  public $FieldURI; // UnindexedFieldURIType
}

class PathToIndexedFieldType extends Type {
  public $FieldURI; // DictionaryURIType
  public $FieldIndex; // string
}

class PathToExceptionFieldType extends Type {
  public $FieldURI; // ExceptionPropertyURIType
}

class PropertyTagType extends Type {
}

class anonymous24 {
}

class PathToExtendedFieldType extends Type {
  public $DistinguishedPropertySetId; // DistinguishedPropertySetType
  public $PropertySetId; // GuidType
  public $PropertyTag; // PropertyTagType
  public $PropertyName; // string
  public $PropertyId; // int
  public $PropertyType; // MapiPropertyTypeType
}

class NonEmptyArrayOfPathsToElementType extends Type {
  public $Path; // BasePathToElementType
}

class NonEmptyArrayOfPropertyValuesType extends Type {
  public $Value; // string
}

class ExtendedPropertyType extends Type {
  public $ExtendedFieldURI; // PathToExtendedFieldType
  public $Value; // string
  public $Values; // NonEmptyArrayOfPropertyValuesType
}

class FolderQueryTraversalType extends Type {
}

class SearchFolderTraversalType extends Type {
}

class ItemQueryTraversalType extends Type {
}

class DefaultShapeNamesType extends Type {
}

class BodyTypeResponseType extends Type {
}

class FolderResponseShapeType extends Type {
  public $BaseShape; // DefaultShapeNamesType
  public $AdditionalProperties; // NonEmptyArrayOfPathsToElementType
}

class ItemResponseShapeType extends Type {
  public $BaseShape; // DefaultShapeNamesType
  public $IncludeMimeContent; // boolean
  public $BodyType; // BodyTypeResponseType
  public $AdditionalProperties; // NonEmptyArrayOfPathsToElementType
}

class AttachmentResponseShapeType extends Type {
  public $IncludeMimeContent; // boolean
  public $BodyType; // BodyTypeResponseType
  public $AdditionalProperties; // NonEmptyArrayOfPathsToElementType
}

class DisposalType extends Type {
}

class ConflictResolutionType extends Type {
}

class ResponseClassType extends Type {
}

class ChangeDescriptionType extends Type {
  public $Path; // BasePathToElementType
}

class ItemChangeDescriptionType extends Type {
}

class FolderChangeDescriptionType extends Type {
}


class SetFolderFieldType extends Type {
  public $Folder; // FolderType
  public $CalendarFolder; // CalendarFolderType
  public $ContactsFolder; // ContactsFolderType
  public $SearchFolder; // SearchFolderType
  public $TasksFolder; // TasksFolderType
}


class DeleteFolderFieldType extends Type {
}

class AppendToItemFieldType extends Type {
  public $Item; // ItemType
  public $Message; // MessageType
  public $CalendarItem; // CalendarItemType
  public $Contact; // ContactItemType
  public $DistributionList; // DistributionListType
  public $MeetingMessage; // MeetingMessageType
  public $MeetingRequest; // MeetingRequestMessageType
  public $MeetingResponse; // MeetingResponseMessageType
  public $MeetingCancellation; // MeetingCancellationMessageType
  public $Task; // TaskType
}

class AppendToFolderFieldType extends Type {
  public $Folder; // FolderType
  public $CalendarFolder; // CalendarFolderType
  public $ContactsFolder; // ContactsFolderType
  public $SearchFolder; // SearchFolderType
  public $TasksFolder; // TasksFolderType
}


class InternetHeaderType extends Type {
  public $_; // string
  public $HeaderName; // string
}

class NonEmptyArrayOfInternetHeadersType extends Type {
  public $InternetMessageHeader; // InternetHeaderType
}

class RequestAttachmentIdType extends Type {
  public $Id; // string
}

class AttachmentIdType extends Type {
  public $RootItemId; // string
  public $RootItemChangeKey; // string
}

class RootItemIdType extends Type {
  public $RootItemId; // string
  public $RootItemChangeKey; // string
}

class NonEmptyArrayOfRequestAttachmentIdsType extends Type {
  public $AttachmentId; // RequestAttachmentIdType
}

class AttachmentType extends Type {
  public $AttachmentId; // AttachmentIdType
  public $Name; // string
  public $ContentType; // string
  public $ContentId; // string
  public $ContentLocation; // string
}

class ItemAttachmentType extends Type {
  public $Item; // ItemType
  public $Message; // MessageType
  public $CalendarItem; // CalendarItemType
  public $Contact; // ContactItemType
  public $MeetingMessage; // MeetingMessageType
  public $MeetingRequest; // MeetingRequestMessageType
  public $MeetingResponse; // MeetingResponseMessageType
  public $MeetingCancellation; // MeetingCancellationMessageType
  public $Task; // TaskType
}

class SyncFolderItemsCreateOrUpdateType extends Type {
  public $Item; // ItemType
  public $Message; // MessageType
  public $CalendarItem; // CalendarItemType
  public $Contact; // ContactItemType
  public $DistributionList; // DistributionListType
  public $MeetingMessage; // MeetingMessageType
  public $MeetingRequest; // MeetingRequestMessageType
  public $MeetingResponse; // MeetingResponseMessageType
  public $MeetingCancellation; // MeetingCancellationMessageType
  public $Task; // TaskType
}

class FileAttachmentType extends Type {
  public $Content; // base64Binary
}

class NonEmptyArrayOfAttachmentsType extends Type {
  public $ItemAttachment; // ItemAttachmentType
  public $FileAttachment; // FileAttachmentType
}

class SensitivityChoicesType extends Type {
}

class ImportanceChoicesType extends Type {
}

class BodyTypeType extends Type {
}

class BodyType extends Type {
    public $_; // string
    public $BodyType; // BodyTypeType

    public function __toString() {
        return (string)$this->_;
    }
}

class BaseFolderIdType extends Type {
}

class FolderClassType extends Type {
}

class DistinguishedFolderIdNameType extends Type {
}



class FolderIdType extends Type {
    public $Id; // string
    public $ChangeKey; // string

    public function __construct($Id = NULL, $ChangeKey = NULL) {
        if ($Id !== NULL)
            $this->Id = $Id;
        if ($ChangeKey !== NULL)
            $this->ChangeKey = $ChangeKey;
    }
}

class DistinguishedFolderIdType extends FolderIdType {
    public $Mailbox; // EmailAddressType
    public $Id; // DistinguishedFolderIdNameType
    public $ChangeKey; // string

}


class NonEmptyArrayOfBaseFolderIdsType extends Type {
  public $FolderId; // FolderIdType
  public $DistinguishedFolderId; // DistinguishedFolderIdType
}

class TargetFolderIdType extends Type {
  public $FolderId; // FolderIdType
  public $DistinguishedFolderId; // DistinguishedFolderIdType
}

class FindFolderParentType extends Type {
  public $Folders; // ArrayOfFoldersType
  public $IndexedPagingOffset; // int
  public $NumeratorOffset; // int
  public $AbsoluteDenominator; // int
  public $IncludesLastItemInRange; // boolean
  public $TotalItemsInView; // int
}

class BaseFolderType extends Type {
  public $FolderId; // FolderIdType
  public $ParentFolderId; // FolderIdType
  public $FolderClass; // string
  public $DisplayName; // string
  public $TotalCount; // int
  public $ChildFolderCount; // int
  public $ExtendedProperty; // ExtendedPropertyType
  public $ManagedFolderInformation; // ManagedFolderInformationType
}

class ManagedFolderInformationType extends Type {
  public $CanDelete; // boolean
  public $CanRenameOrMove; // boolean
  public $MustDisplayComment; // boolean
  public $HasQuota; // boolean
  public $IsManagedFoldersRoot; // boolean
  public $ManagedFolderId; // string
  public $Comment; // string
  public $StorageQuota; // int
  public $FolderSize; // int
  public $HomePage; // string
}

class FolderType extends Type {
  public $UnreadCount; // int
}

class CalendarFolderType extends Type {
}

class ContactsFolderType extends Type {
}

class SearchFolderType extends Type {
  public $SearchParameters; // SearchParametersType
}

class TasksFolderType extends Type {
}

class NonEmptyArrayOfFoldersType extends Type {
  public $Folder; // FolderType
  public $CalendarFolder; // CalendarFolderType
  public $ContactsFolder; // ContactsFolderType
  public $SearchFolder; // SearchFolderType
  public $TasksFolder; // TasksFolderType
}

class BaseItemIdType extends Type {
}

class DerivedItemIdType extends Type {
}

class ItemIdType extends Type {
  public $Id; // string
  public $ChangeKey; // string
}

class NonEmptyArrayOfBaseItemIdsType extends Type {
  public $ItemId; // ItemIdType
  public $OccurrenceItemId; // OccurrenceItemIdType
  public $RecurringMasterItemId; // RecurringMasterItemIdType
}

class ItemClassType extends Type {
}

class ResponseObjectCoreType extends Type {
  public $ReferenceItemId; // ItemIdType
}

class ResponseObjectType extends Type {
  public $ObjectName; // string
}

class NonEmptyArrayOfResponseObjectsType extends Type {
  public $AcceptItem; // AcceptItemType
  public $TentativelyAcceptItem; // TentativelyAcceptItemType
  public $DeclineItem; // DeclineItemType
  public $ReplyToItem; // ReplyToItemType
  public $ForwardItem; // ForwardItemType
  public $ReplyAllToItem; // ReplyAllToItemType
  public $CancelCalendarItem; // CancelCalendarItemType
  public $RemoveItem; // RemoveItemType
  public $SuppressReadReceipt; // SuppressReadReceiptType
}

class FolderChangeType extends Type {
  public $FolderId; // FolderIdType
  public $DistinguishedFolderId; // DistinguishedFolderIdType
  public $Updates; // NonEmptyArrayOfFolderChangeDescriptionsType
}

class NonEmptyArrayOfFolderChangesType extends Type {
  public $FolderChange; // FolderChangeType
}

class WellKnownResponseObjectType extends Type {
  public $ItemClass; // ItemClassType
  public $Sensitivity; // SensitivityChoicesType
  public $Body; // BodyType
  public $Attachments; // NonEmptyArrayOfAttachmentsType
  public $InternetMessageHeaders; // NonEmptyArrayOfInternetHeadersType
  public $Sender; // SingleRecipientType
  public $ToRecipients; // ArrayOfRecipientsType
  public $CcRecipients; // ArrayOfRecipientsType
  public $BccRecipients; // ArrayOfRecipientsType
  public $IsReadReceiptRequested; // boolean
  public $IsDeliveryReceiptRequested; // boolean
  public $ReferenceItemId; // ItemIdType
  public $ObjectName; // string
}

class SmartResponseBaseType extends Type {
  public $Subject; // string
  public $Body; // BodyType
  public $ToRecipients; // ArrayOfRecipientsType
  public $CcRecipients; // ArrayOfRecipientsType
  public $BccRecipients; // ArrayOfRecipientsType
  public $IsReadReceiptRequested; // boolean
  public $IsDeliveryReceiptRequested; // boolean
  public $ReferenceItemId; // ItemIdType
  public $ObjectName; // string
}

class SmartResponseType extends Type {
  public $NewBodyContent; // BodyType
}

class ReplyToItemType extends Type {
}

class ReplyAllToItemType extends Type {
}

class ForwardItemType extends Type {
}

class CancelCalendarItemType extends Type {
}

class ReferenceItemResponseType extends Type {
  public $ReferenceItemId; // ItemIdType
  public $ObjectName; // string
}

class SuppressReadReceiptType extends Type {
}

class FindItemParentType extends Type {
  public $Items; // ArrayOfRealItemsType
  public $Groups; // ArrayOfGroupedItemsType
  public $IndexedPagingOffset; // int
  public $NumeratorOffset; // int
  public $AbsoluteDenominator; // int
  public $IncludesLastItemInRange; // boolean
  public $TotalItemsInView; // int
}


class NonEmptyArrayOfAllItemsType extends Type {
  public $Item; // ItemType
  public $Message; // MessageType
  public $CalendarItem; // CalendarItemType
  public $Contact; // ContactItemType
  public $DistributionList; // DistributionListType
  public $MeetingMessage; // MeetingMessageType
  public $MeetingRequest; // MeetingRequestMessageType
  public $MeetingResponse; // MeetingResponseMessageType
  public $MeetingCancellation; // MeetingCancellationMessageType
  public $Task; // TaskType
  public $ReplyToItem; // ReplyToItemType
  public $ForwardItem; // ForwardItemType
  public $ReplyAllToItem; // ReplyAllToItemType
  public $AcceptItem; // AcceptItemType
  public $TentativelyAcceptItem; // TentativelyAcceptItemType
  public $DeclineItem; // DeclineItemType
  public $CancelCalendarItem; // CancelCalendarItemType
  public $RemoveItem; // RemoveItemType
  public $SuppressReadReceipt; // SuppressReadReceiptType
}

class AcceptItemType extends Type {
}

class TentativelyAcceptItemType extends Type {
}

class DeclineItemType extends Type {
}

class RemoveItemType extends Type {
}

class MimeContentType extends Type {
  public $_; // string
  public $CharacterSet; // string
}

class MessageDispositionType extends Type {
}

class CalendarItemCreateOrDeleteOperationType extends Type {
}

class CalendarItemUpdateOperationType extends Type {
}

class AffectedTaskOccurrencesType extends Type {
}

class MessageType extends Type {
  public $Sender; // SingleRecipientType
  public $ToRecipients; // ArrayOfRecipientsType
  public $CcRecipients; // ArrayOfRecipientsType
  public $BccRecipients; // ArrayOfRecipientsType
  public $IsReadReceiptRequested; // boolean
  public $IsDeliveryReceiptRequested; // boolean
  public $ConversationIndex; // base64Binary
  public $ConversationTopic; // string
  public $From; // SingleRecipientType
  public $InternetMessageId; // string
  public $IsRead; // boolean
  public $IsResponseRequested; // boolean
  public $References; // string
  public $ReplyTo; // ArrayOfRecipientsType
}

class TaskStatusType extends Type {
}

class TaskDelegateStateType extends Type {
}


class BasePagingType extends Type {
  public $MaxEntriesReturned; // int
}

class IndexBasePointType extends Type {
}

class IndexedPageViewType extends Type {
  public $Offset; // int
  public $BasePoint; // IndexBasePointType
}

class FractionalPageViewType extends Type {
  public $Numerator; // int
  public $Denominator; // int
}

class CalendarViewType extends Type {
  public $StartDate; // dateTime
  public $EndDate; // dateTime
}

class ContactsViewType extends Type {
  public $InitialName; // string
  public $FinalName; // string
}

class ResolutionType extends Type {
  public $Mailbox; // EmailAddressType
  public $Contact; // ContactItemType
}

class MeetingRequestTypeType extends Type {
}

class ReminderMinutesBeforeStartType extends Type {
}

class anonymous135 {
}

class anonymous136 {
}

class LegacyFreeBusyType extends Type {
}

class CalendarItemTypeType extends Type {
}

class ResponseTypeType extends Type {
}

class AttendeeType extends Type {
  public $Mailbox; // EmailAddressType
  public $ResponseType; // ResponseTypeType
  public $LastResponseTime; // dateTime
}

class NonEmptyArrayOfAttendeesType extends Type {
  public $Attendee; // AttendeeType
}

class OccurrenceItemIdType extends Type {
  public $RecurringMasterId; // DerivedItemIdType
  public $ChangeKey; // string
  public $InstanceIndex; // int
}

class RecurringMasterItemIdType extends Type {
  public $OccurrenceId; // DerivedItemIdType
  public $ChangeKey; // string
}

class DayOfWeekType extends Type {
}

class DaysOfWeekType extends Type {
}

class DayOfWeekIndexType extends Type {
}

class MonthNamesType extends Type {
}

class RecurrencePatternBaseType extends Type {
}

class IntervalRecurrencePatternBaseType extends Type {
  public $Interval; // int
}

class RegeneratingPatternBaseType extends Type {
}

class DailyRegeneratingPatternType extends Type {
}

class WeeklyRegeneratingPatternType extends Type {
}

class MonthlyRegeneratingPatternType extends Type {
}

class YearlyRegeneratingPatternType extends Type {
}

class RelativeYearlyRecurrencePatternType extends Type {
  public $DaysOfWeek; // DayOfWeekType
  public $DayOfWeekIndex; // DayOfWeekIndexType
  public $Month; // MonthNamesType
}

class AbsoluteYearlyRecurrencePatternType extends Type {
  public $DayOfMonth; // int
  public $Month; // MonthNamesType
}

class RelativeMonthlyRecurrencePatternType extends Type {
  public $DaysOfWeek; // DayOfWeekType
  public $DayOfWeekIndex; // DayOfWeekIndexType
}

class AbsoluteMonthlyRecurrencePatternType extends Type {
  public $DayOfMonth; // int
}

class WeeklyRecurrencePatternType extends Type {
  public $DaysOfWeek; // DaysOfWeekType
}

class DailyRecurrencePatternType extends Type {
}

class TimeChangeType extends Type {
  public $Offset; // duration
  public $RelativeYearlyRecurrence; // RelativeYearlyRecurrencePatternType
  public $AbsoluteDate; // date
  public $Time; // time
  public $TimeZoneName; // string
}

class TimeZoneType extends Type {
  public $BaseOffset; // duration
  public $Standard; // TimeChangeType
  public $Daylight; // TimeChangeType
  public $TimeZoneName; // string
}

class RecurrenceRangeBaseType extends Type {
  public $StartDate; // date
}

class NoEndRecurrenceRangeType extends Type {
}

class EndDateRecurrenceRangeType extends Type {
  public $EndDate; // date
}

class NumberedRecurrenceRangeType extends Type {
  public $NumberOfOccurrences; // int
}

class RecurrenceType extends Type {
  public $RelativeYearlyRecurrence; // RelativeYearlyRecurrencePatternType
  public $AbsoluteYearlyRecurrence; // AbsoluteYearlyRecurrencePatternType
  public $RelativeMonthlyRecurrence; // RelativeMonthlyRecurrencePatternType
  public $AbsoluteMonthlyRecurrence; // AbsoluteMonthlyRecurrencePatternType
  public $WeeklyRecurrence; // WeeklyRecurrencePatternType
  public $DailyRecurrence; // DailyRecurrencePatternType
  public $NoEndRecurrence; // NoEndRecurrenceRangeType
  public $EndDateRecurrence; // EndDateRecurrenceRangeType
  public $NumberedRecurrence; // NumberedRecurrenceRangeType
}

class TaskRecurrenceType extends Type {
  public $RelativeYearlyRecurrence; // RelativeYearlyRecurrencePatternType
  public $AbsoluteYearlyRecurrence; // AbsoluteYearlyRecurrencePatternType
  public $RelativeMonthlyRecurrence; // RelativeMonthlyRecurrencePatternType
  public $AbsoluteMonthlyRecurrence; // AbsoluteMonthlyRecurrencePatternType
  public $WeeklyRecurrence; // WeeklyRecurrencePatternType
  public $DailyRecurrence; // DailyRecurrencePatternType
  public $DailyRegeneration; // DailyRegeneratingPatternType
  public $WeeklyRegeneration; // WeeklyRegeneratingPatternType
  public $MonthlyRegeneration; // MonthlyRegeneratingPatternType
  public $YearlyRegeneration; // YearlyRegeneratingPatternType
  public $NoEndRecurrence; // NoEndRecurrenceRangeType
  public $EndDateRecurrence; // EndDateRecurrenceRangeType
  public $NumberedRecurrence; // NumberedRecurrenceRangeType
}

class OccurrenceInfoType extends Type {
  public $ItemId; // ItemIdType
  public $Start; // dateTime
  public $End; // dateTime
  public $OriginalStart; // dateTime
}

class NonEmptyArrayOfOccurrenceInfoType extends Type {
  public $Occurrence; // OccurrenceInfoType
}

class DeletedOccurrenceInfoType extends Type {
  public $Start; // dateTime
}

class NonEmptyArrayOfDeletedOccurrencesType extends Type {
  public $DeletedOccurrence; // DeletedOccurrenceInfoType
}


class MeetingMessageType extends Type {
  public $AssociatedCalendarItemId; // ItemIdType
  public $IsDelegated; // boolean
  public $IsOutOfDate; // boolean
  public $HasBeenProcessed; // boolean
  public $ResponseType; // ResponseTypeType
}

class MeetingRequestMessageType extends Type {
  public $MeetingRequestType; // MeetingRequestTypeType
  public $IntendedFreeBusyStatus; // LegacyFreeBusyType
  public $Start; // dateTime
  public $End; // dateTime
  public $OriginalStart; // dateTime
  public $IsAllDayEvent; // boolean
  public $LegacyFreeBusyStatus; // LegacyFreeBusyType
  public $Location; // string
  public $When; // string
  public $IsMeeting; // boolean
  public $IsCancelled; // boolean
  public $IsRecurring; // boolean
  public $MeetingRequestWasSent; // boolean
  public $CalendarItemType; // CalendarItemTypeType
  public $MyResponseType; // ResponseTypeType
  public $Organizer; // SingleRecipientType
  public $RequiredAttendees; // NonEmptyArrayOfAttendeesType
  public $OptionalAttendees; // NonEmptyArrayOfAttendeesType
  public $Resources; // NonEmptyArrayOfAttendeesType
  public $ConflictingMeetingCount; // int
  public $AdjacentMeetingCount; // int
  public $ConflictingMeetings; // NonEmptyArrayOfAllItemsType
  public $AdjacentMeetings; // NonEmptyArrayOfAllItemsType
  public $Duration; // string
  public $TimeZone; // string
  public $AppointmentReplyTime; // dateTime
  public $AppointmentSequenceNumber; // int
  public $AppointmentState; // int
  public $Recurrence; // RecurrenceType
  public $FirstOccurrence; // OccurrenceInfoType
  public $LastOccurrence; // OccurrenceInfoType
  public $ModifiedOccurrences; // NonEmptyArrayOfOccurrenceInfoType
  public $DeletedOccurrences; // NonEmptyArrayOfDeletedOccurrencesType
  public $MeetingTimeZone; // TimeZoneType
  public $ConferenceType; // int
  public $AllowNewTimeProposal; // boolean
  public $IsOnlineMeeting; // boolean
  public $MeetingWorkspaceUrl; // string
  public $NetShowUrl; // string
}

class MeetingResponseMessageType extends Type {
}

class MeetingCancellationMessageType extends Type {
}

class ImAddressKeyType extends Type {
}

class EmailAddressKeyType extends Type {
}

class PhoneNumberKeyType extends Type {
}

class PhysicalAddressIndexType extends Type {
}

class PhysicalAddressKeyType extends Type {
}

class FileAsMappingType extends Type {
}

class ContactSourceType extends Type {
}

class CompleteNameType extends Type {
  public $Title; // string
  public $FirstName; // string
  public $MiddleName; // string
  public $LastName; // string
  public $Suffix; // string
  public $Initials; // string
  public $FullName; // string
  public $Nickname; // string
  public $YomiFirstName; // string
  public $YomiLastName; // string
}


class DistributionListType extends Type {
  public $DisplayName; // string
  public $FileAs; // string
  public $ContactSource; // ContactSourceType
}

class SearchParametersType extends Type {
  public $Restriction; // RestrictionType
  public $BaseFolderIds; // NonEmptyArrayOfBaseFolderIdsType
  public $Traversal; // SearchFolderTraversalType
}

class ConstantValueType extends Type {
  public $Value; // string
}

class SearchExpressionType extends Type {
}

class AggregateType extends Type {
}

class AggregateOnType extends Type {
  public $FieldURI; // PathToUnindexedFieldType
  public $IndexedFieldURI; // PathToIndexedFieldType
  public $ExtendedFieldURI; // PathToExtendedFieldType
  public $Aggregate; // AggregateType
}

class BaseGroupByType extends Type {
  public $Order; // SortDirectionType
}

class GroupByType extends Type {
  public $FieldURI; // PathToUnindexedFieldType
  public $IndexedFieldURI; // PathToIndexedFieldType
  public $ExtendedFieldURI; // PathToExtendedFieldType
  public $AggregateOn; // AggregateOnType
}

class StandardGroupByType extends Type {
}

class DistinguishedGroupByType extends Type {
  public $StandardGroupBy; // StandardGroupByType
}

class GroupedItemsType extends Type {
  public $GroupIndex; // string
  public $Items; // ArrayOfRealItemsType
}

class ExistsType extends Type {
  public $Path; // BasePathToElementType
}

class FieldURIOrConstantType extends Type {
  public $Path; // BasePathToElementType
  public $Constant; // ConstantValueType
}

class TwoOperandExpressionType extends Type {
  public $Path; // BasePathToElementType
  public $FieldURIOrConstant; // FieldURIOrConstantType
}

class ExcludesAttributeType extends Type {
}

class ExcludesValueType extends Type {
  public $Value; // ExcludesAttributeType
}

class ExcludesType extends Type {
  public $Path; // BasePathToElementType
  public $Bitmask; // ExcludesValueType
}

class IsEqualToType extends Type {
}

class IsNotEqualToType extends Type {
}

class IsGreaterThanType extends Type {
}

class IsGreaterThanOrEqualToType extends Type {
}

class IsLessThanType extends Type {
}

class IsLessThanOrEqualToType extends Type {
}

class ContainmentModeType extends Type {
}

class ContainmentComparisonType extends Type {
}

class ContainsExpressionType extends Type {
  public $Path; // BasePathToElementType
  public $Constant; // ConstantValueType
  public $ContainmentMode; // ContainmentModeType
  public $ContainmentComparison; // ContainmentComparisonType
}

class NotType extends Type {
  public $SearchExpression; // SearchExpressionType
}

class MultipleOperandBooleanExpressionType extends Type {
  public $SearchExpression; // SearchExpressionType
}

class AndType extends Type {
}

class OrType extends Type {
}

class RestrictionType extends Type {
  public $SearchExpression; // SearchExpressionType
}

class SortDirectionType extends Type {
}

class FieldOrderType extends Type {
  public $Path; // BasePathToElementType
  public $Order; // SortDirectionType
}

class NonEmptyArrayOfFieldOrdersType extends Type {
  public $FieldOrder; // FieldOrderType
}

class NonEmptyArrayOfFolderNamesType extends Type {
  public $FolderName; // string
}

class WatermarkType extends Type {
}

class SubscriptionIdType extends Type {
}

class BaseNotificationEventType extends Type {
  public $Watermark; // WatermarkType
}

class BaseObjectChangedEventType extends Type {
  public $TimeStamp; // dateTime
  public $FolderId; // FolderIdType
  public $ItemId; // ItemIdType
  public $ParentFolderId; // FolderIdType
}

class ModifiedEventType extends Type {
  public $UnreadCount; // int
}

class MovedCopiedEventType extends Type {
  public $OldFolderId; // FolderIdType
  public $OldItemId; // ItemIdType
  public $OldParentFolderId; // FolderIdType
}

class NotificationType extends Type {
  public $SubscriptionId; // SubscriptionIdType
  public $PreviousWatermark; // WatermarkType
  public $MoreEvents; // boolean
  public $CopiedEvent; // MovedCopiedEventType
  public $CreatedEvent; // BaseObjectChangedEventType
  public $DeletedEvent; // BaseObjectChangedEventType
  public $ModifiedEvent; // ModifiedEventType
  public $MovedEvent; // MovedCopiedEventType
  public $NewMailEvent; // BaseObjectChangedEventType
  public $StatusEvent; // BaseNotificationEventType
}

class NotificationEventTypeType extends Type {
}

class NonEmptyArrayOfNotificationEventTypesType extends Type {
  public $EventType; // NotificationEventTypeType
}

class SubscriptionTimeoutType extends Type {
}

class SubscriptionStatusFrequencyType extends Type {
}

class BaseSubscriptionRequestType extends Type {
  public $FolderIds; // NonEmptyArrayOfBaseFolderIdsType
  public $EventTypes; // NonEmptyArrayOfNotificationEventTypesType
  public $Watermark; // WatermarkType
}

class PushSubscriptionRequestType extends Type {
  public $StatusFrequency; // SubscriptionStatusFrequencyType
  public $URL; // string
}

class PullSubscriptionRequestType extends Type {
  public $Timeout; // SubscriptionTimeoutType
}

class SubscriptionStatusType extends Type {
}

class SyncFolderItemsDeleteType extends Type {
  public $ItemId; // ItemIdType
}

class SyncFolderItemsChangesType extends Type {
  public $Create; // SyncFolderItemsCreateOrUpdateType
  public $Update; // SyncFolderItemsCreateOrUpdateType
  public $Delete; // SyncFolderItemsDeleteType
}

class SyncFolderHierarchyCreateOrUpdateType extends Type {
  public $Folder; // FolderType
  public $CalendarFolder; // CalendarFolderType
  public $ContactsFolder; // ContactsFolderType
  public $SearchFolder; // SearchFolderType
  public $TasksFolder; // TasksFolderType
}

class SyncFolderHierarchyDeleteType extends Type {
  public $FolderId; // FolderIdType
}

class SyncFolderHierarchyChangesType extends Type {
  public $Create; // SyncFolderHierarchyCreateOrUpdateType
  public $Update; // SyncFolderHierarchyCreateOrUpdateType
  public $Delete; // SyncFolderHierarchyDeleteType
}

class MaxSyncChangesReturnedType extends Type {
}

class AvailabilityProxyRequestType extends Type {
}

class MeetingAttendeeType extends Type {
}

class CalendarEventDetails {
  public $ID; // string
  public $Subject; // string
  public $Location; // string
  public $IsMeeting; // boolean
  public $IsRecurring; // boolean
  public $IsException; // boolean
  public $IsReminderSet; // boolean
  public $IsPrivate; // boolean
}

class CalendarEvent {
  public $StartTime; // dateTime
  public $EndTime; // dateTime
  public $BusyType; // LegacyFreeBusyType
  public $CalendarEventDetails; // CalendarEventDetails
}

class Duration {
  public $StartTime; // dateTime
  public $EndTime; // dateTime
}

class EmailAddress {
  public $Name; // string
  public $Address; // string
  public $RoutingType; // string
}

class FreeBusyViewType extends Type {
}

class anonymous260 {
}

class FreeBusyViewOptionsType extends Type {
  public $TimeWindow; // Duration
  public $MergedFreeBusyIntervalInMinutes; // int
  public $RequestedView; // FreeBusyViewType
}

class WorkingPeriod {
  public $DayOfWeek; // DaysOfWeekType
  public $StartTimeInMinutes; // int
  public $EndTimeInMinutes; // int
}

class SerializableTimeZoneTime {
  public $Bias; // int
  public $Time; // string
  public $DayOrder; // short
  public $Month; // short
  public $DayOfWeek; // DayOfWeekType
}

class SerializableTimeZone {
  public $Bias; // int
  public $StandardTime; // SerializableTimeZoneTime
  public $DaylightTime; // SerializableTimeZoneTime
}

class WorkingHours {
  public $TimeZone; // SerializableTimeZone
  public $WorkingPeriodArray; // ArrayOfWorkingPeriod
}

class FreeBusyView {
  public $FreeBusyViewType; // FreeBusyViewType
  public $MergedFreeBusy; // string
  public $CalendarEventArray; // ArrayOfCalendarEvent
  public $WorkingHours; // WorkingHours
}

class MailboxData {
  public $Email; // EmailAddress
  public $AttendeeType; // MeetingAttendeeType
  public $ExcludeConflicts; // boolean
}

class SuggestionQuality {
}

class SuggestionsViewOptionsType extends Type {
  public $GoodThreshold; // int
  public $MaximumResultsByDay; // int
  public $MaximumNonWorkHourResultsByDay; // int
  public $MeetingDurationInMinutes; // int
  public $MinimumSuggestionQuality; // SuggestionQuality
  public $DetailedSuggestionsWindow; // Duration
  public $CurrentMeetingTime; // dateTime
  public $GlobalObjectId; // string
}

class AttendeeConflictData {
}

class UnknownAttendeeConflictData {
}

class TooBigGroupAttendeeConflictData {
}

class IndividualAttendeeConflictData {
  public $BusyType; // LegacyFreeBusyType
}

class GroupAttendeeConflictData {
  public $NumberOfMembers; // int
  public $NumberOfMembersAvailable; // int
  public $NumberOfMembersWithConflict; // int
  public $NumberOfMembersWithNoData; // int
}

class Suggestion {
  public $MeetingTime; // dateTime
  public $IsWorkTime; // boolean
  public $SuggestionQuality; // SuggestionQuality
  public $AttendeeConflictDataArray; // ArrayOfAttendeeConflictData
}

class SuggestionDayResult {
  public $Date; // dateTime
  public $DayQuality; // SuggestionQuality
  public $SuggestionArray; // ArrayOfSuggestion
}

class OofState {
}

class ExternalAudience {
}

class ReplyBody {
  public $Message; // string
  public $lang; // UNKNOWN
}

class UserOofSettings {
  public $OofState; // OofState
  public $ExternalAudience; // ExternalAudience
  public $Duration; // Duration
  public $InternalReply; // ReplyBody
  public $ExternalReply; // ReplyBody
}

class Value {
  public $_; // string
  public $Name; // string
}

class ResponseCodeType extends Type {
}

class ResponseMessageType extends Type {
  public $MessageText; // string
  public $ResponseCode; // ResponseCodeType
  public $DescriptiveLinkKey; // int
  public $MessageXml; // MessageXml
  public $ResponseClass; // ResponseClassType
}

class MessageXml {
  public $any; // <anyXML>
}

class BaseResponseMessageType extends Type {
  public $ResponseMessages; // ArrayOfResponseMessagesType
}

class BaseRequestType extends Type {
}

class GetFolderType extends Type {
  public $FolderShape; // FolderResponseShapeType
  public $FolderIds; // NonEmptyArrayOfBaseFolderIdsType
}

class CreateFolderType extends Type {
  public $ParentFolderId; // TargetFolderIdType
  public $Folders; // NonEmptyArrayOfFoldersType
}

class FindFolderType extends Type {
    public $FolderShape; // FolderResponseShapeType
//    public $IndexedPageFolderView; // IndexedPageViewType
//    public $FractionalPageFolderView; // FractionalPageViewType
    public $Restriction; // RestrictionType
    public $ParentFolderIds; // NonEmptyArrayOfBaseFolderIdsType
    public $Traversal; // FolderQueryTraversalType


    public function __construct($parentFolderId = NULL,$parentDistinguished = FALSE) {

        #Default values for the time being
        $this->Traversal = "Shallow"; #default for now
        $this->FolderShape = new FolderResponseShapeType();
        $this->FolderShape->BaseShape = "Default";

        if ($parentFolderId != NULL) {
            $this->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
            if ($parentDistinguished) {
                $this->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType($parentFolderId);
            }
            else {
                $this->ParentFolderIds->FolderId = new FolderIdType($parentFolderId);
            }
        }
    }


    //search for a folder by a certain field (e.g. DisplayName)
    public function setSearchField($field,$name) { 
        $this->Restriction = new RestrictionType();
        $this->Restriction->IsEqualTo->FieldURI->FieldURI = "folder:$field";
        $this->Restriction->IsEqualTo->FieldURIOrConstant->Constant->Value = $name;   
    }



}

class FolderInfoResponseMessageType extends Type {
  public $Folders; // ArrayOfFoldersType
}

class FindFolderResponseMessageType extends Type {
  public $RootFolder; // FindFolderParentType
}

class FindFolderResponseType extends Type {
}

class DeleteFolderType extends Type {
  public $FolderIds; // NonEmptyArrayOfBaseFolderIdsType
  public $DeleteType; // DisposalType
}

class DeleteFolderResponseType extends Type {
}

class BaseMoveCopyFolderType extends Type {
  public $ToFolderId; // TargetFolderIdType
  public $FolderIds; // NonEmptyArrayOfBaseFolderIdsType
}

class MoveFolderType extends Type {
}

class CopyFolderType extends Type {
}

class UpdateFolderType extends Type {
  public $FolderChanges; // NonEmptyArrayOfFolderChangesType
}

class CreateFolderResponseType extends Type {
}

class GetFolderResponseType extends Type {
}

class UpdateFolderResponseType extends Type {
}

class MoveFolderResponseType extends Type {
}

class CopyFolderResponseType extends Type {
}

class GetItemType extends Type {
    public $ItemShape; // ItemResponseShapeType
    public $ItemIds; // NonEmptyArrayOfBaseItemIdsType

    public function __construct($itemIds = array(), $shape = 'AllProperties') {
        $this->ItemShape = new ItemResponseShapeType();
        $this->ItemShape->BaseShape = $shape;

        $this->ItemIds = new NonEmptyArrayOfBaseItemIdsType();

        if (is_array($itemIds))
            $this->ItemIds->ItemId = $itemIds;
        elseif ($itemIds instanceOf ItemIdType)
            $this->ItemIds->ItemId = array($itemIds);

    }


}

class CreateItemType extends Type {
  public $SavedItemFolderId; // TargetFolderIdType
  public $Items; // NonEmptyArrayOfAllItemsType
  public $MessageDisposition; // MessageDispositionType
  public $SendMeetingInvitations; // CalendarItemCreateOrDeleteOperationType
}


class ItemInfoResponseMessageType extends Type {
  public $Items; // ArrayOfRealItemsType
}

class DeleteItemType extends Type {
  public $ItemIds; // NonEmptyArrayOfBaseItemIdsType
  public $DeleteType; // DisposalType
  public $SendMeetingCancellations; // CalendarItemCreateOrDeleteOperationType
  public $AffectedTaskOccurrences; // AffectedTaskOccurrencesType
}

class AttachmentInfoResponseMessageType extends Type {
  public $Attachments; // ArrayOfAttachmentsType
}

class DeleteAttachmentResponseMessageType extends Type {
  public $RootItemId; // RootItemIdType
}

class BaseMoveCopyItemType extends Type {
  public $ToFolderId; // TargetFolderIdType
  public $ItemIds; // NonEmptyArrayOfBaseItemIdsType
}

class MoveItemType extends Type {
}

class CopyItemType extends Type {
}

class SendItemType extends Type {
  public $ItemIds; // NonEmptyArrayOfBaseItemIdsType
  public $SavedItemFolderId; // TargetFolderIdType
  public $SaveItemToFolder; // boolean
}

class SendItemResponseType extends Type {
}

class FindItemType extends Type {
  public $ItemShape; // ItemResponseShapeType
  public $IndexedPageItemView; // IndexedPageViewType
  public $FractionalPageItemView; // FractionalPageViewType
  public $CalendarView; // CalendarViewType
  public $ContactsView; // ContactsViewType
  public $GroupBy; // GroupByType
  public $DistinguishedGroupBy; // DistinguishedGroupByType
  public $Restriction; // RestrictionType
  public $SortOrder; // NonEmptyArrayOfFieldOrdersType
  public $ParentFolderIds; // NonEmptyArrayOfBaseFolderIdsType
  public $Traversal; // ItemQueryTraversalType
}

class CreateAttachmentType extends Type {
  public $ParentItemId; // ItemIdType
  public $Attachments; // NonEmptyArrayOfAttachmentsType
}

class CreateAttachmentResponseType extends Type {
}

class DeleteAttachmentType extends Type {
  public $AttachmentIds; // NonEmptyArrayOfRequestAttachmentIdsType
}

class DeleteAttachmentResponseType extends Type {
}

class GetAttachmentType extends Type {
  public $AttachmentShape; // AttachmentResponseShapeType
  public $AttachmentIds; // NonEmptyArrayOfRequestAttachmentIdsType
}

class GetAttachmentResponseType extends Type {
}

class CreateItemResponseType extends Type {
}

class UpdateItemResponseType extends Type {
}

class GetItemResponseType extends Type {
}

class MoveItemResponseType extends Type {
}

class CopyItemResponseType extends Type {
}

class DeleteItemResponseType extends Type {
}

class FindItemResponseMessageType extends Type {
  public $RootFolder; // FindItemParentType
}

class FindItemResponseType extends Type {
}

class ResolveNamesType extends Type {
  public $UnresolvedEntry; // NonEmptyStringType
  public $ReturnFullContactData; // boolean
}

class ResolveNamesResponseMessageType extends Type {
  public $ResolutionSet; // ArrayOfResolutionType
}

class ResolveNamesResponseType extends Type {
}

class ExpandDLType extends Type {
  public $Mailbox; // EmailAddressType
}

class ExpandDLResponseMessageType extends Type {
  public $DLExpansion; // ArrayOfDLExpansionType
  public $IndexedPagingOffset; // int
  public $NumeratorOffset; // int
  public $AbsoluteDenominator; // int
  public $IncludesLastItemInRange; // boolean
  public $TotalItemsInView; // int
}

class ExpandDLResponseType extends Type {
}

class CreateManagedFolderRequestType extends Type {
  public $FolderNames; // NonEmptyArrayOfFolderNamesType
  public $Mailbox; // EmailAddressType
}

class CreateManagedFolderResponseType extends Type {
}

class SubscribeType extends Type {
  public $PullSubscriptionRequest; // PullSubscriptionRequestType
  public $PushSubscriptionRequest; // PushSubscriptionRequestType
}

class SubscribeResponseMessageType extends Type {
  public $SubscriptionId; // SubscriptionIdType
  public $Watermark; // WatermarkType
}

class SubscribeResponseType extends Type {
}

class UnsubscribeType extends Type {
  public $SubscriptionId; // SubscriptionIdType
}

class UnsubscribeResponseType extends Type {
}

class GetEventsType extends Type {
  public $SubscriptionId; // SubscriptionIdType
  public $Watermark; // WatermarkType
}

class GetEventsResponseMessageType extends Type {
  public $Notification; // NotificationType
}

class GetEventsResponseType extends Type {
}

class SendNotificationResponseMessageType extends Type {
  public $Notification; // NotificationType
}

class SendNotificationResponseType extends Type {
}

class SendNotificationResultType extends Type {
  public $SubscriptionStatus; // SubscriptionStatusType
}

class SyncFolderHierarchyType extends Type {
  public $FolderShape; // FolderResponseShapeType
  public $SyncState; // string
}

class SyncFolderHierarchyResponseMessageType extends Type {
  public $SyncState; // string
  public $IncludesLastFolderInRange; // boolean
  public $Changes; // SyncFolderHierarchyChangesType
}

class SyncFolderHierarchyResponseType extends Type {
}

class SyncFolderItemsType extends Type {
    public $ItemShape; // ItemResponseShapeType
    public $SyncFolderId; // TargetFolderIdType
    public $SyncState; // string
    public $Ignore; // ArrayOfBaseItemIdsType
    public $MaxChangesReturned; // MaxSyncChangesReturnedType

    public function __construct($parentFolderId = NULL,$parentDistinguished = FALSE,$syncState = '') {

        #Default values for the time being
        $this->ItemShape = new ItemResponseShapeType();
        $this->ItemShape->BaseShape = "IdOnly";
        $this->MaxChangesReturned = 512;
        $this->SyncState = $syncState;

        if ($parentFolderId != NULL) {
            $this->SyncFolderId = new TargetFolderIdType();
            if ($parentDistinguished) {
                $this->SyncFolderId->DistinguishedFolderId = new DistinguishedFolderIdType($parentFolderId);
            }
            else {
                $this->SyncFolderId->FolderId = new FolderIdType($parentFolderId);
            }
        }
    }
}

class SyncFolderItemsResponseMessageType extends Type {
  public $SyncState; // string
  public $IncludesLastItemInRange; // boolean
  public $Changes; // SyncFolderItemsChangesType
}

class SyncFolderItemsResponseType extends Type {
}

class GetUserAvailabilityRequestType extends Type {
  public $TimeZone; // SerializableTimeZone
  public $MailboxDataArray; // ArrayOfMailboxData
  public $FreeBusyViewOptions; // FreeBusyViewOptionsType
  public $SuggestionsViewOptions; // SuggestionsViewOptionsType
}

class FreeBusyResponseType extends Type {
  public $ResponseMessage; // ResponseMessageType
  public $FreeBusyView; // FreeBusyView
}

class SuggestionsResponseType extends Type {
  public $ResponseMessage; // ResponseMessageType
  public $SuggestionDayResultArray; // ArrayOfSuggestionDayResult
}

class GetUserAvailabilityResponseType extends Type {
  public $FreeBusyResponseArray; // ArrayOfFreeBusyResponse
  public $SuggestionsResponse; // SuggestionsResponseType
}

class GetUserOofSettingsRequest {
  public $Mailbox; // EmailAddress
}

class GetUserOofSettingsResponse {
  public $ResponseMessage; // ResponseMessageType
  public $OofSettings; // UserOofSettings
  public $AllowExternalOof; // ExternalAudience
}

class SetUserOofSettingsRequest {
  public $Mailbox; // EmailAddress
  public $UserOofSettings; // UserOofSettings
}

class SetUserOofSettingsResponse {
  public $ResponseMessage; // ResponseMessageType
}

?>
