<?php

/**
 *
 */
class EGithubGistComment extends EGithubModel
{
	/**
	 * @todo implement
	 *
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'id',
		);
	}

	public function findScopes()
	{
		return array();
	}

	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
}
