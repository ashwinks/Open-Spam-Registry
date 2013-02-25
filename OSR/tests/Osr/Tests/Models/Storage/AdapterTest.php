<?php

	namespace Osr\Tests\Models\Storage;

	use Osr\Models\Storage\SpammerIp;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class AdapterTest extends OsrTestAbstract {
		
		public function testAddToBatch(){
			
			$obj = new SpammerIp();
			$obj->getAdapter()->initBatch();
			
			for ($i = 0; $i < 30; $i++){
				$obj->ip = rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100);
				$obj->email = 'BATCH-TEST-someemail@' . uniqid() . '.com';
				$obj->create();
			}
			
			$requests = $obj->getAdapter()->getBatchRequests();
			
		//	$this->logger(print_r($requests, true));
			
			//$response = $obj->getAdapter()->processBatch();
			//$this->logger(print_r($response, true));
			
		}
		
	}