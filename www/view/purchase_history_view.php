<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'cart.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($historys) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>購入明細</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($historys as $history){ ?>
          <tr>
            <td><?php print(h($history['order_id'])); ?></td>
            <td><?php print(h($history['buy_datetime'])); ?></td>
            <td><?php print(h(number_format($history['total']))); ?>円</td>
            <td>
            <form method="post" action="purchase_details_history.php">
              <input type="submit" value="購入明細表示" class="btn btn-secondary">
              <input type="hidden" name="order_id" value="<?php print($history['order_id']); ?>">
              <input type="hidden" name="total" value="<?php print($history['total']); ?>">
              <input type="hidden" name="buy_datetime" value="<?php print($history['buy_datetime']); ?>">
              <input type="hidden" name="csrf_token" value="<?php print($token); ?>">
            </form>
          </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>購入履歴はありません。</p>
    <?php } ?>     