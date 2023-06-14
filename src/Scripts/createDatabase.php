<?php

declare (strict_types=1);

namespace Tobizzlelol;

use Tobizzlelol\Departures\Classes\departureController;

if (PHP_SAPI !== 'cli') {
    exit;
}

$root_app = dirname(__DIR__);

if (!is_file($root_app . '/vendor/autoload.php')) {
    $root_app = dirname(__DIR__, 4);
}

require $root_app . '/vendor/autoload.php';

$command = new createDatabase();
$command->run();

final class createDatabase
{
    public const HTTP_OK = "HTTP/1.1 200 OK";

    /**
     * @var string
     */
    public const HTTP_UNAUTHORIZED = "HTTP/1.1 401 Unauthorized";

    public function run(): void
    {
        $departureController = new departureController('mysql:dbname=db;host=ddev-departures-db:3306','db','db');
        $departureController->update();
    }
}