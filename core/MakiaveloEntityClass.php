<?php

class MakiaveloEntity {
	protected $errors;


	public function __get_entity_name() {
		return Makiavelo::camel_to_underscore(get_class($this));
	}

	public function is_new() {
		return ($this->id == null);
	}

	private function getTableDescription() {
		$tname = $this->__get_entity_name();
		if(!isset($_SESSION['makiavelo']['t_descriptions'][$tname])) {
			$_SESSION['makiavelo']['t_descriptions'][$tname] = DBLayer::describeTable($tname);
		}
		return $_SESSION['makiavelo']['t_descriptions'][$tname];
	}

	// translate the value based on the type of the field on the database
	private function translateValue($attr, $val) {
		$data = $this->getTableDescription();
		$type = $data[$attr];
		//Makiavelo::info("Looking for a translation for " . print_r($data, true));
		Makiavelo::info("Attr: $attr - Value: " . $val);
		if(strstr($type, "tinyint")) {
			if(strtolower($val) == "on") {
				return 1;
			} else {
				return $val;
			}
		} else {
			return $val;
		}
	}

	public function load_from_array($arr) {
		Makiavelo::info("Inside load_from_array....");
		Makiavelo::info("Data to be loaded: " . print_r($arr, true));
		foreach($arr as $attr => $value) {
			Makiavelo::info("Setting {$attr} to {$value}");
			$this->$attr = $this->translateValue($attr, $value);
			Makiavelo::info("getting {$attr} to {$this->$attr}");
		}
	}

	public function validate() {
		Makiavelo::info("== Validating model ==");

		$class_name = get_class($this);
		$tmp_entity = new $class_name;

		$reflect = new ReflectionClass($class_name);
		$properties = $reflect->getProperties();
		$validates = true;

		foreach($properties as $prop) {
			$attr = $prop->getName();
			$value = $this->$attr;
			Makiavelo::info("-- Validating attr: " . $attr);
			if(!isset($tmp_entity::$validations[$attr])) {
				Makiavelo::info("-- No validation set");
				continue;
			}
			$this->errors[$attr] = array();
			foreach($tmp_entity::$validations[$attr] as $validator) {
				Makiavelo::info("-- Validation: " . $validator);
				$validator_class = ucwords($validator) . "Validator";
				$v = new $validator_class;
				if(!$v->validate($value)) {
					$this->errors[$attr][] = $attr . " " . $v->errorMsg();
					$validates = false;
				}
			}
		}
		Makiavelo::info("== Validation result == ");
		Makiavelo::info(print_r($this->errors, true));
		return $validates;
	}
	
	public function __set($name, $val) {
		$this->$name = $val;
	}

	public function __get($name) {
		return $this->$name;
	}
}

?>