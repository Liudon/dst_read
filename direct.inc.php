<?php
/**
 * direct.inc.php
 *
 * 作者：Don(i.mu@qq.com)
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$tid = intval($_GET['tid']);
$pid = intval($_GET['pid']);

if ($tid < 1 || $pid < 1) {
	showmessage('submit_invalid');
}

$newpid = intval(C::t('#dst_read#forum_post_ext')->get_new_pid('tableid:' . $tid, $tid, $pid));
if (!$newpid) {
	// 已阅楼层被删除，后面没有新回复
	$newpost = C::t('forum_post')->fetch_visiblepost_by_tid('tableid:' . $tid, $tid, 0, 1);
	if ($newpost) {
		$newpid = intval($newpost['pid']);
	}
}

$url = 'forum.php?mod=redirect&goto=findpost&ptid=' . $tid . '&pid=' . $newpid;

$domain = urlencode(dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]));

include template('dst_read:redirect');