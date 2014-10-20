<?php
/**
 * upgrade.inc.php
 *
 * 作者：Don(i.mu@qq.com)
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_forum_thread_read` (
  `tid` mediumint(8) unsigned NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `name` varchar(15) NOT NULL,
  `position` int(8) unsigned NOT NULL,
  `posttime` int(8) unsigned NOT NULL,
  `dateline` int(8) unsigned NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB;

EOF;

// 检查是否已有readuser字段
$table = 'forum_post';
$query = DB::query("Describe ".DB::table($table) . " readuser", 'SILENT');
if (DB::fetch($query) === false) {
$sql .= <<<EOF
ALTER TABLE  `pre_forum_post` ADD  `readuser` VARCHAR( 15 ) NOT NULL
EOF;
}

runquery($sql);

$finish = TRUE;
