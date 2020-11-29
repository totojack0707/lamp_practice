<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'cart.php';

//特定のユーザーの購入履歴、購入明細テーブルのデータを取得
function get_history($db, $user_id){
  $sql = "
    SELECT
      order_id,
      total,
      user_id,
      buy_datetime,
      detail_id,
      buy_price,
      buy_amount
    FROM
      purchase_history
    JOIN
      purchase_history ON purchase_details_history.order_id=purchase_history.order_id
    WHERE
      user_id = :user_id
  ";

  return fetch_query($db, $sql, array(':user_id' => $user_id));
}
//管理者用の購入履歴、購入明細テーブルのデータの取得
function get_historys($db){
  $sql = '
    SELECT
    order_id,
    total,
    user_id,
    buy_datetime,
    detail_id,
    buy_price,
    buy_amount
    FROM
      purchase_history
    JOIN
      purchase_history ON purchase_details_history.order_id=purchase_history.order_id
  ';
  return fetch_all_query($db, $sql);
}
//トランザクションを用いてインサート
function regist_history_transaction($db, $total, $user_id, $carts){
  $db->beginTransaction();
  if(purchase_carts($db, $carts) === false){
    $db->rollback();
    return false;
  } 
  if(insert_purchase_history($db, $total, $user_id) === false){
    $db->rollback();
    return false;
  }  
  $order_id = $db -> lastInsertId();
  foreach($carts as $cart){
    if(insert_purchase_details_history($db, $order_id, $cart['item_id'], $cart['price'], $cart['amount']) === false){
      $db->rollback();
      return false;
    }
  }
  $db->commit();
    return true;
}
//購入履歴の登録
function insert_purchase_history($db, $total, $user_id){
  $sql = "
    INSERT INTO
      purchase_history(
        total,
        user_id
      )
    VALUES(:total, :user_id);
  ";

  return execute_query($db, $sql, $params = array(':total' => $total, ':user_id' => $user_id));
}
//購入明細履歴の登録
function insert_purchase_details_history($db, $order_id, $item_id, $buy_price, $buy_amount){
  $sql = "
    INSERT INTO
      purchase_details_history(
        order_id,
        item_id,
        buy_price,
        buy_amount
      )
    VALUES(:order_id, :item_id, :buy_price, :buy_amount);
  ";

  return execute_query($db, $sql, $params = array(':order_id' => $order_id, ':item_id' => $item_id, ':buy_price' => $buy_price, ':buy_amount' => $buy_amount));
}

