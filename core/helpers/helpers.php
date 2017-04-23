<?php

defined('SCRIPTOK') OR exit('No direct script access allowed');

/**
* Helpers functions
*
* 
*
*/


/**
* password()
*
* @param string $string  String to be hashed.
*
* @return string $password using hash function
*/
function password($string)
{
  return hash(PASSWORD_ALGO, PASSWORD_KEY.$string);
} // password()

/**
* checkPassword()
*
* @param string $hashed  Already hashed password.
* @param string $password  Password to be compared to $hashed.
*
* @return boolean true if same passwords
*/
 function checkPassword($hashed, $password)
{ 
  return (password($password) === $hashed);
} // password()


/**
* Replace accent into string, function from codeigniter.
*
* @param string $string
* 
* @return string with replaced accent.
*/
function replace_accent($string)
{
  static $foreign_characters = null;
  static $array_from = null;
  static $array_to = null;
  
  if ($foreign_characters === null) {

    $foreign_characters = array(
      '/¨/' => '', // BD
      '/ä|æ|ǽ/' => 'ae',
      '/ö|œ/' => 'oe',
      '/ü/' => 'ue',
      '/Ä/' => 'Ae',
      '/Ü/' => 'Ue',
      '/Ö/' => 'Oe',
      '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
      '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
      '/Б/' => 'B',
      '/б/' => 'b',
      '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
      '/ç|ć|ĉ|ċ|č/' => 'c',
      '/Д/' => 'D',
      '/д/' => 'd',
      '/Ð|Ď|Đ|Δ/' => 'Dj',
      '/ð|ď|đ|δ/' => 'dj',
      '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
      '/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
      '/Ф/' => 'F',
      '/ф/' => 'f',
      '/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
      '/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
      '/Ĥ|Ħ/' => 'H',
      '/ĥ|ħ/' => 'h',
      '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
      '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
      '/Ĵ/' => 'J',
      '/ĵ/' => 'j',
      '/Ķ|Κ|К/' => 'K',
      '/ķ|κ|к/' => 'k',
      '/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
      '/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
      '/М/' => 'M',
      '/м/' => 'm',
      '/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
      '/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
      '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
      '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
      '/П/' => 'P',
      '/п/' => 'p',
      '/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
      '/ŕ|ŗ|ř|ρ|р/' => 'r',
      '/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
      '/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
      '/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
      '/ț|ţ|ť|ŧ|т/' => 't',
      '/Þ|þ/' => 'th',
      '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
      '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
      '/Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
      '/ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
      '/В/' => 'V',
      '/в/' => 'v',
      '/Ŵ/' => 'W',
      '/ŵ/' => 'w',
      '/Ź|Ż|Ž|Ζ|З/' => 'Z',
      '/ź|ż|ž|ζ|з/' => 'z',
      '/Æ|Ǽ/' => 'AE',
      '/ß/' => 'ss',
      '/Ĳ/' => 'IJ',
      '/ĳ/' => 'ij',
      '/Œ/' => 'OE',
      '/ƒ/' => 'f',
      '/ξ/' => 'ks',
      '/π/' => 'p',
      '/β/' => 'v',
      '/μ/' => 'm',
      '/ψ/' => 'ps',
      '/Ё/' => 'Yo',
      '/ё/' => 'yo',
      '/Є/' => 'Ye',
      '/є/' => 'ye',
      '/Ї/' => 'Yi',
      '/Ж/' => 'Zh',
      '/ж/' => 'zh',
      '/Х/' => 'Kh',
      '/х/' => 'kh',
      '/Ц/' => 'Ts',
      '/ц/' => 'ts',
      '/Ч/' => 'Ch',
      '/ч/' => 'ch',
      '/Ш/' => 'Sh',
      '/ш/' => 'sh',
      '/Щ/' => 'Shch',
      '/щ/' => 'shch',
      '/Ъ|ъ|Ь|ь/' => '',
      '/Ю/' => 'Yu',
      '/ю/' => 'yu',
      '/Я/' => 'Ya',
      '/я/' => 'ya'
    );

	  $array_from = array_keys($foreign_characters);
	  $array_to = array_values($foreign_characters);
  } // if


	return preg_replace($array_from, $array_to, $string);
} // replace_accent()


/**
* tosearch()
*
* Replace characters for 'search' columns in databases
*
* @param string $string Reference for replacements
*
* @return string  Replaced straing.
*/
function tosearch($string)
{
  if ($string === null) return null;
  return strtolower(str_replace(array('-',"'", '_'), ' ', replace_accent($string)));
} // tosearch()

/**
* mexplode()
*
* mexplode : multi-explode: php explode with multi characters
*
* @param array $search  Characters for exploding reference.
* @param array $string  String to be exploded.
*
* @return array $result  Result of exploded string.
*/
function mexplode($search, $string)
{
  if ($string === null) { return null; }

  $return[0] = array('separator' => 'first', 'operator' => 'like', 'name' => '');

  $imax = strlen($string);
  $jmax = count($search);

  $index = 0;

  for($i=0; $i<$imax; $i++) {
    for($j=0; $j<$jmax; $j++) {
      if ($string[$i] == $search[$j]) {
        $index++;
        $return[$index]['separator'] = $search[$j];
        $return[$index]['operator'] = 'like';
        $return[$index]['name'] = '';
        $i++;
        break;
      } // if
    } // for j

    if ($string[$i] != '"') {
      $return[$index]['name'] .= $string[$i];
    } else {
      $return[$index]['operator'] = 'equal';
    }
  } // for i
  
  return $return;
} // mexplode()