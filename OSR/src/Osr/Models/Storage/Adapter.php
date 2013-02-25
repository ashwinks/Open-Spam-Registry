<?php

	namespace Osr\Models\Storage;
	
	use Monolog\Logger;

	use Osr\Registry;

	use Aws\DynamoDb\Enum\AttributeAction;
	use Aws\DynamoDb\Enum\ReturnValue;

	use Aws\Common\Aws;
	use Aws\DynamoDb\Enum;
	use Aws\Common\Enum\Region;
	use Aws\DynamoDb\DynamoDbClient;
	use Aws\DynamoDb\Exception\DynamoDbException;
	
	class Adapter implements AdapterInterface{
		
		private $_client;
		private $_batch_requests = null;
	
		public function __construct(){
			
			$aws = Aws::factory(array(
				'key' => '',
				'secret' => '',
				'region' => Region::US_EAST_1
			));
			
			$this->_client = $aws->get('dynamodb');

		}
		
		public function getClient(){
			
			return $this->_client;
			
		}
		
		public function update(Object $object){
			
			$meta = $object->getMeta();
		
			$request = array(
				'TableName' => $object->getTableName()
			);

			$request['Key']['HashKeyElement'] = $this->getClient()->formatValue($object->{$meta['HashKeyElement']});
			
			if (isset($meta['RangeKeyElement']) && !empty($meta['RangeKeyElement'])){
				$request['Key']['RangeKeyElement'] = $this->getClient()->formatValue($object->{$meta['RangeKeyElement']});
			}
				
			$request['AttributeUpdates'] = array();

			$data = $object->toArray();
			unset($data[$meta['HashKeyElement']]);
			
			foreach ($data as $k => $v){
				$tmp = array(
					'Action' => AttributeAction::PUT,
					'Value' => $this->getClient()->formatValue($v)
				);
				$request['AttributeUpdates'][$k] = $tmp;
				unset($tmp);
			}
			
			$request['ReturnValues'] = ReturnValue::ALL_OLD;

			$response = $this->getClient()->updateItem($request);
			
			if ($response->hasKey('Attributes')){
				return $object;	
			}else{
				return false;
			}
			
		}
		
		public function create(Object $object){
			
			if ($this->isBatch()){
				$this->addBatchCreate($object);
				return $this;;
			}
			
			$meta = $object->getMeta();
			
			$request = array(
				'TableName' => $object->getTableName()
			);
			
			$data = $object->toArray();
			$request['Item'] = $this->getClient()->formatAttributes($data);

			$response = $this->getClient()->putItem($request);

			if ($response->hasKey('ConsumedCapacityUnits')){
				return $object;
			}else{
				return false;
			}
			
		}
		
		public function addBatchCreate(Object $object){
			
			$this->initBatch();
			
			$meta = $object->getMeta();
			$table_name = $object->getTableName();
			
			if (!isset($this->_batch_requests['RequestItems'][$table_name]) || !is_array($this->_batch_requests['RequestItems'][$table_name])){
				$this->_batch_requests['RequestItems'][$table_name] = array();
			}
			
			$request = array();
			$request['PutRequest'] = array();
			$data = $object->toArray();
			$request['PutRequest']['Item'] = $this->getClient()->formatAttributes($data);
	
			return $this->_addToBatch($table_name, $request);
		}
		
		public function get(Object $object, $consistentRead = false, array $fields = array()){

			$meta = $object->getMeta();
			
			$this->_validateObject($object);
			
			$request = array(
				'TableName' => $object->getTableName(),
				'ConsistentRead' => $consistentRead
			);

			$request['Key']['HashKeyElement'] = $this->getClient()->formatValue($object->{$meta['HashKeyElement']});
			
			if (isset($meta['RangeKeyElement']) && !empty($meta['RangeKeyElement'])){
				$request['Key']['RangeKeyElement'] = $this->getClient()->formatValue($object->{$meta['RangeKeyElement']});
			}

			if (count($fields) > 0){
				$request['AttributesToGet'] = $fields;
			}
			
			//Registry::get('logger')->addInfo("Request: " . print_r($request, true));
			
			$response = $this->getClient()->getItem($request);
			
			//Registry::get('logger')->addInfo('Response: ' . print_r($response, true));
			
			if ($response->hasKey('Item')){
				
				$obj_type = get_class($object);
				$found = new $obj_type($this->toArrayFromItem($response->get('Item')), $this);
				
				return $found;
				
			}else{
				
				return false;
			}

		}
		
		public function delete(Object $object){
			
			$meta = $object->getMeta();
			
			$this->_validateObject($object);
			
			$request = array(
				'TableName' => $object->getTableName()
			);

			$request['Key']['HashKeyElement'] = $this->getClient()->formatValue($object->{$meta['HashKeyElement']});
				
			if (isset($meta['RangeKeyElement']) && !empty($meta['RangeKeyElement'])){
				$request['Key']['RangeKeyElement'] = $this->getClient()->formatValue($object->{$meta['RangeKeyElement']});
			}
			
			$response = $this->getClient()->deleteItem($request);
			
			if ($response->hasKey('Attributes')){
				return true;
			}else{
				return false;
			}
			
		}
		
		public function createTable(Object $object){
			
			$meta = $object->getMeta();
			$this->_validateObject($object);
			$table_name = $object->getTableName();
	
			// create both the test table and the regular table
			foreach (array($table_name, 'test_' . $table_name) as $tname){
			
				$request = array();
				$request['TableName'] = $tname;
				$request['KeySchema']['HashKeyElement']['AttributeName'] = $meta['HashKeyElement'];
				$request['KeySchema']['HashKeyElement']['AttributeType'] = $meta['HashKeyElementType'];
				
				if (isset($meta['RangeKeyElement']) && !empty($meta['RangeKeyElement'])){
					
					$request['KeySchema']['RangeKeyElement']['AttributeName'] = $meta['RangeKeyElement'];
					$request['KeySchema']['RangeKeyElement']['AttributeType'] = $meta['RangeKeyElementType'];
		
				}
				
				if (isset($meta['ReadCapacityUnits']) && $meta['ReadCapacityUnits'] > 0){
					$request['ProvisionedThroughput']['ReadCapacityUnits'] = $meta['ReadCapacityUnits'];
				}else{
					$request['ProvisionedThroughput']['ReadCapacityUnits'] = 10;
				}
				
				if (isset($meta['WriteCapacityUnits']) && $meta['WriteCapacityUnits'] > 0){
					$request['ProvisionedThroughput']['WriteCapacityUnits'] = $meta['WriteCapacityUnits'];
				}else{
					$request['ProvisionedThroughput']['WriteCapacityUnits'] = 5;
				}
	
				$response = $this->getClient()->createTable($request);
				
				Registry::get('logger')->addInfo(print_r($response, true));
				
			}

		}
		
		private function _validateObject(Object $object){
			
			$meta = $object->getMeta();
			if (empty($meta['HashKeyElement'])){
				throw new StorageException("Invalid meta attribute 'HashKeyElement'. This should be the name of the PrimaryKey field.");
			}
				
			if (empty($meta['HashKeyElementType'])){
				throw new StorageException("Invalid meta attribute 'HashKeyElementType'. This should be either S for string, N for numeric or B for binary.");
			}
				
			$table_name = $object->getTableName();
			if (empty($table_name)){
				throw new StorageException("Invalid table name");
			}
			
		}
		
		public function isBatch(){
			return is_array($this->_batch_requests);
		}
		
		public function initBatch(){
			
			if (!is_array($this->_batch_requests)){
				$this->_batch_requests = array();
			}
			
			if (!isset($this->_batch_requests['RequestItems']) || !is_array($this->_batch_requests['RequestItems'])){
				$this->_batch_requests['RequestItems'] = array();
			}
			
			return $this;
		}
		
		private function _addToBatch($table_name, $request){
			
			array_push($this->_batch_requests['RequestItems'][$table_name], $request);
			
			return $this;
			
		}
		
		public function processBatch(){
			
			$responses = array();
			$responses['success'] = array();
			$responses['failed'] = array();
			$i = 0;
			foreach ($this->_batch_requests['RequestItems'] as $table => $payload){
				
				if (count($this->_batch_requests['RequestItems'][$table]) <= 25){
					
					Registry::get('logger')->addInfo("Less than 25, doin regular batch");
					$response = $this->getClient()->batchWriteItem($this->_batch_requests);
					array_push($responses['success'], $response->get('Responses'));
					array_push($responses['failed'], $response->get('UnprocessedItems'));
					
				}else{
					
					$tmp_reqs = array_chunk($this->_batch_requests['RequestItems'][$table], 25);
					foreach ($tmp_reqs as $req){
						$batch_temp_req = array(
							'RequestItems' => array(
								$table => $req	
							)
						);
						Registry::get('logger')->addInfo("Doing batch Batch: " . print_r($batch_temp_req, true));
						$response = $this->getClient()->batchWriteItem($batch_temp_req);
						array_push($responses['success'], $response->get('Responses'));
						array_push($responses['failed'], $response->get('UnprocessedItems'));
					}
				}
				
			}

			return $responses;

		}

		public function getBatchRequests(){
			return $this->_batch_requests;
		}
		protected function toArrayFromItem($data){
			
			$ar = array();
			foreach ($data as $k => $v){
				list($type, $value) = each($v);
				$ar[$k] = $value;
			}
			
			return $ar;
			
		}

	}