<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Middlewares\TrailingSlash;
use MongoDB\BSON\ObjectId;

require __DIR__ . '/vendor/autoload.php';


$app = AppFactory::create();
$app->add(new TrailingSlash(false));

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

//Don't know how to test this.
$app->get('/cards/{id}', function (Request $request, Response $response, array $args) {
    $dbconnect = (new MongoDB\Client("mongodb://localhost:27017/flashcards-react-jerry"));
    $collection = $dbconnect->selectCollection('flashcards-react-jerry', 'cards'); 
    $cursor = $collection->findOne([
        '_id' => $args['id'],
    ]);
    $response->getBody()->write(json_encode($cursor));
    return $response;
});

//Not a clue why this is working (after reload)
$app->post('/cards', function ($request, $response, $args) {
    $dbconnect = (new MongoDB\Client("mongodb://localhost:27017/flashcards-react-jerry"));
    $collection = $dbconnect->selectCollection('flashcards-react-jerry', 'cards'); 
    $postArray = $request->getParsedBody();
    $collection->insertOne($postArray);

    /* Why does the above insert work, but not the lower one 
    $collection->insertOne([
        'question' => $postArray['question'],
        'answer' => $postArray['answer'],
        'tags' => $postArray['tags']
    ]);
    */

    /* Why does this missing throws a 500, but everything else is still working (sometimes)?*/
    $html = var_export($postArray, true);
    $response->getBody()->write($html);
    return $response;
    
});


//Throws 200, response is fine, but not loaded from/written to db.
$app->patch('/cards/{id}', function ($request, $response, $args) {
    $dbconnect = (new MongoDB\Client("mongodb://localhost:27017/flashcards-react-jerry"));
    $collection = $dbconnect->selectCollection('flashcards-react-jerry', 'cards'); 
    $postArray = $request->getParsedBody();
    $collection->updateOne(
        ['_id' => $args['id']], 
        ['$set' => $postArray]);
    $html = var_export($postArray, true);
    $response->getBody()->write($html);
    return $response;
});



// DELETE /cards/all

// DELETE /cards/{id}

$app->addBodyParsingMiddleware(); //What is this Middleware for?
$app->addErrorMiddleware(true, false, false);
$app->run();
