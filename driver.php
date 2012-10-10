<?php
require_once 'Net/Qiita.php';
$USER_NAME = 'hoge';
$PASS = 'fuga';
$qiita = new Net_Qiita();
$qiita->auth ( $USER_NAME, $PASS );
$qiita->post ( '/items', array (
  'title' => 'hoge',
  'tags' =>  json_encode ( array ( array ( 'name' => 'PHP' )  ) ) ,
  'body' => 'piyo',
  'private' => false,
  'tweet' => true,
));
