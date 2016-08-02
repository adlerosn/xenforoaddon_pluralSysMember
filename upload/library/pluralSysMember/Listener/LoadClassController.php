<?php

class pluralSysMember_Listener_LoadClassController{
	public static function callback($class, array &$extend){
		if($class=='XenForo_ControllerPublic_Account'){
			$extend[]='pluralSysMember_ControllerPublic_Account';
		}
	}
}
