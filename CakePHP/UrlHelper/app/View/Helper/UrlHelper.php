<?php
/**
 * UrlHelper.php 
 * CakePHP 2.x
 * 
 * This helper offer some functions to deal with URLs as well as to determine whether it
 * is a youtube video or an image.
 * It also contains some functions to detect and erase URLs inside a text.
 * 
 * @author Alvaro Trigo 
 * @package Helper
 */

App::uses('AppHelper', 'View/Helper');

class UrlHelper extends AppHelper {
	
	/**
	 * Obtains the real URL from a shorted one.
	 * In case the URL isn't shorted, it returns the same.
	 *
	 * @access public
	 * @param string $short URL
	 * @return string real URL.
	 */
	public function getRealURL($short){
		$headers = get_headers($short, 1);		
		$loc = $headers['Location'];
				
		if(is_array($loc)){
			// get the highest numeric index
			$key = max(array_keys( $loc));
			
			return $loc[$key];
		}else if(!empty($loc)){
			return $loc;
		}else{
			return $short;
		}
	}
	
	/**
	 * Obtains the domain of the URL. 
	 * For example: google.com / youtube.com
	 * 
	 * @access public
	 * @param String $url
	 * @return String domain name
	 */
	public function getDomain($url){
		$url = $this->addHttp($url);
		return str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
	}
	
	/**
	 * Determines whether the URL stars with https protocol or not. 
	 * 
	 * @access public
	 * @param String $url
	 * @return boolean
	 */
	public function isSecure($url){
    	return $this->getProtocol($url)=='https';
	}
	
		
	/**
	 * Obtains the protocol of the URL.
	 * For example: http, https, ssl...
	 * 
	 * @access public
	 * @param String $url
	 * @return String URL protocol
	 */
	public function getProtocol($url){
		return parse_url($url, PHP_URL_SCHEME);
	}
	
		
	/**
	 * Obtains the port of the URL.
	 * For example: 8888
	 * 
	 * @access public
	 * @param String $url
	 * @return String URL port
	 */
	public function getPort($url){
		return parse_url($url, PHP_URL_PORT);
	}


	/**
	 * Gets the URLs contained in a text.
	 *
	 * @access public
	 * @param string $text text
	 * @param $number optional. If it's is set to "all", it will return an array of URLs. 
	 * If not, it will just return the first URL.
	 * @return array 
	 */
	public function getURLFromText($text){	
		preg_match_all('#\b(https?://|www[.]?)[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $text, $match);

		if(!empty($match[0])){
			return $match[0];
		}
		return array();
	}	
	
	
	/**
	 * Removes URLs from a given text.
	 * 
	 * @access public
	 * @param String $text
	 * @return String text without URLs
	 */
	public function eraseURlsFromText($text){
		$urls = $this->getURLFromText($text);
		
		foreach($urls as $url){
			$text = str_replace($url, '', $text);
		}
		
		return $text;
	}
	

	/**
	 * Shorts all the URLs finding in the given text.
	 * 
	 * @access public
	 * @param String $text
	 * @return String text with shorted URLs
	 */
	public function shortURLsFromText($text){
		$urls = $this->getURLFromText($text);

		foreach($urls as $url){
			$short = $this->getShortURL($url);
			$text = str_replace($url, $short, $text);
		}
		
		return $text;
	}	
	
	
	/**
	 * Obtains the extension of a URL
	 * For example: www.test.com/test.php?a=1&b=2 ==> php
	 * 
	 * @access public
	 * @param String $url
	 * @return String url extension
	 */
	public function getExtension($url){
		$params = '?'.parse_url( $url, PHP_URL_QUERY );
		$extension = pathinfo($url, PATHINFO_EXTENSION);	

		if(!empty($extension)){	
			return str_replace($params, '', $extension);
		}
		return NULL;
	}
		
	/**
	 * Adds 'http://' string to the given URL
	 * For example: www.google.com ==> http://www.google.com
	 * 
	 * @access public
	 * @param String $url
	 * @return String url with http://
	 */
	private function addHttp($url) {
	    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
	        $url = "http://" . $url;
	    }
	    return $url;
	}
	
	/**
	 * Obtains the shorted URL from the given one. 
	 * It uses tinyURL service to do so.
	 * 
	 * @access public
	 * @param String $url
	 * @return String shorted url
	 */
	public function getShortURL($url){
		$url = $this->addHttp($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://tinyurl.com/api-create.php?url=".($url));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$short_url = curl_exec($ch);
		curl_close($ch);
		if(empty($short_url)) return $url; else return $short_url;
	}
		
	/**
	 * Determines whether the given URL is a youtube video URL or not.
	 * 
	 * @access public
	 * @param String $url
	 * @return boolean
	 */
	public function isYoutubeVideo($url){
		$realUrl = $this->getRealURL($url);		
		if($this->getDomain($realUrl)=='youtube.com'){
			$param = $this->getParam($realUrl, 'v');
			if(!empty($param)){
				return true;
			}
		}
		return false;
	}	
	
	/**
	 * Obtains the value of the given parameter for the given URL.
	 * 
	 * @access public
	 * @param String $url 
	 * @param String $var variable or parameter for which we want to 
	 * obtain the value.
	 * @return String parameter value
	 */
	public function getParam($url, $var){
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
		if(isset($my_array_of_vars[$var])){
			return $my_array_of_vars[$var];
		}
		return NULL;
	}
	
	/**
	 * Obtains the number of parameters on the given URL.
	 * 
	 * @access public
	 * @param String $url
	 * @return int number of parameters
	 */
	public function getNumberOfParams($url){
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
		return count($my_array_of_vars);
	}
	
	/**
	 * Obtains the value of the given parameters for the given URL.
	 * 
	 * @access public
	 * @param String $url
	 * @param array $vars array of parameters for which we want to obtain 
	 * the value, for example: array('v','lang');
	 * @return array with the parameters value: array('v' => '10','lang'=>'en');
	 */
	public function getParams($url, $vars){
		$params = array();
		
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
		if(isset($vars)){
			foreach($vars as $var){
				if(isset($my_array_of_vars[$var])){
					$params[$var] = $my_array_of_vars[$var];
				}
			}
		}
		return $params;	
	}
	
	/**
	 * Determines whether the given URL is an image or not.
	 * 
	 * @access public
	 * @param String $url
	 * @return boolean 
	 */
	public function isImage($url){
		return is_array(@getimagesize($url));
	}	
}