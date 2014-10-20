<?php
/**
 * uninstall.php
 *
 * 作者：Don(i.mu@qq.com)
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
