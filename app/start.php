<?php

use Aura\SqlQuery\QueryFactory;
use DI\ContainerBuilder;
use League\Plates\Engine;

$containerBuilder = new ContainerBuilder;

$containerBuilder->addDefinitions([
    Engine::class    =>  function() {
        return new Engine('../app/views');
    },
    QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },
    PDO::class => function() {
        return new PDO("mysql:host=localhost;dbname=test","root","");
    }
]);

$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\controllers\TasksController', 'index']);
    $r->addRoute('GET', '/tasks', ['App\controllers\TasksController', 'index']);
    $r->addRoute('GET', '/tasks/{id:\d+}', ['App\controllers\TasksController', 'show']);
    $r->addRoute('GET', '/tasks/{id:\d+}/edit', ['App\controllers\TasksController', 'edit']);
    $r->addRoute('POST', '/tasks/{id:\d+}/update', ['App\controllers\TasksController', 'update']);
    $r->addRoute('GET', '/tasks/create', ['App\controllers\TasksController', 'create']);
    $r->addRoute('POST', '/tasks/store', ['App\controllers\TasksController', 'store']);
    $r->addRoute('GET', '/tasks/{id:\d+}/delete', ['App\controllers\TasksController', 'delete']);
    // $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ...
        dd('404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ...
        dd('405 Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $container->call($handler, $vars);
        // ... call $handler with $vars
        break;
}