<?php
/**
 * Nefa Libs
 * Debug Class
 * helper class for debugging
 *
 * Copyright 2015 Steffen Lange
 * Licensed under the WTFPL license
 *
 * @author Steffen Lange
 * @version 1.3.0
 */
class Debug{
	
	const CLASS_INFO = 'info';
	const CLASS_ERROR = 'error';
	
	const STYLE_PRE = 'text-align:left;padding:9.5px;';
	const STYLE_INFO = 'background-color:#eff8ff;border:1px solid #90c9f6;';
	const STYLE_ERROR = 'background-color:#ffe8e8;border:1px solid #f69090;';
	const STYLE_SUCCESS = 'background-color: #f0ffef;border:1px solid #97e597;';
	const STYLE_DEFAULT = 'background-color:#f2f2f2;border:1px solid lightgrey;';
	
	/* ================================================================================ */
	private static $times = [];
	/* ================================================================================ */
	private static $isDirectOut = false;
	
	private static function isDirectOutput(){return self::$isDirectOut;}
	
	private static function getClassAttr($class=null){
		return isset($class) ? ' class="'.$class.'"' : '';
	}
	
	private static function getStyleAttr($style){
		return ' style="'.$style.'"';
	}
	
	private static function print_r($v, $class=null, $style=null){
		if(self::isDirectOutput()) return print_r($v, true)."\n";
		$ca = self::getClassAttr($class);
		$s = isset($style) ? $style : '';
		$sa = self::getStyleAttr(self::STYLE_PRE.$s);
		return '<pre'.$ca.$sa.'>'.print_r($v, true).'</pre>';
	}
	
	/* ================================================================================ */
	public static final function init(){
		self::time();
	}

	public static final function time(){
		self::$times[] = microtime(true);
	}

	public static final function getEndTime(){
		self::time();
		$start = self::$times[0];
		$end = end(self::$times);
		reset(self::$times);
		return $end-$start;
	}

	/* ================================================================================ */
	public static function direct($state=true){
		self::toDirectOutput($state);
	}
	
	public static function toDirectOutput($state=true){
		self::$isDirectOut = $state;
	}
	
	public static function r($v, $return=false){
		$out = self::print_r($v, self::CLASS_INFO, self::STYLE_DEFAULT);
		if($return) return $out;
		else echo $out;
	}
	
	public static function i($v, $return=false){
		$out = self::print_r($v, self::CLASS_INFO, self::STYLE_INFO);
		if($return) return $out;
		else echo $out;
	}
	
	public static function s($v, $return=false){
		$out = self::print_r($v, self::CLASS_INFO, self::STYLE_SUCCESS);
		if($return) return $out;
		else echo $out;
	}
	
	public static function e($v, $return=false){
		$out = self::print_r($v, self::CLASS_ERROR, self::STYLE_ERROR);
		if($return) return $out;
		else echo $out;
	}
	
	public static function v($v){
		if(self::isDirectOutput()) var_dump($v);
		else{
			echo '<pre'.self::getClassAttr(self::CLASS_INFO).self::getStyleAttr(self::STYLE_PRE.self::STYLE_INFO).'>';
			var_dump($v);
			echo '</pre>';
		}
	}
	
	public static function exitOnError($e = 0, $m=null, $v=null){
		$out = 'ERROR('.$e.'): ';
		switch($e){
			case 99: $out .= 'missing parameter'; break;
			case 98: $out .= 'could not connect to database'; break;
			case 95: $out .= 'could not connect to solr'; break;
			case 90: $out .= 'operation failed'; break;
			case 10: $out .= 'Zugriff nicht erlaubt!'; break;
			case 1: $out = $m; break;
			default: $out = 'unkown error'; break;
		}
		if($e != 1 && isset($m)) $out .= "\n".$m;
		if(isset($v)) $out .= "\n\n".print_r($v, true);
		if(!self::isDirectOutput()) self::e($out)."\n";
		else echo $out;
		exit();
	}

	public static function exitOnSuccess($code=0){
		echo $code;
		exit();
	}
}

Debug::init();