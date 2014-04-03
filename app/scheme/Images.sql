CREATE TABLE IF NOT EXISTS `Images` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`uuid` varchar(36) COLLATE utf8_bin NOT NULL,
	`parts` tinyint(2) unsigned NOT NULL,
	`enabled` varchar(100) COLLATE utf8_bin NOT NULL,
	`type` varchar(4) COLLATE utf8_bin NOT NULL,
	`width` smallint(4) unsigned NOT NULL,
	`height` smallint(4) unsigned NOT NULL,
	`horizontal` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `uuid` (`uuid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;
