<?php
	
	class Painel
	{

		
		public static $cargos = [
		'0' => 'Normal',
		'1' => 'Sub Administrador',
		'2' => 'Administrador'];
		

		public static function generateSlug($str){
			$str = mb_strtolower($str);
			$str = preg_replace('/(â|á|ã)/', 'a', $str);
			$str = preg_replace('/(ê|é)/', 'e', $str);
			$str = preg_replace('/(í|Í)/', 'i', $str);
			$str = preg_replace('/(ú)/', 'u', $str);
			$str = preg_replace('/(ó|ô|õ|Ô)/', 'o',$str);
			$str = preg_replace('/(_|\/|!|\?|#)/', '',$str);
			$str = preg_replace('/( )/', '-',$str);
			$str = preg_replace('/ç/','c',$str);
			$str = preg_replace('/(-[-]{1,})/','-',$str);
			$str = preg_replace('/(,)/','-',$str);
			$str=strtolower($str);
			return $str;
		}

		public static function logado(){
			@session_start();
			return isset($_COOKIE['login']) ? true : false;

			
		}

		public static function loggout(){
			session_destroy();
			echo'<script>(location.href="Login")</script>';
		}

	
	}