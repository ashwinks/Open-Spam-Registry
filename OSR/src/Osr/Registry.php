<?php 

	namespace Osr;
	
	class Registry {

		private static $_instance = null;
		private static $_register = array();
		
		public static function getInstance(){
			
			if (self::$_instance === null){
				self::$_instance = new self();
			}
			
			return self::$_instance;
			
		}
		
		public static function set($key, $value){
			
			self::getInstance();
			
			self::$_register[$key] = $value;
			
		}
		
		public static function get($key){
			
			self::getInstance();
			
			return self::$_register[$key];
			
		}
		
		public function isRegistered($key){
			
			if (self::$_instance === null){
				return false;
			}
			
			return array_key_exists($key, self::$_register);
			
		}
	}