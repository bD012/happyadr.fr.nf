<?php

/*
 * Routes definition for Router
 *
 * [*]/controller@call/{varname}
 *  * : Main Controller
 */


/*
 * 
 *
 */
$routes['/'] = '*/happyadr@index';

/*
 * API : first API for faster answer
 *
 *  '_' = ' '
 *  '$' means AND
 *  '+' means OR
 *  "search" : exact search
 *
 * /api/city/95390
 * /api/city/saint_prix
 * /api/city/saint+prix
 * /api/city/saint$prix
 *
 * /api/street/
 */
$routes['/api/city/#postcode'] = '*/api@city/{postcode}';
$routes['/api/city/#city'] = '*/api@city/{city}';
$routes['/api/street/#street*'] = '*/api@street/{street}';
$routes['/api/address/#address{2,50}'] = '*/api@address/{search}';
$routes['/api/#all'] = '*/api@error403';

/*
 * FRONT
 *
 */
$routes['/happyadr'] = '*/happyadr@index';
$routes['/happyadr/index'] = '*/happyadr@index';

$routes['/developer/index'] = '*/developer@index';

$routes['/contact/contact'] = '*/contact@contact';

$routes['/user/signin'] = '/user@signin';
$routes['/user/signin/#char{1,12}'] = '/user@signin/{switch}';
$routes['/user/signout'] = '/user@signout';
$routes['/user/account'] = '/user@account';
$routes['/user/register'] = '/user@register';
$routes['/user/register/#char{1,12}'] = '/user@register/{switch}';
//$routes['/happyadr/index/user/signin'] = array('browser' => 'happyadr@index', 'ajax' => 'user@signin');


/*
 * BACK
 *
 */
$routes['/backoffice'] = '*/backoffice@index';
$routes['/backoffice/index'] = '*/backoffice@index';

$routes['/backoffice/update-insee/all'] = '*/backoffice@update_insee/{howmany}';
$routes['/backoffice/update-insee/all/#num{1,2}/#num{1}'] = '*/backoffice@update_insee/{howmany}/{index}/{step}';
$routes['/backoffice/update-insee/one/#num{1,2}'] = '*/backoffice@update_insee/{howmany}/{index}';
$routes['/backoffice/update-insee/one/#num{1,2}/#num{1}'] = '*/backoffice@update_insee/{howmany}/{index}/{step}';

$routes['/backoffice/delete-insee/one/#num{1,2}'] = '*/backoffice@delete_insee/{howmany}/{index}';
$routes['/backoffice/delete-insee/one/#num{1,2}/#num{1}'] = '*/backoffice@delete_insee/{howmany}/{index}/{step}';


$routes['/backoffice/update-database/all'] = '*/backoffice@update_database/{howmany}';
$routes['/backoffice/update-database/all/#num{1,2}/#num{1}'] = '*/backoffice@update_database/{howmany}/{index}/{step}';
$routes['/backoffice/update-database/one/#num{1,2}'] = '*/backoffice@update_database/{howmany}/{index}';
$routes['/backoffice/update-database/one/#num{1,2}/#num{1}'] = '*/backoffice@update_database/{howmany}/{index}/{step}';

$routes['/backoffice/delete-database/one/#num{1,2}'] = '*/backoffice@delete_database/{howmany}/{index}';
$routes['/backoffice/delete-database/one/#num{1,2}/#num{1}'] = '*/backoffice@delete_database/{howmany}/{index}/{step}';
$routes['/backoffice/delete-database/all'] = '*/backoffice@delete_database/{howmany}';
$routes['/backoffice/delete-database/all/#num{1}'] = '*/backoffice@delete_database/{howmany}/{step}';
