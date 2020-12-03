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

$db = get_db_connect();
$user = get_login_user($db);


$historys = array_reverse(get_purchase_history($user, $db, $user['user_id']));
//トークンの生成
$token = get_csrf_token();
include_once VIEW_PATH . 'purchase_history_view.php';