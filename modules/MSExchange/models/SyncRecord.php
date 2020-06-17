<?php

class MSExchange_SyncRecord_Model {

	const CREATE_MODE = 'create';
	const UPDATE_MODE = 'update';
	const DELETE_MODE = 'delete';
	const SAVE_MODE = 'save';
	
	//SPecifies the module with which the model belong to
	
	protected $type;

	protected $mode;

	protected $data;

	function  __construct($values = array()) {
		$this->data = $values;
	}

	public function getData(){
		return $this->data;
	}

	public function setData($values){
		$this->data = $values;
		return $this;
	}

	public function set($key,$value){
		$this->data[$key] = $value;
		return $this;
	}

	public function get($key){
		return $this->data[$key];
	}

	public function has($key) {
		return array_key_exists($key, $this->data);
	}

	/**
	 * Function to check if the key is empty.
	 * @param type $key
	 */
	public function isEmpty($key) {
		return (!isset($this->data[$key]) || empty($this->data[$key]));
	}
	
	public function getId(){
		return $this->get('id');
	}
	
	public function setId($id){
		return $this->set('id',$id);
	}
	
	public function setModifiedTime($modifiedTime){
		return $this->set('modifiedtime',$modifiedTime);
	}

	public function getModifiedTime(){
		return $this->get('modifiedtime');
	}

	public function setType($type){
		$this->type = $type;
		return $this;
	}

	public function getType(){
		return $this->type;
	}

	public function setMode($mode){
		$this->mode = $mode;
		return $this;
	}

	public function getMode(){
		return $this->mode;
	}

	public function isDeleteMode(){
		return ($this->mode == self::DELETE_MODE) ? true :false;
	}

	public function isCreateMode(){
		return ($this->mode == self::CREATE_MODE) ? true : false;
	}

	public function getSyncIdentificationKey(){
		return $this->get('_syncidentificationkey');
	}

	public function setSyncIdentificationKey($key){
		return $this->set('_syncidentificationkey',$key);
	}

	public static function getInstanceFromValues($recordValues){
	    $model = new MSExchange_SyncRecord_Model($recordValues);
		return $model;
	}
	
	public function getTimeZoneOffset($name){
		
	    echo 'In Function getTimeZoneOffset';
	    exit;
	    
		$timezoneArray = array(
			'Alpha Time Zone' 						=> array('A' => '+1:00'),
			'Australian Central Daylight Time' 		=> array('ACDT' => '+10:30'),
			'Australian Central Standard Time'		=> array('ACST' => '+9:30'),
			'Acre Time'								=> array('ACT' => '-5:00'),
			'Australian Central Time'				=> array('ACT' => '+10:30/+9:30'),
			'Australian Central Western Standard Time' => array('ACWST' => '+8:45'),
			
			'Arabia Daylight Time' 				=> array('ADT' => '+3:00'),
			'Atlantic Daylight Time' 				=> array('ADT' => '-3:00'),
			'Australian Eastern Daylight Time' 		=> array('AEDT' => '+11:00'),
			'Australian Eastern Standard Time'		=> array('AEST' => '+10:00'),
			
			'India Standard Time' => array('IST'=>'+5:30'),
		);
	
		return $timezoneArray[$name];
	}

}
?>
