<?php

class pluralSysMember_Listener_FrontControllerPreView{
	public static function debug($data){
		die(print_r($data,true));
	}
	public static function callback(XenForo_FrontController $fc,
		XenForo_ControllerResponse_Abstract &$controllerResponse,
		XenForo_ViewRenderer_Abstract &$viewRenderer,
		array &$containerParams){
		if($controllerResponse instanceof XenForo_ControllerResponse_View){
			if ($controllerResponse->templateName == 'editor_dialog_pluralsys'){
				$controllerResponse->params['system_members'] = pluralSysMember_Model::getInstance()->getMembersFromUserId(XenForo_Visitor::getUserId());
			}
		}
	}
}
