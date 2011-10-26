<?php

/**
 *
 */
class EGithubGistHistoryChangeStatus extends EGithubModel
{
	/**
	 * http://developer.github.com/v3/gists/#get-a-single-gist
	 *
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'deletions',
			'additions',
			'total'
		);
	}

	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
}
