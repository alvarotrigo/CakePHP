<?php 

/**
 * UrlHelperTest.php
 * 
 * Test for the UrlHelper.
 * 
 * @autor Alvaro Trigo
 */

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('UrlHelper', 'View/Helper');

class UrlHelperTest extends CakeTestCase {
    public $UrlHelper = null;

    // Here we instantiate our helper
    public function setUp() {
        parent::setUp();
        $Controller = new Controller();
        $View = new View($Controller);
        $this->UrlHelper = new UrlHelper($View);
    }
    
    
    public function testGetRealURL() {
    	$result = $this->UrlHelper->getRealURL('http://www.google.es');
    	$this->assertEquals($result, "http://www.google.es");
    	
    	$result = $this->UrlHelper->getRealURL('http://bit.ly/I9WKJ4');
    	$this->assertEquals($result, "http://tustitulares.com/img/shareTwitter.jpg");
	    
    	$result = $this->UrlHelper->getRealURL('http://tinyurl.com/cvdu6xa');
    	$this->assertEquals($result, "http://tustitulares.com/img/shareTwitter.jpg");   	
    }
    

    // Testing the usd() function
    public function testGetDomain() {
    	//http
    	$result = $this->UrlHelper->getDomain('http://google.com/ajdfklajsklfjskaljfka/asldjfklasjk/&b=1?a=2');
       	$this->assertEquals($result, "google.com");
       	
       	//http + www
       	$result = $this->UrlHelper->getDomain('http://www.google.com/ajdfklajsklfjskaljfka/asldjfklasjk/&b=1?a=2');
       	$this->assertEquals($result, "google.com");
       	
       	//only www
       	$result = $this->UrlHelper->getDomain('www.google.com/ajdfklajsklfjskaljfka/asldjfklasjk/&b=1?a=2');
       	$this->assertEquals($result, "google.com");
       	
       	//with port
       	$result = $this->UrlHelper->getDomain('http://localhost:8888/ajdfklajsklfjskaljfka/asldjfklasjk/&b=1?a=2');
       	$this->assertEquals($result, "localhost");    	
    }
    
    public function testIsSecure(){
       	$result = $this->UrlHelper->isSecure('http://localhost:8888/&a=1?b=2');
       	$this->assertFalse($result);  
       	
       	$result = $this->UrlHelper->isSecure('https://localhost:8888/&a=1?b=2');
       	$this->assertTrue($result); 
    }
    
    
    public function testGetProtocol(){
       	$result = $this->UrlHelper->getProtocol('http://localhost:8888/&a=1?b=2');
       	$this->assertEquals($result, "http");  
       	
       	$result = $this->UrlHelper->getProtocol('https://localhost:8888/&a=1?b=2');
       	$this->assertEquals($result, "https");  
       	
       	$result = $this->UrlHelper->getProtocol('ssl://localhost:8888/&a=1?b=2');
       	$this->assertEquals($result, "ssl");  
    }
    
    
    public function testGetPort(){
       	$result = $this->UrlHelper->getPort('http://localhost:8888/&a=1?b=2');
       	$this->assertEquals($result, '8888');  
       	
       	$result = $this->UrlHelper->getPort('https://localhost:8765/&a=1?b=2');
       	$this->assertEquals($result, "8765");  
       	
       	$result = $this->UrlHelper->getPort('http://google.com/&a=1?b=2');
       	$this->assertNull($result);
    }
    
    public function testGetURLFromText(){
    	$result = $this->UrlHelper->getURLFromText('hola pepe http://www.google.es adios pepe');
    	$this->assertEquals($result[0], "http://www.google.es");
    	
    	$result = $this->UrlHelper->getURLFromText('hola pepe http://www.google.es adios pepe www.apple.com y otra más http://youtube.com', 'all');
    	$expected = array('http://www.google.es', 'www.apple.com', 'http://youtube.com');
    	$this->assertEquals($result, $expected);
    	
    	$result = $this->UrlHelper->getURLFromText('no hay URL', 'all');
    	$this->assertEquals($result, array());
    }
    
    public function testEraseURLsFromText() {
    	$result = $this->UrlHelper->eraseURLsFromText('hola pepe http://www.google.es adios pepe');
    	$this->assertEquals($result, "hola pepe  adios pepe");
    	
    	$result = $this->UrlHelper->eraseURLsFromText('hola pepe http://www.google.es adios pepe www.apple.com y otra más http://youtube.com');
    	$this->assertEquals($result, "hola pepe  adios pepe  y otra más ");
    }
    
