<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>カート</title>
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <ul>
        <li>注文番号：<?php print($order_id); ?></li>
        <li>購入日時：<?php print($purchased_datetime); ?></li>
        <li>合計金額：<?php print(number_format($total_price)); ?></li>
    </ul>
    <?php if(count($histories) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>購入時の商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($histories as $history){ ?>
            <tr>
              <td><?php print(h($history['name'])); ?></td>
              <td><?php print(number_format($history['price'])); ?>円</td>
              <td><?php print($history['amount']); ?></td>
              <td><?php print(number_format($history['total_price'])); ?>円</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>カートに商品はありません。</p>
    <?php } ?> 
  </div>
</body>
</html> 