<?php

use Osr\Api;
use Osr\Controllers;
use Osr\Models\Storage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Api();

/**
 * User end points
 */
$app->get('/user/{email}', array(new Controllers\User($app), 'getAction'));
$app->post('/user', array(new Controllers\User($app), 'postAction'));

/**
 * Spammer end points
 */
$app->get('/spammer/{email}', array(new Controllers\Spammer($app), 'getAction'));
$app->post('/spammer', array(new Controllers\Spammer($app), 'postAction'));


return $app;