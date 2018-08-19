CREATE TABLE IF NOT EXISTS `#__cwtraffic` (
  `id` int(11) NOT NULL auto_increment,
  `tm` int NOT NULL,
  `ip` varchar(50) NOT NULL DEFAULT '0.0.0.0',
  `iphash` BINARY(20) not null,
  `browser` varchar(255) NOT NULL,
  `bversion` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `referer` varchar(255) NOT NULL,
  `country_code` varchar( 10 )DEFAULT NULL,
  `country_name` varchar( 50 )DEFAULT NULL,
  `city` varchar( 50 )DEFAULT NULL,
  `useragent` varchar(1024) NULL,
  `continent_code` varchar(50) NULL,
  `location_latitude` decimal(10,8) NULL,
  `location_longitude` decimal(11,8) NULL,
  `location_time_zone` varchar(50) NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_ip` (`ip`),
  KEY `idx_iphash` (`iphash`),
  KEY `idx_tm` (`tm`),
  KEY `idx_iptm` (`ip`,`tm`),
  KEY `idx_iphashtm` (`iphash`,`tm`),
  UNIQUE KEY `unique_tm_ip` (`tm`, `ip`),
  UNIQUE KEY `unique_tm_iphash` (`tm`, `iphash`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cwtraffic_knownips` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '0.0.0.0',
  `botname` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `count` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`)
) ENGINE=MyISAM  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cwtraffic_whoisonline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` bigint(20) NOT NULL,
  `country_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`),
  KEY `countrycode` (`country_code`)
) ENGINE=MyISAM  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cwtraffic_total` (
  `tcount` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__cwtraffic_total` (`tcount`) VALUES (0);
