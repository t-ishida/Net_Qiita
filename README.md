This is PHP Class for http://qiita.com

* Installation

Clone the repository from Github

```shell
git clone git://github.com/t-ishida/Net_Qiita.git
```

```PHP
<?php
require_once 'Net/Qiita.php';
$USER_NAME = 'USER_NAME';
$PASS = 'PASS_WORD';
$qiita = new Net_Qiita();
$qiita->auth ( $USER_NAME, $PASS );
var_dump ( $qiita->postWithJSON ( '/items',  array (
  'title' => 'hoge',
  'tags' =>   array ( array ( 'name' => 'PHP' ) ) ,
  'body' => 'piyo',
  'private' => 'false',
  'tweet' => 'true',
)));
```
