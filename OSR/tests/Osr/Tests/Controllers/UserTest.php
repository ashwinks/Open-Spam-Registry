<?php

	namespace Osr\Tests\Controllers;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;
	use Symfony\Component\HttpFoundation\JsonResponse;

	class UserTest extends OsrTestAbstract {
		
		public function testGetAction(){
			
			$email = 'testing@openspamregistry.com';
			
			$crawler = $this->client->request('GET', '/user/' . $email);
			$response = $this->client->getResponse();

			$this->assertTrue($response instanceof JsonResponse);
			$this->assertTrue($response->getStatusCode() == 200);

			$arr = json_decode($response->getContent(), true);
			$data = $arr['data'];
		
			$this->assertEquals(200, $arr['code']);
			$this->assertTrue(isset($arr['data']));
			$this->assertTrue($arr['items'] == 1);
			$this->assertEquals($email, $data['email']);
			$this->assertTrue(!empty($data['website']));
			$this->assertTrue(!empty($data['date_created']));
			$this->assertTrue(!empty($data['date_updated']));
	
		}
		
		public function testGetActionUserNotFound(){
			
			$email = '83838shouldnotexist@fuckoff.com';
			
			$crawler = $this->client->request('GET', '/user/' . $email);
			$response = $this->client->getResponse();

			$this->assertTrue($response instanceof JsonResponse);
			$this->assertTrue($response->getStatusCode() == 404);
			
 			$arr = json_decode($response->getContent(), true);
 			$data = $arr['data'];
			
 			$this->assertEquals(404, $arr['code']);
 			$this->assertTrue($data == null);
		
		}
		
		public function testPostAction(){
			
			$params = array(
				'email' => 'testemail@' . uniqid() . '.com',
				'website' => uniqid() . 'site.com'
			);
	
			$crawler = $this->client->request('POST', '/user', $params);
			$response = $this->client->getResponse();
			
			$this->assertTrue($response instanceof JsonResponse);
			$this->assertTrue($response->getStatusCode() == 201);
			
			$arr = json_decode($response->getContent(), true);
			$data = $arr['data'];
			
			$this->assertEquals(201, $arr['code']);
			$this->assertTrue(isset($arr['data']));
			$this->assertTrue($arr['items'] == 1);
			$this->assertEquals($params['email'], $data['email']);
			$this->assertEquals($params['website'], $data['website']);
			$this->assertTrue(!empty($data['api_key']));
			$this->assertTrue(!empty($data['api_secret']));
			$this->assertTrue(!empty($data['date_created']));
			$this->assertTrue(!empty($data['date_updated']));
			
		}
		
		public function testPostActionInvalidEmailAddress(){
			
			$crawler = $this->client->request('POST', '/user');
			$response = $this->client->getResponse();
			
			$this->assertTrue($response instanceof JsonResponse);
			$this->assertTrue($response->getStatusCode() == 500);
			
			$arr = json_decode($response->getContent(), true);
			$this->assertEquals("Invalid email address", $arr['message']);
			$this->assertFalse($arr['success']);
			$this->assertTrue($arr['data'] == null);

		}
		
	}