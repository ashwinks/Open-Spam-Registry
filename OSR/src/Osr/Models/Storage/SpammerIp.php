<?php

	namespace Osr\Models\Storage;

	use Osr\Registry;

	class SpammerIp extends Object {
		
		protected $_name = 'osr_spammer_ip';
		
		/** 
		 * Required
		 */
		public $ip;
		public $email;
		
		public $ip_string;
		
		protected $_meta = array(
			'HashKeyElement' => 'ip',
			'HashKeyElementType' => 'N',
			'ReadCapacityUnits' => 10,
			'WriteCapacityUnits' => 5
		);

		public function create(){

			if (empty($this->ip)){
				throw new StorageException("Invalid IP address");
			}
			
			if (is_string($this->ip)){
				$this->ip = trim($this->ip);
				$this->ip_string = $this->ip;
				$this->ip = ip2long($this->ip);
			}else{
				$this->ip_string = long2ip($this->ip);
			}

			$this->email = trim($this->email);
			if (empty($this->email)){
				throw new StorageException("Invalid email address");
			}

			return parent::create();
		}
		
		public function get(){
			
			$this->ip = trim($this->ip);
			if (empty($this->ip)){
				throw new StorageException("Invalid IP address");
			}
			$this->ip = ip2long($this->ip);

			$found = parent::get();
			if ($found){
				$found->ip = long2ip($found->ip);
				return $found;
			}
			
			return false;
			
		}
		
	}