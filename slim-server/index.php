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
    $dbconnect = (new MongoDB\Client("mongodb://localhost:27017/flashcards-react-jerry"));
    $collection = $dbconnect->selectCollection('flashcards-react-jerry', 'cards'); 
    $cursor = $collection->findOne([
        '_id' => $args['1'],
    ]);

    $response->getBody()->write(json_encode($cursor));
    return $response;
});

// POST /cards
//request body {"question":"Hallo","answer":"Du","tags":["js"]}
$app->post('/cards', function ($request, $response, $args) {
    $dbconnect = (new MongoDB\Client("mongodb://localhost:27017/flashcards-react-jerry"));
    $collection = $dbconnect->selectCollection('flashcards-react-jerry', 'cards'); 
    $postContent = $request->getParsedBody();
    /* NICHT AUSPROBIEREN, auf jeden FAll nur in abgwandelter Form
    //hat beim letzten MAl alles zerschossen
    $cursor = $collection->insertOne([
        'question' => $args['question'],
        'answer' => $args['answer'],
        'tags' => $args['tags']
    ]);
    */
    $response->getBody()->write($postContent);
});


/*
$app->patch('/cards/{id}', function ($request, $response, $args) {
    $dbconnect = (new MongoDB\Client("mongodb://localhost:27017/flashcards-react-jerry"));
    $collection = $dbconnect->selectCollection('flashcards-react-jerry', 'cards'); 
    $cursor = $collection->updateOne(
        [
            '_id' => $args['1'],
        ], 
        [
            $args['1'],
        ]);
    $response->getBody()->write(json_encode($cursor));
    return $response;
});
*/

// DELETE /cards/all

// DELETE /cards/{id}


$app->addErrorMiddleware(true, false, false);
$app->run();
