<?php

namespace garethp\ews;

use garethp\ews\API\Message\FreeBusyResponseType;
use garethp\ews\API\Type\CalendarItemType;
use garethp\ews\API\Type;
use garethp\ews\API\Enumeration;
use DateTime;

/**
 * An API end point for Calendar items
 *
 * Class API
 * @package garethp\ews\Calendar
 */
class CalendarAPI extends API
{
    /**
     * @var Type\FolderIdType
     */
    protected $folderId;

    /**
     * Pick a Calendar based on it's name
     *
     * @param string|null $displayName
     * @return $this
     */
    public function pickCalendar($displayName = null)
    {
        if ($displayName == 'default.calendar' || $displayName == null) {
            $folder = $this->getFolderByDistinguishedId('calendar');
        } else {
            $folder = $this->getFolderByDisplayName($displayName, 'calendar');
        }

        $this->folderId = $folder->getFolderId();

        return $this;
    }

    /**
     * @return Type\FolderIdType
     */
    public function getFolderId()
    {
        if ($this->folderId === null) {
            $this->pickCalendar();
        }

        return $this->folderId;
    }

    /**
     * @param Type\FolderIdType $folderId
     * @return $this
     */
    public function setFolderId($folderId)
    {
        $this->folderId = $folderId;

        return $this;
    }

    /**
     * Create one or more calendar items
     *
     * @param $items CalendarItemType[]|CalendarItemType|array or more calendar items to create
     * @param $options array Options to merge in to the request
     * @return Type\ItemIdType[]
     */
    public function createCalendarItems($items, $options = array())
    {
        $items = Utilities\ensureIsArray($items, true);
        $item = array('CalendarItem' => $items);
        $defaultOptions = array(
            'SendMeetingInvitations' => Enumeration\CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE,
            'SavedItemFolderId' => array(
                'FolderId' => $this->getFolderId()->toXmlObject()
            )
        );

        $options = array_replace_recursive($defaultOptions, $options);

        $items = $this->createItems($item, $options);
        return Utilities\ensureIsArray($items);
    }

    /**
     * Get a list of calendar items between two dates/times
     *
     * @param string|DateTime $start
     * @param string|DateTime $end
     * @param array $options
     * @return CalendarItemType[]|Type\FindItemParentType
     */
    public function getCalendarItems($start = '12:00 AM', $end = '11:59 PM', $options = array())
    {
        error_reporting(E_ALL & ~E_WARNING);
        ini_set("display_errors", 1);
        
        $start = Utilities\ensureIsDateTime($start);
        $end = Utilities\ensureIsDateTime($end);

        $lastUpdatedTime = "2016-01-01T00:00:00Z";
        
        $request = [
            'Traversal' => 'Shallow',
            'ItemShape' => [
                'BaseShape' => 'AllProperties'
            ],
            'Restriction' => [
                'Contains' => array(
                    'FieldURI' => array('FieldURI' => API\FieldURIManager::getFieldUriByName('uid', 'calendar')),
                    'Constant' => array('Value' => '040000008200E00074C5B7101A82E008000000009C969A1572E6D20100000000000000001000000037AC45EC31B80147A0183CD4808DBFE6')
                ),
                /*'And' => [
                
                /*'IsEqualTo' => array(
                    'FieldURI' => array('FieldURI' => API\FieldURIManager::getFieldUriByName('uid', 'calendar')),
                    'FieldURIOrConstant' => array('Constant' => array('Value' => '040000008200E00074C5B7101A82E008000000009C969A1572E6D20100000000000000001000000037AC45EC31B80147A0183CD4808DBFE6'))
                ),
                'IsGreaterThanOrEqualTo' => array(
                    'FieldURI' => array('FieldURI' => API\FieldURIManager::getFieldUriByName('lastModifiedTime')),
                    'FieldURIOrConstant' => array('Constant' => array('Value' => $lastUpdatedTime))
                )
                ]*/
            ],
            'ParentFolderIds' => [
                'FolderId' => $this->getFolderId()->toXmlObject()
            ]
        ];

        $request = array_replace_recursive($request, $options);

        $request = Type::buildFromArray($request);
        $response = $this->getClient()->FindItem($request);
        $items = $response;

        return $items;
    }

    /**
     * @param $id
     * @param $changeKey
     * @return Type\CalendarItemType
     */
    public function getCalendarItem($id, $changeKey)
    {
        return $this->getItem(['Id' => $id, 'ChangeKey' => $changeKey]);
    }

