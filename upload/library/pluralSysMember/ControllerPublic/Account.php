<?php

class pluralSysMember_ControllerPublic_Account extends XFCP_pluralSysMember_ControllerPublic_Account{
	public static function debug($data){
		die(print_r($data,true));
	}
	public static function startsWith($haystack, $needle)
	{//http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
		 $length = strlen($needle);
		 return (substr($haystack, 0, $length) === $needle);
	}
	public function actionSystemMembers(){
		$visitor = XenForo_Visitor::getInstance();
		$uid = $visitor['user_id'];
		$dw = pluralSysMember_DataWriter::getInstance();
		$md = pluralSysMember_Model::getInstance();
		//self::debug($dw);
		//self::debug($md);
		if (!$visitor->hasPermission('pluralsysgrp','showbtnineditor'))
		{
			return $this->responseNoPermission();
		}
		$members = $md->getMembersFromUserId($uid);
		foreach($members as $k=>$v){
			foreach($v as $kk=>$vv){
				if($kk=='color_hue'){
					$members[$k][$kk]=intval($vv)%360;
				}
			}
		}
		//self::debug($members);
		if($this->_request->isPost()){
			//do the saving work
			//first calculate the changes to be done in database
			$brutedata = $this->_input->getInput();
			$toChange = array('new'=>array(),
							  'modify'=>array(),
							  'delete'=>array());
			$prefixes = array(
				'new'=>array('member_nm_new_','color_hue_new_'),
				'modify'=>array('member_nm_edit_','color_hue_edit_'),
				'field'=>array('member_nm','color_hue'),
			);
			$prefixes2=array('new','modify');
			foreach($brutedata as $identifier => $value){
				foreach($prefixes2 as $action){
					foreach($prefixes[$action] as $prefix){
						if(self::startsWith($identifier,$prefix)){
							foreach($prefixes['field'] as $prefix2){
								if(self::startsWith($identifier,$prefix2)){
									$memberid = str_split($identifier,strlen($prefix))[1];
									if(!array_key_exists($action,$toChange)){
										$toChange[$action]=array();
									}
									if(!array_key_exists($memberid,$toChange[$action])){
										$toChange[$action][$memberid]=array();
									}
									$toChange[$action][$memberid][$prefix2]=$value;
								}
							}
						}
					}
				}
			}
			foreach($members as $member){
				if(!array_key_exists($member['member_id'],$toChange['modify'])){
					$toChange['delete'][$member['member_id']]=array();
					foreach($prefixes['field'] as $field){
						$toChange['delete'][$member['member_id']][$field] = $member[$field];
					}
				}else{
					foreach($prefixes['field'] as $field){
						if(array_key_exists($member['member_id'],$toChange['modify'])){
							if($toChange['modify'][$member['member_id']][$field] == $member[$field]){
								unset($toChange['modify'][$member['member_id']][$field]);
							}
						}
					}
					if(count($toChange['modify'][$member['member_id']])==0){
						unset($toChange['modify'][$member['member_id']]);
					}
				}
			}
			//self::debug($toChange);
			// $toChange is prepared
			foreach($toChange['new'] as $insertion){
				$dw = pluralSysMember_DataWriter::getInstance();
				$dw->set('user_id', $uid);
				$dw->set('member_id',null);
				foreach($insertion as $field => $value){
					$dw->set($field, $value);
				}
				$dw->save();
			}
			foreach($toChange['modify'] as $memberid => $update){
				$dw = pluralSysMember_DataWriter::getInstance();
				$dw->setExistingData($memberid);
				foreach($update as $field => $value){
					$dw->set($field, $value);
				}
				$dw->save();
			}
			foreach($toChange['delete'] as $memberid => $deletion){
				$dw = pluralSysMember_DataWriter::getInstance();
				$dw->setExistingData($memberid);
				$dw->delete();
			}
			//done
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, 'account/system-members');
		}
		$viewParams = array('members' => $members);
		return $this->_getWrapper(
			'account', 'systemMembers',
			$this->responseView('XenForo_ViewPublic_Base', 'account_pluralsystemmembers', $viewParams)
		);
	}
}
