<?php
/**
 * read.inc.php
 *
 * 作者：Don(i.mu@qq.com)
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if(!$_G['uid'] || !$_G['adminid']){
    // 游客和没有管理权限的用户直接退出
    showMessage('dst_read:no_perm');
}

$pid = intval($_GET['pid']);
$tid = intval($_GET['tid']);

if ($tid < 1 || $pid < 1) {
    showmessage('submit_invalid');
}

if(empty($_GET['hash']) || $_GET['hash'] != formhash()) {
    showmessage('submit_invalid');
}

include libfile('function/forum');

// 判断tid和pid是对应的
$post = get_post_by_pid($pid);
if ($post['tid'] != $tid) {
    showmessage('thread_nonexistence');
}

$thread = get_thread_by_tid($tid);
if (empty($thread)) {
    showmessage('thread_nonexistence');
}

$hasPerm = false;
if ($_G['adminid'] == 1 || $_G['adminid'] == 2) {
    $hasPerm = true;
} elseif ($_G['adminid'] == 3) {
    // 判断当前用户为当前版块版主
    $ismoderator = C::t('forum_moderator')->fetch_uid_by_fid_uid($thread['fid'], $_G['uid']);
    if ($ismoderator) {
        $hasPerm = true;
    }
}

// 判断用户权限
if (!$hasPerm) {
    showMessage('forum_access_disallow');
}

$read = C::t('#dst_read#forum_thread_read')->fetch($tid);

$readpid = 0;
if ($read) {
    $readpid = intval($read['pid']);
}

if ($readpid >= $pid) {
    $extra['extrajs'] = '<script type="text/javascript">$("read_' . $pid . '").innerHTML = "' . htmlspecialchars($read['name']) . ' ' . $_G['cache']['plugin']['dst_read']['hadRead'] . '";</script>';
    showmessage('dst_read:had_read', '', array(), $extra);
}

// 更新post表的read字段
C::t('#dst_read#forum_post_ext')->update_readuser('tid:' . $tid, $tid, $readpid, $pid, $_G['username']);

$data = array(
    'tid' => $tid,
    'pid' => $pid,
    'uid' => $_G['uid'],
    'name' => $_G['username'],
    'position' => $post['position'],
    'posttime' => $post['dateline'],
    'dateline' => TIMESTAMP,
);

C::t('#dst_read#forum_thread_read')->insert($data, false, true);

// 记录日志
$logMessage = sprintf('[uid: %s], [username: %s], [tid: %s], [pid: %s - %s]', $_G['uid'], $_G['username'], $tid, $readpid, $pid);
writelog('read', $logMessage);

$search = array(
    '{user}'
);
$replace = array(
    htmlspecialchars($_G['username']),
);
$lang = str_replace($search, $replace, $_G['cache']['plugin']['dst_read']['hadRead']);

$extra['extrajs'] = '<script type="text/javascript">$("read_' . $pid . '").innerHTML = "' . $lang . '";</script>';

showmessage('dst_read:read_success', '', array(), $extra);
