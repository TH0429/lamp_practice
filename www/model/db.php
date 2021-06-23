<?php
// データベースに接続
function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
 
  try {
    // データベースに接続
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  return $dbh;
}
//データベース接続情報、実行したいsql文（SELECT文）を渡すとデータがある場合はそのデータを連想配列で返し、データがない場合はfalseを返す
function fetch_query($db, $sql, $params = array()){
  try{
     //sql文を実行する準備
    $statement = $db->prepare($sql);
    //sqlを実行
    $statement->execute($params);
    return $statement->fetch();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}
//データベース接続情報、実行したいsql文（SELECT文）を渡すと全てのデータを連想配列で返し、データがない場合はfalseを返す
function fetch_all_query($db, $sql, $params = array()){
  try{
     //sql文を実行する準備
    $statement = $db->prepare($sql);
    //sqlを実行
    $statement->execute($params);
    return $statement->fetchAll();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}
//データベース接続情報、実行したいsql文（SELECT文）を渡すと配列を実行し、データがない場合はfalseを返す
function execute_query($db, $sql, $params = array()){
  try{
     //SQL文を実行する準備
    $statement = $db->prepare($sql);
    //sqlを実行
    return $statement->execute($params);
  }catch(PDOException $e){
    set_error('更新に失敗しました。'.$e);
  }
  return false;
}