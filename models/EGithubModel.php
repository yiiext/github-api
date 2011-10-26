<?php

/**
 *
 */
abstract class EGithubModel extends CModel
{
	private $_fullPopulated = false;
	private $_attributes = array();				// attribute name => attribute value
	private $_related = array();

	// @todo: add relation

	// @todo: mark new records

	// @todo: skope


	private static $_models=array();			// class name => model

	/**
	 * Returns the static model of the specified EGithubModel class.
	 * The model returned is a static instance of the class.
	 * It is provided for invoking class-level methods (something similar to static class methods.)
	 *
	 * EVERY derived class must override this method as follows,
	 * <pre>
	 * public static function model($className=__CLASS__)
	 * {
	 *     return parent::model($className);
	 * }
	 * </pre>
	 *
	 * @param string $className EGithubModel class name.
	 * @return EGithubModel model class instance.
	 */
	public static function model($className=__CLASS__)
	{
		if(isset(self::$_models[$className]))
			return self::$_models[$className];
		else
		{
			$model=self::$_models[$className]=new $className(null);
			//$model->attachBehaviors($model->behaviors());
			return $model;
		}
	}

	//abstract public function attributeNames();

	const RELATION_ONE = 0;
	const RELATION_MANY = 1;
	public function relations()
	{
		return array();
	}

	protected function findScopes()
	{
		throw new CException('This model does not provide find methods. It can only be created over relations.');
	}

	public function findAll($scope='default')
	{
		$scopes = $this->findScopes();
		if (isset($scopes[$scope])) {
			$url = $scopes[$scope];
		} else {
			throw new CException('Provided scope ' . $scope . ' is not defined by findScopes().');
		}
		list($responseCode, $data) = EGithub::githubRequest($url, 'GET', array());
		if ($responseCode != 200) {
			throw new EGithubModelRequestException($responseCode, 'Find request failed.');
		}
		$class = get_class($this);
		$models = array();
		foreach($data as $modelData) {
			$model = new $class();
			$model->populate($modelData);
			$model->_fullPopulated = true;
			$models[] = $model;
		}
		return $models;
	}

	public function find($id)
	{
		$scopes = $this->findScopes();
		if (isset($scopes['find'])) {
			$url = $scopes['find'] . '/' . urlencode($id);
		} else {
			throw new CException('Default scope for find is not defined by findScopes().');
		}
		list($responseCode, $data) = EGithub::githubRequest($url, 'GET', array());
		if ($responseCode != 200) {
			throw new EGithubModelRequestException($responseCode, 'Find request failed.');
		}
		$class = get_class($this);
		$model = new $class();
		$model->populate($data);
		$model->_fullPopulated = true;
		return $model;
	}

	public function apiRequest($url, $requestType, $data)
	{
		return EGithub::githubRequest($url, $requestType, $data);
	}

	protected function populate($attributes)
	{
		$relations = $this->relations();
		$attributeNames = $this->attributeNames();
		foreach($attributes as $attribute => $value)
		{
			if (in_array($attribute, $attributeNames)) {
				$this->_attributes[$attribute] = $value;
			}
			elseif (isset($relations[$attribute]))
			{
				$className = $relations[$attribute][1];
				switch ($relations[$attribute][0])
				{
					case static::RELATION_ONE:
						$this->_related[$attribute] = new $className();
						if (!($this->_related[$attribute] instanceof EGithubModel)) {
							throw new CException('GithubModel related class ' . $className . ' must be a subclass of EGithubModel.');
						}
						$this->_related[$attribute]->populate($value);
					break;
					case static::RELATION_MANY:
						$this->_related[$attribute] = array();
						if (!is_array($value) && !is_object($value)) {
							break;
						}
						//echo "<pre>" . $attribute . ' := '. print_r($value,1) . '</pre>';die($className);
						foreach($value as $key => $relatedRecord) {
							$this->_related[$attribute][$key] = new $className();
							if (!($this->_related[$attribute][$key] instanceof EGithubModel)) {
								throw new CException('GithubModel related class ' . $className . ' must be a subclass of EGithubModel.');
							}
							$this->_related[$attribute][$key]->populate($relatedRecord);
						}
					break;
					default:
						throw new CException('Unknown GithubModel relation type specified for relation ' . $attribute . '.');
				}
			}
		}
	}

	/**
	 * PHP getter magic method.
	 * This method is overridden so that attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 */
	public function __get($name)
	{
		if (isset($this->_attributes[$name])) {
			return $this->_attributes[$name];
//   	} else if(isset($this->_related[$name])) {
//   			return $this->_related[$name];
		} else {
			return parent::__get($name);
		}
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that attributes can be accessed like properties.
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name, $value)
	{
		if (in_array($name, $this->attributeNames())) {
			$this->_attributes[$name] = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	/**
	 * Checks if a property value is null.
	 * This method overrides the parent implementation by checking
	 * if the named attribute is null or not.
	 * @param string $name the property name or the event name
	 * @return boolean whether the property value is null
	 */
	public function __isset($name)
	{
		if (isset($this->_attributes[$name])) {
			return true;
		} else if (in_array($name, $this->attributeNames())) {
			return false;
		} else {
			return parent::__isset($name);
		}
	}

	/**
	 * Sets a component property to be null.
	 * This method overrides the parent implementation by clearing
	 * the specified attribute value.
	 * @param string $name the property name or the event name
	 */
	public function __unset($name)
	{
		if (isset($this->_attributes[$name])) {
			unset($this->_attributes[$name]);
//        } else if(isset($this->getMetaData()->relations[$name])) {
//   			unset($this->_related[$name]);
		} else {
			parent::__unset($name);
		}
	}

	/**
	 * Calls the named method which is not a class method.
	 * Do not call this method. This is a PHP magic method that we override
	 * to implement the named scope feature.
	 * @param string $name the method name
	 * @param array $parameters method parameters
	 * @return mixed the method return value
	 */
	public function __call($name,$parameters)
	{
		if(isset($this->getMetaData()->relations[$name]))
		{
			if(empty($parameters))
				return $this->getRelated($name,false);
			else
				return $this->getRelated($name,false,$parameters[0]);
		}

		$scopes=$this->scopes();
		if(isset($scopes[$name]))
		{
			$this->getDbCriteria()->mergeWith($scopes[$name]);
			return $this;
		}

		return parent::__call($name,$parameters);
	}


}

class EGithubModelRequestException extends CException
{
	private $_responseCode;

	public function getResponseCode()
	{
		return $this->_responseCode;
	}

	public function __construct($responseCode, $message)
	{
		$this->_responseCode = $responseCode;
		parent::__construct($message);
	}
}
