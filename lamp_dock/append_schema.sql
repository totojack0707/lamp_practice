-- 購入履歴テーブル項目（注文番号(主キー）、該当の注文の合計金額、ユーザーid、購入日時)
CREATE TABLE `purchase_history` (
  `order_id` int(11) AUTO_INCREMENT NOT NULL,
  `total` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `buy_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 購入明細テーブル項目（明細番号（主キー）、注文番号、商品id、購入時の商品価格、購入数、）
CREATE TABLE `purchase_details_history` (
  `detail_id` int(11) AUTO_INCREMENT NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `buy_price` int(11) NOT NULL,
  `buy_amount` int(11) NOT NULL,
  PRIMARY KEY (detail_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;