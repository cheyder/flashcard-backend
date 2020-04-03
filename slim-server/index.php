<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Middlewares\TrailingSlash;


require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->add(new TrailingSlash(false)); // remove trailing slashes

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write(file_get_contents('../client/build/index.html'));
    return $response;
});

$app->get('/cards', function (Request $request, Response $response, array $args) {
    $dbconnect = (new MongoDB\Client("mongodb://localhost:27017/flashcards-react-jerry"));
    $collection = $dbconnect->selectCollection('flashcards-react-jerry', 'cards'); 
    $cursor = $collection->find([]);
    $response->getBody()->write(json_encode($cursor->toArray()));
    return $response;
});

// GET /cards/{id}
$app->get('/cards/{id}', function (Request $request, Response $response, array $args) {
    $response->getBody()->write(file_get_contents('../client/build/index.html'));
    return $response;
});

// POST /cards

// PATCH /cards/{id}

// DELETE /cards/all

// DELETE /cards/{id}


$app->addErrorMiddleware(true, false, false);
$app->run();
