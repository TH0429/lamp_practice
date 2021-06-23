<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入明細</title>
    <style>
       table {
         width: 1000px;
         border-collapse: collapse;
      }

      table,
      tr,
      th,
      td {
         border: solid 1px;
         padding: 10px;
      }
   </style>
</head>
<body>
<?php include VIEW_PATH . 'templates/header_logined.php'; ?>
<h2>購入明細</h2>
<table>
 <ul>
  <li>注文番号：<?php print $history['order_id'];?></li>
  <li>購入日時：<?php print $history['purchased_datetime'];?></li>
  <li>合計金額：<?php print $history['total_price'];?></li>
 </ul>
</table>
   <table>
    <tr>
        <th>商品名</th>
        <th>購入時の商品価格</th>
        <th>購入数</th>
        <th>小計</th>
    </tr>
    <?php foreach ($details as $detail) {?>
    <tr>
        <td><?php print h($detail['name']);?></td>
        <td><?php print h($detail['price']);?></td>
        <td><?php print h($detail['amount']);?></td>
        <td><?php print h($detail['price']*$detail['amount']);?></td>
    </tr>
    <?php }?>
   </table> 
</body>
</html> 