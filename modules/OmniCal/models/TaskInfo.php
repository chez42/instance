<?php

class OmniCal_TaskInfo_Model extends Vtiger_Module{
    public $recurrence, $reminder;
    public $exchange_id, $exchange_change_key;
    public $subject, $sensitivity, $body, $importance, $isRecurring, $reminderIsSet, $status, $statusDescription;
    public $dateTimeReceived, $dateTimeSent, $dateTimeCreated, $dueDate;

    public function __construct() {
        parent::__construct();
        $this->reminder = new OmniCal_Reminder_Model();
        $this->recurrence = new OmniCal_Recurrence_Model();
    }
    
}

?>