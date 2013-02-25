<?php

	namespace Osr\Tests\Models\Storage;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class ApiKeyTest extends OsrTestAbstract {
		
		public function testCreateDefaultLookupAccount(){
		
			$obj = new Storage\ApiKey();
			$obj->api_key = 'thisissomeapikey';
			$obj->email = 'testing@openspamregistry.com';
			$response = $obj->create();
			
			$this->assertTrue($response instanceof \Osr\Models\Storage\ApiKey);
			$this->assertEquals($obj->email, $response->email);
			$this->assertEquals($obj->api_key, $response->api_key);
		}

		public function testCreate(){
				
			$obj = new Storage\ApiKey();
			$obj->api_key = 'thisissomeapikey' . uniqid();
			$obj->email = 'someemail@' . uniqid() . '.com';
			$response = $obj->create();
				
			$this->assertTrue($response instanceof \Osr\Models\Storage\ApiKey);
			$this->assertEquals($obj->email, $response->email);
			$this->assertEquals($obj->api_key, $response->api_key);
		}
		
		public function testGet(){
				
			$obj = new Storage\ApiKey();
			$obj->api_key = 'thisissomeapikey';
			$response = $obj->get();
				
			$this->assertTrue($response instanceof \Osr\Models\Storage\ApiKey);
			$this->assertEquals($obj->api_key, $response->api_key);
			$this->assertTrue(!empty($response->email));
		}
		
	}