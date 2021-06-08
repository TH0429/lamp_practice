<?php
//デバック用
function dd($var){
  //var_dumpで情報をページ内に出力する
  var_dump($var);
  exit();
}
//リダイレクト処理
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}
//GETの入力値の取得
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}
//POSTの入力値の取得
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}
//ファイルの入力値の取得
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}
//セッションの入力値を取得
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}
//セッションデータの設定
function set_session($name, $value){
  $_SESSION[$name] = $value;
}
//エラーメッセージの設定
function set_error($error){
  $_SESSION['__errors'][] = $error;
}
//エラーメッセージの取得
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}
//エラーが存在するかの確認
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}
//完了メッセージの設定
function set_message($message){
  $_SESSION['__messages'][] = $message;
}
//完了メッセージを取得
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}
//ログインチェック
function is_logined(){
    //セッションのユーザIDを返す
  return get_session('user_id') !== '';
}
//画像をアップロードする
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
   //ファイル拡張子のチェック
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}
//ランダムな文字列を生成
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}
//画像を保存する
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}
//画像を消去する
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  //指定した値が存在しない場合、falseを返す
  return false;
  
}


//文字列のバリデーション
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}
//正規表現の英数字
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}
//正規表現の正の整数
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}
//正規表現によるマッチング
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

//画像のバリデーション
function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    //指定した値が存在しない場合、falseを返す
    return false;
  }
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    //指定した値が存在しない場合、falseを返す
    return false;
  }
  return true;
}
//htmlspecialcharsをオリジナルの関数hで示す
function h($str){
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

// ランダムな文字列（トークン）を生成、セッションにトークンを設定し$tokenを返す
function get_csrf_token(){
  $token = get_random_string(30);
  set_session('csrf_token', $token);
  return $token;
}

// トークン情報を確認、一致しない場合はfalseを返す
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  return $token === get_session('csrf_token');
}
