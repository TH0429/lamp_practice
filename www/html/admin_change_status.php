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

$user = get_login_user($db);
//adminユーザー以外でログインした場合、LOGIN_URLへリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
//アイテム情報変更
$item_id = get_post('item_id');
$changes_to = get_post('changes_to');

if($changes_to === 'open'){
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  set_message('ステータスを変更しました。');
}else if($changes_to === 'close'){
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  set_message('ステータスを変更しました。');
}else {
  set_error('不正なリクエストです。');
}

//ADMIN_URLへリダイレクト
redirect_to(ADMIN_URL);