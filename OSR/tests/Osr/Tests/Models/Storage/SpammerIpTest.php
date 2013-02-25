<?php

	namespace Osr\Tests\Models\Storage;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class SpammerIpTest extends OsrTestAbstract {
		
		public function testCreateDefaultLookupAccount(){
		
			$obj = new Storage\SpammerIp();
			$obj->ip = '192.168.1.1';
			$obj->email = 'testing@openspamregistry.com';
			$response = $obj->create();
			
			$this->assertTrue($response instanceof \Osr\Models\Storage\SpammerIp);
			$this->assertEquals($obj->ip, $response->ip);
			$this->assertEquals($obj->email, $response->email);
		}

		public function testCreate(){
				
			$obj = new Storage\SpammerIp();
			$obj->ip = rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100);
			$obj->email = 'someemail@' . uniqid() . '.com';
			$response = $obj->create();
				
			$this->assertTrue($response instanceof \Osr\Models\Storage\SpammerIp);
			$this->assertEquals($obj->ip, $response->ip);
			$this->assertEquals($obj->email, $response->email);
		}
		
		public function testGet(){
				
			$obj = new Storage\SpammerIp();
			$obj->ip = '192.168.1.1';
			$response = $obj->get();

			$this->assertTrue($response instanceof \Osr\Models\Storage\SpammerIp);
			$this->assertSame(long2ip($obj->ip), $response->ip);
			$this->assertTrue(!empty($response->email));
		}
		
	}