<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'cart.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($historys) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <h2>
            注文番号:<?php print(h($order_id)); ?> &nbsp;
            購入日時:<?php print(h($buy_datetime)); ?> &nbsp;
            合計金額:<?php print(h($total)); ?> &nbsp;
          </h2>
          <tr>
            <th>商品名</th>
            <th>商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($historys as $history){ ?>
          <tr>
            <td><?php print(h($history['name'])); ?></td>
            <td><?php print(h($history['buy_price'])); ?>円</td>
            <td><?php print(h(number_format($history['buy_amount']))); ?>点</td>
            <td><?php print($history['subtotal']); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php }  ?>