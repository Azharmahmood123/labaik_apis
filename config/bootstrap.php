<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__.'/config.php';
require_once __DIR__.'/response.php';
require_once __DIR__.'/database.php';
require_once __DIR__.'/helper.php';
require_once __DIR__.'/auth.php';
require_once __DIR__ . '/../classes/ApiRequest.php';
require_once __DIR__ . '/../classes/Validator.php';

authenticate();