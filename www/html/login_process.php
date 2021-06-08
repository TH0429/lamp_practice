<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
//セッション開始
session_start();
//ログイン失敗の場合、LOGIN_URLへリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}
//ユーザー名取得
$name = get_post('name');
//パスワード取得
$password = get_post('password');
//データベース接続
$db = get_db_connect();
//トークン取得
$token = get_post('token');       

// 受け取ったトークンを確認し、falseが返された場合はLOGIN_URLへリダイレクト
if (is_valid_csrf_token($token) === false){
  set_error('不正なアクセスが行われました。');
  redirect_to(LOGIN_URL);
}

//一般ユーザーのログイン処理（失敗の場合、LOGIN_URLへリダイレクト）
$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  redirect_to(LOGIN_URL);
}
//adminユーザーのログイン処理（成功の場合、ADMIN_URLへリダイレクト）
set_message('ログインしました。');
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
//HOME_URLへリダイレクト
redirect_to(HOME_URL);