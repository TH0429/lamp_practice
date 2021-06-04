<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
//DBの接続情報、ユーザーIDを渡して特定のユーザーの情報を返す
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = {$user_id}
    LIMIT 1
  ";

  return fetch_query($db, $sql);
}
//DBの接続情報、ユーザー名情報を渡して名前から特定のユーザーの情報を返す
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";

  return fetch_query($db, $sql);
}
//データベース接続情報、ユーザー名情報、パスワード情報を渡しユーザー名とパスワードに沿ったユーザー情報を返す
function login_as($db, $name, $password){
  $user = get_user_by_name($db, $name);
  if($user === false || $user['password'] !== $password){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}
//データベース接続情報を渡し、ログインしているユーザーの情報を返す
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}
//データベース接続情報、ユーザー名情報、パスワード情報、パスワード確認情報を渡し指定された条件に合っていればユーザーの情報を登録する
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}
//ユーザー名情報を渡しadminユーザーであるかを確認
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}
//ユーザー名情報、パスワード情報、確認用パスワード情報を渡しそのユーザーが有効であるか確認
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}
//ユーザー名情報を渡しそのユーザー名が有効であるか確認
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//パスワード情報とパスワード確認情報を渡し、そのパスワードが有効かどうかを確認
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}
//データベース接続情報、ユーザー名情報、パスワード情報を渡しユーザー情報を追加する
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES ('{$name}', '{$password}');
  ";

  return execute_query($db, $sql);
}

