<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'history.php';
require_once MODEL_PATH . 'cart.php';
session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$token = get_post('csrf_token');
if(is_valid_csrf_token($token) === false){
  //エラーメッセージ
  set_error('不正なアクセスです');
  redirect_to(HOME_URL);
}
//$_SESSION["csrf_token"]の削除
unset($_SESSION["csrf_token"]);
//データベース接続
$db = get_db_connect();
//
$user = get_login_user($db);

$carts = get_user_carts($db, $user['user_id']);



$total = sum_carts($carts);
//商品の購入後、購入履歴、明細の登録、カートの削除
if(regist_history_transaction($db, $total, $user['user_id'], $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
}
include_once '../view/finish_view.php';