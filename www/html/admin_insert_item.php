<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
//セッション開始
session_start();
//ログインしていない場合、LOGIN_URLへリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
//データベース接続
$db = get_db_connect();
//ログインしたユーザーの情報を取得
$user = get_login_user($db);
//adminユーザーでなかった場合、LOGIN_URLへリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
//商品情報の登録
$name = get_post('name');
$price = get_post('price');
$status = get_post('status');
$stock = get_post('stock');
// トークン情報取得
$token = get_post('token');  

$image = get_file('image');

// トークン確認でfalseを返された場合、ADMIN_URLへリダイレクト
if (is_valid_csrf_token($token) === false){
  set_error('不正なアクセスが行われました。');
  redirect_to(ADMIN_URL);
}

if(regist_item($db, $name, $price, $stock, $status, $image)){
  set_message('商品を登録しました。');
}else {
  set_error('商品の登録に失敗しました。');
}

//ADMIN_URLへリダイレクト
redirect_to(ADMIN_URL);