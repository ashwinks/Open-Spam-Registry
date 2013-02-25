<?php

	namespace Osr\Models\Storage;

	class Spammer extends Object {
		
		protected $_name = 'osr_spammer';
		
		/** 
		 * Required
		 */
		public $email;
		public $submitted_by_api_key;
		
		/**
		 * Optional
		 */
		public $submitted_by_email;
		public $ip;
		public $ip_string;
		public $date_created;
		public $date_updated;
		
		protected $_meta = array(
			'HashKeyElement' => 'email',
			'HashKeyElementType' => 'S',
			'ReadCapacityUnits' => 10,
			'WriteCapacityUnits' => 5
		);
		
		/**
		 * (non-PHPdoc)
		 * @see \Osr\Models\Storage\Object::create()
		 * @return Spammer
		 */
		public function create(){
			
			$this->email = trim($this->email);
			if (empty($this->email) || !$this->isValidEmail($this->email)){
				throw new StorageException("Invalid email address");
			}
			$this->email = $this->handleEmailCleansing($this->email);
						
			$this->submitted_by_api_key = trim($this->submitted_by_api_key);
			if (empty($this->submitted_by_api_key)){
				throw new StorageException("Invalid api key");
			}
			
			if (empty($this->date_created)){
				$this->date_created = $this->date_updated = $this->getDate();
			}
			
			if (!empty($this->ip)){
				$this->ip_string = $this->ip;
				$this->ip = ip2long($this->ip);
			}
			
			return parent::create();
		}
		
		public function get(){
			
			if (!$this->isValidEmail($this->email)){
				return false;
			}
			$this->email = $this->handleEmailCleansing($this->email);
			
			$found = parent::get();
			if ($found){
				$found->ip_string = long2ip($found->ip);
				return $found;
			}
			
			return false;
			
		}
		
		public function update(){
			
			$this->date_updated = $this->getDate();
			
			return parent::update();
			
		}
		
		public function isValidEmail($email_address){
			
			if (strstr($email_address, '@')){
				return true;
			}
			
			return false;
			
		}
		
		public function handleEmailCleansing($email_address){
			
			$email_address = strtolower($email_address);
			$parts = explode('@', $email_address);
			
			$local_part = $parts[0];
			$domain_part = $parts[1];
			
			/**
			 * @todo this should be done via a factory pattern so we can support custom formatting for different domains
			 */
			if ($domain_part == 'gmail.com'){
				$ar_search = array('.', '+');
				$local_part = str_replace($ar_search, '', $local_part);
			}
			
			return $local_part . '@' . $domain_part;
			
		}
		
	}