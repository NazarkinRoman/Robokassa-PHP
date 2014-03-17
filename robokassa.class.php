<?php

/* ===================================
 * Author: Nazarkin Roman
 * -----------------------------------
 * Contacts:
 * email - roman@nazarkin.su
 * icq - 642971062
 * skype - roman444ik
 * -----------------------------------
 * GitHub:
 * https://github.com/NazarkinRoman
 * ===================================
*/

class Robokassa {

	private $login, $password1, $password2,
	$endpoint = 'https://merchant.roboxchange.com/Index.aspx?',
	$customVars = array();

	public $OutSum, $Email = false, $InvId = 0, $Desc, $IncCurrLabel = '', $Culture = 'ru'; /* request parameters */

	/**
	* Вносит в класс данные для генерации защищенной подписи
 	*
   	* @param string $login логин мерчанта
   	* @param string $pass1 пароль №1
   	* @param string $pass2 пароль №2
   	* @param boolean $test работа с тестовым сервером
	*
   	* @return none
	*/
	public function __construct($login, $pass1, $pass2, $test = false)
	{
		$this->login = $login;
		$this->password1 = $pass1;
		$this->password2 = $pass2;

		if($test) $this->endpoint = 'http://test.robokassa.ru/Index.aspx?';
	}

	/**
	* Добавление пользовательских значений в запрос
 	*
   	* @param array $vars именованный массив с переменными(названия указывать с суффиксом shp_)
   	* @return none
	*/
	public function addCustomValues($vars)
	{
		if(!is_array($vars)) throw new Exception('Function `addCustomValues` take only array`s');

		foreach($vars as $k => $v)
			$this->customVars[$k] = $v;

	}

	/**
	* Получение URL для запроса
 	*
   	* @return string $url
	*/
	public function getRedirectURL()
	{
		$customVars = $this->getCustomValues();
		$hash = md5("{$this->login}:{$this->OutSum}:{$this->InvId}:{$this->password1}{$customVars}");
		$invId = ($this->InvId !== '') ? '&InvId=' . $this->InvId : '';
		$IncCurrLabel = ($this->IncCurrLabel !== '') ? '&IncCurrLabel=' . $this->IncCurrLabel : '';
		$Email = ($this->Email !== '') ? '&Email=' . $this->Email : '';

		return $this->endpoint . 'MrchLogin=' . $this->login
			. '&OutSum=' . (float) $this->OutSum
			. $invId
			. '&Desc=' . urlencode($this->Desc)
			. '&SignatureValue=' . $hash
			. $IncCurrLabel
            . $Email
			. '&Culture=' . $this->Culture
			. $this->getCustomValues($url = true);
	}

	/**
	* Проверка оплаты. Сравнение хеша
 	*
   	* @param string $hash значение SignatureValue, переданное кассой на Result URL
   	* @return boolean $hashValid
	*/
	public function checkHash($hash)
	{
		$customVars = $this->getCustomValues();
		$hashGenerated = md5("{$this->OutSum}:{$this->InvId}:{$this->password2}{$customVars}");

		return (strtolower($hash) == $hashGenerated);
	}

	/**
	* Получение строки с пользовательскими данными для шифрования
 	*
    * @param boolean $url генерация строки для использования в URL true/false
   	* @return string
	*/
	private function getCustomValues($url = false)
	{
		$out = '';
		$customVars = array();
		if(!empty($this->customVars))
		{
			foreach($this->customVars as $k => $v)
				$customVars[$k] = $k . '=' . $v;
				
			sort($customVars);

			if($url === TRUE)
				$out = '&' . join('&', $customVars);
			else
				$out = ':' . join(':', $customVars);
		}

		return $out;
	}

}
