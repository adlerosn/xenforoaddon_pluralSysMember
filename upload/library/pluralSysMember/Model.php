<?php

class pluralSysMember_Model extends XenForo_Model {
	protected static $_instance = null;
	public static function getInstance(){
		if(is_null(static::$_instance)){
			static::$_instance = XenForo_Model::create('pluralSysMember_Model');
		}
		return static::$_instance;
	}
	public function getMemberId($member_id){
		$member_id = intval($member_id);
		return $this->_getDb()->fetchRow('SELECT * FROM kiror_pluralsys_members WHERE member_id = ?',$member_id);
	}
	public function getMembersFromUserId($user_id){
		$user_id = intval($user_id);
		return $this->_getDb()->fetchAll('SELECT * FROM kiror_pluralsys_members WHERE user_id = ?',$user_id);
	}
}
