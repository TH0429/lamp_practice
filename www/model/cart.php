<?php 
//MODELファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
//DBの接続情報、ユーザーIDを引数として渡し、使用するユーザーのカート情報を返す
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
      carts.user_id = {$user_id}
  ";
  return fetch_all_query($db, $sql);
}
//DBの接続情報、ユーザーID、商品IDを渡してカート内の特定の商品の情報を返す
//そのユーザーIDでその商品IDがcartsテーブルに登録されていない時はfalseを返す
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
      carts.user_id = {$user_id}
    AND
      items.item_id = {$item_id}
  ";

  return fetch_query($db, $sql);

}
//DBの接続情報、ユーザーID、商品IDを渡してカート内の商品の個数を１個増やした状態で成功した場合はtrue、失敗した場合はfalseを返す
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}
//データベース接続情報、ユーザーID、商品ID、商品の個数情報を渡しカートに商品情報とその商品の個数を追加して成功した場合はtrue、失敗した場合はfalseを返す
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES({$item_id}, {$user_id}, {$amount})
  ";

  return execute_query($db, $sql);
}
//データベース接続情報、カートID、個数情報を渡しカート内の商品の個数を追加して成功した場合はtrue、失敗した場合はfalseを返す
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = {$amount}
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";
  return execute_query($db, $sql);
}
//データベース接続情報とカートIDを渡しカート内の商品を削除し成功した場合はtrue、失敗した場合はfalseを返す
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";

  return execute_query($db, $sql);
}
//データベース接続情報、カート情報を渡し、validate_cart_purchaseがfalseの場合falseを返す
//ユーザーのカート内情報とカート情報を確認して商品IDと商品購入数に問題がなければユーザーのカート情報を削除する
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
}
//データベース接続情報、ユーザーIDを渡しカート情報を削除
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = {$user_id}
  ";

  execute_query($db, $sql);
}

//カート情報を渡し、カート内の商品の合計金額を返す(値段×購入数)
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}
//カートの中身を確認してカート内に商品がなければfalseを返す
//カートの中身を確認して商品が公開されていないならfalseを返し、商品の在庫数からカート内の商品の個数を引いた数が１未満の場合falseを返す
//エラーがある場合、falseを返す
//カート内に商品があり、その商品が公開されている商品かつ購入数が在庫数を上回っており、エラーがない場合trueを返す
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

