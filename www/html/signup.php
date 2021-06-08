<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
//セッション開始
session_start();
//ログインしている場合、HOME_URLへリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}
//トークン生成
$token = get_csrf_token();
//VIEWファイル読み込み
include_once VIEW_PATH . 'signup_view.php';



