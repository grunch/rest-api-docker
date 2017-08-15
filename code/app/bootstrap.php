<?php

if (!file_exists($autoloadFile = BASE_DIR . '/vendor/autoload.php')) {
    die('You must set up the project dependencies, use composer for this.');
}

require $autoloadFile;
