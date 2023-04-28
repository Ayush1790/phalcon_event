<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Escaper;
use Phalcon\Flash\Direct as Flash;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
$loader->registerNamespaces([
    "handler\Listener" => APP_PATH . "/handlers/",
    "handler\Aware" => APP_PATH . "/handlers/",
]);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'escaper',
    function () {
        return new Escaper();
    }
);

// $container->set(
//     'flash',
//     function () {
//         return new Flash([
//             'error'   => 'alert alert-danger',
//             'success' => 'alert alert-success',
//             'notice'  => 'alert alert-info',
//             'warning' => 'alert alert-warning'
//         ]);
//     }
// );


// $di->set('flash', function () {
//     return new Flash([
//         'error'   => 'alert alert-danger',
//         'success' => 'alert alert-success',
//         'notice'  => 'alert alert-info',
//         'warning' => 'alert alert-warning'
//     ]);
// });


$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'testPhlacon',
            ]
        );
    }
);

$container->set(
    'mongo',
    function () {
        $mongo = new MongoClient();

        return $mongo->selectDB('phalt');
    },
    true
);


$application = new Application($container);
try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
