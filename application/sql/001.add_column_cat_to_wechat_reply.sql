ALTER TABLE `wechat_reply` ADD COLUMN `cat_name` VARCHAR(64) NOT NULL DEFAULT 'exact_match';

update `wechat_reply` set `cat_name` = 'exact_match';
