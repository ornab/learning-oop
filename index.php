<?php
require_once __DIR__ . '/vendor/autoload.php';

use RRF\Database as DB;

$array = [
    'database_name' => 'rrfcms',
    'database_user' => 'homestead',
    'database_pass' => 'secret',
    'database_host' => 'localhost',
];

$database = new DB($array);



