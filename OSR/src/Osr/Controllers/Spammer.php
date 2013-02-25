<?php

	namespace Osr\Controllers;
	
	use Symfony\Component\HttpFoundation\Request;

	use Osr\Registry;

	use Osr\Api;
	use Osr\Models\Storage;

	class Spammer extends ControllerAbstract{

		/**
		 * Get a spammer by email address
		 * 
		 * @param string $email
		 */
		public function getAction($email){
			
			try{
			
				$spammer = new Storage\Spammer();
				$spammer->email = $email;
				$found = $spammer->get();
			
				if ($found){
					return $this->_app->getJsonResponse(Api::CODE_GET_SUCCESS, $found);
				}else{
					return $this->_app->getJsonResponse(Api::CODE_NOT_FOUND);
				}
			
			}catch(Storage\StorageException $e){
			
				return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, $e->getMessage());
			
			}
			
		}
		
		public function postAction(Request $request){
			
			Registry::get('logger')->addInfo("Attempting to add a spammer...");
			
			$email = $request->get('email');
			if (empty($email)){
				return $this->_app->getJsonResponse(Api::CODE_BAD_REQUEST, "Invalid email address");
			}
			
			$api_key = $request->get('api_key');
			if (empty($api_key)){
				return $this->_app->getJsonResponse(Api::CODE_BAD_REQUEST, "Invalid api key");
			}
			
			$ip = $request->get('ip');
			
			try{
				
				// validate our api key first
				$ak = new Storage\ApiKey();
				$ak->api_key = $api_key;
				$valid_key = $ak->get();
				if (!$valid_key){
					return $this->_app->getJsonResponse(Api::CODE_BAD_REQUEST, "Could not verify api key {$api_key}");
				}
				Registry::get('logger')->addInfo("Api key is valid...");
				
			}catch (\Exception $e){
			
				Registry::get('logger')->addInfo("Api Key Validation - Exception - {$e->getMessage()}");
				return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, "Could not validate API key, please try again");
			}
			
			try{
				
				// api key is valid - create teh spammer
				$s = new Storage\Spammer();
				$s->email = $email;
				$s->submitted_by_api_key = $valid_key->api_key;
				$s->submitted_by_email = $valid_key->email;
				if (!empty($ip)){
					$s->ip = $ip;
				}
				Registry::get('logger')->addInfo('Creating spammer: ' . print_r($s->toArray(), true));
				$new_spammer = $s->create();
				if (!$new_spammer){
					return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, "Could not add spammer because of a server error. Please try again");
				}
				
				Registry::get('logger')->addInfo("New spammer {$new_spammer->email} created successfully");
				
			}catch (\Exception $e){
					
				Registry::get('logger')->addInfo("Creating spammer - Exception - {$e->getMessage()}");
				return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, "Could not add spammer because of a server error. Please try again");
			}
			
			// create an entry in the spammer ip table
			if (!empty($new_spammer->ip)){
				$this->_createSpammerIpRecord($new_spammer->email, $new_spammer->ip_string);
			}
				
			// create entry in last seen table 
			if ($new_spammer){
				$this->_createLastSeenRecord($new_spammer->email);
			}
			
			// create a record in the user spammer submissions table
			if (!empty($new_spammer->email)){
				
				try{
					
					Registry::get('logger')->addInfo("Adding this spammer to the user submissions table {$new_spammer->email}");
					
					$us = new Storage\UserSpammer();
					$us->user_email = $valid_key->email;
					$us->spammer_email = $new_spammer->email;
					$us->date = $new_spammer->date_created;
					
					$us_new = $us->create();
					if ($us_new){
						Registry::get('logger')->addInfo("User spammer submission table entry added successfully");
					}
					
				}catch (\Exception $e){
					
					Registry::get('logger')->addInfo("Adding spammer to user submissions table - Exception - {$e->getMessage()}");
					
				}

			}
			
			$response = array(
				'email' => $new_spammer->email,
				'ip' => $new_spammer->ip_string,
				'date_created' => $new_spammer->date_created
			);
			
			return $this->_app->getJsonResponse(Api::CODE_POST_SUCCESS, $response);
	
		}
		
		private function _createLastSeenRecord($email_address){
			
			try{
					
				Registry::get('logger')->addInfo("Adding this spammer to the last seen table {$email_address}");
				
				$ls = new Storage\SpammerLastSeen();
				$ls->email = $email_address;
				$ls_new = $ls->create();
				
				if ($ls_new){
					Registry::get('logger')->addInfo("Spammer successfully added to the last seen table {$email_address}");
					return true;
				}

				return false;
				
			}catch (\Exception $e){
				
				Registry::get('logger')->addInfo("Adding spammer to last seen table - Exception - {$e->getMessage()}");
				
			}
			
		}
		
		private function _createSpammerIpRecord($email, $ip_address){
			
			try{
					
				Registry::get('logger')->addInfo("We have an ip address {$ip_address} - adding to IP table");
					
				$ip = new Storage\SpammerIp();
				$ip->email = $email;
				$ip->ip = $ip_address;
	
				$new_spammer_ip = $ip->create();
					
				if ($new_spammer_ip){
					Registry::get('logger')->addInfo("New spammer IP added successfully IP: {$ip_address} - Email: {$email}");
					return true;
				}
				
				return false;
					
			}catch (\Exception $e){
					
				Registry::get('logger')->addInfo("Adding spammer IP - Exception - {$e->getMessage()}");
					
			}
			
		}
		
	}