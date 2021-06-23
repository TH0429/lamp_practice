<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'history.php';
//セッション開始
session_start();
//ログインできなかった場合、LOGIN_URLへリダイレクト
if(is_logined() === false) {
    redirect_to(LOGIN_URL);
  }
//データベース接続
$db = get_db_connect();
//ログインしたユーザーの情報を取得
$user = get_login_user($db);

//注文番号情報を取得
$order_id = get_post('order_id');
//購入履歴情報を取得
$history = get_history($db, $order_id);

//adminユーザーではなく、ユーザー情報と商品履歴情報のユーザーIDが一致しない場合HOME_URLへリダイレクト
if(is_admin($user) === false) {
    if($user['user_id'] !== $history['user_id']) {
        redirect_to(HOME_URL);    
    } 
}
//購入明細情報を取得
$details = get_details($db, $order_id);

//VIEWファイル取得
include_once VIEW_PATH . 'details_view.php'; 