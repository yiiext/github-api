<?php

/**
 *
 */
class EGithubUser extends EGithubModel
{
	/**
	 * http://developer.github.com/v3/users/#get-a-single-user
	 * http://developer.github.com/v3/users/#get-the-authenticated-user
	 *
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'login',
	        'id',
	        'avatar_url',
			//'gravatar_id',
	        'url',
	        'name',
	        'company',
	        'blog',
	        'location',
	        'email',
	        'hireable',
	        'bio',
	        'public_repos',
			'public_gists',
			'followers',
			'following',
			'html_url',
			'created_at',
			'type', // User
			// for private users only
			'total_private_repos',
			'owned_private_repos',
			'private_gists',
			'disk_usage',
			'collaborators',
//			'plan',
		);
	}

	public function relations()
	{
		return array(
			'plan' => array(static::RELATION_ONE, 'EGithubUserPlan'),
		);
	}

	public function findScopes()
	{
		return array(
			'find' => '/users'
		);
	}

	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
}
