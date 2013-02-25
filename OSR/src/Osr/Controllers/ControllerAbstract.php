<?php

	namespace Osr\Controllers;
		
	use Silex\Application;

	abstract class ControllerAbstract {
		
		protected $_app;
		
		public function __construct(Application $app){
		
			$this->_app = $app;
	
		}
		
	}