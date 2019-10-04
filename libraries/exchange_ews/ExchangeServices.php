<?php

require_once("libraries/exchange_ews/ExchangeBaseTypes.php");
require_once("libraries/exchange_ews/ExchangeTypes.php");
require_once('libraries/exchange_ews/ntlmsoap.php');


/**
 * ExchangeServices class
 * 
 *  
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class ExchangeServices extends NTLMSoapClient {

  private static $classmap = array(
                                    'ArrayOfStringsType' => 'ArrayOfStringsType',
                                    'SidAndAttributesType' => 'SidAndAttributesType',
                                    'NonEmptyArrayOfGroupIdentifiersType' => 'NonEmptyArrayOfGroupIdentifiersType',
                                    'NonEmptyArrayOfRestrictedGroupIdentifiersType' => 'NonEmptyArrayOfRestrictedGroupIdentifiersType',
                                    'SerializedSecurityContextType' => 'SerializedSecurityContextType',
                                    'ConnectingSIDType' => 'ConnectingSIDType',
                                    'ExchangeImpersonationType' => 'ExchangeImpersonationType',
                                    'ServerVersionInfo' => 'ServerVersionInfo',
                                    'NonEmptyStringType' => 'NonEmptyStringType',
                                    'BaseEmailAddressType' => 'BaseEmailAddressType',
                                    'MailboxTypeType' => 'MailboxTypeType',
                                    'EmailAddressType' => 'EmailAddressType',
                                    'SingleRecipientType' => 'SingleRecipientType',
                                    'UnindexedFieldURIType' => 'UnindexedFieldURIType',
                                    'DictionaryURIType' => 'DictionaryURIType',
                                    'ExceptionPropertyURIType' => 'ExceptionPropertyURIType',
                                    'GuidType' => 'GuidType',
                                    'DistinguishedPropertySetType' => 'DistinguishedPropertySetType',
                                    'MapiPropertyTypeType' => 'MapiPropertyTypeType',
                                    'BasePathToElementType' => 'BasePathToElementType',
                                    'PathToUnindexedFieldType' => 'PathToUnindexedFieldType',
                                    'PathToIndexedFieldType' => 'PathToIndexedFieldType',
                                    'PathToExceptionFieldType' => 'PathToExceptionFieldType',
                                    'PropertyTagType' => 'PropertyTagType',
                                    'anonymous24' => 'anonymous24',
                                    'PathToExtendedFieldType' => 'PathToExtendedFieldType',
                                    'NonEmptyArrayOfPathsToElementType' => 'NonEmptyArrayOfPathsToElementType',
                                    'NonEmptyArrayOfPropertyValuesType' => 'NonEmptyArrayOfPropertyValuesType',
                                    'ExtendedPropertyType' => 'ExtendedPropertyType',
                                    'FolderQueryTraversalType' => 'FolderQueryTraversalType',
                                    'SearchFolderTraversalType' => 'SearchFolderTraversalType',
                                    'ItemQueryTraversalType' => 'ItemQueryTraversalType',
                                    'DefaultShapeNamesType' => 'DefaultShapeNamesType',
                                    'BodyTypeResponseType' => 'BodyTypeResponseType',
                                    'FolderResponseShapeType' => 'FolderResponseShapeType',
                                    'ItemResponseShapeType' => 'ItemResponseShapeType',
                                    'AttachmentResponseShapeType' => 'AttachmentResponseShapeType',
                                    'DisposalType' => 'DisposalType',
                                    'ConflictResolutionType' => 'ConflictResolutionType',
                                    'ResponseClassType' => 'ResponseClassType',
                                    'ChangeDescriptionType' => 'ChangeDescriptionType',
                                    'ItemChangeDescriptionType' => 'ItemChangeDescriptionType',
                                    'FolderChangeDescriptionType' => 'FolderChangeDescriptionType',
                                    'SetItemFieldType' => 'SetItemFieldType',
                                    'SetFolderFieldType' => 'SetFolderFieldType',
                                    'DeleteItemFieldType' => 'DeleteItemFieldType',
                                    'DeleteFolderFieldType' => 'DeleteFolderFieldType',
                                    'AppendToItemFieldType' => 'AppendToItemFieldType',
                                    'AppendToFolderFieldType' => 'AppendToFolderFieldType',
                                    'NonEmptyArrayOfItemChangeDescriptionsType' => 'NonEmptyArrayOfItemChangeDescriptionsType',
                                    'NonEmptyArrayOfFolderChangeDescriptionsType' => 'NonEmptyArrayOfFolderChangeDescriptionsType',
                                    'ItemChangeType' => 'ItemChangeType',
                                    'NonEmptyArrayOfItemChangesType' => 'NonEmptyArrayOfItemChangesType',
                                    'InternetHeaderType' => 'InternetHeaderType',
                                    'NonEmptyArrayOfInternetHeadersType' => 'NonEmptyArrayOfInternetHeadersType',
                                    'RequestAttachmentIdType' => 'RequestAttachmentIdType',
                                    'AttachmentIdType' => 'AttachmentIdType',
                                    'RootItemIdType' => 'RootItemIdType',
                                    'NonEmptyArrayOfRequestAttachmentIdsType' => 'NonEmptyArrayOfRequestAttachmentIdsType',
                                    'AttachmentType' => 'AttachmentType',
                                    'ItemAttachmentType' => 'ItemAttachmentType',
                                    'SyncFolderItemsCreateOrUpdateType' => 'SyncFolderItemsCreateOrUpdateType',
                                    'FileAttachmentType' => 'FileAttachmentType',
                                    'NonEmptyArrayOfAttachmentsType' => 'NonEmptyArrayOfAttachmentsType',
                                    'SensitivityChoicesType' => 'SensitivityChoicesType',
                                    'ImportanceChoicesType' => 'ImportanceChoicesType',
                                    'BodyTypeType' => 'BodyTypeType',
                                    'BodyType' => 'BodyType',
                                    'BaseFolderIdType' => 'BaseFolderIdType',
                                    'FolderClassType' => 'FolderClassType',
                                    'DistinguishedFolderIdNameType' => 'DistinguishedFolderIdNameType',
                                    'DistinguishedFolderIdType' => 'DistinguishedFolderIdType',
                                    'FolderIdType' => 'FolderIdType',
                                    'NonEmptyArrayOfBaseFolderIdsType' => 'NonEmptyArrayOfBaseFolderIdsType',
                                    'TargetFolderIdType' => 'TargetFolderIdType',
                                    'FindFolderParentType' => 'FindFolderParentType',
                                    'BaseFolderType' => 'BaseFolderType',
                                    'ManagedFolderInformationType' => 'ManagedFolderInformationType',
                                    'FolderType' => 'FolderType',
                                    'CalendarFolderType' => 'CalendarFolderType',
                                    'ContactsFolderType' => 'ContactsFolderType',
                                    'SearchFolderType' => 'SearchFolderType',
                                    'TasksFolderType' => 'TasksFolderType',
                                    'NonEmptyArrayOfFoldersType' => 'NonEmptyArrayOfFoldersType',
                                    'BaseItemIdType' => 'BaseItemIdType',
                                    'DerivedItemIdType' => 'DerivedItemIdType',
                                    'ItemIdType' => 'ItemIdType',
                                    'NonEmptyArrayOfBaseItemIdsType' => 'NonEmptyArrayOfBaseItemIdsType',
                                    'ItemClassType' => 'ItemClassType',
                                    'ResponseObjectCoreType' => 'ResponseObjectCoreType',
                                    'ResponseObjectType' => 'ResponseObjectType',
                                    'NonEmptyArrayOfResponseObjectsType' => 'NonEmptyArrayOfResponseObjectsType',
                                    'FolderChangeType' => 'FolderChangeType',
                                    'NonEmptyArrayOfFolderChangesType' => 'NonEmptyArrayOfFolderChangesType',
                                    'WellKnownResponseObjectType' => 'WellKnownResponseObjectType',
                                    'SmartResponseBaseType' => 'SmartResponseBaseType',
                                    'SmartResponseType' => 'SmartResponseType',
                                    'ReplyToItemType' => 'ReplyToItemType',
                                    'ReplyAllToItemType' => 'ReplyAllToItemType',
                                    'ForwardItemType' => 'ForwardItemType',
                                    'CancelCalendarItemType' => 'CancelCalendarItemType',
                                    'ReferenceItemResponseType' => 'ReferenceItemResponseType',
                                    'SuppressReadReceiptType' => 'SuppressReadReceiptType',
                                    'FindItemParentType' => 'FindItemParentType',
                                    'ItemType' => 'ItemType',
                                    'NonEmptyArrayOfAllItemsType' => 'NonEmptyArrayOfAllItemsType',
                                    'AcceptItemType' => 'AcceptItemType',
                                    'TentativelyAcceptItemType' => 'TentativelyAcceptItemType',
                                    'DeclineItemType' => 'DeclineItemType',
                                    'RemoveItemType' => 'RemoveItemType',
                                    'MimeContentType' => 'MimeContentType',
                                    'MessageDispositionType' => 'MessageDispositionType',
                                    'CalendarItemCreateOrDeleteOperationType' => 'CalendarItemCreateOrDeleteOperationType',
                                    'CalendarItemUpdateOperationType' => 'CalendarItemUpdateOperationType',
                                    'AffectedTaskOccurrencesType' => 'AffectedTaskOccurrencesType',
                                    'MessageType' => 'MessageType',
                                    'TaskStatusType' => 'TaskStatusType',
                                    'TaskDelegateStateType' => 'TaskDelegateStateType',
                                    'TaskType' => 'TaskType',
                                    'BasePagingType' => 'BasePagingType',
                                    'IndexBasePointType' => 'IndexBasePointType',
                                    'IndexedPageViewType' => 'IndexedPageViewType',
                                    'FractionalPageViewType' => 'FractionalPageViewType',
                                    'CalendarViewType' => 'CalendarViewType',
                                    'ContactsViewType' => 'ContactsViewType',
                                    'ResolutionType' => 'ResolutionType',
                                    'MeetingRequestTypeType' => 'MeetingRequestTypeType',
                                    'ReminderMinutesBeforeStartType' => 'ReminderMinutesBeforeStartType',
                                    'anonymous135' => 'anonymous135',
                                    'anonymous136' => 'anonymous136',
                                    'LegacyFreeBusyType' => 'LegacyFreeBusyType',
                                    'CalendarItemTypeType' => 'CalendarItemTypeType',
                                    'ResponseTypeType' => 'ResponseTypeType',
                                    'AttendeeType' => 'AttendeeType',
                                    'NonEmptyArrayOfAttendeesType' => 'NonEmptyArrayOfAttendeesType',
                                    'OccurrenceItemIdType' => 'OccurrenceItemIdType',
                                    'RecurringMasterItemIdType' => 'RecurringMasterItemIdType',
                                    'DayOfWeekType' => 'DayOfWeekType',
                                    'DaysOfWeekType' => 'DaysOfWeekType',
                                    'DayOfWeekIndexType' => 'DayOfWeekIndexType',
                                    'MonthNamesType' => 'MonthNamesType',
                                    'RecurrencePatternBaseType' => 'RecurrencePatternBaseType',
                                    'IntervalRecurrencePatternBaseType' => 'IntervalRecurrencePatternBaseType',
                                    'RegeneratingPatternBaseType' => 'RegeneratingPatternBaseType',
                                    'DailyRegeneratingPatternType' => 'DailyRegeneratingPatternType',
                                    'WeeklyRegeneratingPatternType' => 'WeeklyRegeneratingPatternType',
                                    'MonthlyRegeneratingPatternType' => 'MonthlyRegeneratingPatternType',
                                    'YearlyRegeneratingPatternType' => 'YearlyRegeneratingPatternType',
                                    'RelativeYearlyRecurrencePatternType' => 'RelativeYearlyRecurrencePatternType',
                                    'AbsoluteYearlyRecurrencePatternType' => 'AbsoluteYearlyRecurrencePatternType',
                                    'RelativeMonthlyRecurrencePatternType' => 'RelativeMonthlyRecurrencePatternType',
                                    'AbsoluteMonthlyRecurrencePatternType' => 'AbsoluteMonthlyRecurrencePatternType',
                                    'WeeklyRecurrencePatternType' => 'WeeklyRecurrencePatternType',
                                    'DailyRecurrencePatternType' => 'DailyRecurrencePatternType',
                                    'TimeChangeType' => 'TimeChangeType',
                                    'TimeZoneType' => 'TimeZoneType',
                                    'RecurrenceRangeBaseType' => 'RecurrenceRangeBaseType',
                                    'NoEndRecurrenceRangeType' => 'NoEndRecurrenceRangeType',
                                    'EndDateRecurrenceRangeType' => 'EndDateRecurrenceRangeType',
                                    'NumberedRecurrenceRangeType' => 'NumberedRecurrenceRangeType',
                                    'RecurrenceType' => 'RecurrenceType',
                                    'TaskRecurrenceType' => 'TaskRecurrenceType',
                                    'OccurrenceInfoType' => 'OccurrenceInfoType',
                                    'NonEmptyArrayOfOccurrenceInfoType' => 'NonEmptyArrayOfOccurrenceInfoType',
                                    'DeletedOccurrenceInfoType' => 'DeletedOccurrenceInfoType',
                                    'NonEmptyArrayOfDeletedOccurrencesType' => 'NonEmptyArrayOfDeletedOccurrencesType',
                                    'CalendarItemType' => 'CalendarItemType',
                                    'MeetingMessageType' => 'MeetingMessageType',
                                    'MeetingRequestMessageType' => 'MeetingRequestMessageType',
                                    'MeetingResponseMessageType' => 'MeetingResponseMessageType',
                                    'MeetingCancellationMessageType' => 'MeetingCancellationMessageType',
                                    'ImAddressKeyType' => 'ImAddressKeyType',
                                    'EmailAddressKeyType' => 'EmailAddressKeyType',
                                    'PhoneNumberKeyType' => 'PhoneNumberKeyType',
                                    'PhysicalAddressIndexType' => 'PhysicalAddressIndexType',
                                    'PhysicalAddressKeyType' => 'PhysicalAddressKeyType',
                                    'FileAsMappingType' => 'FileAsMappingType',
                                    'ContactSourceType' => 'ContactSourceType',
                                    'CompleteNameType' => 'CompleteNameType',
                                    'ImAddressDictionaryEntryType' => 'ImAddressDictionaryEntryType',
                                    'EmailAddressDictionaryEntryType' => 'EmailAddressDictionaryEntryType',
                                    'PhoneNumberDictionaryEntryType' => 'PhoneNumberDictionaryEntryType',
                                    'PhysicalAddressDictionaryEntryType' => 'PhysicalAddressDictionaryEntryType',
                                    'ImAddressDictionaryType' => 'ImAddressDictionaryType',
                                    'EmailAddressDictionaryType' => 'EmailAddressDictionaryType',
                                    'PhoneNumberDictionaryType' => 'PhoneNumberDictionaryType',
                                    'PhysicalAddressDictionaryType' => 'PhysicalAddressDictionaryType',
                                    'ContactItemType' => 'ContactItemType',
                                    'DistributionListType' => 'DistributionListType',
                                    'SearchParametersType' => 'SearchParametersType',
                                    'ConstantValueType' => 'ConstantValueType',
                                    'SearchExpressionType' => 'SearchExpressionType',
                                    'AggregateType' => 'AggregateType',
                                    'AggregateOnType' => 'AggregateOnType',
                                    'BaseGroupByType' => 'BaseGroupByType',
                                    'GroupByType' => 'GroupByType',
                                    'StandardGroupByType' => 'StandardGroupByType',
                                    'DistinguishedGroupByType' => 'DistinguishedGroupByType',
                                    'GroupedItemsType' => 'GroupedItemsType',
                                    'ExistsType' => 'ExistsType',
                                    'FieldURIOrConstantType' => 'FieldURIOrConstantType',
                                    'TwoOperandExpressionType' => 'TwoOperandExpressionType',
                                    'ExcludesAttributeType' => 'ExcludesAttributeType',
                                    'ExcludesValueType' => 'ExcludesValueType',
                                    'ExcludesType' => 'ExcludesType',
                                    'IsEqualToType' => 'IsEqualToType',
                                    'IsNotEqualToType' => 'IsNotEqualToType',
                                    'IsGreaterThanType' => 'IsGreaterThanType',
                                    'IsGreaterThanOrEqualToType' => 'IsGreaterThanOrEqualToType',
                                    'IsLessThanType' => 'IsLessThanType',
                                    'IsLessThanOrEqualToType' => 'IsLessThanOrEqualToType',
                                    'ContainmentModeType' => 'ContainmentModeType',
                                    'ContainmentComparisonType' => 'ContainmentComparisonType',
                                    'ContainsExpressionType' => 'ContainsExpressionType',
                                    'NotType' => 'NotType',
                                    'MultipleOperandBooleanExpressionType' => 'MultipleOperandBooleanExpressionType',
                                    'AndType' => 'AndType',
                                    'OrType' => 'OrType',
                                    'RestrictionType' => 'RestrictionType',
                                    'SortDirectionType' => 'SortDirectionType',
                                    'FieldOrderType' => 'FieldOrderType',
                                    'NonEmptyArrayOfFieldOrdersType' => 'NonEmptyArrayOfFieldOrdersType',
                                    'NonEmptyArrayOfFolderNamesType' => 'NonEmptyArrayOfFolderNamesType',
                                    'WatermarkType' => 'WatermarkType',
                                    'SubscriptionIdType' => 'SubscriptionIdType',
                                    'BaseNotificationEventType' => 'BaseNotificationEventType',
                                    'BaseObjectChangedEventType' => 'BaseObjectChangedEventType',
                                    'ModifiedEventType' => 'ModifiedEventType',
                                    'MovedCopiedEventType' => 'MovedCopiedEventType',
                                    'NotificationType' => 'NotificationType',
                                    'NotificationEventTypeType' => 'NotificationEventTypeType',
                                    'NonEmptyArrayOfNotificationEventTypesType' => 'NonEmptyArrayOfNotificationEventTypesType',
                                    'SubscriptionTimeoutType' => 'SubscriptionTimeoutType',
                                    'SubscriptionStatusFrequencyType' => 'SubscriptionStatusFrequencyType',
                                    'BaseSubscriptionRequestType' => 'BaseSubscriptionRequestType',
                                    'PushSubscriptionRequestType' => 'PushSubscriptionRequestType',
                                    'PullSubscriptionRequestType' => 'PullSubscriptionRequestType',
                                    'SubscriptionStatusType' => 'SubscriptionStatusType',
                                    'SyncFolderItemsDeleteType' => 'SyncFolderItemsDeleteType',
                                    'SyncFolderItemsChangesType' => 'SyncFolderItemsChangesType',
                                    'SyncFolderHierarchyCreateOrUpdateType' => 'SyncFolderHierarchyCreateOrUpdateType',
                                    'SyncFolderHierarchyDeleteType' => 'SyncFolderHierarchyDeleteType',
                                    'SyncFolderHierarchyChangesType' => 'SyncFolderHierarchyChangesType',
                                    'MaxSyncChangesReturnedType' => 'MaxSyncChangesReturnedType',
                                    'AvailabilityProxyRequestType' => 'AvailabilityProxyRequestType',
                                    'MeetingAttendeeType' => 'MeetingAttendeeType',
                                    'CalendarEventDetails' => 'CalendarEventDetails',
                                    'CalendarEvent' => 'CalendarEvent',
                                    'Duration' => 'Duration',
                                    'EmailAddress' => 'EmailAddress',
                                    'FreeBusyViewType' => 'FreeBusyViewType',
                                    'anonymous260' => 'anonymous260',
                                    'FreeBusyViewOptionsType' => 'FreeBusyViewOptionsType',
                                    'WorkingPeriod' => 'WorkingPeriod',
                                    'SerializableTimeZoneTime' => 'SerializableTimeZoneTime',
                                    'SerializableTimeZone' => 'SerializableTimeZone',
                                    'WorkingHours' => 'WorkingHours',
                                    'FreeBusyView' => 'FreeBusyView',
                                    'MailboxData' => 'MailboxData',
                                    'SuggestionQuality' => 'SuggestionQuality',
                                    'SuggestionsViewOptionsType' => 'SuggestionsViewOptionsType',
                                    'AttendeeConflictData' => 'AttendeeConflictData',
                                    'UnknownAttendeeConflictData' => 'UnknownAttendeeConflictData',
                                    'TooBigGroupAttendeeConflictData' => 'TooBigGroupAttendeeConflictData',
                                    'IndividualAttendeeConflictData' => 'IndividualAttendeeConflictData',
                                    'GroupAttendeeConflictData' => 'GroupAttendeeConflictData',
                                    'Suggestion' => 'Suggestion',
                                    'SuggestionDayResult' => 'SuggestionDayResult',
                                    'OofState' => 'OofState',
                                    'ExternalAudience' => 'ExternalAudience',
                                    'ReplyBody' => 'ReplyBody',
                                    'UserOofSettings' => 'UserOofSettings',
                                    'Value' => 'Value',
                                    'ResponseCodeType' => 'ResponseCodeType',
                                    'ResponseMessageType' => 'ResponseMessageType',
                                    'MessageXml' => 'MessageXml',
                                    'BaseResponseMessageType' => 'BaseResponseMessageType',
                                    'BaseRequestType' => 'BaseRequestType',
                                    'GetFolderType' => 'GetFolderType',
                                    'CreateFolderType' => 'CreateFolderType',
                                    'FindFolderType' => 'FindFolderType',
                                    'FolderInfoResponseMessageType' => 'FolderInfoResponseMessageType',
                                    'FindFolderResponseMessageType' => 'FindFolderResponseMessageType',
                                    'FindFolderResponseType' => 'FindFolderResponseType',
                                    'DeleteFolderType' => 'DeleteFolderType',
                                    'DeleteFolderResponseType' => 'DeleteFolderResponseType',
                                    'BaseMoveCopyFolderType' => 'BaseMoveCopyFolderType',
                                    'MoveFolderType' => 'MoveFolderType',
                                    'CopyFolderType' => 'CopyFolderType',
                                    'UpdateFolderType' => 'UpdateFolderType',
                                    'CreateFolderResponseType' => 'CreateFolderResponseType',
                                    'GetFolderResponseType' => 'GetFolderResponseType',
                                    'UpdateFolderResponseType' => 'UpdateFolderResponseType',
                                    'MoveFolderResponseType' => 'MoveFolderResponseType',
                                    'CopyFolderResponseType' => 'CopyFolderResponseType',
                                    'GetItemType' => 'GetItemType',
                                    'CreateItemType' => 'CreateItemType',
                                    'UpdateItemType' => 'UpdateItemType',
                                    'ItemInfoResponseMessageType' => 'ItemInfoResponseMessageType',
                                    'DeleteItemType' => 'DeleteItemType',
                                    'AttachmentInfoResponseMessageType' => 'AttachmentInfoResponseMessageType',
                                    'DeleteAttachmentResponseMessageType' => 'DeleteAttachmentResponseMessageType',
                                    'BaseMoveCopyItemType' => 'BaseMoveCopyItemType',
                                    'MoveItemType' => 'MoveItemType',
                                    'CopyItemType' => 'CopyItemType',
                                    'SendItemType' => 'SendItemType',
                                    'SendItemResponseType' => 'SendItemResponseType',
                                    'FindItemType' => 'FindItemType',
                                    'CreateAttachmentType' => 'CreateAttachmentType',
                                    'CreateAttachmentResponseType' => 'CreateAttachmentResponseType',
                                    'DeleteAttachmentType' => 'DeleteAttachmentType',
                                    'DeleteAttachmentResponseType' => 'DeleteAttachmentResponseType',
                                    'GetAttachmentType' => 'GetAttachmentType',
                                    'GetAttachmentResponseType' => 'GetAttachmentResponseType',
                                    'CreateItemResponseType' => 'CreateItemResponseType',
                                    'UpdateItemResponseType' => 'UpdateItemResponseType',
                                    'GetItemResponseType' => 'GetItemResponseType',
                                    'MoveItemResponseType' => 'MoveItemResponseType',
                                    'CopyItemResponseType' => 'CopyItemResponseType',
                                    'DeleteItemResponseType' => 'DeleteItemResponseType',
                                    'FindItemResponseMessageType' => 'FindItemResponseMessageType',
                                    'FindItemResponseType' => 'FindItemResponseType',
                                    'ResolveNamesType' => 'ResolveNamesType',
                                    'ResolveNamesResponseMessageType' => 'ResolveNamesResponseMessageType',
                                    'ResolveNamesResponseType' => 'ResolveNamesResponseType',
                                    'ExpandDLType' => 'ExpandDLType',
                                    'ExpandDLResponseMessageType' => 'ExpandDLResponseMessageType',
                                    'ExpandDLResponseType' => 'ExpandDLResponseType',
                                    'CreateManagedFolderRequestType' => 'CreateManagedFolderRequestType',
                                    'CreateManagedFolderResponseType' => 'CreateManagedFolderResponseType',
                                    'SubscribeType' => 'SubscribeType',
                                    'SubscribeResponseMessageType' => 'SubscribeResponseMessageType',
                                    'SubscribeResponseType' => 'SubscribeResponseType',
                                    'UnsubscribeType' => 'UnsubscribeType',
                                    'UnsubscribeResponseType' => 'UnsubscribeResponseType',
                                    'GetEventsType' => 'GetEventsType',
                                    'GetEventsResponseMessageType' => 'GetEventsResponseMessageType',
                                    'GetEventsResponseType' => 'GetEventsResponseType',
                                    'SendNotificationResponseMessageType' => 'SendNotificationResponseMessageType',
                                    'SendNotificationResponseType' => 'SendNotificationResponseType',
                                    'SendNotificationResultType' => 'SendNotificationResultType',
                                    'SyncFolderHierarchyType' => 'SyncFolderHierarchyType',
                                    'SyncFolderHierarchyResponseMessageType' => 'SyncFolderHierarchyResponseMessageType',
                                    'SyncFolderHierarchyResponseType' => 'SyncFolderHierarchyResponseType',
                                    'SyncFolderItemsType' => 'SyncFolderItemsType',
                                    'SyncFolderItemsResponseMessageType' => 'SyncFolderItemsResponseMessageType',
                                    'SyncFolderItemsResponseType' => 'SyncFolderItemsResponseType',
                                    'GetUserAvailabilityRequestType' => 'GetUserAvailabilityRequestType',
                                    'FreeBusyResponseType' => 'FreeBusyResponseType',
                                    'SuggestionsResponseType' => 'SuggestionsResponseType',
                                    'GetUserAvailabilityResponseType' => 'GetUserAvailabilityResponseType',
                                    'GetUserOofSettingsRequest' => 'GetUserOofSettingsRequest',
                                    'GetUserOofSettingsResponse' => 'GetUserOofSettingsResponse',
                                    'SetUserOofSettingsRequest' => 'SetUserOofSettingsRequest',
                                    'SetUserOofSettingsResponse' => 'SetUserOofSettingsResponse',
                                   );

  public function ExchangeServices($wsdl = "libraries/exchange_ews/wsdl/Services.wsdl", $options = array(),$ntlm_user,$ntlm_pass) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options,$ntlm_user,$ntlm_pass);
  }

  /**
   *  
   *
   * @param ResolveNamesType $request
   * @return ResolveNamesResponseType
   */
  public function ResolveNames(ResolveNamesType $request) {
    return $this->__soapCall('ResolveNames', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param ExpandDLType $request
   * @return ExpandDLResponseType
   */
  public function ExpandDL(ExpandDLType $request) {
    return $this->__soapCall('ExpandDL', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param FindFolderType $request
   * @return FindFolderResponseType
   */
  public function FindFolder(FindFolderType $request) {
    return $this->__soapCall('FindFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param FindItemType $request
   * @return FindItemResponseType
   */
  public function FindItem(FindItemType $request) {
    return $this->__soapCall('FindItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GetFolderType $request
   * @return GetFolderResponseType
   */
  public function GetFolder(GetFolderType $request) {
    return $this->__soapCall('GetFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param CreateFolderType $request
   * @return CreateFolderResponseType
   */
  public function CreateFolder(CreateFolderType $request) {
    return $this->__soapCall('CreateFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param DeleteFolderType $request
   * @return DeleteFolderResponseType
   */
  public function DeleteFolder(DeleteFolderType $request) {
    return $this->__soapCall('DeleteFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param UpdateFolderType $request
   * @return UpdateFolderResponseType
   */
  public function UpdateFolder(UpdateFolderType $request) {
    return $this->__soapCall('UpdateFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param MoveFolderType $request
   * @return MoveFolderResponseType
   */
  public function MoveFolder(MoveFolderType $request) {
    return $this->__soapCall('MoveFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param CopyFolderType $request
   * @return CopyFolderResponseType
   */
  public function CopyFolder(CopyFolderType $request) {
    return $this->__soapCall('CopyFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SubscribeType $request
   * @return SubscribeResponseType
   */
  public function Subscribe(SubscribeType $request) {
    return $this->__soapCall('Subscribe', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param UnsubscribeType $request
   * @return UnsubscribeResponseType
   */
  public function Unsubscribe(UnsubscribeType $request) {
    return $this->__soapCall('Unsubscribe', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GetEventsType $request
   * @return GetEventsResponseType
   */
  public function GetEvents(GetEventsType $request) {
    return $this->__soapCall('GetEvents', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SyncFolderHierarchyType $request
   * @return SyncFolderHierarchyResponseType
   */
  public function SyncFolderHierarchy(SyncFolderHierarchyType $request) {
    return $this->__soapCall('SyncFolderHierarchy', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SyncFolderItemsType $request
   * @return SyncFolderItemsResponseType
   */
  public function SyncFolderItems(SyncFolderItemsType $request) {
    return $this->__soapCall('SyncFolderItems', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GetItemType $request
   * @return GetItemResponseType
   */
  public function GetItem(GetItemType $request) {
    return $this->__soapCall('GetItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param CreateItemType $request
   * @return CreateItemResponseType
   */
  public function CreateItem(CreateItemType $request) {
    return $this->__soapCall('CreateItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param DeleteItemType $request
   * @return DeleteItemResponseType
   */
  public function DeleteItem(DeleteItemType $request) {
    return $this->__soapCall('DeleteItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param UpdateItemType $request
   * @return UpdateItemResponseType
   */
  public function UpdateItem(UpdateItemType $request) {
    return $this->__soapCall('UpdateItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SendItemType $request
   * @return SendItemResponseType
   */
  public function SendItem(SendItemType $request) {
    return $this->__soapCall('SendItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param MoveItemType $request
   * @return MoveItemResponseType
   */
  public function MoveItem(MoveItemType $request) {
    return $this->__soapCall('MoveItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param CopyItemType $request
   * @return CopyItemResponseType
   */
  public function CopyItem(CopyItemType $request) {
    return $this->__soapCall('CopyItem', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param CreateAttachmentType $request
   * @return CreateAttachmentResponseType
   */
  public function CreateAttachment(CreateAttachmentType $request) {
    return $this->__soapCall('CreateAttachment', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param DeleteAttachmentType $request
   * @return DeleteAttachmentResponseType
   */
  public function DeleteAttachment(DeleteAttachmentType $request) {
    return $this->__soapCall('DeleteAttachment', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GetAttachmentType $request
   * @return GetAttachmentResponseType
   */
  public function GetAttachment(GetAttachmentType $request) {
    return $this->__soapCall('GetAttachment', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param CreateManagedFolderRequestType $request
   * @return CreateManagedFolderResponseType
   */
  public function CreateManagedFolder(CreateManagedFolderRequestType $request) {
    return $this->__soapCall('CreateManagedFolder', array($request),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GetUserAvailabilityRequestType $GetUserAvailabilityRequest
   * @return GetUserAvailabilityResponseType
   */
  public function GetUserAvailability(GetUserAvailabilityRequestType $GetUserAvailabilityRequest) {
    return $this->__soapCall('GetUserAvailability', array($GetUserAvailabilityRequest),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GetUserOofSettingsRequest $GetUserOofSettingsRequest
   * @return GetUserOofSettingsResponse
   */
  public function GetUserOofSettings(GetUserOofSettingsRequest $GetUserOofSettingsRequest) {
    return $this->__soapCall('GetUserOofSettings', array($GetUserOofSettingsRequest),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SetUserOofSettingsRequest $SetUserOofSettingsRequest
   * @return SetUserOofSettingsResponse
   */
  public function SetUserOofSettings(SetUserOofSettingsRequest $SetUserOofSettingsRequest) {
    return $this->__soapCall('SetUserOofSettings', array($SetUserOofSettingsRequest),       array(
            'uri' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
            'soapaction' => ''
           )
      );
  }

    public function _setImpersonation($username)
    {
        $sv = new SoapVar("<ExchangeImpersonation  xmlns=\"http://schemas.microsoft.com/exchange/services/2006/types\" ><ConnectingSID><PrincipalName>$username</PrincipalName></ConnectingSID></ExchangeImpersonation>",XSD_ANYXML);
        $impHeader = new SoapHeader('http://schemas.microsoft.com/exchange/services/2006/types','ExchangeImpersonation',$sv);
        $this->__setSOapHeaders();
        $this->__setSoapHeaders(array($impHeader));
    }


    public function _getFolderIdByName($folderName,$parentId,$parentIsDistinguished = TRUE)
    {

        $find = new FindFolderType($parentId,$parentIsDistinguished);
        $find->setSearchField("DisplayName",$folderName);


        #$result = $this->FindFolder(new SoapVar($reqXML,XSD_ANYXML));
        $result = $this->FindFolder($find);
        //print_r($this->__getLastRequest());
        //print_r($result);
        if ($result && $result->ResponseMessages->FindFolderResponseMessage[0]->ResponseClass == 'Success')
        {
            $folder = $result->ResponseMessages->FindFolderResponseMessage[0]->RootFolder->Folders;
            foreach(array('Folder','CalendarFolder','ContactsFolder','SearchFolder','TasksFolder') as $k) {
                if ($folder->$k && $folder->{$k}[0]->FolderId)
                    return $folder->{$k}[0]->FolderId->Id;
            }
        }
    }

    public function _getFolderId($folderId)
    {

        $getFolder = new GetFolderType();

        $getFolder->FolderShape->BaseShape = 'Default';
        $getFolder->FolderIds->DistinguishedFolderId = new DistinguishedFolderIdType($folderId);

        #$result = $this->FindFolder(new SoapVar($reqXML,XSD_ANYXML));
        $result = $this->GetFolder($getFolder);
        //print_r($this->__getLastRequest());
        //print_r($result);
        if ($result && $result->ResponseMessages->GetFolderResponseMessage[0]->ResponseClass == 'Success')
        {
            $folder = $result->ResponseMessages->GetFolderResponseMessage[0]->Folders;
            foreach(array('Folder','CalendarFolder','ContactsFolder','SearchFolder','TasksFolder') as $k) {
                if ($folder->$k && $folder->{$k}[0]->FolderId)
                    return $folder->{$k}[0]->FolderId->Id;
            }
        }
    }

    public function _syncFolderItems($folderId,$folderIsDistinguished,$syncState = '')
    {

        $result = $this->SyncFolderItems(new SyncFolderItemsType($folderId,$folderIsDistinguished,$syncState));
        //print_r($this->__getLastRequest());
        //print_r($this->__getLastResponse());
        return $result;
    }

}

?>
