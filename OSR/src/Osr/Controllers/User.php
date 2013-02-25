<?php

	namespace Osr\Controllers;
	
	use Symfony\Component\HttpFoundation\Request;

	use Osr\Registry;

	use Osr\Api;
	use Osr\Models\Storage;

	class User extends ControllerAbstract{

		public function postAction(Request $request){
			
			Registry::get('logger')->addInfo("In User postAction");

			$email = $request->get('email');
			$website = $request->get('website');
			
			try{
			
				$user = new Storage\User();
				$user->email = $email;
			
				// if this user already exists, return their record
				$exists = $user->get();
				if ($exists){
					Registry::get('logger')->addInfo("User {$email} already exists");
					return $this->_app->getJsonResponse(Api::CODE_POST_SUCCESS, $exists);
				}
			
				// user does not exist, create the user recrod
				if (!empty($website)){
					$user->website = $request->get('website');
				}
			
				$new = $user->create();
			
			
			}catch(Storage\StorageException $e){

				Registry::get('logger')->addInfo("StorageException thrown - Message: " . $e->getMessage());
				return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, $e->getMessage());
			
			}
			
			try{
			
				if ($new){
						
					Registry::get('logger')->addInfo("User successfully created - Email: {$new->email}");
						
					// create the api key record
					$api = new Storage\ApiKey();
					$api->api_key = $new->api_key;
					$api->email = $new->email;
						
					$response = $api->create();
		
					if (!$response){
			
						Registry::get('logger')->addInfo("Deleting user - ApiKey save failed");
			
						// delete user
						$user->delete();
			
						return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, "Could not create user");
			
					}
					
					Registry::get('logger')->addInfo("ApiKey saved successfully");
						
					return $this->_app->getJsonResponse(Api::CODE_POST_SUCCESS, $new);
						
				}
			
			}catch(Storage\StorageException $e){
			
				// delete user
				Registry::get('logger')->addInfo("Deleting user - StorageException: " . $e->getMessage() . $e->getTraceAsString());
				$user->delete();
			
				return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, $e->getMessage());
			
			}catch(\Exception $e){
			
				// delete user
				Registry::get('logger')->addInfo("Deleting user - Regular Exception: " . $e->getMessage() . $e->getTraceAsString());
				// delete user
				$user->delete();
			
				return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, $e->getMessage());
			
			}
			
		}
		
		/**
		 * Get a user account by email address
		 * 
		 * @param string $email
		 */
		public function getAction($email){
			
			try{
			
				$user = new Storage\User();
				$user->email = $email;
				$found = $user->get();
			
				if ($found){
					return $this->_app->getJsonResponse(Api::CODE_GET_SUCCESS, $found);
				}else{
					return $this->_app->getJsonResponse(Api::CODE_NOT_FOUND);
				}
			
			}catch(Storage\StorageException $e){
			
				return $this->_app->getJsonResponse(Api::CODE_SERVER_ERROR, $e->getMessage());
			
			}
			
		}
	}