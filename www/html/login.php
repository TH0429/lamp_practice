<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

//セッション開始
session_start();
//ログインできている場合、HOME_URLに移動
if(is_logined() === true){
  redirect_to(HOME_URL);
}

// トークンの生成
$token = get_csrf_token();

//VIEWファイル読み込み
include_once VIEW_PATH . 'login_view.php';