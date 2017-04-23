<?php

namespace modules\backoffice;

use core\controller\controller;
use core\ajax\ajax;
use core\router\router;
use core\user\user;

use modules\database\citiesModel;
use modules\database\streetsModel;

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class BackofficeController
*
* This class manage the backoffice.
*
*/
class BackofficeController extends Controller
{
  const INSEE = 'https://bano.openstreetmap.fr/data';
  const LOCAL = ROOT.'/public/insee';

	protected $controller = 'backoffice';

	/**
	* index()
	*
	* index Page.
	*/
  public function index()
  {

    global $config;

    debugTitle('Backoffice::index()');

    // Redirect to happyadr/index if not ADMIN
    if (User::getRole() != USER_ROLE_ADMIN) {
      Router::redirect('happyadr', 'index');
      return;
    }

    // create public directory if not exist
    if (!file_exists(self::LOCAL)) {
      mkdir(self::LOCAL);
    }

    // $sections[] = $this->getView('/Backoffice/introduction.html', array());
    // $sections[] = $this->getView('/Backoffice/description.html', array());
    // $sections[] = $this->getView('/Backoffice/documentation.html', array());

    $nav = $this->getView('/backoffice/nav.html', array());

    $date = $this->getFileDate(self::LOCAL.'/'.$this->getFileName(1));
    $insee['date'] = 'Last Update : ';
    $insee['date'] .= ($date !== null) ? ($date) : ('Never !');

    $insee['departments'] = serialize($this->insee());
    $insee = $this->getView('/backoffice/insee.html', $insee);

    $database['departments'] = serialize($this->database());
    $database = $this->getView('/backoffice/database.html', $database);

    $sections[] = $this->getView('/backoffice/backoffice.html', array(
      'insee' => $insee, 'database' => $database));

		$this->htmlRender(array(
		  'meta_description' => "happy{adr} is secured site based on INSEE files for adress in france, here is the backoffice where files and databases are managed.",
      'nav' => $nav,
      'sections' => $sections
      ));
  } // index


