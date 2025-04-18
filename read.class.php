<?php
/**
 * read.class.php
 *
 * 作者：Don(i.mu@qq.com)
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
        return '<style>.read{ background: url("static/image/common/scf.gif") no-repeat 4px 50%; padding-left: 25px; }</style>';
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
                $search = array(
                    '{user}'
                );
                $replace = array(
                    htmlspecialchars($post['readuser']),
                );
                $return[] = str_replace($search, $replace, $this->settings['hadRead']);
            }
        }

        return $return;
    }

    function _forumdisplay_output() {
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

            if ($thread['dblastpost'] <= $readlist[$thread['tid']]['posttime']) {
                $search = array(
                    '{user}'
                );
                $replace = array(
                    htmlspecialchars($readlist[$thread['tid']]['name']),
                );
                $lang = str_replace($search, $replace, $this->settings['hadRead']);
            } else {
                $search = array(
                    '{user}',
                    '{number}',
                );
                $replace = array(
                    htmlspecialchars($readlist[$thread['tid']]['name']),
                    $readlist[$thread['tid']]['position'],
                );
                $lang = str_replace($search, $replace, $this->settings['readTo']);
            }

            $style = sprintf('color:%s;', $this->settings['color']);

            if ($this->settings['direct']) {
                // 楼层被删除后的跳转，增加一个跳转页面
                $directurl = sprintf('plugin.php?id=dst_read:direct&tid=%s&pid=%s', $thread['tid'], $readlist[$thread['tid']]['pid']);
            } else {
                $directurl = sprintf('forum.php?mod=redirect&goto=findpost&ptid=%s&pid=%s', $thread['tid'], $readlist[$thread['tid']]['pid']);
            }

            $return[] = '<a href="' . $directurl . '" style="' . $style . '">' . $lang . '</a>';
        }

        return $return;
    }

    function forumdisplay_thread_output() {
        global $_G;

        if (empty($_G['forum']['picstyle']) || $_G['cookie']['forumdefstyle']) {
            return array();
        }

        return $this->_forumdisplay_output();
    }

    function forumdisplay_thread_subject_output() {
        return $this->_forumdisplay_output();
    }
}
