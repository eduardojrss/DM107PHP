<?php
	 use \Psr\Http\Message\ServerRequestInterface as Request;
	 use \Psr\Http\Message\ResponseInterface as Response;
	 require '../vendor/autoload.php';
 
	$config['displayErrorDetails'] = true;
	$config['addContentLengthHeader'] = false;

	$config['db']['host'] = "localhost";
	$config['db']['user'] = "root";
	$config['db']['pass'] = "";
	$config['db']['dbname'] = "produtodb";

	$app = new \Slim\App(["config" => $config]);
	$container = $app->getContainer();
	
	$container['db'] = function ($c) {
		$dbConfig = $c['config']['db'];
		$pdo = new PDO("mysql:host=" . $dbConfig['host'] . ";dbname=" .
		$dbConfig['dbname'],
		$dbConfig['user'], $dbConfig['pass']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
		PDO::FETCH_ASSOC);
		$db = new NotORM($pdo);
		return $db;
	};

	//buscando entrega pelo id
	$app->get('/entrega/{id}', function (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$entrega = $this->db->entrega()->where('id', $id)->fetch();

		return $response->withJson($entrega);
	});
	
	//inserindo dados de uma entrega no banco
	$app->post('/entrega', function (Request $request, Response $response) {
		$json = $request->getBody();
		$entrega = (array) json_decode($json, true);
		$result = $this->db->entrega()->insert($entrega);
		return $response->withJson($result);
	});
	
	//atualizando dados de uma entrega no banco de dados
	$app->put('/entrega/{id}', function (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$entrega = $this->db->entrega()->where('id', $id)->fetch();
		
		$json = $request->getBody();
		$entregaToUpdate = (array) json_decode($json, true);
		
		$updated = $entrega->update($entregaToUpdate);

		return $response->withJson($updated);
	});
	
	//deletando uma entrega
	$app->delete('/entrega/{id}', function (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$entrega = $this->db->entrega()->where('id', $id)->fetch();
		
		$deleted = $entrega->delete();

		return $response->withJson($deleted);
	});
	
	//definindo autenticação do serviço.
	$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
	 "users" => [
	 "admin" => "admin"
	 ]
	 ]));
	
	$app->run();
?>