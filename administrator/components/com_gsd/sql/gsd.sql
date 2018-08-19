CREATE TABLE IF NOT EXISTS `#__gsd` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `params` text,
  `thing` int(11) NOT NULL DEFAULT '0' COMMENT 'The primary key of the referenced item',
  `plugin` varchar(50) NOT NULL DEFAULT '0' COMMENT 'The plugin name of the referenced item',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `thing_plugin` (`thing`,`plugin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__gsd_config` (
  `name` varchar(255) NOT NULL,
  `params` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Store any configuration in key => params maps';