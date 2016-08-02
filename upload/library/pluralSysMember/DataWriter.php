<?php

class pluralSysMember_DataWriter extends XenForo_DataWriter{
	public static function getInstance(){
		return XenForo_DataWriter::create('pluralSysMember_DataWriter');
	}
	const TABLE_NAME = 'kiror_pluralsys_members';
	const TABLE_PK   = 'member_id';
	protected function _getFields() {
		return array(
			self::TABLE_NAME => array(
				'member_id' => array('type' => self::TYPE_UINT,   'autoIncrement' => true),
				'user_id'   => array('type' => self::TYPE_UINT,   'required' => true),
				'member_nm' => array('type' => self::TYPE_STRING, 'default' => ''),
				'color_hue' => array('type' => self::TYPE_INT,    'default' => 0)
		));
	}
	protected function _getExistingData($data) {
		if ($id = $this->_getExistingPrimaryKey($data, self::TABLE_PK))
		{
			return array(self::TABLE_NAME => $this->_getModel()->getMemberId($id));
		}
		else return false;
	}
	protected function _getUpdateCondition($tableName)
	{
		return self::TABLE_PK . ' = ' . $this->_db->quote($this->getExisting(self::TABLE_PK, self::TABLE_NAME));
	}
	protected function _getModel()
	{
		return $this->getModelFromCache('pluralSysMember_Model');
	}
}
