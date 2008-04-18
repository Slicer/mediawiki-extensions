-- 
-- Table structure for table `poll_info`
-- 

CREATE TABLE `poll_info` (
  `poll_id` varchar(32) NOT NULL default '',
  `poll_txt` text,
  `poll_date` datetime default NULL,
  `poll_title` varchar(255) default NULL,
  `poll_domain` varchar(10) default NULL,
  PRIMARY KEY  (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `poll_message`
-- 

CREATE TABLE `poll_message` (
  `poll_id` varchar(32) NOT NULL default '',
  `poll_user` varchar(255) NOT NULL default '',
  `poll_ip` varchar(255) default NULL,
  `poll_msg` varchar(255) default NULL,
  `poll_date` datetime default NULL,
  PRIMARY KEY  (`poll_id`,`poll_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `poll_vote`
-- 

CREATE TABLE `poll_vote` (
  `poll_id` varchar(32) NOT NULL default '',
  `poll_user` varchar(255) NOT NULL default '',
  `poll_ip` varchar(255) default NULL,
  `poll_answer` int(3) default NULL,
  `poll_date` datetime default NULL,
  PRIMARY KEY  (`poll_id`,`poll_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

