<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
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

//adminユーザーではなかった場合、その指定されたユーザーの購入履歴情報を返す
//adminユーザーの場合、全てのユーザーの購入履歴情報を返す
if(is_admin($user) === false){
    $histories = get_histories($db, $user['user_id']);
} else {
    $histories = get_all_histories($db);
}

//VIEWファイル取得
include_once VIEW_PATH . 'histories_view.php';