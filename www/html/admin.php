<?php
require_once '../conf/const.php'; //ファイルが読み込まれてなければ読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
//セッションスタート
session_start();
//$_SESSION['user_id']に値が格納されてなければログインページへ遷移
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
//データベースへ接続する関数を＄dbに代入
$db = get_db_connect();
//ログインしているユーザーのデータを取得し$userに代入
$user = get_login_user($db);
//$user['type']が１でなければログインページへ
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
//$itemsに全ての商品のデータを格納
$items = get_all_items($db);
///admin_view.php'の読み込み
include_once VIEW_PATH . '/admin_view.php';
