<?php

	namespace Osr\Models\Storage;

	use Aws\CloudFront\Exception\Exception;

	class ApiKey extends Object {
		
		protected $_name = 'osr_api_keys';
		
		public $api_key;
		public $email;
		
		protected $_meta = array(
			'HashKeyElement' => 'api_key',
			'HashKeyElementType' => 'S',
			'ReadCapacityUnits' => 10,
			'WriteCapacityUnits' => 5
		);
		
		public function create(){
				
			$this->email = trim($this->email);
			if (empty($this->email)){
				throw new StorageException("Invalid for api key email address");
			}
				
			$this->api_key = trim($this->api_key);
			if (empty($this->api_key)){
				throw new StorageException("Invalid api key");
			}
		
			return parent::create();
		
		}
		
		/**
		 * (non-PHPdoc)
		 * @see \Osr\Models\Storage\Object::get()
		 * @return ApiKey
		 */
		public function get(){
				
			$this->api_key = trim($this->api_key);
			if (empty($this->api_key)){
				throw new StorageException("Invalid api key");
			}
				
			return parent::get();

		}
		
	}