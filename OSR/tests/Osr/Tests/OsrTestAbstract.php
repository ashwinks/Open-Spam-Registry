<?php

	namespace Osr\Tests;

	use Osr;
	use Silex\Application;
	use Silex\WebTestCase;
	
	abstract class OsrTestAbstract extends WebTestCase {
		
		public $client;

		public function setUp(){
			
			parent::setUp();
			
			$this->app->register(new \Silex\Provider\MonologServiceProvider(), array(
				'monolog.logfile' => __DIR__ . '/app-tests.log'
			));
			
			\Osr\Registry::set('logger', $this->app['monolog']);
			
			$this->client = $this->createClient(
				array(
					'HTTP_HOST' => 'osr.local', 
					'SERVER_NAME' => 'osr.local'
				)
			);

		}
		
		public function createApplication(){

			$app = require __DIR__ . '/../../../src/app.php';
			
			return $app;
		}
		
		public function logger($mixed){
				
			if (is_array($mixed) || is_object($mixed)){
				$this->app['monolog']->addInfo(print_r($mixed, true));
			}else{
				$this->app['monolog']->addInfo($mixed);
			}
				
		}
	}