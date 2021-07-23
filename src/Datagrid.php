<?php

namespace Camohub\LaravelDatagrid;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class Datagrid
{
	/** @var  string $sess_name */
	public $sess_name;

	/** @var  string  $name */
	public $name;

	/** @var  string  $slug */
	public $slug;

	/** @var  Request $request */
	public $request;

	/** @var  Collection  $model */
	public $model;

	/** @var  integer  $defaultPerPage */
	public $defaultPerPage;

	/** @var  array  $perPage */
	public $perPage;

	/** @var  integer  $onEachSide */
	public $onEachSide;

	/** @var  array  $columns */
	protected $columns;



	public function __construct(
		Request $request,
		$model = NULL
	) {
		$this->request = $request;
		$this->model = $model;
		$this->sess_name = self::class . $this->slug;

		return $this;
	}


	public function setName(String $name)
	{
		$this->name = $name;
		$this->slug = Str::slug($name);

		return $this;
	}


	public function setItemPerPage($defaultPerPage, array $perPage, $onEachSide = 3)
	{
		$this->defaultPerPage = $defaultPerPage;
		$this->perPage = $perPage;
		$this->onEachSide = $onEachSide;

		return $this;
	}




	public function addColumn($fieldName, $type = 'text', $title = '')
	{
		$this->columns[] = $column = new Column($fieldName, $type, $title);

		return $column;
	}









	public function render()
	{
		$model = $this->model->paginate($this->defaultPerPage)
			->onEachSide($this->onEachSide)
			->withQueryString();


		return view('camohubLaravelDatagrid::base', [
			'model' => $model,
			'columns' => $this->columns,
			'grid' => $this,
		]);
	}

}