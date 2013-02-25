<?php

	namespace Osr\Models\Storage;

	class SpammerLastSeen extends Object {
		
		protected $_name = 'osr_spammer_last_seen';
		
		/** 
		 * Required
		 */
		public $email;
		
		/**
		 * Optional
		 */
		public $date = null;
		
		protected $_meta = array(
			'HashKeyElement' => 'email',
			'HashKeyElementType' => 'S',
			'RangeKeyElement' => 'date',
			'RangeKeyElementType' => 'N',
			'ReadCapacityUnits' => 10,
			'WriteCapacityUnits' => 5
		);

		public function create(){

			$this->email = trim($this->email);
			if (empty($this->email)){
				throw new StorageException("Invalid email address");
			}
			
			if (!$this->date){
				$this->date = $this->getDate();
			}
			
			if (!is_int($this->date)){
				throw new StorageException("Date should be a int timestamp");
			}

			return parent::create();
		}
		
		public function get(){
			
			$this->email = trim($this->email);
			if (empty($this->email)){
				throw new StorageException("Invalid email address");
			}
				
			$this->date = (int) $this->date;
			
			return parent::get();
			
		}
		
	}