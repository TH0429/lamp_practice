<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
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
//カートの商品を削除
$cart_id = get_post('cart_id');

if(delete_cart($db, $cart_id)){
  set_message('カートを削除しました。');
} else {
  set_error('カートの削除に失敗しました。');
}
//CART_URLへリダイレクト
redirect_to(CART_URL);