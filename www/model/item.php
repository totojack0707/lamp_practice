<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// DB利用
//特定の商品を取得する
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
      item_id = :item_id
  ";

  return fetch_query($db, $sql, array(':item_id' => $item_id));
}
//全ての商品を取得するsqlかstatusが公開になってる商品を取得するsqlを作成
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
//全ての商品のデータを取得
function get_all_items($db){
  return get_items($db);
}
//statusが公開の商品を取得
function get_open_items($db){
  return get_items($db, true);
}
//正しく値が変数に代入されているか確認し商品データをトランザクションを用いて登録
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}
//トランザクションを用いてインサート
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
//商品の登録
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
    VALUES(:name, :price, :stock, :filename, :status_value);
  ";

  return execute_query($db, $sql, $params = array(':name' => $name, ':price' => $price, ':stock' => $stock, ':filename' => $filename, ':status_value' => $status_value));
}
//statusの変更
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = :status
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array(':status' => $status, ':item_id' => $item_id));
}
//在庫の変更
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = :stock
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array(':stock' => $stock, ':item_id' => $item_id));
}
//商品の削除
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
//特定の商品のテーブルからの削除
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array(':item_id' => $item_id));
}


// 非DB

function is_open($item){
  return $item['status'] === 1;
}
//バリデーションに適合した変数をそれぞれ代入
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
//$nameのバリデーション
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}
//$priceのバリデーション
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//$stockのバリデーション
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
//$filenameのバリデーション
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}//$statusのバリデーション
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}
//公開されている全商品数を求める
function count_items($db){
  $sql = " 
    SELECT COUNT(*) AS count FROM items
    WHERE status = 1
  ";
  return fetch_query($db, $sql);
}
//必要なページ数を求める
function full_page($count){
  return ceil($count / PAGE_VIEW_MAX);
}
//現在いるページのページ番号を取得
function get_page(){
  if(!isset($_GET['page_id'])){ 
    $now = 1;
  }else{
    $now = $_GET['page_id'];
  }
  return $now;
} 
//開始位置
function now_page($now){
  if ($now == 1){
    $start = $now - 1;
  } else {
    $start = ($now - 1) * 8;
  }
  return $start;
}  
//８件ごとに取得
function get_limit_items($db, $start){
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
    LIMIT 
      :start,:max
  ';
  return fetch_all_query($db, $sql, array(':start' => $start, ':max' => PAGE_VIEW_MAX));
}