<?php

/**
 *
 */
class EGithubUserPlan extends EGithubModel
{
	/**
	 * http://developer.github.com/v3/users/#get-the-authenticated-user
	 *
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'name',
			'space',
			'collaborators',
			'private_repos',
		);
	}

	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
}
