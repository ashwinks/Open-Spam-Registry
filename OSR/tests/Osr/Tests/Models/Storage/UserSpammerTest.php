<?php

	namespace Osr\Tests\Models\Storage;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class UserSpammerTest extends OsrTestAbstract {
		
		public function testCreateDefaultLookupAccount(){
		
			$obj = new Storage\UserSpammer();
			$obj->user_email = 'testing@openspamregistry.com';
			$obj->spammer_email = 'somespammeremail-' . uniqid() . '@stuff.com';
			$response = $obj->create();
			
			$this->assertTrue($response instanceof \Osr\Models\Storage\UserSpammer);
			$this->assertEquals($obj->user_email, $response->user_email);
			$this->assertEquals($obj->spammer_email, $response->spammer_email);
		}

		public function testCreate(){
				
			$obj = new Storage\UserSpammer();
			$obj->user_email = 'someemail@' . uniqid() . '.com';
			$obj->spammer_email = 'somespammeremail-' . uniqid() . '@stuff.com';
			$response = $obj->create();
				
			$this->assertTrue($response instanceof \Osr\Models\Storage\UserSpammer);
			$this->assertEquals($obj->user_email, $response->user_email);
			$this->assertEquals($obj->spammer_email, $response->spammer_email);
		}
		
		public function testGet(){
				
			$obj = new Storage\UserSpammer();
			$obj->user_email = 'testing@openspamregistry.com';
			$obj->date = 1360360195;
			$response = $obj->get();
			
			Registry::get('logger')->addInfo(print_r($response, true));

			$this->assertTrue($response instanceof \Osr\Models\Storage\UserSpammer);
			$this->assertSame($obj->user_email, $response->user_email);
			$this->assertTrue(!empty($response->spammer_email));
		}
		
	}