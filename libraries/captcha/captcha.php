<?php

namespace libraries\captcha;


defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* class Captcha
*
* This class manage Captcha.
* The file /public/captcha.php is parsed and copy to ./public/captcha repertory.
*/
class Captcha
{
	protected $_destination;
	protected $_codelength;
	protected $_width;
	protected $_height;
 	
	 /**
	 * __construct()
	 *
	 * Initialize all variables and copy pased captcha.php and font file.
	 *
	 * @param string $destination  Destination repertory where parsed captcha.php will be copied.
	 * @param string $webdestination  Destination for web calling.
	 * @param integer $codelength  Length of the code (default is 4 characters).
	 * @param integer $width  Width of the captcha image (default is 150).
	 * @param integer $height  Height of the captcha image (default is 50).
	 */
	 public function __construct($destination, $webdestination, $codelength=4, $width=150, $height=50)
	 {
			// Defaults values
			$this->_destination = $destination;
			$this->_webdestination = $webdestination;
			$this->_codelength = $codelength;
			$this->_width = $width;
			$this->_height = $height;

			// file management
			if (!file_exists($this->_destination)) {
				mkdir($this->_destination);
				if (!copy(ROOT.'/libraries/captcha/public/fff_tusj.ttf', $this->_destination.'/fff_tusj.ttf')) {
					trigger_error("File fff_tusj.ttf cannot be copied.");
				} // if
			}
			if (($data = file_get_contents(ROOT.'/libraries/captcha/public/captcha.php')) === false) {
				trigger_error("File captcha.php cannot be copied.");
			}
			$data = str_replace(
				array('{{WIDTH}}', '{{HEIGHT}}', '{{CODE_LENGTH}}'), 
				array($this->_width, $this->_height, $this->_codelength),
				$data);

			if (!file_put_contents($this->_destination.'/captcha.php', $data)) {
				trigger_error("File captcha.php cannot be fill with data.");
			} // if
	 } // __construct()


	 /**
	  * check()
		*
		* @return boolean $result  Compare Session and Post.
		*/
		public function check()
		{
			return true; // bd modification, waiting for .htaccess modification...
			$return = false;

			if ((isset($_SESSION['CAPTCHA'])) && (isset($_POST['captcha']))) {
				$return = ($_SESSION['CAPTCHA'] == md5($_POST['captcha']));
			}
			return $return;
		}

	 /**
	  * getImg()
		*
		* @return string $tag  Html tag view.
		*/
		public function getImg()
		{
			return '<img id="captcha_loaded" style="width:'.$this->_width.'px; height:'.$this->_height.'px" src="'.$this->_webdestination.'/captcha.php">';
		}


	 /**
	  * getReload()
		*
		* @return stirng $tag  Html tags (svg) for Reload button view.
		*/
		public function getReload()
		{
			$return = '<div style="cursor:pointer;" onclick="document.images.captcha_loaded.src=\''.$this->_webdestination.'/captcha.php?captcha=\'+Math.round(Math.random(0)*1000)">';
			$return .= '<?xml version="1.0" encoding="utf-8"?>';
			$return .= '<svg width="'.$this->_height.'px" height="'.$this->_height.'px" xmlns="http://www.w3.org/2000/svg" ';
			$return .= 'viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-reload">';
			$return .= '<rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect>';
			$return .= '<g><path d="M50 15A35 35 0 1 0 74.787 25.213" fill="none" stroke="#AAAAAA" stroke-width="12px"></path>';
			$return .= '<path d="M50 0L50 30L66 15L50 0" fill="#AAAAAA"></path>';
			$return .= '<animateTransform attributeName="transform" type="rotate" from="0 50 50" to="360 50 50" dur="12s" repeatCount="indefinite">';
			$return .= '</animateTransform></g></svg>';
			$return .= '</div>';

			return $return;
		} // getReload()
} // class Captcha