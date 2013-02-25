<?php

	namespace Osr\Models\Storage;
	
	interface AdapterInterface {
		
		public function create(Object $object);
		public function update(Object $object);
		public function delete(Object $object);
		public function get(Object $object);
		
	}