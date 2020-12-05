<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
//ソート
$sort = (int)get_get('sort');
//dd($sort);
//公開されている全商品数を求める
$count = count_items($db);

//総合ページ数を求める
$full_page = (int)full_page($count['count']);
//現在いるページのページ番号を取得
$now = (int)get_page();

//開始位置
$start = now_page($now);
//８件ごとに取得
$items = get_limit_items($db, $start, $sort);

//トークンの生成
$token = get_csrf_token();

include_once VIEW_PATH . 'index_view.php';