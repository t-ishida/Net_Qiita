<?php
require_once dirname (__FILE__) . '/../Net/Qiita.php';
error_reporting ( E_ALL -E_NOTICE );
class Net_QiitaTest extends PHPUnit_Framework_TestCase {
  private $_Target = null;
  public function setup () {
    $this->_Target = new Net_Qiita ();
  }
  
  public function testAuth () {
    $target = $this->getMockBuilder ( 'Net_Qiita' )
      ->setMethods ( array ( 'post' ) )
      ->getMock();
    $target->expects ( $this->once () )->method ( 'post' )
      ->with ( 
        $this->equalTo ( '/auth' ),
        $this->equalTo ( array (
          'url_name' => 'user_name',
          'password' => 'password',
        ))
      )
      ->will ( $this->returnValue ( (object) array ( 
        'url_name' => 'user_name',
        'token' => 'token' 
      )));
    $target->auth ( 'user_name', 'password' );
    $result = $target->getData ( );
    $this->assertEquals ( 'token', $result['token'] );
    $this->assertEquals ( 'user_name', $result['url_name'] );
  }

  public function testBuildUrl() {
    $this->assertEquals ( 'https://qiita.com/api/v1/auth',  $this->_Target->buildUrl ( '/auth' ) );
    $this->assertEquals ( 'https://qiita.com/api/v1/auth',  $this->_Target->buildUrl ( 'auth' ) );
    $this->_Target->setData ( array ( 'token' => 'hoge' ) );
    $this->assertEquals ( 'https://qiita.com/api/v1/xyzzy?token=hoge',  $this->_Target->buildUrl ( 'xyzzy' ) );
    $this->_Target->setData ( null );
  }

  public function testBuildCurlOptions () {
      $this->assertEquals (array (
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => 1,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'net-qiita-api',
        CURLOPT_URL            => 'http://hoge/fuga.html?name=value',
        CURLOPT_HTTPHEADER     => array (
          'header1',
          'header2',
        ),    
      ), $this->_Target->buildCurlOptions ( 
        'GET',
        'http://hoge/fuga.html',
        array ( 'name' => 'value' ),
        array ( 'header1', 'header2') 
      ));

      $this->assertEquals (array (
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => 1,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'net-qiita-api',
        CURLOPT_URL            => 'http://hoge/fuga.html',
        CURLOPT_POST           => 1,
        CURLOPT_POSTFIELDS     => http_build_query ( array ( 'name' => 'value' ), null, '&'),
      ), $this->_Target->buildCurlOptions ( 
        'POST',
        'http://hoge/fuga.html',
        array ( 'name' => 'value' )
      ));

      $this->assertEquals (array (
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => 1,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'net-qiita-api',
        CURLOPT_URL            => 'http://hoge/fuga.html',
        CURLOPT_CUSTOMREQUEST  => 'DELETE',
        CURLOPT_POSTFIELDS     => http_build_query ( array ( 'name' => 'value' ), null, '&'),
      ), $this->_Target->buildCurlOptions ( 
        'DELETE',
        'http://hoge/fuga.html',
        array ( 'name' => 'value' )
      ));

      $this->assertEquals (array (
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => 1,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'net-qiita-api',
        CURLOPT_URL            => 'http://hoge/fuga.html',
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_POSTFIELDS     => http_build_query ( array ( 'name' => 'value' ), null, '&'),
      ), $this->_Target->buildCurlOptions ( 
        'PUT',
        'http://hoge/fuga.html',
        array ( 'name' => 'value' )
      ));

  }
}
