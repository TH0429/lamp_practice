<?php
//MODELファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// DB利用
//DBの接続情報、商品IDを渡して特定の商品の情報を返す
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = {$item_id}
  ";

  return fetch_query($db, $sql);
}
//データベース接続情報、公開されている商品情報を渡し公開されている商品情報を返す
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}
//データベース接続情報を渡し、全ての商品情報を返す
function get_all_items($db){
  return get_items($db);
}
//データベース接続情報を渡し、公開中の商品情報を返す
function get_open_items($db){
  return get_items($db, true);
}
//データベース接続情報、商品名、商品価格、商品在庫数、商品情報、商品画像を渡し、ファイル名が存在しない場合ファイル名を登録して返す
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}
//データベース接続情報、商品名、商品価格、商品在庫数、商品情報、商品画像、ファイル名を渡しトランザクションによって商品を登録する
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}
//データベース接続情報、商品名情報、商品価格情報、商品在庫数情報、商品ファイル名情報、商品公開情報を渡し商品情報を追加する
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES('{$name}', {$price}, {$stock}, '{$filename}', {$status_value});
  ";

  return execute_query($db, $sql);
}
//データベース接続情報、商品ID、商品情報を渡し商品情報を更新する
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}
//データベース接続情報、商品ID、商品在庫数情報を渡し商品在庫を更新する
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = {$stock}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}
//データベース接続情報、商品IDを渡しトランザクションによって商品情報を破棄する
function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}
//データベース接続情報、商品IDを渡し商品を削除する
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}


// 非DB
//商品情報を渡し公開されていれば公開されている商品情報（変数$is_open）として返す
function is_open($item){
  return $item['status'] === 1;
}
//商品名情報、商品価格情報、商品在庫数情報、商品ファイル名情報、商品公開情報を渡し、全て有効であれば有効な商品情報として返す
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}
//商品名情報を渡し要件に合っていれば変数$is_validとして返す
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}
//商品価格情報を渡し要件に合っていれば変数$is_validとして返す
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//商品在庫数情報を渡し要件に合っていれば変数$is_validとして返す
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//商品のファイル名情報を渡し要件に合っていれば変数$is_validとして返す
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}
//商品公開情報を渡し公開されている情報であれば変数$is_validとして返す
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}