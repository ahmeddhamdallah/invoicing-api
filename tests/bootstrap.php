<?php

require_once __DIR__.'/../vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel;

$app = require __DIR__.'/../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap(); 