<?php

	namespace Osr\Tests;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;

	class AppTest extends OsrTestAbstract {
		
		
		public function testGetUser(){
			
		}
		
		public function testCreateUser(){

			$params = array(
				'email' => 'apirequest@' . uniqid() . '.com',
				'website' =>  uniqid() . '.com'
			);
			//$this->app->
			//$request = \Symfony\Component\HttpFoundation\Request::create('/user/ashwin@redinkdesign.net');
			//$response = $this->app->handle($request);
			$response = $this->client->request('GET', '/user/ashwin@redinkdesign.net');
			
// 			$this->assertTrue(
// 			    $this->client->getResponse()->headers->contains(
// 			        'Content-Type',
// 			        'application/json'
// 			    )
// 			);
			
			// Assert a specific 200 status code
// 			$this->assertEquals(
// 				200,
// 				$client->getResponse()->getStatusCode()
// 			);
			
			Registry::get('logger')->addInfo("API Request Response: " . print_r($response, true));
			
		}
		
	}