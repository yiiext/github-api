<?php

class EGithub extends CComponent
{
	public static $apiBaseUrl = 'https://api.github.com/';

	public $username = '';
	public $password = '';
	private $authMode = 0;

	/**
	 * @todo: implement
	 */
	public function authenticatePassword()
	{

	}

	/**
	 * @todo: implement
	 */
	public function authenticateOAuth()
	{

	}






	public static function getApiBaseUrl()
	{
		return (substr(static::$apiBaseUrl, -1, 1) == '/') ?
				substr(static::$apiBaseUrl, 0, -1) :
				static::$apiBaseUrl;
	}

	public static function githubRequest($url, $requestType, $data)
	{
		$url = static::getApiBaseUrl() . $url;

		$c = curl_init();

//		if ($this->authMode == 0) {
//			//@todo: add auth params
//		}

		// @todo: implement POST/DELETE/... request types
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($c);
		curl_close($c);

		return array(200, json_decode($response));
	}


}
