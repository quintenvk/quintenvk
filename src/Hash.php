<?php
namespace quintenvk;
class Hash {
	public static function generateSalt($max = 15) {
		mt_srand(microtime(true)*100000 + memory_get_usage(true));
		return md5(uniqid(mt_rand(), true));
	}
}