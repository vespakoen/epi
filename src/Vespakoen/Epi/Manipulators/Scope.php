<?php namespace Vespakoen\Epi\Manipulators;

use Exception;

use Vespakoen\Epi\Interfaces\Manipulators\ScopeInterface;

class Scope extends Manipulator implements ScopeInterface {

	public $model;

	public $scope;

	public function make($model, $scope)
	{
		$this->model = $model;
		$this->scope = $scope;

		return $this;
	}

	public function applyTo($query)
	{
		$method = 'scope'.ucfirst($this->scope);

		if( ! method_exists($this->model, $method))
		{
			throw new Exception('Scope "'.$method.'" not found on "'.get_class($this->model).'"');
		}

		return $this->model->$method($query);
	}

}
