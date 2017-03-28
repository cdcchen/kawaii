#!/usr/bin/env php

<?php

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$config = __DIR__ . '/../config/app.php';
$app = new kawaii\web\Application($config);

//$app->hook(function (\kawaii\web\Context $context, callable $next) {
////    var_dump($next);
//    $context = $next($context);
//    $context->response->getBody()->write('HOOK #1<br />BEFORE');
//
////    echo '---------------------' . PHP_EOL;
////    var_dump((string)$response->getBody());
////    echo '---------------------' . PHP_EOL;
//    $context->response->getBody()->write(__FILE__);
//    $context->response->getBody()->write('AFTER<br />');
////    echo '-----------111111----------' . PHP_EOL;
////    var_dump((string)$response->getBody());
////    echo '------------111111---------' . PHP_EOL;
//
//    return $context;
//});
//
//$app->hook(function (
//    \Psr\Http\Message\RequestInterface $request,
//    \Psr\Http\Message\ResponseInterface $response,
//    callable $next
//) {
//    $response = $next($request, $response);
//    $response->getBody()->write('This is BEFORE.<br />');
//
//    $response = $response->withAddedHeader('name', 'chendong');
//    $response->getBody()->write('00000000');
//
//    $collection = new \kawaii\base\Collection([__FILE__, __CLASS__, __METHOD__]);
//    $collection->add($request);
//    $collection->add($request);
//    $collection->remove($request);
//    $response->getBody()->write(var_export($collection, true));
//    $response->getBody()->write($collection->contains($request) ? 'yes' : 'no');
//
//    return $response;
//});
//
//$app->hook(function (\kawaii\web\Request $request, \kawaii\web\Response $response, callable $next) {
//    $response = $next($request, $response);
//    $response->getBody()->write('<hr /><pre>');
//    $response->getBody()->write(var_export($request->getQueryParams(), true) . '<hr />');
//    $response->getBody()->write(var_export($request->getParsedBody(), true) . '<hr />');
//    $response->getBody()->write(var_export($request->getCookieParams(), true) . '<hr />');
//    $response->getBody()->write(var_export($request->getServerParams(), true) . '<hr />');
//    $response->getBody()->write(var_export($request->getHeaders(), true) . '<hr />');
//    $response->getBody()->write(var_export($request->getHeader('uni'), true) . '<hr /><hr />');
//    return $response;
//});
//
//$app->hook(function (\kawaii\web\Request $request, \kawaii\web\Response $response, callable $next) {
//    $response = $next($request, $response);
//    $response = $response->withHeader('ppppp', 'xxxx')
//                         ->withAddedHeader('ppppp', 'yyyy');
//
//    $response->getBody()->write(var_export($response->getHeader('ppppp'), true));
//    $response->getBody()->write('<hr />');
//    $response->getBody()->write(var_export(sys_getloadavg(), true));
//
//    $map = new \kawaii\base\HashMap(['name' => 'cdcchen']);
//    $response->getBody()->write(var_export($map->toArray(), true));
//
//    /* @var \kawaii\web\Response $response */
//    $response = $response->addCookie('testcookie', 'cookietest', time() + 10)
//                         ->addCookie('gogogog', 'hahaha');
//
//    return $response;
//});
//
//$app->get('/user/', function (\kawaii\web\Context $context, $next) {
//    /* @var \kawaii\web\Context $context */
//    $context = $next($context);
//    $context->response->getBody()->write('ROUTER #1 <br />');
//    return $context;
//});
//
//$app->get('/user/{uid:\d+}/post/{pid:\d+}',
//    function (\kawaii\web\Context $context, $next) {
//        /* @var \kawaii\web\Context $context */
//        $context = $next($context);
//        $context->response->getBody()->write(var_export($context->routeParams, true));
//        $context->response->getBody()->write('ROUTER #2 <br />');
//        return $context;
//    });
//$app->get('site/user/{uid:\w+}',
//    function (\kawaii\web\Context $context, $next) {
//        /* @var \kawaii\web\Context $context */
//        $context = $next($context);
//        $context->response->getBody()->write(var_export($context->routeParams, true));
//        $context->response->getBody()->write('<br />ROUTER #3 <br />');
//        return $context;
//    });
//$app->get('/', function (\kawaii\web\Context $context, $next) {
//    /* @var \kawaii\web\Context $context */
//    $context = $next($context);
//    $context->response->getBody()->write('Hello world!!!!!!');
//    return $context;
//});

$server = new \kawaii\server\HttpServer('127.0.0.1', 9512);
$server->run($app);
