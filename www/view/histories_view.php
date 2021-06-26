<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入履歴</title>
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
    <h2>購入履歴</h2>
    <table>
        <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>購入明細</th>
        </tr>
        <?php foreach ($histories as $history){ ?>
        <tr>
            <td><?php print h($history['order_id']);?></td>   
            <td><?php print h($history['purchased_datetime']);?></td>   
            <td><?php print h($history['total_price']); ?></td>  
            <td>
                <form method="post" action="details.php">
                <input type="hidden" name="order_id" value=<?php print $history['order_id'];?>>
                <input type="submit" value="購入明細表示">
                </form>
            </td> 
        <?php }?>
        </tr>
    </table>
</body>
</html> 