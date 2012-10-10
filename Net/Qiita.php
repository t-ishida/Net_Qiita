<?php
/**
 * Qiita APIのラッパ
 * @author t_ishida
 */
class Net_Qiita {
  private $_Data       = null;
  private $_EndPoint   = 'https://qiita.com/api';
  private $_Version    = 'v1';
  private  static $CURL_OPTIONS = array(
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => 1,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_USERAGENT      => 'net-qiita-api',
  );
 
  /**
   * コンストラクタ
   * @param データ
   */
  public function __construct ( $data = null ) {
    if ( $data ) $this->setData ( $data );
    else         $this->setData ( array () );
  }

  /**
   * データをセットする(Webならばセッションなどから設定すると良い)
   * @param データ
   */
  public function setData ( $data ) {
    $this->_Data = $data;
  }

  /**
   * データを返す(Webならばセッションなどに書き戻すと良い)
   * @return データ
   */
  public function getData () {
    return $this->_Data;
  }

  /**
   * 認証してトークンを取得する
   * @param ユーザー名
   * @param パスワード
   */
  public function auth ( $user_name, $password ) {
    if ( $this->_Data['token'] )  return null;
    $result = $this->post ( '/auth', array (
      'url_name' => $user_name,
      'password' => $password,
    ));
    $this->_Data['url_name'] = $result->url_name;
    $this->_Data['token']    = $result->token;
  }

  /**
   * getする
   * @param url 
   * @param パラメータ
   * @return 取得した結果
   */
  public function get ( $name, $params = null ) {
    return json_decode ( $this->request ( 'GET', $this->buildUrl ( $name ) , $params ) );
  }

  /**
   * postする
   * @param url 
   * @param パラメータ
   * @return 取得した結果
   */
  public function post ( $name, $params = null ) {
    return json_decode ( $this->request ( 'POST', $this->buildUrl ( $name ) , $params ) );
  }

  /**
   * JSON付きでPOST
   * @param url 
   * @param パラメータ
   * @return 取得した結果
   */
  public function postWithJSON ( $name, $params = null ) {
    return json_decode ( $this->request ( 'POST', $this->buildUrl ( $name ) , json_encode ( $params ), array ( 'Content-Type: application/json' ) ) );
  }
  /**
   * putする
   * @param url 
   * @param パラメータ
   * @return 取得した結果
   */
  public function put ( $name, $params = null ) {
    return json_decode ( $this->request ( 'PUT', $this->buildUrl ( $name ) , $params ) );
  }

  /**
   * deleteする
   * @param url 
   * @param パラメータ
   * @return 取得した結果
   */
  public function delete ( $name, $params = null ) {
    return json_decode ( $this->request ( 'DELETE', $this->buildUrl ( $name ) , $params ) );
  }

  /**
   * cURLでリクエストする
   * @param メソッド名
   * @param URL
   * @param パラメータ
   * @param 付加したいヘッダ
   * @return 本文
   */
  public function request ( $method, $url, $params = null , $headers = null ) {
    $curl    = curl_init();
    curl_setopt_array ( $curl, $this->buildCurlOptions ( $method, $url, $params, $headers ) );
    $result    = curl_exec ( $curl );
    $curl_info = curl_getinfo ( $curl );
    $http_code = $curl_info["http_code"] - 0;
    
    if ( $http_code < 200 || $http_code > 300 ) throw new Exception ( $url . ':' . $http_code . ':' . $result . '|' . var_export ( $params, true ) );
    curl_close ( $curl );
    list ( $header, $body ) = preg_split( '#\n.?\n#', $result );
    return $body;
  }

  /**
   * QiitaのURLを作る
   * @param API名
   * @return URL
   */
  public function buildUrl ( $name ) {
    $url  = $this->_EndPoint . '/';
    $url .= $this->_Version;
    preg_match ( '#^/#', $name ) or $name = '/' . $name;
    $url .= $name;
    if ( $this->_Data['token'] ) $url .= '?' . http_build_query ( array ( 'token' => $this->_Data['token'] ), null, '&' );
    return $url;
  }

  /**
   * cURLのパラメータ設定
   * @param メソッド名 (GET, POST, PUT, DELETE に 設定 )
   * @param array | ファイルパス | 他値なんでも
   * @param ヘッダに付加したい情報
   * @return array()
   */
  public function buildCurlOptions ( $method, $url, $params = null , $headers = null ) {
    $method = strtoupper ( $method );
    $options = self::$CURL_OPTIONS;
    if ( $method == 'GET' ){
      if ( is_array ( $params ) ) $url .= '?' . http_build_query ( $params, null, '&' );
    }
    else {
      // POST 系の パラメータの設定
      if     ( is_array ( $params ) ) $options[CURLOPT_POSTFIELDS] = http_build_query ( $params, null, '&' );
      elseif ( is_file ( $params ) )  $options[CURLOPT_POSTFIELDS] = file_get_contents ( $params );
      elseif ( $params )              $options[CURLOPT_POSTFIELDS] = $params;
      // method の 決定
      if     ( $method == 'POST' )    $options[CURLOPT_POST]       = 1;
      elseif ( in_array ( $method, array ( 'DELETE', 'PUT' )  ) )  $options[CURLOPT_CUSTOMREQUEST] = $method;
      else                                                         throw new Exception ( 'Invalid Method:' . $method );
    }

    if     ( is_array ( $headers ) ) $options[CURLOPT_HTTPHEADER] = $headers;
    elseif ( $headers )              throw new Exception ( 'Invalid Header' . var_export ( $headers, true ) );

    $options[CURLOPT_URL] = $url;
    return $options;
  }
}
