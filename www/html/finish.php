<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'history.php';
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
//カートの情報を取得
$carts = get_user_carts($db, $user['user_id']);
//購入失敗の場合、メッセージを表示しCART_URLへリダイレクト
if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
} 
//カートの商品の合計金額を取得
$total_price = sum_carts($carts);
//VIEWファイル取得
include_once '../view/finish_view.php';