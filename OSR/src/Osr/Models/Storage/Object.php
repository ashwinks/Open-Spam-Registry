<?php

	namespace Osr\Models\Storage;
	
	use Osr\Registry;

	use Osr\Models\Storage;
	
	abstract class Object {
		
		private $_adapter;
		
		protected $_reflection;
		
		public function __construct(array $data = array(), Adapter $adapter = null){

			if (!$adapter){
				$this->_adapter = new Storage\Adapter();
			}

			$this->_reflection = new \ReflectionClass(get_class($this));
			
			if (count($data) > 0){
				
				foreach ($data as $k => $v){
					$this->{$k} = $v;
				}
				
			}
				
		}
		
		public function toArray(){
		
			$properties = $this->_reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
		
			$ar = array();
			foreach ($properties as $property){
				$prop_name = $property->getName();
				if (!empty($this->{$prop_name})){
					$ar[$prop_name] = $this->{$prop_name};
				}
			}
		
			return $ar;
		
		}
		
		public function responseToArray(){
			
		}
		
		protected function getDate(){
			
			return time();
			
		}
		
		/**
		 * 
		 * @return \Osr\Models\Storage\Adapter
		 */
		public function getAdapter(){
			
			return $this->_adapter;
			
		}
		
		/** 
		 * Use test tables for development
		 * 
		 * @return string
		 */
		public function getTableName(){

			if (strstr(strtolower($_SERVER['SCRIPT_NAME']), 'phpunit')){
				return 'test_' . $this->_name;
			}else{
				return $this->_name;
			}
			
		}
		
		public function getMeta(){
			
			return $this->_meta;
			
		}
		
		public function delete(){
			
			return $this->getAdapter()->delete($this);
			
		}
		
		public function create(){
			
			return $this->getAdapter()->create($this);
			
		}
		
		public function update(){
			
			return $this->getAdapter()->update($this);
			
		}
		
		public function get(){
			
			return $this->getAdapter()->get($this);
			
		}
		
		public function createTable(){
			
			return $this->getAdapter()->createTable($this);
			
		}
		
	}