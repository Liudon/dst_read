<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: uninstall.php 6752 2010-03-25 08:47:54Z cnteacher $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE pre_forum_thread_read;

ALTER TABLE pre_forum_post DROP `readuser`;

EOF;

runquery($sql);

$finish = TRUE;