<?php

/**
 *
 */
class EGithubGistHistory extends EGithubModel
{
	/**
	 * http://developer.github.com/v3/gists/#get-a-single-gist
	 *
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'url',
			'version',
//			'user',
//			'change_status',
			'committed_at',
		);
	}

	public function relations()
	{
		return array(
			'user'          => array(static::RELATION_ONE, 'EGithubUser'),
			'change_status' => array(static::RELATION_ONE, 'EGithubGistHistoryChangeStatus'),
		);
	}

	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
}
