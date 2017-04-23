<?php
// session
session_start();

// no time limit !!!
set_time_limit(0);

// 'SCRIPTOK' has to be defined for no direct access
define('SCRIPTOK',true);

/**
*
* configurations files
*
*/
require 'config.php';
require 'routes.php';

/**
*
* CORE
*
*/
require 'core/core.php';