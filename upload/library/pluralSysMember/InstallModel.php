<?php

class pluralSysMember_InstallModel extends XenForo_Model {
	public function install(){
		$q = 'CREATE TABLE IF NOT EXISTS kiror_pluralsys_members (
			user_id INT,
			member_id SERIAL PRIMARY KEY,
			member_nm VARCHAR(255),
			color_hue INT
		) CHARACTER SET utf8 COLLATE utf8_general_ci;';
		$this->_getDb()->query($q);
	}
	public function uninstall(){
		$q = 'DROP TABLE IF EXISTS kiror_pluralsys_members;';
		$this->_getDb()->query($q);
	}
}
