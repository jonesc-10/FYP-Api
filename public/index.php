<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

require '../includes/dbOps.php';

$app = AppFactory::create();

// Add Slim routing middleware
$app->addRoutingMiddleware();

// Set the base path to run the app in a subdirectory.
// This path is used in urlFor().
$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(true, true, true);

$app->post('/userlogin', function(Request $request, Response $response){

	if(!haveEmptyParameters(array('username', 'password'), $request, $response)){
		$request_data = $request->getParsedBody(); 

		$username = $request_data['username'];
		$password = $request_data['password'];

		$db = new dbOps; 

		$result = $db->userLogin($username, $password);

		if($result == USER_AUTHENTICATED){  
			$user = $db->getUserByUsername($username);
			$response_data = array();

			$response_data['error']=false; 
			$response_data['message'] = 'Login Successful';
			$response_data['user']=$user; 

			$response->getBody()->write(json_encode($response_data));

			return $response
				->withHeader('Content-type', 'application/json')
				->withStatus(200);    

		}else if($result == USER_NOT_FOUND){
			$response_data = array();

			$response_data['error']=true; 
			$response_data['message'] = 'User not exist';

			$response->getBody()->write(json_encode($response_data));

			return $response
				->withHeader('Content-type', 'application/json')
				->withStatus(200);    

		}else if($result == USER_NOT_AUTHENTICATED){
			$response_data = array();

			$response_data['error']=true; 
			$response_data['message'] = 'Invalid credential';

			$response->getBody()->write(json_encode($response_data));

			return $response
				->withHeader('Content-type', 'application/json')
				->withStatus(200);  
		}
	}

	return $response
		->withHeader('Content-type', 'application/json')
		->withStatus(422);    
});


### Function to check if parameters are empthy 

function haveEmptyParameters($required_params, $request, $response){
	$error = false; 
	$error_params = '';
	$request_params = $request->getParsedBody(); 

	foreach($required_params as $param){
		if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
			$error = true; 
			$error_params .= $param . ', ';
		}
	}

	if($error){
		$error_detail = array();
		$error_detail['error'] = true; 
		$error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
		$response->getBody()->write(json_encode($error_detail));
	}
	return $error; 
}


$app->run();