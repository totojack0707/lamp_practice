<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'cart.php';
//admin用購入履歴
function get_historys($db){
  $sql = "
    SELECT
      order_id, 
      total,
      user_id,
      buy_datetime
    FROM
      purchase_history
  ";

  return fetch_all_query($db, $sql);
}
//一般ユーザー用購入履歴
function get_history($db, $user_id){
  $sql = "
    SELECT
      order_id, 
      total,
      user_id,
      buy_datetime
    FROM
      purchase_history
    WHERE
      user_id = :user_id
  ";

  return fetch_all_query($db, $sql, array(':user_id' => $user_id));
}

//管理者用の購入履歴、購入明細テーブルのデータの取得
function get_join_historys($db, $order_id){
  $sql = '
    SELECT
      purchase_history.order_id,
      purchase_history.total,
      purchase_history.user_id,
      purchase_history.buy_datetime,
      purchase_details_history.detail_id,
      purchase_details_history.buy_price,
      purchase_details_history.buy_amount,
      items.name
    FROM
      purchase_history
    JOIN
      purchase_details_history 
    ON 
      purchase_history.order_id=purchase_details_history.order_id
    JOIN
      items
    ON 
      purchase_details_history.item_id=items.item_id
    WHERE 
      purchase_history.order_id = :order_id
  ';
  return fetch_all_query($db, $sql, array(':order_id' => $order_id));
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
//管理者なら全ての購入履歴をselect、一般ユーザーはそのユーザーの購入履歴をselect
function get_purchase_history($user, $db, $user_id){
  if(is_admin($user) === true){
    return get_historys($db);
  } else {
    
    return get_history($db, $user_id);
  }
}
//購入明細
function get_purchase_details_history($db, $order_id){
    return get_join_historys($db, $order_id);
}
//小計
function subtotal($historys){
  foreach($historys as $history){
    $subtotal[] = $history['buy_amount'] * $history['buy_price'];
  }
  foreach($historys as $key => $value){
    $historys[$key]['subtotal'] = $subtotal[$key];
  }
  return $historys;
}
