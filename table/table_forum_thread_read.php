<?php
/**
 * table_forum_thread_read.php
 *
 * 作者：Don(i.mu@qq.com)
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_thread_read extends discuz_table {

	public function __construct() {
		$this->_table = 'forum_thread_read';
		$this->_pk = 'tid';

		parent::__construct();
	}

}
?>