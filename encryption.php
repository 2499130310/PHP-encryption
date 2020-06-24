<?php
/**
 * 服务器端数值校验规则，检测发给用户的验证值是否正确
 *
 * @param string $str 字符串
 * @param int $timeout 时间差内有效
 * @return void
 */
function state(string $str = '', int $timeout = 0)
{
	$position = [9, 1, 14, 18, 28, 21, 24];
	//加密位置（前3位校验值，后4位是时间校验值）可以更改
	sort($position);
	if (empty($str)) {
		$time = time();
		$md5 = md5(uniqid(rand(), true));
		$md5arr = str_split($md5);
		$pass = 0;
		foreach ($md5arr as $v) {
			if (is_numeric($v)) {
				$pass += $v;
			}
		}
		if (strlen($pass) == 2) {
			$pass = (string)$pass.'0';
		}
		$passStr = (string)$pass.substr($time, 3, 4);
		$passArr = str_split($passStr);
		$i = 0;
		foreach ($position as $v) {
			array_splice($md5arr, $v, 0, $passArr[$i]);
			$i++;
		}
		$str = implode('', $md5arr).substr($time, 0, 3).substr($time, 7);
		return base64_encode(strtoupper($str));
	}
	else {
		//验证值合法
		$str = base64_decode($str);
		if (strlen($str) != 45) {
			return false;
		}
		$arr = str_split($str);
		$passArr = [];
		foreach ($position as $v) {
			$passArr[] = $arr[$v];
			unset($arr[$v]);
		}
		$str = implode('', $arr);
		$passStr = implode('', $passArr);
		$md5 = str_split(substr($str, 0, 32));
		$key = intval(substr($passStr, 0, 3));
		$time = intval(substr($str, 32, 3).substr($passStr, 3).substr($str,
			35));
		//参数时间
		if (time() - $time > $timeout) {
			return false;
		}
		$num = 0;
		foreach ($md5 as $v) {
			if (is_numeric($v)) {
				$num += $v;
			}
		}
		if (strlen($num) == 2) {
			$num = (string)$num.'0';
		}
		if (intval($num) == $key) {
			return true;
		}
		return false;
	}
}
/* 作者：相思 github：https://github.com/2499130310/PHP-encryption */
/* 加密后大概是这样：NTFFNzQ2Q0I1MDM2RjA4NTNGMjVBOTA5Nzg5QzM1MDA5MDExQjg5MTU5MDU3 */
/* 加密方法：state() */
/* 验证方法：state('加密的字符串',60) 返回布尔值，60表示在60秒内有效 */
