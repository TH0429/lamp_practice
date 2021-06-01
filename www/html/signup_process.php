<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
//セッション開始
session_start();
//ログインしている場合、HOME_URLへリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}
//ユーザー名取得
$name = get_post('name');
//パスワード取得
$password = get_post('password');
//パスワード確認
$password_confirmation = get_post('password_confirmation');
//データベース接続
$db = get_db_connect();
//ユーザー登録処理（失敗：SIGNUP_URLへリダイレクト）
try{
  $result = regist_user($db, $name, $password, $password_confirmation);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}
//ユーザー登録処理（成功：HOME_URLへリダイレクト）
set_message('ユーザー登録が完了しました。');
login_as($db, $name, $password);
redirect_to(HOME_URL);