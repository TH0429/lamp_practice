<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
//セッション開始
session_start();
//ログイン失敗の場合、LOGIN_URLへリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
//データベース接続
$db = get_db_connect();
//ログインしたユーザーの情報を取得
$user = get_login_user($db);
//adminユーザーのログインに失敗した場合、LOGIN_URLへリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
//アイテム個数変更
//商品情報取得
$item_id = get_post('item_id');
//商品在庫情報取得
$stock = get_post('stock');
//商品在庫数追加
if(update_item_stock($db, $item_id, $stock)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}
//ADMIN_URLへリダイレクト
redirect_to(ADMIN_URL);