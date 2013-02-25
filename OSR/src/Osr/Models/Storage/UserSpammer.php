<?php

	namespace Osr\Models\Storage;

	class UserSpammer extends Object {
		
		protected $_name = 'osr_user_spammer';
		
		/** 
		 * Required
		 */
		public $user_email;
		public $spammer_email;
		
		/**
		 * Optional
		 */
		public $date = null;
		
		protected $_meta = array(
			'HashKeyElement' => 'user_email',
			'HashKeyElementType' => 'S',
			'RangeKeyElement' => 'date',
			'RangeKeyElementType' => 'N',
			'ReadCapacityUnits' => 10,
			'WriteCapacityUnits' => 5
		);

		public function create(){

			$this->user_email = trim($this->user_email);
			if (empty($this->user_email)){
				throw new StorageException("Invalid user email address");
			}
			
			$this->spammer_email = trim($this->spammer_email);
			if (empty($this->spammer_email)){
				throw new StorageException("Invalid spammer email address");
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
			
			$this->user_email = trim($this->user_email);
			if (empty($this->user_email)){
				throw new StorageException("Invalid user email address");
			}
				
			$this->date = (int) $this->date;
			
			return parent::get();
			
		}
		
	}