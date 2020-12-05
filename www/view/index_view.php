<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'index.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <div class="d-inline">
      <ul class="list-unstyled list-group-horizontal ">
        <li class=""><h1 class="">商品一覧</h1></li>
        <li class="text-right"><div class="dropdown ">
          <form method='get'>
            <select name='sort'>
              <option value='1' name="newer">新着順</option>
              <option value='2' name="cheap">価格の低い順</option>
              <option value='3' name="expensive">価格の高い順</option>
            </select>
            <input type='submit' value='並び替え' />
          </form>
        </div></li>
      </ul>  
    </div>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="card-deck mt-5">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print(h($item['name'])); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(h(IMAGE_PATH . $item['image'])); ?>">
              <figcaption>
                <?php print(h(number_format($item['price']))); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print(h($item['item_id'])); ?>">
                    <input type="hidden" name="csrf_token" value="<?php print($token); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    <div class="page mt-5">
      <nav aria-label="Page navigation  ">
        <ul class="pagination justify-content-center">
          <?php if($now !== 1){ ?>
            <li class="page-item"><a class="page-link" href="./index.php?sort=<?php print(h($sort));?>&page_id=<?php print(h($now - 1));?>">Prev</a></li>
          <?php } ?>
          <?php
            for ( $n = 1; $n <= $full_page; $n ++){
              if ( $n === $now ){ ?>
                  <li class="page-item active "><span class="page-link" ><?php print h($now);?><span class="sr-only">(current)</span></span></li>
              <?php }else{ ?>
                <li class="page-item"><a class="page-link" href="./index.php?sort=<?php print(h($sort));?>&page_id=<?php print(h($n));?>"><?php print(h($n));?></a></li>
              <?php } ?>
            <?php } ?>       
          <?php if($now !== $full_page){ ?>
            <li class="page-item"><a class="page-link" href="./index.php?sort=<?php print(h($sort));?>&page_id=<?php print(h($now + 1));?>">Next</a></li>
          <?php } ?>
        </ul>
      </nav>
    </div>       
    <h6 class="text-center mb-5"><?php print(h($count['count']));?>件中 <?php print(h($start + 1));?> - <?php print(h(min($start + 8, $count['count'])));?>件目の商品</h6>       
  </div>
</body>