<?php 
//MODELファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'history.php';

//引数として使用するユーザーのカート情報取得
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql,array($user_id));
}

//DBの接続情報、ユーザーID、商品IDを渡してカート内の特定の商品の情報を返す
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, array($user_id, $item_id));

}

//カート内の商品の個数変更
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//カートに情報を追加
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, array($item_id, $user_id, $amount));
}

//追加処理
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, array($amount, $cart_id));
}

//カート内の商品を削除
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, array($cart_id));
}

//データベース接続情報、カート情報を渡し、validate_cart_purchaseがfalseの場合falseを返す
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  //商品が購入できる場合、購入履歴・購入明細へデータを追加、在庫数の更新、カート情報を削除するトランザクション処理を実行する
  //トランザクション処理開始
  $db->beginTransaction();
    //購入履歴に情報を追加できなかった場合、トランザクション処理を取り消しfalseを返す
    if (insert_history($db, $carts[0]['user_id']) === false) {
      $db->rollback();
      return false;
    }
    //注文番号情報を取得
    $order_id = $db->lastInsertId('order_id');
    foreach($carts as $cart){
      //購入明細に情報が追加できなかった場合、トランザクション処理を取り消しfalseを返す
      if (insert_detail($db, $order_id, $cart['item_id'], $cart['amount'], $cart['price']) === false) {
        $db->rollback();
        return false;
      }
      //商品在庫を更新できなかった場合、トランザクション処理を取り消しfalseを返す
      if(update_item_stock(
          $db, 
          $cart['item_id'], 
          $cart['stock'] - $cart['amount']
        ) === false){
        $db->rollback();
        set_error($cart['name'] . 'の購入に失敗しました。');
        return false;
      }
    }
    //カートの削除ができなかった場合、トランザクション処理を取り消しfalseを返す
    if (delete_user_carts($db, $carts[0]['user_id']) === false) {
    $db->rollback();
    return false;
  }
  //一連のトランザクション処理全てにalseが返されなかった場合処理を確定しtrueを返す
  $db->commit();
  return true;
}

//カート情報を削除
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, array($user_id));
}

//カート内の商品の合計金額を表示
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}
//商品が購入できるか検証
function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

