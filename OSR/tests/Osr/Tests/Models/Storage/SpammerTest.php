<?php

	namespace Osr\Tests\Models\Storage;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class SpammerTest extends OsrTestAbstract {
		
		public function testCreateDefaultLookupAccount(){
		
			$obj = new Storage\Spammer();
			$obj->email = 'testing@openspamregistry.com';
			$obj->submitted_by_email = 'testsubmit@osr.com';
			$obj->submitted_by_api_key = 'someapikey';
			$obj->ip = '192.168.1.1';
			$response = $obj->create();
			
			$this->assertTrue($response instanceof \Osr\Models\Storage\Spammer);
			$this->assertEquals($obj->email, $response->email);
			$this->assertEquals($obj->submitted_by_email, $response->submitted_by_email);
			$this->assertEquals($obj->submitted_by_api_key, $response->submitted_by_api_key);
			$this->assertEquals($obj->ip, $response->ip);
			$this->assertEquals(long2ip($response->ip), $response->ip_string);
			$this->assertTrue(!empty($response->date_created));
			$this->assertTrue(!empty($response->date_updated));
		}

		public function testCreate(){
				
			$obj = new Storage\Spammer();
			$obj->email = 'testing-funny@' . uniqid() . '.com';
			$obj->submitted_by_email = 'testsubmit@' . uniqid() . '.com';
			$obj->submitted_by_api_key = 'someapikey' . uniqid();
			$obj->ip = rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100);
			$response = $obj->create();
			
			$this->assertTrue($response instanceof \Osr\Models\Storage\Spammer);
			$this->assertEquals($obj->email, $response->email);
			$this->assertEquals($obj->submitted_by_email, $response->submitted_by_email);
			$this->assertEquals($obj->submitted_by_api_key, $response->submitted_by_api_key);
			$this->assertEquals($obj->ip, $response->ip);
			$this->assertEquals(long2ip($response->ip), $response->ip_string);
			$this->assertTrue(!empty($response->date_created));
			$this->assertTrue(!empty($response->date_updated));
		}
		
		public function testCreateGmail(){
		
			$obj = new Storage\Spammer();
			$obj->email = 'testing.fu+nny@gmail.com';
			$obj->submitted_by_email = 'testsubmit@' . uniqid() . '.com';
			$obj->submitted_by_api_key = 'someapikey' . uniqid();
			$obj->ip = rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100);
			$response = $obj->create();
				
			$this->assertTrue($response instanceof \Osr\Models\Storage\Spammer);
			$this->assertEquals($obj->email, $response->email);
			$this->assertEquals($obj->submitted_by_email, $response->submitted_by_email);
			$this->assertEquals($obj->submitted_by_api_key, $response->submitted_by_api_key);
			$this->assertEquals($obj->ip, $response->ip);
			$this->assertEquals(long2ip($response->ip), $response->ip_string);
			$this->assertTrue(!empty($response->date_created));
			$this->assertTrue(!empty($response->date_updated));
		}
		
		public function testGet(){
				
			$obj = new Storage\Spammer();
			$obj->email = 'testing@openspamregistry.com';
			$response = $obj->get();

			$this->assertTrue($response instanceof \Osr\Models\Storage\Spammer);
			$this->assertEquals($obj->email, $response->email);
		}
		
		public function testIsValidEmail(){
			
			
			$obj = new Storage\Spammer();
			$valid = $obj->isValidEmail('testing@openspamregistry.com');
			
			$this->assertTrue($valid);
			
		}
		
		public function testIsValidEmailIsInvalid(){
			
			$obj = new Storage\Spammer();
			$valid = $obj->isValidEmail('testingopenspamregistry.com');
				
			$this->assertFalse($valid);
			
		}
		
		public function testHandleEmailCleansing(){
			
			$obj = new Storage\Spammer();
			$formatted = $obj->handleEmailCleansing('testing-open.spamregistry@gmail.com');
			$this->assertEquals('testing-openspamregistry@gmail.com', $formatted);
			
			$formatted = $obj->handleEmailCleansing('testing-open.spam+registry@gmail.com');
			$this->assertEquals('testing-openspamregistry@gmail.com', $formatted);
			
			$formatted = $obj->handleEmailCleansing('testingopen.spam+registry@gmail.com');
			$this->assertEquals('testingopenspamregistry@gmail.com', $formatted);
			
			$formatted = $obj->handleEmailCleansing('testingopenspamregistry@gmail.com');
			$this->assertEquals('testingopenspamregistry@gmail.com', $formatted);
			
			$formatted = $obj->handleEmailCleansing('testingopen.spamregistry@osr.com');
			$this->assertEquals('testingopen.spamregistry@osr.com', $formatted);
			
		}
		
	}