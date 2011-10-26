<?php

/**
 *
 */
class EGithubGist extends EGithubModel
{
	/**
	 * http://developer.github.com/v3/gists/#get-a-single-gist
	 *
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'id',
			'url',
			'description',
			'public',
//			'user',
//			'files',
			'comments', // no relation, boolean
			'html_url',
			'git_pull_url',
			'git_push_url',
			'created_at',
//			'forks',
//			'history',
		);
	}

	public function relations()
	{
		return array(
			'user'    => array(static::RELATION_ONE,  'EGithubUser'),
			'files'   => array(static::RELATION_MANY, 'EGithubGistFile'),
			'forks'   => array(static::RELATION_MANY, 'EGithubGist'),
			'history' => array(static::RELATION_MANY, 'EGithubGistHistory'),
		);
	}

	protected function findScopes()
	{
		return array(
			'find' => '/gists',
			'default' => '/gists',
			'public' => '/gists/public',
			'starred' => '/gists/starred',
		);
	}


	public function create()
	{
		$this->apiRequest('/gists', 'POST', $this->getAttributes());
	}

	protected function postUrls()
	{
		return array(
			'patch' => array(
				'url' => '/gists/:id',
				'requestType' => 'PATCH',
			),
			'star' => array(
				'url' => '/gists/:id/star',
				'requestType' => 'PUT',
			),
			'unstar' => array(
				'url' => '/gists/:id/star',
				'requestType' => 'DELETE',
			),
			'isStarred' => array(
				'url' => '/gists/:id/star',
				'requestType' => 'GET',
			),
		);
		$findUrl = '/gists/:id';
	}

	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
}
