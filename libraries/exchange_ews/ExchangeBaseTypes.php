<?php

class Type {

    public function getField($key) {
        list($key,$entry,$field) = explode('.',$key);
  
        $value = $this->$key;
        if ($value instanceof DictionaryType) {
            if ($entry) {
                $value = $value->getEntry($entry);
                if ($value instanceof DictionaryEntryType)
                    if ($field)
                        $value = $value->$field;
                    else
                        $value = $value->_;
            }
            else {
                $value = $value->toArray();
            }
        }
        elseif ($value instanceof ItemIdType || $value instanceof stdClass) {
            if ($entry) {
                $value = $value->$entry;
            }
        }
        elseif ($value instanceOf BodyType) {
            if ($entry) {
                $value = $value->$entry;
            }
            else 
                $value = $value->_;
        }
        elseif ($value instanceof ArrayOfStringsType) {
            if ($entry)
                $value = $value->String->$entry;
            else
                $value = (string)$value;
        }
        
        return $value;
    }
    public function setField($key,$value) {
  
        //don't set empty values
        if (strlen($value) == 0 || $value == '0000-00-00')
            return;
  
  
  
        list($key,$entry,$field) = explode('.',$key);

        $type = $this->getFieldType($key);
        if (class_exists($type,FALSE))
            $this->$key = new $type;
        if ($this->$key instanceof DictionaryType) {
           if (strlen($entry) > 0) { //need a dictionary type
               if (!$this->$key->hasEntry($entry))
                   $this->$key->setEntry($entry,array($field=>$value));
               else
                   $this->$key->getEntry($entry)->$field = $value;
           }
        }
        elseif ($this->$key instanceof BodyType) {
            if (strlen($entry) == 0) {
                $this->$key->_ = $value;
                $this->$key->BodyType = 'HTML';
            }
        }
        elseif ($this->$key instanceof ArrayOfStringsType) {
           $strings = preg_split('/ ?[,;] ?/',$value);
           $this->$key->String = $strings;
        }
        elseif ($type == 'dateTime') {
            //fix date values
            if (preg_match('/^(\d{4}-\d{2}-\d{2}) ?(\d{2}:\d{2}:\d{2})?/',$value,$m)) {
                $date = $m[1];
                $time = $m[2];
                if (!$time)
                    $time = '00:00:00';
                if ($date == '0000-00-00') 
                    unset($this->$key);
                else
                    $this->$key = $date .'T'. $time . 'Z';
            }
        }
        else {
            $this->$key = $value;
        }
    }
    public static function getFieldType($field) {
        throw new Exception("Unknown type for field $field");
    }


    public function getFieldURI($field,$depth = 0) {
        //depth specifies how far back we need to check in the parent class to find the property we're looking for...
        $reflection = new ReflectionClass($this);
        for ($i = $depth; $i > 0 && $reflection !== NULL; $i--) {
            $reflection = $reflection->getParentClass();
            
        }
        if ($reflection == NULL)    
            return "NOTFOUND $field DEPTH=$depth";

        $parentReflection = $reflection->getParentClass();
        if ($parentReflection) {
            $parentVars = array_keys($parentReflection->getDefaultProperties());
        }
        $vars = array_keys($reflection->getDefaultProperties());


        if (is_array($parentVars))
            $vars = array_diff($vars,$parentVars);
        #print "VARS: "; print_r($vars);

        if (in_array($field,$vars)) {
            $className = $reflection->name;
            return call_user_func(array($className,'getFieldURIScope')) . ':' . $field;

        }
        else
            return $this->getFieldURI($field,$depth+1);

    }



    public function toArray() {
        $thisArr = (array)$this;
        foreach ($thisArr as $k => $v) {
            if (is_object($v)) {
                $thisArr[$k] = method_exists($v,'toArray') ? $v->toArray() : (array)$v;
            }
        }
        return $thisArr;
    }
}

class DictionaryType extends Type {
    public $Entry;

    public function getEntry($Key) {
        if (is_array($this->Entry)) {
            foreach ($this->Entry as $e) {
                if ($e instanceOf DictionaryEntryType && $e->Key == $Key) {
                    return $e;
                }
            }
        }
    }

