<?php
//MODELファイル読み込み
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
//セッション開始
session_start();
//ログイン成功の場合、HOME_URLへリダイレクト
if(is_logined() === true){
  redirect_to(HOME_URL);
}
//VIEWファイル読み込み
include_once VIEW_PATH . 'signup_view.php';



