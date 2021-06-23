<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

function insert_history($db, $user_id){
    $sql = "
     INSERT INTO
       histories(
         user_id
       )
     VALUES(?)
     ";
  
   return execute_query($db, $sql, array($user_id));
  }
  
  function insert_detail($db, $order_id, $item_id, $amount, $price){
    $sql = "
      INSERT INTO
      details(
          order_id,
          item_id,
          amount,
          price
        )
      VALUES(?, ?, ?, ?)
    ";  
    return execute_query($db, $sql, array($order_id, $item_id, $amount, $price));  
  }
  //データベース接続情報とユーザー情報を渡し、注文した日時順（降順）で購入履歴情報を取得
  function get_histories($db, $user_id) {
    $sql = "
    SELECT
      histories.order_id,
      histories.purchased_datetime,
      SUM(details.amount * details.price) AS total_price
    FROM
      histories
    JOIN
      details
    ON
      histories.order_id = details.order_id
    WHERE
      histories.user_id = ?  
    GROUP BY
      histories.order_id
    ORDER BY 
      purchased_datetime DESC 
    ";

    return fetch_all_query($db, $sql,array($user_id));
  }
  //データベース接続情報と注文番号情報を渡し、注文した日時順（降順）で特定の購入履歴情報を取得
  function get_history($db, $order_id) {
    $sql = "
    SELECT
      histories.order_id,
      histories.purchased_datetime,
      histories.user_id,
      SUM(details.amount * details.price) AS total_price
    FROM
      histories
    JOIN
      details
    ON
      histories.order_id = details.order_id
    WHERE
      histories.order_id = ?  
    GROUP BY
      histories.order_id
    ORDER BY 
      purchased_datetime DESC 
    ";
 
    return fetch_query($db, $sql, array($order_id));
  }
  //データベース接続情報を渡し、注文した日時順（降順）で全てのユーザーの購入履歴情報を取得
  function get_all_histories($db) {
    $sql = "
    SELECT
    histories.order_id,
    histories.purchased_datetime,
    SUM(details.amount * details.price) AS total_price
    FROM
      histories
    JOIN
      details
    ON
      histories.order_id = details.order_id
    GROUP BY
      histories.order_id
    ORDER BY 
      purchased_datetime DESC
    ";
  
    return fetch_all_query($db, $sql);
   }
  //データベース接続情報と注文番号情報を渡し、商品明細情報を渡す
   function get_details($db, $order_id) {
     $sql = "
      SELECT
        details.order_id,
        details.item_id,
        details.price,
        details.amount,
        items.name
      FROM
        details
      JOIN
        items
      ON
        details.item_id = items.item_id  
      WHERE
        order_id = ?
     ";
  
     return fetch_all_query($db, $sql, array($order_id));
   }
  
  