DROP TABLE IF EXISTS #__sef_config;

CREATE TABLE #__sef_config (
  `id` TINYINT(1) NOT NULL auto_increment,
  `archive` varchar(50) NOT NULL default 'archive',
  `weblinks` varchar(50) NOT NULL default 'weblinks',
  `poll` varchar(50) NOT NULL default 'poll',
  `banners` varchar(50) NOT NULL default 'banners',
  `contact` varchar(50) NOT NULL default 'contact',
  `search` varchar(50) NOT NULL default 'search',
  `newsfeeds` varchar(50) NOT NULL default 'newsfeeds',
  `custom_comp` text NOT NULL default '',
  `enabled` tinyint(1) NOT NULL default '1',
  `space` varchar(10) NOT NULL default '-',
  `sufix` varchar(10) NOT NULL default '',
  `alias` tinyint(1) NOT NULL default '0',
  `lowercase` tinyint(1) NOT NULL default '1',
  `inc_sec` tinyint(1) NOT NULL default '1',
  `inc_cat` tinyint(1) NOT NULL default '1',
  `uniqitem` tinyint(1) NOT NULL default '0',
  `fish` tinyint(1) NOT NULL default '0',
  `nupd` tinyint(1) NOT NULL default '0',
  `bird` tinyint(1) NOT NULL default '0',
  `nsrd` tinyint(1) NOT NULL default '0',
  `lgrd` tinyint(1) NOT NULL default '0',
  `www_redirect` tinyint(1) NOT NULL default '0',
  `debug` tinyint(1) NOT NULL default '0',
  `debugip` varchar(20) NOT NULL default '',
  `custom404` varchar(255) NOT NULL default '',
  `url_replace` text NOT NULL default '',
  `url_exception` text NOT NULL default '',
  `com_exception` text NOT NULL default '',
  `cache` tinyint(1) NOT NULL default '0',
  `cachetime` int(11) NOT NULL default '0',
  `log404` tinyint(1) NOT NULL default '0',
  `seo_h1` tinyint(1) NOT NULL default '0',
  `seo_title` tinyint(1) NOT NULL default '0',
  `seo_alt` tinyint(1) NOT NULL default '0',
  `seo_canonical` tinyint(1) NOT NULL default '0',
  `seo_nofollow` tinyint(1) NOT NULL default '0',
  `seo_blank` tinyint(1) NOT NULL default '0',
  `seo_icon` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO #__sef_config VALUES (
  '1',
  'archive',
  'weblinks',
  'poll',
  'banners',
  'contact',
  'search',
  'newsfeeds',
  'customcomp=>cc',
  '1',
  '-',
  '.html',
  '0',
  '1',
  '1',
  '1',
  '0',
  '0',
  '0',
  '0',
  '0',
  '0',
  '0',
  '0',
  '',
  'default',
  '&aring;=>aa',
  '?|!|,|;|:',
  '',
  '0',
  '86400',
  '0',
  '0',
  '0',
  '0',
  '0',
  '0',
  '0',
  '0'
);

DROP TABLE IF EXISTS #__sef_alias;

CREATE TABLE #__sef_alias (
  `id` int(11) NOT NULL auto_increment,
  `non_sef_url` text NOT NULL default '',
  `alias` text NOT NULL default '',
  `title` text NOT NULL default '',
  `metakey` text NOT NULL default '',
  `metadesc` text NOT NULL default '',
  `canonical` text NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `non_sef_url` (`non_sef_url`(255)),
  KEY `alias` (`alias`(255)),
  KEY `published` (`published`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO #__sef_alias VALUES (
  '1',
  'index.php?option=com_content&view=article&id=42&catid=3',
  'Newsflash-4',
  '',
  '',
  '',
  '',
  '0'
);

DROP TABLE IF EXISTS #__sef_redirect;

CREATE TABLE #__sef_redirect (
  `id` int(11) NOT NULL auto_increment,
  `source` text NOT NULL default '',
  `target` text NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `source` (`source`(255)),
  KEY `published` (`published`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO #__sef_redirect VALUES (
  '1',
  'some/url/',
  'animals/pets/cat/',
  '301 Moved Permanently',
  '0'
);

DROP TABLE IF EXISTS #__sef_log;

CREATE TABLE #__sef_log (
  `id` int(11) NOT NULL auto_increment,
  `url` text NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL default '',
  `referer` text NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `url` (`url`(255))
) TYPE=MyISAM DEFAULT CHARSET=utf8;
