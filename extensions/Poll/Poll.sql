CREATE TABLE `poll` (
  `poll_id` VARCHAR(32),
  `poll_user` VARCHAR(255),
  `poll_ip` VARCHAR(255),
  `poll_answer` INTEGER(3),
  `poll_date` DATETIME,
  PRIMARY KEY  (`poll_id`,`poll_user`)
);
 
CREATE TABLE `comments` (
  `poll_id` VARCHAR(32),
  `poll_user` VARCHAR(255),
  `poll_ip` VARCHAR(255),
  `poll_comment` VARCHAR(255),
  `poll_date` DATETIME,
  PRIMARY KEY  (`poll_id`,`poll_user`)
);

