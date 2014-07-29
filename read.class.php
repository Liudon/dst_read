<?php
/**
 * read.class.php
 *
 * 作者：Don(i@liudon.org)
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dst_read {

	public $showAll = false;
	public $settings = array();

	function plugin_dst_read() {
		global $_G;
		$this->settings = $_G['cache']['plugin']['dst_read'];
		$this->showAll = $_G['cache']['plugin']['dst_read']['showAll'];
	}
}

class plugin_dst_read_forum extends plugin_dst_read {

	function viewthread_top_output() {
		return '<style>.read{ background: url("static/image/common/scf.gif") no-repeat 0 50%; }</style>';
	}

	function viewthread_postfooter_output() {
		global $_G, $postlist;

		$return = array();

		// 仅对管理组用户可见标记已阅按钮
		if (!$_G['uid'] || !$_G['adminid']) {
			return $return;
		}

		// 回复列表为空
		if (!$postlist) {
			return $return;
		}

		$readinfo = C::t('#dst_read#forum_thread_read')->fetch($_G['tid']);

		$readpid = 0;
		if ($readinfo) {
			$readpid = intval($readinfo['pid']);
		}

		foreach ($postlist as $pid => $post) {
			if ($pid > $readpid) {
				$return[] = '<span id="read_' . $pid . '"><a class="read" href="plugin.php?id=dst_read:read&tid=' . $_G['tid'] . '&pid=' . $pid . '&hash=' . $_G['formhash'] . '" onclick="ajaxmenu(this, 3000, 1, 0, \'43\', \'\');return false;">' . $this->settings['click'] . '</a></span>';
			} elseif ($pid <= $readpid) {
				$return[] = '[' . htmlspecialchars($post['readuser']) . ' ' . $this->settings['hadRead'] . ']';
			}
		}

		return $return;
	}

	function forumdisplay_thread_subject_output() {
		global $_G;

		$return = array();

		// 仅对管理组用户可见
		if (!$this->showAll && !$_G['adminid']) {
			return $return;
		}

		// 主题列表为空
		if (!$_G['forum_threadlist']) {
			return $return;
		}

		$tids = array();
		foreach ($_G['forum_threadlist'] as $key => $thread) {
			$tids[] = intval($thread['tid']);
		}

		// 根据tids批量查询已阅信息
		$readlist = C::t('#dst_read#forum_thread_read')->fetch_all($tids);

		if (!$readlist) {
			return $return;
		}

		foreach ($_G['forum_threadlist'] as $key => $thread) {

			// 不存在已阅信息
			if (!$readlist[$thread['tid']]) {
			 	$return[] = '';
				continue;
			}

			$lang = sprintf('%s %s %s#', htmlspecialchars($readlist[$thread['tid']]['name']), $this->settings['readTo'], $readlist[$thread['tid']]['position']);
			if ($thread['dblastpost'] <= $readlist[$thread['tid']]['posttime']) {
				$lang = sprintf('%s %s', htmlspecialchars($readlist[$thread['tid']]['name']), $this->settings['hadRead']);
			}

			$style = sprintf('color:%s;', $this->settings['color']);
			
			// 楼层被删除后的跳转，增加一个跳转页面
			$return[] = '[<a href="plugin.php?id=dst_read:direct&tid=' . $thread['tid'] . '&pid=' . $readlist[$thread['tid']]['pid'] . '" style="' . $style . '">' . $lang . '</a>]';
		}

		return $return;
	}
}

?>