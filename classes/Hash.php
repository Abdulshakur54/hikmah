<?php
	class Hash{
		public static function generate($string){
			return hash('sha256', $string);
		}
	}