    /**
     * Updates a calendar item with changes
     *
     * @param $itemId Type\ItemIdType
     * @param array $changes
     * @param array $options
     * @return Type\CalendarItemType[]
     */
    public function updateCalendarItem(Type\ItemIdType $itemId, $changes, $options = array())
    {
        
        $id2 = "AAMkADRlZTFjNWY2LTI1ZjItNGM2MC1iM2Y1LTJhZWQzZWVjMDVkOQBGAAAAAADgUH+VonHaTqUjIxiGOlUIBwBGIHvgD9TuRrVrxRuLx+V0AAAAAAEOAABGIHvgD9TuRrVrxRuLx+V0AAKAQm1xAAA=";
        
        $id1 = "AAMkADRlZTFjNWY2LTI1ZjItNGM2MC1iM2Y1LTJhZWQzZWVjMDVkOQBGAAAAAADgUH+VonHaTqUjIxiGOlUIBwBGIHvgD9TuRrVrxRuLx+V0AAAAAAEOAABGIHvgD9TuRrVrxRuLx+V0AAFbBACbAAA=";
        
        $itemId = new Type\ItemIdType($id1);
        
        $itemId2 = new Type\ItemIdType($id2);
        
        error_reporting(E_ALL & ~E_WARNING);
        
        ini_set("display_errors", 1);
        
        
        //Create the request
        $request = array(
            'ItemChange' => [
                [
                'ItemId' => $itemId->toArray(),
                'Updates' => API\ItemUpdateBuilder::buildUpdateItemChanges('CalendarItem', 'calendar', $changes)
                ],
                [
                'ItemId' => $itemId2->toArray(),
                'Updates' => API\ItemUpdateBuilder::buildUpdateItemChanges('CalendarItem', 'calendar', $changes)
                ]
            ]
        );

        $defaultOptions = [
            'SendMeetingInvitationsOrCancellations' => 'SendToNone'
        ];
        echo '<pre>';print_r($request);
        echo '</pre>';
        $options = array_replace_recursive($defaultOptions, $options);
        $items = $this->updateItems($request, $options);
        echo '<pre>';print_r($items);
        echo '</pre>';
        exit;
        
        //->getCalendarItem();
        return Utilities\ensureIsArray($items);
    }

    /**
     * @param Type\ItemIdType $itemId
     * @param array $options
     * @return bool
     */
    public function deleteCalendarItem(Type\ItemIdType $itemId, $options = array())
    {
        $defaultOptions = array(
            'SendMeetingCancellations' => 'SendToNone'
        );

        $options = array_replace_recursive($defaultOptions, $options);
        return $this->deleteItems($itemId, $options);
    }

    /**
     * @param string $start
     * @param string $end
     * @param array $options
     */
    public function deleteAllCalendarItems($start = '12:00 AM', $end = '11:59 PM', $options = array())
    {
        $items = $this->getCalendarItems($start, $end, $options);
        foreach ($items as $item) {
            $this->deleteCalendarItem($item->getItemId());
        }
    }

    /**
     * Get a list of changes on the calendar items
     *
     * @param null $syncState
     * @param array $options
     * @return API\Message\SyncFolderItemsResponseMessageType
     */
    public function listChanges($syncState = null, $options = array())
    {
        return parent::listItemChanges($this->getFolderId(), $syncState, $options);
    }

    /**
     * @param Type\ItemIdType $itemId
     * @param string $message
     * @param string $sensitivity
     * @param array $options
     *
     * @return Type\ItemIdType[]
     */
    public function acceptMeeting($itemId, $message, $sensitivity = 'Private', $options = array())
    {
        $request = [
            'AcceptItem' => [
                'Sensitivity' => $sensitivity,
                'Body' => ['BodyType' => 'HTML', '_value' => $message],
                'ReferenceItemId' => $itemId->toArray()
            ]
        ];

        $defaultOptions = ['MessageDisposition' => 'SendOnly'];
        $options = array_replace_recursive($defaultOptions, $options);

        $return = $this->createItems($request, $options)->getCalendarItem();
        return Utilities\ensureIsArray($return);
    }

    /**
     * @param $itemId
     * @param $message
     * @param string $sensitivity
     * @param array $options
     * @return Type\ItemIdType[]
     */
    public function declineMeeting($itemId, $message, $sensitivity = 'Private', $options = array())
    {
        $request = [
            'DeclineItem' => [
                'Sensitivity' => $sensitivity,
                'Body' => ['BodyType' => 'HTML', '_value' => $message],
                'ReferenceItemId' => $itemId->toArray()
            ]
        ];

        $defaultOptions = array('MessageDisposition' => 'SendOnly');
        $options = array_replace_recursive($defaultOptions, $options);

        $return = $this->createItems($request, $options)->getCalendarItem();
        return Utilities\ensureIsArray($return);
    }

