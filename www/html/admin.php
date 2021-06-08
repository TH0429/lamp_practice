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
//トークン情報取得
$token = get_csrf_token();
//adminユーザーでなかった場合、LOGIN_URLへリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
//商品情報取得
$items = get_all_items($db);
//VIEWファイル読み込み
include_once VIEW_PATH . '/admin_view.php';
