<?php

	namespace Osr\Models\Storage;

	use Aws\CloudFront\Exception\Exception;

	class User extends Object {
		
		protected $_name = 'osr_user';
		
		/**
		 * Required 
		 */
		public $email;
		public $website;

		/**
		 * Optional
		 */
		public $date_created;
		public $date_updated;
		public $api_key;
		public $api_secret;
		
		protected $_meta = array(
			'HashKeyElement' => 'email',
			'HashKeyElementType' => 'S',
			'ReadCapacityUnits' => 10,
			'WriteCapacityUnits' => 5
		);

		public function create(){
			
			$this->email = trim($this->email);
			if (empty($this->email)){
				throw new StorageException("Invalid user email address");
			}
			
			$this->website = trim($this->website);
			if (empty($this->website)){
				throw new StorageException("Invalid user website");
			}

			if (empty($this->date_created)){
				$this->date_created = $this->date_updated = $this->getDate();
			}
			
			if (empty($this->api_key)){
				$this->generateApiCredentials();
			}

			return parent::create();

		}
		
		public function update(){
			
			$this->date_updated = $this->getDate();
			
			return parent::update();
			
		}
		
		public function get(){
			
			$this->email = trim($this->email);
			if (empty($this->email)){
				throw new StorageException("Invalid email address");
			}
			
			return parent::get();
		
		}
		
		public function generateApiCredentials(){
			
			if (empty($this->email)){
				throw new StorageException("Invalid user email address - Email address must be set before generating api credentials");
			}
			
			if (empty($this->date_created)){
				throw new StorageException("Invalid user date created - Date created must be set before generating api credentials");
			}
			
			$this->api_key = sha1($this->email . $this->date_created);
			$this->api_secret = sha1($this->api_key . $this->email . $this->date_created);
			
			return $this;
	
		}
		
	}