    public function testShortURLsFromText() {
    	$url = "esto es un texto muy largo http://tustitulares.com/img/shareTwitter.jpg 
    			con URL http://tustitulares.com/img/shareYoutube.jpg";
    	
    	$result = $this->UrlHelper->shortURLsFromText($url);
    	
    	//we just check if the new URL is shorter
    	$this->assertTrue(strlen($url) > strlen($result));
    }
    
    
    public function testGetExtension(){
    	//no extension
    	$result = $this->UrlHelper->getExtension('http://www.google.com/&a=1?b=2');
       	$this->assertNull($result);
       	
       	//3 characteres extension
    	$result = $this->UrlHelper->getExtension('http://www.google.com/justTesting.php');
       	$this->assertEqual($result, "php");
       	
       	//4 characteres extension
    	$result = $this->UrlHelper->getExtension('http://www.google.com/justTesting.html');
       	$this->assertEqual($result, "html");
       	
       	//with parameters after the extension
    	$result = $this->UrlHelper->getExtension('http://www.google.com/justTesting.php?p=1&a=2');
       	$this->assertEqual($result, "php");
       	
       	//with parameters after the extension + more than 4 characteres
    	$result = $this->UrlHelper->getExtension('http://www.google.com/justTesting.abcdef?p=1&a=2');
       	$this->assertEqual($result, "abcdef");
    }
    
	public function testGetShortURL($url){
    	$url = "http://tustitulares.com/img/shareYoutube.jpg";
    	
    	$result = $this->UrlHelper->shortURL($url);
    	
    	//we just check if the new URL is shorter
    	$this->assertTrue(strlen($url) > strlen($result));
	}
	
	public function testIsYoutubeVideo(){
		//not youtube URL
		$result = $this->UrlHelper->isYoutubeVideo('http://www.google.com/justTesting.html');
       	$this->assertFalse($result);
       	
       	//youtube URL but not a video one
       	$result = $this->UrlHelper->isYoutubeVideo('http://www.youtube.com/videos?feature=mh');
       	$this->assertFalse($result);
       	
       	//youtube URL with video and parameters
       	$result = $this->UrlHelper->isYoutubeVideo('http://www.youtube.com/watch?v=nGeKSiCQkPw&feature=relmfu');
       	$this->assertTrue($result);
       	
       	//youtube shorted URL with video
       	$result = $this->UrlHelper->isYoutubeVideo('http://youtu.be/nGeKSiCQkPw');
       	$this->assertTrue($result);
	}
	
	public function testGetParam(){
		//1st param
		$result = $this->UrlHelper->getParam('http://www.google.com/justTesting.php?a=param1&b=param2&param3', 'a');
       	$this->assertEquals($result, 'param1');
       	
        //2nd param
		$result = $this->UrlHelper->getParam('http://www.google.com/justTesting.php?a=param1&b=param2&c=param3', 'b');
       	$this->assertEquals($result, 'param2');
       	
       	//no parameters
		$result = $this->UrlHelper->getParam('http://www.google.com/justTesting.php', 'b');
       	$this->assertNull($result);
	}
	
	public function testGetNumberOfParams(){
		//1 param
		$result = $this->UrlHelper->getNumberOfParams('http://www.google.com/justTesting.php?a=param1');
       	$this->assertEquals($result, 1);
       	
        //3 params
		$result = $this->UrlHelper->getNumberOfParams('http://www.google.com/justTesting.php?a=param1&b=param2&c=param3');
       	$this->assertEquals($result, 3);
       	
       	//no parameters
		$result = $this->UrlHelper->getNumberOfParams('http://www.google.com/justTesting.php');
       	$this->assertEquals($result, 0);
	}
	
	public function testGetParams(){
		//1st param
		$result = $this->UrlHelper->getParams('http://www.google.com/justTesting.php?a=param1', array('a'));
		$expected = array('a'=>'param1');
       	$this->assertEquals($result, $expected);
       	
        //1st and 3rd param
		$result = $this->UrlHelper->getParams('http://www.google.com/justTesting.php?a=param1&b=param2&c=param3', array('a','c'));
		$expected = array('a'=>'param1', 'c' => 'param3');
       	$this->assertEquals($result, $expected);
       	
       	//no parameters
		$result = $this->UrlHelper->getParams('http://www.google.com/justTesting.php', 'a');
       	$this->assertEquals($result, array());
	}
	
	public function testIsImage(){
		$result = $this->UrlHelper->isImage('http://i4.ytimg.com/vi/_AMpugNjTKk/default.jpg');
       	$this->assertTrue($result);
       	
       	$result = $this->UrlHelper->isImage('http://google.com');
       	$this->assertFalse($result);
       	
       	//shorted url of an image
       	$result = $this->UrlHelper->isImage('http://tinyurl.com/cer2awt');
       	$this->assertTrue($result);
	}
	
	
	
}