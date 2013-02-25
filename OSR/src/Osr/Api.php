<?php 

	namespace Osr;
	
	class Api extends \Silex\Application {
		
		private $_startTime;
		private $_endTime;
		
		private $_response = array();
		
		const api_version = "1.0";
		
		const CODE_GET_SUCCESS = 200;
        const CODE_POST_SUCCESS = 201;
        const CODE_PUT_SUCCESS = 200;
        const CODE_DELETE_SUCCESS = 204;
        const CODE_NO_CONTENT = 204;
        const CODE_UNAUTHORIZED = 401;
        const CODE_NOT_FOUND = 404;
        const CODE_BAD_REQUEST = 400;
        const CODE_SERVER_ERROR = 500;
        
        protected $_debug = true;
		
		public function __construct(){

			$this->_startTime = microtime(true);
			$this->_response['version'] = self::api_version;
			
			parent::__construct();
			
			$this->register(new \Silex\Provider\MonologServiceProvider(), array(
				'monolog.logfile' => __DIR__ . '/app.log'
			));
			
			Registry::set('logger', $this['monolog']);
			
		}
		
		public function logger($mixed){
			
			if (is_array($mixed) || is_object($mixed)){
				$this['monolog']->addInfo(print_r($mixed, true));
			}else{
				$this['monolog']->addInfo($mixed);
			}
			
		}
		
		public function getJsonResponse($code, $data = array()){

			return $this->json($this->getResponse($code, $data), $code);
			
		}
		
		public function getResponse($code, $data = array()){
			
			if (is_object($data)){
				$data = $data->toArray();
			}
				
			if (isset($data[0]) && is_array($data[0])){
				$this->_response['items'] = count($data);
			}elseif (empty($data) || is_string($data)){
				$this->_response['items'] = 0;
			}else{
				$this->_response['items'] = 1;
			}
			
			$this->_response['code'] = (int) $code;
			if ($code >= 400){
				$this->_response['success'] = false;
				$this->_response['data'] = null;
				$this->_response['message'] = $data;
			}else{
				$this->_response['success'] = true;
				$this->_response['data'] = $data;
			}
			
			$this->_response['response_time'] = round((microtime(true) - $this->_startTime), 4);
			
			return $this->_response;
			
		}
		
	}