<?php

class pluralSysMember_sharedStatic {
	public static function debug($data){
		die(print_r($data,true));
	}
	public static function startsWith($haystack, $needle)
	{//http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
		 $length = strlen($needle);
		 return (substr($haystack, 0, $length) === $needle);
	}
    public static $hm2color = array();
    public static $colorPalete = array(0,
									   120,
									   240,
									   60,
									   180,
									   300,
									   30,
									   150,
									   270,
									   90,
									   210,
									   330);
    public static $colorPaleteState = 0;
    public static function getColorForHeadmate($hmn){
		if(array_key_exists($hmn,self::$hm2color)){
			return self::$hm2color[$hmn];
		}
		else{
			$matches = array();
			preg_match('/color\_hue=([0-9]+);(.*)/i',$hmn,$matches);
			if(count($matches)==3){
				self::$hm2color[$hmn] = intval($matches[1]);
				self::$hm2color[$matches[2]] = intval($matches[1]);
			}else{
				$paletelen = count(self::$colorPalete);
				self::$hm2color[$hmn]=self::$colorPalete[self::$colorPaleteState];
				self::$colorPaleteState=(self::$colorPaleteState+1)%(($paletelen>0)?$paletelen:1);
			}
			return self::$hm2color[$hmn];
		}
	}
	public static function bbrenderCallback(array $tag, array $rendererStates, XenForo_BbCode_Formatter_Base $formatter)
    {
		$inner = $formatter->renderTree($tag['children']);
		if(substr($inner,0,6)=='<br />'){
			$inner = substr($inner,6);
		}
		if(substr($inner,-6)=='<br />'){
			$inner = substr($inner,0,-6);
		}
		$hm = $tag['option'];
		$rot = self::getColorForHeadmate($tag['option']);
		$matches = array();
		preg_match('/color\_hue=([0-9]+);(.*)/i',$hm,$matches);
		if(count($matches)==3){
			$hm = $matches[2];
		}
		$outerbef = '';
		$outeraft = '';
		if($formatter instanceof XenForo_BbCode_Formatter_Text){
			$outerbef = '«'.$hm.' ‹ ';
			$outeraft = '›»';
		}
		else{
			$outerbef = '<div style="background-color:hsla('.(string)$rot.', 100%, 50%, 0.1);padding:5px;margin:5px;border:1px solid hsla('.(string)$rot.', 100%, 50%, 0.4);border-radius:5px;">';
			$outeraft = '<br /><span style="'.
						'display:block;'.
						'float:right;'.
						'background-color:hsla('.(string)$rot.', 100%, 95%, 0.3);'.
						'padding:3px;'.
						'margin:3px;'.
						'border:1px solid hsla('.(string)$rot.', 100%, 80%, 0.5);'.
						'border-radius:3px;'.
						'color:hsl('.(string)$rot.', 100%, 15%);'.
						'text-shadow:1px 1px 5px #FFFFFF,-1px 1px 5px #FFFFFF,1px -1px 5px #FFFFFF,-1px -1px 5px #FFFFFF;'.
											'">&nbsp'.htmlspecialchars($hm).'&nbsp</span><span style="padding:3px;margin:3px;">&nbsp</span></div>';
		}
		return $outerbef.$inner.$outeraft;
	}
}
