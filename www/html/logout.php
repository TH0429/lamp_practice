<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
//セッション開始
session_start();
//セッション変数を全て削除
$_SESSION = array();
// ユーザーのcookieに保存されているセッションIDを削除
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
//セッションIDを無効化
session_destroy();
//LOGIN_URLへリダイレクト
redirect_to(LOGIN_URL);

