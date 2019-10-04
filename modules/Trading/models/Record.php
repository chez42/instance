<?php

class Trading_Record_Model extends Vtiger_Base_Model {
        public function save() {
                $db = PearDatabase::getInstance();
                $db->pquery('INSERT INTO vtiger_myrss (url, title) VALUES(?,?)', array(
                        $this->get('url'), $this->get('title')
                ));
                return $db->getLastInsertID();
        }
        static function create($data) {
                $instance = self::findWithUrl($data['url']);
                if ($instance) {
                        throw new Exception('Duplicate Feed');
                }
                $instance = new self($data);
                return $instance->save();
        }
        static function findWithUrl($url) {
                $db = PearDatabase::getInstance();
                $rs = $db->pquery('SELECT * FROM vtiger_myrss WHERE url=?', array($url));
                return $db->num_rows($rs)? new self($db->fetch_array($rs)) : NULL;
        }
        static function findAll() {
                $db = PearDatabase::getInstance();
                $instances = array();
                $rs = $db->pquery('SELECT * FROM vtiger_myrss ORDER BY id DESC', array());
                if ($db->num_rows($rs)) {
                        while ($data = $db->fetch_array($rs)) {
                                $instances[] = new self($data);
                        }
                }
                return $instances;
        }
}
?>
