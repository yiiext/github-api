<?php

class ESimpleGithub extends CComponent
{
	private $_apiBaseUrl = 'https://api.github.com';

	private $_rateLimit = 0;
	private $_rateLimitRemaining = 0;

	public function getRateLimit()
	{
		return $this->_rateLimit;
	}

	public function getRateLimitRemaining()
	{
		return $this->_rateLimitRemaining;
	}

	/**
	 * @param string $url
	 */
	public function setApiBaseUrl($url)
	{
		$this->_apiBaseUrl = trim($url, '/');
	}

	/**
	 * @return string
	 */
	public function getApiBaseUrl()
	{
		return $this->_apiBaseUrl;
	}

	/**
	 * do a github request and return array
	 *
	 * @param string $url
	 * @param string $requestType GET POST PUT DELETE etc...
	 * @param array $data
	 * @return array
	 */
	protected function requestInternal($url, $requestType='GET', $data=array(), $header=true)
	{
		$c = curl_init();

		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($c, CURLOPT_USERAGENT, "yiiext.components.github-api (v 0.5dev)");
		curl_setopt($c, CURLOPT_TIMEOUT, 30);

		curl_setopt($c, CURLOPT_HEADER, $header); // returns header in output
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

		//curl_setopt($c, CURLOPT_HTTPHEADER, array());

		switch($requestType)
		{
			default:
			case 'GET':
				curl_setopt($c, CURLOPT_HTTPGET, true);
			break;
			case 'POST':
				curl_setopt($c, CURLOPT_POST, true);
				curl_setopt($c, CURLOPT_POSTFIELDS, $data);
			break;
			case 'PUT':
				curl_setopt($c, CURLOPT_PUT, true);
//				curl_setopt($c, CURLOPT_INFILE, $file=fopen(...));
//				curl_setopt($c, CURLOPT_INFILESIZE, strlen($data));
			break;
		}

		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($c);
		curl_close($c);

		return $response;
	}

	/**
	 * do a github request and return array
	 *
	 * @param string $url
	 * @param string $requestType GET POST PUT DELETE etc...
	 * @param array $data
	 * @return array
	 */
	public function request($url, $requestType='GET', $data=array())
	{
		$url = $this->getApiBaseUrl() . $url;

		$response = $this->requestInternal($url, $requestType, $data);

		// parse response
		$header = true;
		$content = array();
		$status = 200;
		foreach(explode("\r\n", $response) as $line)
		{
			if ($line == '') {
				$header = false;
			}
			else if ($header) {
				$line = explode(': ', $line);
				switch($line[0]) {
//					case 'Content-Type': // application/json; charset=utf-8
//					break;
					case 'Status': $status = substr($line[1], 0, 3); break;
					case 'X-RateLimit-Limit': $this->_rateLimit = intval($line[1]); break;
					case 'X-RateLimit-Remaining': $this->_rateLimitRemaining = intval($line[1]); break;
				}
			} else {
				$content[] = $line;
			}
		}

		return array($status, json_decode(implode("\n", $content)));
	}

	public function getFile($user, $repo, $branch, $file)
	{
		$url = 'https://raw.github.com/' . $user . '/' . $repo . '/' . $branch . '/' . ltrim($file, '/');

		return $this->requestInternal($url, 'GET', array(), false);
	}


}
