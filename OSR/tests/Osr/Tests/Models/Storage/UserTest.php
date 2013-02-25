<?php

	
	namespace Osr\Tests\Models\Storage;

	use Osr;
	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class UserTest extends OsrTestAbstract {
		
		public function testCreateDefaultLookupAccount(){
				
			$user = new Storage\User();
			$user->email = 'testing@openspamregistry.com';
			$user->website = 'http://www.openspamregistry.com';
			$response = $user->create();
				
			$this->assertTrue($response instanceof \Osr\Models\Storage\User);
			$this->assertEquals($user->email, $response->email);
			$this->assertEquals($user->website, $response->website);
			$this->assertTrue(!empty($response->api_key));
			$this->assertTrue(!empty($response->api_secret));
			$this->assertTrue(!empty($response->date_created));
			$this->assertTrue(!empty($response->date_updated));
		
		}
		
		public function testCreate(){
			
			$user = new Storage\User();
			$user->email = 'someemail@' . uniqid() . '.com';
			$user->website = 'http://www.' . uniqid() . '.com';
			$response = $user->create();
			
			$this->assertTrue($response instanceof \Osr\Models\Storage\User);
			$this->assertEquals($user->email, $response->email);
			$this->assertEquals($user->website, $response->website);
			$this->assertTrue(!empty($response->api_key));
			$this->assertTrue(!empty($response->api_secret));
			$this->assertTrue(!empty($response->date_created));
			$this->assertTrue(!empty($response->date_updated));

		}
		
		public function testGet(){
			
			$user = new Storage\User();
			$user->email = 'testing@openspamregistry.com';
			$response = $user->get();

			$this->assertTrue($response instanceof \Osr\Models\Storage\User);
			$this->assertEquals($user->email, $response->email);
 			$this->assertTrue(!empty($response->website));
			$this->assertTrue(!empty($response->api_key));
			$this->assertTrue(!empty($response->api_secret));
			$this->assertTrue(!empty($response->date_created));
			$this->assertTrue(!empty($response->date_updated));
			
			
		}
		
		public function testUpdate(){
			
			$user = new \Osr\Models\Storage\User();
			$user->email = 'testing@openspamregistry.com';
			$user->website = 'updated.' . uniqid() . '.com';
			$response = $user->update();
	
			$this->assertTrue($response instanceof \Osr\Models\Storage\User);
			$this->assertEquals($user->email, $response->email);
			$this->assertEquals($user->website, $response->website);
			$this->assertTrue(!empty($response->date_updated));
			
		}
		
	} 