    /**
     * @param $startTime
     * @param $endTime
     * @param array $users
     * @param array $options
     *
     * @return API\Message\GetUserAvailabilityResponseType
     */
    public function getAvailabilityFor($startTime, $endTime, array $users, array $options = array())
    {
        $startTime = Utilities\ensureIsDateTime($startTime);
        $endTime = Utilities\ensureIsDateTime($endTime);

        $request = [
            'MailboxDataArray' => ['MailboxData' => []],
            'FreeBusyViewOptions' => [
                'TimeWindow' => [
                    'StartTime' => $startTime->format('c'),
                    'EndTime' => $endTime->format('c'),
                ],
                'RequestedView' => 'FreeBusyMerged',
                'MergedFreeBusyIntervalInMinutes' => 30
            ],
        ];

        $users = array_map(function ($user) {
            return [
                'Email' => ['Address' => $user],
                'AttendeeType' => 'Required',
                'ExcludeConflicts' => false
            ];
        }, $users);

        $request['MailboxDataArray']['MailboxData'] = $users;

        $request = array_replace_recursive($request, $options);
        $response = $this->getClient()->GetUserAvailability($request);
        return $response;
    }

    /**
     * @param $startTime
     * @param $endTime
     * @param int $period The period of time to see if users of free for (in minutes)
     * @param array $users
     * @param array $options
     *
     * @return boolean
     */
    public function areAvailable($startTime, $endTime, $period, array $users, array $options = [])
    {
        $options = array_replace_recursive($options, [
            'FreeBusyViewOptions' => [
                'MergedFreeBusyIntervalInMinutes' => $period, 'RequestedView' => 'MergedOnly'
            ]]);
        $availability = $this->getAvailabilityFor($startTime, $endTime, $users, $options);

        $availabilities = array_map(function (FreeBusyResponseType $freeBusyResponseType) {
            return str_split($freeBusyResponseType->getFreeBusyView()->getMergedFreeBusy());
        }, $availability->getFreeBusyResponseArray()->FreeBusyResponse);

        foreach ($availabilities[0] as $periodIndex => $availability) {
            if ($availability != 0) {
                continue;
            }

            $free = true;
            foreach ($availabilities as $userAvailability) {
                if ($userAvailability[$periodIndex] != 0) {
                    $free = false;
                    break;
                }
            }

            if ($free === false) {
                continue;
            }

            return true;
        }

        return false;
    }
    
    public function getCalendarSEItems($start = '12:00 AM', $end = '11:59 PM', $options = array())
    {
        $start = Utilities\ensureIsDateTime($start);
        $end = Utilities\ensureIsDateTime($end);
        
        $request = [
            'Traversal' => 'Shallow',
            'ItemShape' => [
                'BaseShape' => 'AllProperties'
            ],
            'CalendarView' => [
                'MaxEntriesReturned' => 100,
                'StartDate' => $start->format('c'),
                'EndDate' => $end->format('c')
            ],
            'ParentFolderIds' => [
                'FolderId' => $this->getFolderId()->toXmlObject()
            ]
        ];
        
        $request = array_replace_recursive($request, $options);
        
        $request = Type::buildFromArray($request);
        $response = $this->getClient()->FindItem($request);
        $items = $response;
        
        return $items;
    }
    
    function getCalendarExceptionItems(){
        
        error_reporting(E_ALL & ~E_WARNING);
        
        ini_set("display_errors", 1);
        
        $request = array(
            'ItemShape' => array('BaseShape' => 'AllProperties'),
            'ItemIds' => array('OccurrenceItemId' => array("RecurringMasterId" => "AAMkADRlZTFjNWY2LTI1ZjItNGM2MC1iM2Y1LTJhZWQzZWVjMDVkOQBGAAAAAADgUH+VonHaTqUjIxiGOlUIBwBGIHvgD9TuRrVrxRuLx+V0AAAAAAEOAABGIHvgD9TuRrVrxRuLx+V0AAEZsiieAAA=", "InstanceIndex" => 2))
        );
        
        $request = array_replace_recursive($request, array());
        
        $response = $this->getClient()->GetItem($request);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
        exit;
        
    }
    
    function getItems($itemIds){
        
        $Items = array();
        
        foreach($itemIds as $id){
            $Item = new Type\ItemIdType($id);
            $Items[] = $Item->toArray();
        }
        
        return $this->getItem($Items);
    }
}
