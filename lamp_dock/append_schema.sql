--購入履歴テーブル
CREATE TABLE `histories` (
    `order_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `purchased_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--テーブルのインデックス `histories`
ALTER TABLE `histories`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--テーブルのAUTO_INCREMENT `histories`
ALTER TABLE `histories`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT,

--購入明細テーブル
CREATE TABLE `details` (
    `order_id` int(11) NOT NULL,
    `item_id` int(11) NOT NULL,
    `amount` int(11) NOT NULL,
    `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--テーブルのインデックス `details`
ALTER TABLE `details`
  ADD PRIMARY KEY ('order_id', 'item_id'),
  ADD KEY `item_id` (`item_id`);