<?php

/**
 *
 */
class EGithubGistFile extends EGithubModel
{
	/**
	 * http://developer.github.com/v3/gists/#get-a-single-gist
	 *
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'size',
			'filename',
			'raw_url',
			'content',
		);
	}

	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
}
