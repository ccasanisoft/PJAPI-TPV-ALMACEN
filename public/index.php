<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Allow from any Origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: false');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

}
// Set up dependencies
require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../App/Controllers/GuiaController.php';

require __DIR__ . '/../App/Models/Db.php';
require __DIR__ . '/../App/Models/GuiaModel.php';

require __DIR__ . '/../App/Entity/EntityReferralGuide.php';
require __DIR__ . '/../App/Entity/EntityReferralGuideItems.php';
require __DIR__ . '/../App/Entity/EntityInfoEmpresa.php';
require __DIR__ . '/../App/Entity/EntitySendInvoiceGR.php';


// Register middleware
require __DIR__ . '/../src/middleware.php';



// Register routes
require __DIR__ . '/../src/routes.php';




// ************************************************
// *************Datos de configuracion*************
// ************************************************
/*putenv('localhost=localhost');
putenv('dbname=BDEmpresasTPV');
putenv('db_user_name=root');
putenv('db_password=123');*/
putenv('WSurl_AWS=http://www.ccasanisoft.com:8080/FEApi/api/');
putenv('WSurl_general=http://localhost:81/PJAPI-GENERAL/public/api/');
// ************************************************

// Run app
$app->run();
