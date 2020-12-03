<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'history.php';

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
$order_id = get_post('order_id');
$total = get_post('total');
$buy_datetime = get_post('buy_datetime');
$historys = get_join_historys($db, $order_id);


$historys = subtotal($historys);
include_once VIEW_PATH . 'purchase_details_history_view.php';