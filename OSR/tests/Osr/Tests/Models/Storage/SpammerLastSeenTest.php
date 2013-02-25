<?php

	namespace Osr\Tests\Models\Storage;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class SpammerLastSeenTest extends OsrTestAbstract {
		
		public function testCreateDefaultLookupAccount(){
		
			$obj = new Storage\SpammerLastSeen();
			$obj->email = 'testing@openspamregistry.com';
			$response = $obj->create();
			
			$this->assertTrue($response instanceof \Osr\Models\Storage\SpammerLastSeen);
			$this->assertEquals($obj->email, $response->email);
			$this->assertTrue(!empty($response->date));
		}

		public function testCreate(){
				
			$obj = new Storage\SpammerLastSeen();
			$obj->email = 'someemail@' . uniqid() . '.com';
			$response = $obj->create();
				
			$this->assertTrue($response instanceof \Osr\Models\Storage\SpammerLastSeen);
			$this->assertEquals($obj->email, $response->email);
			$this->assertTrue(!empty($response->date));
		}
		
		public function testGet(){
				
			$obj = new Storage\SpammerLastSeen();
			$obj->email = 'testing@openspamregistry.com';
			$obj->date = 1359577310;
			$response = $obj->get();

			$this->assertTrue($response instanceof \Osr\Models\Storage\SpammerLastSeen);
			$this->assertEquals($obj->email, $response->email);
			$this->assertEquals($obj->date, $response->date);
		}
		
	}