<?php

	namespace Osr\Tests\Controllers;

	use Osr\Registry;
	use Osr\Tests\OsrTestAbstract;
	use Osr\Models\Storage;
	use Symfony\Component\HttpFoundation\JsonResponse;

	class SpammerTest extends OsrTestAbstract {
		
		public function testGetAction(){
				
			$email = 'testing@openspamregistry.com';
				
			$crawler = $this->client->request('GET', '/spammer/' . $email);
			$response = $this->client->getResponse();
		
			$this->assertTrue($response instanceof JsonResponse);
			$this->assertTrue($response->getStatusCode() == 200);
		
			$arr = json_decode($response->getContent(), true);
			$data = $arr['data'];
		
			$this->assertEquals(200, $arr['code']);
			$this->assertTrue(isset($arr['data']));
			$this->assertTrue($arr['items'] == 1);
			$this->assertEquals($email, $data['email']);
			$this->assertTrue(!empty($data['ip']));
			$this->assertTrue(!empty($data['ip_string']));
			$this->assertTrue(!empty($data['date_created']));
			$this->assertTrue(!empty($data['date_updated']));
			$this->assertTrue(!empty($data['submitted_by_api_key']));
			$this->assertTrue(!empty($data['submitted_by_email']));
		
		}
		
		public function testGetActionNotFound(){
				
			$email = '83838shouldnotexist@fuckoff.com';
				
			$crawler = $this->client->request('GET', '/spammer/' . $email);
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
				'email' => 'spammer' . uniqid() . '@spam.com',
				'ip' => rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100) . '.' . rand(1,100),
				'api_key' => 'thisissomeapikey'
			);
			
			$crawler = $this->client->request('POST', '/spammer', $params);
			$response = $this->client->getResponse();
			
			$this->assertTrue($response instanceof JsonResponse);
			$this->assertTrue($response->getStatusCode() == 201);
			
			$arr = json_decode($response->getContent(), true);
			Registry::get('logger')->addInfo(print_r($arr, true));
			$data = $arr['data'];
			
			$this->assertEquals($params['email'], $data['email']);
			$this->assertEquals($params['ip'], $data['ip']);

		}

	}