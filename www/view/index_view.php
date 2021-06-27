<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'index.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print(h($item['name'])); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . $item['image']); ?>">
              <figcaption>
                <?php print(h(number_format($item['price']))); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">

                    <input type="hidden" name="item_id" value="<?php print($item['item_id']); ?>">
                    <input type="hidden" name="token" value="<?php print($token); ?>">

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
  </div>

  <div class="container item" >
    <h1>人気ランキング</h1>
    <table class="table table-bordered">
      <tr>
        <th>第1位</th>
        <th>第2位</th>
        <th>第3位</th>
      </tr>
      <tr>
        <?php foreach($rankings as $ranking){ ?>

        <td class="text-center">
          <?php print(h($ranking['name'])); ?>
        </td>
        <?php }; ?>
      </tr>
      <tr>
        <?php foreach($rankings as $ranking){ ?>

        <td>
          <img class="card-img" src = "<?php print IMAGE_PATH.$ranking['image']; ?>">
        </td>
        <?php }; ?>
      </tr>
      <tr>
        <?php foreach($rankings as $ranking){ ?>

        <td class="text-center">
          <?php print number_format($ranking['price']); ?>円
        </td>
        <?php }; ?>
      </tr>
    </table>
  </div>

</body>
</html>