	/**
	* update_insee()
	*
	* Manage the update_insee button request, Ajax only...
	*/
  public function update_insee()
  {
		debugTitle('backofficeController::update_insee()');

    // Redirect to happyadr/index if not ADMIN
    if (User::getRole() != USER_ROLE_ADMIN) {
      Router::redirect('happyadr', 'index');
    }

    $index = Router::getVariable('index', 1);
    $indexdd = sprintf('%02d', $index);
    $howmany = Router::getVariable('howmany', 'one');
    $step = Router::getVariable('step', 1);
    $nextstep = $step + 1;

    switch($step)
    {
      case 1 :

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/update-insee/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $icon = array('icon' => 'download');
      $this->setView("backoffice-insee-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-insee-check-$indexdd";
      break;

      case 2:

      $filename = $this->getFileName($index);
      @unlink(self::LOCAL.'/'.$filename);
      @copy(self::INSEE.'/'.$filename, self::LOCAL.'/'.$filename);

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/update-insee/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $icon = array('icon' => 'question');
      $this->setView("backoffice-insee-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-insee-check-$indexdd";
      break;

      case 3:

      if (file_exists(self::LOCAL.'/'.$this->getFileName($index))) {
        $icon = array('icon' => 'checkmark');
      } else {
        $icon = array('icon' => 'cross');
      }

      if ($howmany != 'all') {
        $nextstep = 0;
      }

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/update-insee/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $this->setView("backoffice-insee-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-insee-check-$indexdd";
      break;

      case 4:
      if ($index <96) {
        $indexdd = sprintf('%02d', $index+1);

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/update-insee/$howmany/$indexdd/$nextstep",
          'delay' => '250'
        ));
      }

      $render = 'ajax';
      break;

      default:
        Ajax::add(array(
          'action' => 'setLoading',
          'value' => 'off'
        ));

      $render = 'ajax';
      break;
    } // switch

    $this->render($render);
  } // update_insee()


	/**
	* delete_insee()
	*
	* Manage the delete_insee button request, Ajax only...
	*/
  public function delete_insee()
  {
		debugTitle('backofficeController::delete_insee()');

    // Redirect to happyadr/index if not ADMIN
    if (User::getRole() != USER_ROLE_ADMIN) {
      Router::redirect('happyadr', 'index');
    }

    $index = Router::getVariable('index');
    $indexdd = sprintf('%02d', $index);
    $howmany = Router::getVariable('howmany', 'one');
    $step = Router::getVariable('step', 1);
    $nextstep = $step + 1;

    switch($step)
    {
      case 1 :

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/delete-insee/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $icon = array('icon' => 'cancel-circle');
      $this->setView("backoffice-insee-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-insee-check-$indexdd";
      break;

      case 2:

      $filename = $this->getFileName($index);
      @unlink(self::LOCAL.'/'.$filename);

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/delete-insee/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $icon = array('icon' => 'question');
      $this->setView("backoffice-insee-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-insee-check-$indexdd";
      break;

      case 3:

      if (file_exists(self::LOCAL.'/'.$this->getFileName($index))) {
        $icon = array('icon' => 'checkmark');
      } else {
        $icon = array('icon' => 'cross');
      }

      if ($howmany != 'all') {
        $nextstep = 0;
      }

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/delete-insee/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $this->setView("backoffice-insee-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-insee-check-$indexdd";
      break;


      default:
        Ajax::add(array(
          'action' => 'setLoading',
          'value' => 'off'
        ));

      $render = 'ajax';
      break;
    } // switch

    $this->render($render);
  } // delete_insee()




	/**
	* update_database()
	*
	* Manage the update_database button request, Ajax only...
	*/
  public function update_database()
  {
		debugTitle('backofficeController::update_database()');

    // Redirect to happyadr/index if not ADMIN
    if (User::getRole() != USER_ROLE_ADMIN) {
      Router::redirect('happyadr', 'index');
    }

    $index = Router::getVariable('index', 1);
    $indexdd = sprintf('%02d', $index);
    $howmany = Router::getVariable('howmany', 'one');
    $step = Router::getVariable('step', 1);
    $nextstep = $step + 1;

    switch($step)
    {
      case 1 :

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/update-database/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $icon = array('icon' => 'hour-glass');
      $this->setView("backoffice-database-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-database-check-$indexdd";
      break;

      case 2:

      $this->_cities_streets_update($index);

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/update-database/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $icon = array('icon' => 'question');
      $this->setView("backoffice-database-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-database-check-$indexdd";
      break;

      case 3:

      if ($this->_cities_has_department($index)) {
        $icon = array('icon' => 'checkmark');
      } else {
        $icon = array('icon' => 'cross');
      }

      if ($howmany != 'all') {
        $nextstep = 0;
      }

      Ajax::add(array(
        'action' => 'httpRequest',
        'uri' => WEBROOT."/backoffice/update-database/$howmany/$indexdd/$nextstep",
        'delay' => '250'
      ));

      $this->setView("backoffice-database-check-$indexdd",'/backoffice/icon.html', $icon);
      $render = "backoffice-database-check-$indexdd";
      break;

      case 4:
      if ($index <96) {
        $indexdd = sprintf('%02d', $index+1);

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/update-database/$howmany/$indexdd/$nextstep",
          'delay' => '250'
        ));
      }

      $render = 'ajax';
      break;


      default:
        Ajax::add(array(
          'action' => 'setLoading',
          'value' => 'off'
        ));

      $render = 'ajax';
      break;
    } // switch

    $this->render($render);
  } // update_database()


	/**
	* delete_database()
	*
	* Manage the delete_database button request, Ajax only...
	*/
  public function delete_database()
  {
		debugTitle('backofficeController::delete_database()');

    // Redirect to happyadr/index if not ADMIN
    if (User::getRole() != USER_ROLE_ADMIN) {
      Router::redirect('happyadr', 'index');
    }

    $index = Router::getVariable('index', 1);
    $indexdd = sprintf('%02d', $index);
    $howmany = Router::getVariable('howmany', 'one');
    $step = Router::getVariable('step', 1);
    $nextstep = $step + 1;

    if ($howmany == 'one') {
      switch($step)
      {
        case 1 :

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/delete-database/$howmany/$indexdd/$nextstep",
          'delay' => '250'
        ));

        $icon = array('icon' => 'cancel-circle');
        $this->setView("backoffice-database-check-$indexdd",'/backoffice/icon.html', $icon);
        $render = "backoffice-database-check-$indexdd";
        break;

        case 2:

        $this->_cities_delete($index);

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/delete-database/$howmany/$indexdd/$nextstep",
          'delay' => '250'
        ));

        $icon = array('icon' => 'question');
        $this->setView("backoffice-database-check-$indexdd",'/backoffice/icon.html', $icon);
        $render = "backoffice-database-check-$indexdd";
        break;

        case 3:

        if ($this->_cities_has_department($index)) {
          $icon = array('icon' => 'checkmark');
        } else {
          $icon = array('icon' => 'cross');
        }

        $nextstep = 0;

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/delete-database/$howmany/$indexdd/$nextstep",
          'delay' => '250'
        ));

        $this->setView("backoffice-database-check-$indexdd",'/backoffice/icon.html', $icon);
        $render = "backoffice-database-check-$indexdd";
        break;

      default:
        Ajax::add(array(
          'action' => 'setLoading',
          'value' => 'off'
        ));

      $render = 'ajax';
      break;
    } // switch

    } else {

      // delete 'all'
      switch($step)
      {
        case 1 :

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/delete-database/$howmany/$nextstep",
          'delay' => '250'
        ));

        $render = 'ajax';
        break;

        case 2:

        $this->_cities_delete();

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/delete-database/$howmany/$nextstep",
          'delay' => '250'
        ));

        $render = 'ajax';
        break;

        case 3:

        $database['departments'] = serialize($this->database());

        $nextstep = 0;

        Ajax::add(array(
          'action' => 'httpRequest',
          'uri' => WEBROOT."/backoffice/delete-database/$howmany/$nextstep",
          'delay' => '250'
        ));

        $this->setView("backoffice_database",'/backoffice/database.html', $database);
        $render = "backoffice_database";
        break;
      
        default:
          Ajax::add(array(
            'action' => 'setLoading',
            'value' => 'off'
          ));

        $render = 'ajax';
        break;
      } // switch
    }


    $this->render($render);
  } // delete_database()


	/**
	* _cities_delete()
	*
	* Delete $department cities definition, if $department === null then truncate table.
  *
  * @param integer $department  Department to be deleted, truncae table if null.
  *
  * @return boolean $result
	*/
  protected function _cities_delete($department = null)
  {
    $return = false;

    // delete all streets
    $street = new StreetsModel();

    $city = new CitiesModel();

    if ($department === null) {

      // delete all (truncate)
      $return = $city->truncate();
      $street->truncate();

    } else {

      // delete one
      $dd = sprintf('%02d', $department);
      // $query = "DELETE FROM cities WHERE postcode LIKE '$dd%';";
      $query = "DELETE streets.*, cities.* FROM cities LEFT JOIN streets ON cities.id = streets.id_city WHERE cities.postcode LIKE '$dd%';";
      $return = $city->raw($query, false);
    } // if

    return $return;
  } // _cities_delete()


  protected function _cities_has_department($department)
  {
    $return = false;

    $city = new CitiesModel();

    if ($department === null) {

    } else {

      // select like
      $dd = sprintf('%02d', $department);
      $query = "SELECT id FROM cities WHERE postcode LIKE '$dd%';";
      $return = $city->raw($query);
    } // if

    return ($return != 0);
  } // _cities_has_department()







	/**
	* ()
	*
	*
	*/
  protected function insee()
  {
    // Init
    $bano = null;
    $return=null;
    for($i=1; $i <= 95; $i++) {

      $fileName = $this->getFileName($i);
      $return[$i]['index'] = sprintf('%02d', $i);
      $return[$i]['source'] = self::INSEE.'/' . $fileName;

      if (file_exists(self::LOCAL.'/' . $fileName)) {
        $return[$i]['destination'] = true;
        $return[$i]['icon'] = 'checkmark';
      } else {
        $return[$i]['destination'] = false;
        $return[$i]['icon'] = 'cross';
      } // if
    } // for
    return $return;
  } // insee()


	/**
	* ()
	*
	*
	*/
  protected function database()
  {
    // Init
    $bano = null;
    for($i=1; $i <= 95; $i++) {
      $return[$i]['index'] = sprintf('%02d', $i);
      if ($this->_cities_has_department($i)) {
        $return[$i]['icon'] = 'checkmark';
      } else {
        $return[$i]['icon'] = 'cross';
      }
    } // for
    return $return;
  } // insee()


	/**
	* _cities_streets_update()
	*
	* /!\ temporary modification of $config['dev'] dur to too much memory usage.
  * Open file.json.gz, dezip this file, parse json contents.
  * Then update cities and streets databases with joins.
  *
  * @param integer $index  Department number to parse.
  *
  * @return boolean $result
	*/
  protected function _cities_streets_update($index)
  {
    global $config;

    // No debug because of too much memory usage
    $dev = $config['dev'];
    $config['dev'] = false;

    // Init
    $cities = array('city', 'town', 'village');
    $city = new CitiesModel();
    $street = new StreetsModel();

    $filename = self::LOCAL.'/' . $this->getFileName($index);
    if (file_exists($filename)) {
      $gzip = gzopen($filename,'r');
      $continue = true;

      while(!gzeof($gzip) && ($continue)) {
        if (($json = json_decode(gzgets($gzip), true)) === false) {
          trigger_error("Error on gzgets() or json_decode() calls.");
          $continue = false;
        } // if

        // for city
        $name = utf8_decode($json['name']);
        $search = tosearch($json['name']);
        $postcode = $json['postcode'];
        $department = utf8_decode($json['departement']);
        $search_department = tosearch($json['departement']);
        
        // for street
        if ((int)$index == (int)($postcode/1000)) {
          // city, town or village
          if (in_array($json['type'], $cities)) {
            if ((
            $city->set('city', $name)
              ->set('search_city', $search)
              ->set('search_department', $search_department)
              ->set('postcode', $postcode)
              ->set('department', $department)
              ->create()) === false) {

              trigger_error("Cannot create database.cities data.");
              $continue = false;
            }
          }

          if ($json['type'] == 'street') {
            if ($index != 75) {
              $toSearch = tosearch($json['city']);
              if (($cityId = $city
                ->where('search_city', '=', $toSearch)
                ->where('postcode', 'LIKE', $json['postcode'][0].$json['postcode'][1].'%')
                ->read()) === false) {

                trigger_error("Cannot read database.streets data.");
                $continue = false;
              } // if
            } else {
              if (($cityId = $city
                ->where('postcode', '=', $json['postcode'])
                ->read()) === false) {

                trigger_error("Cannot read database.streets data.");
                $continue = false;
              } // if
            } // if

            $cityId = $city->id(0);

            if (($street->set('street', $name)
              ->set('search', $search)
              ->set('id_city', $cityId)
              ->create()) === false) {

              trigger_error("Cannot create database.streets data.");
              $continue = false;
            } // if
          } // if
        } // if
      } // while
    } else {
      trigger_error("the file $filename not exists.");
    } // if

    $config['dev'] = $dev;

    return $continue;
  } // _cities_streets_update()


	/**
	* getFileName()
	*
	* @return string $bano  bano file name according insee definition.
	*/
  protected function getFileName($department)
  {
    return sprintf('bano-%02d.json.gz', $department);
  } // getFileName();

	/**
	* getFileDate()
	*
	* @return string $date  Date of the filename.
	*/
  protected function getFileDate($fileName)
  {
    $return = null;

    if (($time = filemtime($fileName)) != 0) {
      $return = date('Y-m-d', $time);
    } 

    return $return;
  } // getFileDate()
} // class BackofficeController