<?php

class EGithub extends CComponent
{
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
	






	protected function githubRequest()
	{
		$c = curl_init();
		if ($this->authMode == 0) {
			//@todo: add auth params
		}
		//@todo: add url

		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($c);
		curl_close($c);
		// @todo: evaluate response
	}


}
