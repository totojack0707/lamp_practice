<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
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
unset($_SESSION["csrf_token"]);
$db = get_db_connect();
$user = get_login_user($db);

$carts = get_user_carts($db, $user['user_id']);

if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
} 

$total_price = sum_carts($carts);
//トークンの生成
$token = get_csrf_token();
include_once '../view/finish_view.php';