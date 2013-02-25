<?php

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

use Osr\Api;
use Osr\Models\Storage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Api();

/**
 * Default landing page
 */
$app->get('/create', function(){

	$obj = new Storage\User();
	$obj->createTable();
	
	$obj = new Storage\Spammer();
	$obj->createTable();
	
	$obj = new Storage\ApiKey();
	$obj->createTable();
	
	$obj = new Storage\SpammerIp();
	$obj->createTable();
	
	$obj = new Storage\SpammerLastSeen();
	$obj->createTable();
	
	$obj = new Storage\UserSpammer();
	$obj->createTable();

	return new Symfony\Component\HttpFoundation\Response("Api documentation should go here - or redirect to openspamregistry.com");
});

$app->get('/delete-all', function(){
	
	$tables = array();
	
	$obj = new Storage\User();
	$tables[] = $obj->getTableName();

	$obj = new Storage\Spammer();
	$tables[] = $obj->getTableName();
	
	$obj = new Storage\ApiKey();
	$tables[] = $obj->getTableName();
	
	$obj = new Storage\SpammerIp();
	$tables[] = $obj->getTableName();
	
	$obj = new Storage\SpammerLastSeen();
	$tables[] = $obj->getTableName();
	
	$obj = new Storage\UserSpammer();
	$tables[] = $obj->getTableName();
	
	foreach ($tables as $table){

		foreach (array($table, 'test_' . $table) as $tn){
			//$obj->getAdapter()->getClient()->deleteTable(array('TableName' => $tn));
		}
		
	}
	
});

$app->run();