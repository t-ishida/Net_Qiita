<?php
require_once 'Net/Qiita.php';
$USER_NAME = 'USER_NAME';
$PASS = 'PASS_WORD';
$qiita = new Net_Qiita();
$qiita->auth ( $USER_NAME, $PASS );
var_dump ( $qiita->postWithJSON ( '/items', json_encode ( array (
  'title' => 'hoge',
  'tags' =>   array ( array ( 'name' => 'PHP' ) ) ,
  'body' => 'piyo',
  'private' => 'false',
  'tweet' => 'true',
))));
