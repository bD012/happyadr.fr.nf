<?php

namespace modules\api;

use core\controller\controller;
use core\http\http;
use core\router\router;
use core\session\session;
use core\token\token;
use core\user\user;

use modules\database\citiesModel;
use modules\database\streetsModel;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class ApiController
*
* This class manage all API requests.
*
*/
class ApiController extends Controller
{
	protected $controller = 'user';

	protected $feedback;

	/**
	* initialize()
	*
	* called by Controller::__construct
	*/
	public function initialize()
	{
	} // initialize()


	/**
	* _http_reponse()
	*
	* Prepare (header, status_code, utf8/latin, token) and send the API response.
	*
	* @param integer $code  Response http status code.
	* @param mixed $params  body response.
	*/
   protected function _http_response($code, $params = null)
	 {
		 $http_response_code = array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found'
		);

		header('HTTP/1.1 '.$code.' '.$http_response_code[$code]);
		header('Content-Type: application/json; charset=utf-8');

		$response['http_code'] = $code;
		$response['username'] = Token::get('username','admin');
		$response['data-size'] = count($params);

		// to UTF-8
		if ($params !== null) {
			$imax = count($params);
			for($i=0; $i<$imax; $i++) {
				foreach($params[$i] as $key => $value) {
					$response['data'][$i][$key] = utf8_encode($value);
				} // foreach
			}
			// $response['data'] = $params;
		} // if

  	$json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		echo $json;
		die();
	 } // ()



	/**
	* city()
	*
	* Manage API response for city request.
	* Create the MySql specific request, then pass it to _execute()
	* 
	*/
   public function city()
   {
		 $this->_first_checks();

     $postcode = Router::getVariable('postcode');
		//  $city = tosearch(Router::getVariable('city'));
		 $cities = mexplode(array('+','$'),tosearch(Router::getVariable('city')));

		 if ($postcode !== null) {
     	$query = "SELECT city, postcode, department FROM cities WHERE postcode = '$postcode';";
		 } else

		 if ($cities !== null) {
     	$query = "SELECT city, postcode, department FROM cities";

			foreach($cities as $city) {
				if ($city['separator'] == 'first') {
					$query .= " WHERE cities.search_city";
				} else 
				if ($city['separator'] == '+') {
					$query .= " OR cities.search_city";
				} else {
					$query .= " AND cities.search_city";
				} // if

				if ($city['operator'] == 'equal') {
					$query .= " =";
					$query .= " '$city[name]'";
				} else {
					$query .= " LIKE";
					$query .= " '%$city[name]%'";
				}
			} // foreach
			$query .= " LIMIT 0,12;";
		 }
		 
		 $this->_execute_query($query);
   } // city()

	/**
	* street()
	*
	* Manage API response for street request.
	* Create the MySql specific request, then pass it to _execute()
	*/
   public function street()
   {
		 $this->_first_checks();

		 $streets = mexplode(array('+','$'),tosearch(Router::getVariable('street')));

     $query = "SELECT streets.street, cities.city, cities.postcode, cities.department";
		 $query .= " FROM streets INNER JOIN cities ON cities.id = streets.id_city";

		 foreach($streets as $street) {
			 if ($street['separator'] == 'first') {
			 	$query .= " WHERE streets.search";
			 } else 
			 if ($street['separator'] == '+') {
				$query .= " OR streets.search";
			 } else {
				$query .= " AND streets.search";
			 } // if

			 if ($street['operator'] == 'equal') {
				$query .= " =";
			  $query .= " '$street[name]'";
			 } else {
				$query .= " LIKE";
			  $query .= " '%$street[name]%'";
			 }
		 } // foreach
		 $query .= " LIMIT 0,12;";

		 $this->_execute_query($query);
   } // street()


	/**
	* address()
	*
	* Manage API response for search request.
	* Create the MySql specific request, then pass it to _execute()
	*/
   public function address()
   {
		 $this->_first_checks();

		 $searchs = explode(',', tosearch(Router::getVariable('search')));

     $query = "SELECT streets.search,cities.city,cities.postcode,cities.department";
		 $query .= " FROM streets INNER JOIN cities ON streets.id_city=cities.id";

		 $first = true;
		 foreach($searchs as $search) {
			 if ($first) {
		 			$query .= " WHERE";
					 $first = false;
			 } else {
		 			$query .= " AND";
			 }
			 if (preg_match('#^[0-9]*$#',$search)) {
				 // is postcode
			 	 $query .= " CONCAT(cities.postcode) LIKE ";
				 $query .= "'$search%'";
			 } else {
				 // else
			 	 $query .= " CONCAT(streets.search,' ',cities.search_city,' ',cities.search_department) LIKE ";
				 $query .= "'%$search%'";
			 } // if
		 } // foreach

		//  $query .= "%'";
		 $query .= " LIMIT 0,99;";
		// print_r(utf8_encode($query));
	  $this->_execute_query($query);
   } // address()


	/**
	* error404()
	*
	* Manage 404 error (Not found).
	*/
	public function error404()
	{
		$this->_first_checks();

		$this->_http_response(404, null);
	} // error404()


	/**
	* error403()
	*
	* Manage 403 error (Forbidden). 
	*/
	public function error403()
	{
		$this->_first_checks();

		$this->_http_response(403, null);
	} // error403()




	/**
	* _execute_query()
	*
	* Execute the MySql query and manage 200 (OK) and 500 (internal error) status codes.
	*/
   protected function _execute_query($query)
   {
		 $db = new CitiesModel();

		 $status_code = 200;
		 $data = $db->raw($query);
		 if ($data === false) {
			 $status_code = 500;
			 $data = null;
		 } else 
		 if ($data === null) {
			 $status_code = 404;
			 $data = null;
		 }

		 $this->_http_response($status_code, $data);
	 } // _execute_query()

	/**
	* _first_checks()
	*
	* Manage first checks to do before managing the API request.
	* Manage the token and date validity.
	*/
	 protected function _first_checks()
	 {
		 // initialize
		 $return = null;

		// 401 : Unauthorized
		if (User::getUsername('admin', null) !== 'admin') {
			$status = 401;
			$ok = false;
			if (($gettoken = Http::get('token', null)) !== null) {
				Token::decode($gettoken);
				if (date('Y-m-d') === Token::get('date', null)) {
					$ok = true;
				} // if
			} // if
			if (!$ok) {
			$this->_http_response(401, null);
			} // if
		} // if


		 // 501 : PUT, POST, DELETE Not implemented
		 if ($_SERVER['REQUEST_METHOD'] != 'GET') {
			 $this->_http_response(501, null);
		 }
	 }
} // class Api