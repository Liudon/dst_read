<?php
/**
 * table_forum_post_ext.php
 *
 * 作者：Don(i.mu@qq.com)
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_forum_post_ext extends table_forum_post {

    /**
     * get_new_pid
     * 获取最新的pid
     */
    public function get_new_pid($tableid, $tid, $pid) {
        return DB::result_first('SELECT pid FROM %t WHERE tid=%d and pid >= %d AND invisible = 0 ORDER BY position ASC LIMIT 1',
                array(self::get_tablename($tableid), $tid, $pid));
    }

    /**
     * update_readuser
     * 更新已阅用户字段
     */
    public function update_readuser($tableid, $tid, $startpid, $endpid, $username) {

        $where = array();
        $where[] = DB::field('tid', $tid);
        $where[] = DB::field('pid', $startpid, '>');
        $where[] = DB::field('pid', $endpid, '<=');

        return DB::update(self::get_tablename($tableid), array('readuser' => $username), implode(' AND ', $where));
    }
}