    public function hasEntry($Key) {
        if (is_array($this->Entry)) {
            foreach ($this->Entry as $e) {
                if ($e instanceOf DictionaryEntryType && $e->Key == $Key) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    public function getKeys() {
        $keys = array();
        foreach ($this->Entry as $e) {
            $keys[] = $e->Key;
        }
        return $keys;
    }

    public function setEntry($key,$values) {
        if (!is_array($this->Entry))
            $this->Entry = array();
        
        $e = $this->newEntry();
        $e->Key = $key;
        if (is_array($values) || is_object($values))
            foreach ($values as $k => $v) {
                if (strlen($k) == 0)
                    $k = '_';

                $e->$k = $v;
            }
        else
            $e->_ = $values;
    

        $this->Entry[] = $e;

    }

    public function newEntry() { return new DictionaryEntryType; }





    public function toArray() {
        $arr = array();
        if (is_array($this->Entry)) {
            foreach ($this->Entry as $e) {
                if ($e->_ !== NULL) 
                    $arr[$e->Key] = $e->_;
                else {
                    $e_arr = (array)$e;
                    unset($e_arr['_']);
                    unset($e_arr['Key']);
                    $arr[$e->Key] = $e_arr;
                }
            }
        }
        return $arr;
    }


}

class DictionaryEntryType {
    public $Key;

}

class ArrayOfStringsType {
    public $String;

    public function __toString() {
        if (is_array($this->String))
            return implode(', ',$this->String);
        return '';
    }

}

class ItemType extends Type {
    public $MimeContent; // MimeContentType
    public $ItemId; // ItemIdType
    public $ParentFolderId; // FolderIdType
    public $ItemClass; // ItemClassType
    public $Subject; // string
    public $Sensitivity; // SensitivityChoicesType
    public $Body; // BodyType
    public $Attachments; // NonEmptyArrayOfAttachmentsType
    public $DateTimeReceived; // dateTime
    public $Size; // int
    public $Categories; // ArrayOfStringsType
    public $Importance; // ImportanceChoicesType
    public $InReplyTo; // string
    public $IsSubmitted; // boolean
    public $IsDraft; // boolean
    public $IsFromMe; // boolean
    public $IsResend; // boolean
    public $IsUnmodified; // boolean
    public $InternetMessageHeaders; // NonEmptyArrayOfInternetHeadersType
    public $DateTimeSent; // dateTime
    public $DateTimeCreated; // dateTime
    public $ResponseObjects; // NonEmptyArrayOfResponseObjectsType
    public $ReminderDueBy; // dateTime
    public $ReminderIsSet; // boolean
    public $ReminderMinutesBeforeStart; // ReminderMinutesBeforeStartType
    public $DisplayCc; // string
    public $DisplayTo; // string
    public $HasAttachments; // boolean
    public $ExtendedProperty; // ExtendedPropertyType
    public $Culture; // language

    public static function getFieldURIScope() { return 'item'; }

    public static function getFieldType($field) { 
        $types = array (
            'MimeContent' => 'MimeContentType',
            'ItemId' => 'ItemIdType',
            'ParentFolderId' => 'FolderIdType',
            'ItemClass' => 'ItemClassType',
            'Subject' => 'string',
            'Sensitivity' => 'SensitivityChoicesType',
            'Body' => 'BodyType',
            'Attachments' => 'NonEmptyArrayOfAttachmentsType',
            'DateTimeReceived' => 'dateTime',
            'Size' => 'int',
            'Categories' => 'ArrayOfStringsType',
            'Importance' => 'ImportanceChoicesType',
            'InReplyTo' => 'string',
            'IsSubmitted' => 'boolean',
            'IsDraft' => 'boolean',
            'IsFromMe' => 'boolean',
            'IsResend' => 'boolean',
            'IsUnmodified' => 'boolean',
            'InternetMessageHeaders' => 'NonEmptyArrayOfInternetHeadersType',
            'DateTimeSent' => 'dateTime',
            'DateTimeCreated' => 'dateTime',
            'ResponseObjects' => 'NonEmptyArrayOfResponseObjectsType',
            'ReminderDueBy' => 'dateTime',
            'ReminderIsSet' => 'boolean',
            'ReminderMinutesBeforeStart' => 'ReminderMinutesBeforeStartType',
            'DisplayCc' => 'string',
            'DisplayTo' => 'string',
            'HasAttachments' => 'boolean',
            'ExtendedProperty' => 'ExtendedPropertyType',
            'Culture' => 'language',
        );
        return array_key_exists($field,$types) ? $types[$field] : parent::getFieldType($field);
    }


}

?>

