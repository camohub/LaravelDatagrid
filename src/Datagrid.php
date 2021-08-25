<?php

namespace Camohub\LaravelDatagrid;


use Camohub\LaravelDatagrid\Exceptions\DatagridConstructException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DBBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class Datagrid
{
	/** @var Request $request_name */
	public $request;

	/** @var string $sess_name */
	public $sess_name;

	/** @var string $name */
	public $name;

	/** @var string $slug */
	public $slug;

	/** @var Builder $model */
	public $model;

	/** @var integer $defaultPerPage */
	public $defaultPerPage = 25;

	/** @var array $perPage */
	public $perPage = [10, 25, 50, 100];

	/** @var integer $onEachSide */
	public $onEachSide = 3;

	/** @var string $tableClass  */
	public $tableClass = 'table table-striped table-hover table-bordered';

	/** @var array $columns */
	protected $columns;

	/** @var array $columnsNames */
	protected $columnsNames = [];  // If column is used more than one times in grid its name needs to have suffix. This is the collection of names in use.

	/** @var integer $columnsCount */
	public $columnsCount;

	/** @var integer $columnsFiltersCount */
	public $columnsFiltersCount;

	/** @var callable $defaultSort */
	public $defaultSort = NULL;

	/** @var integer $jsFilterTimeout */
	public $jsFilterTimeout = 250;

	/** @var boolean $submitOnEnter */
	public $submitOnEnter = FALSE;

	/** @var array $getParams */
	public $getParams = [];



	public function __construct(
		$model
	) {
		if( !$model instanceof Builder && !$model instanceof DBBuilder) throw new DatagridConstructException();

		$this->request = request();
		$this->model = new QueryBuilder($model);
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


	public function setDefaultSort(callable $defaultSort)
	{
		$this->defaultSort = $defaultSort;
	}


	public function setTableClass(string $class)
	{
		$this->tableClass = $class;
	}


	public function setJSFilterTimeout(int $timeout)
	{
		$this->jsFilterTimeout = $timeout;
	}


	public function setSubmitOnEnter()
	{
		$this->submitOnEnter = TRUE;
	}


	public function addGetParam(string $name)
	{
		$this->getParams[] = $name;
	}





	public function addColumn($fieldName, $title = '', $type = Column::TYPE_TEXT)
	{
		$this->columnsNames[] = $fieldName;
		$fieldNameCount = array_count_values($this->columnsNames)[$fieldName];
		// If col with the same fieldName already has been added then new one needs suffix.
		$suffix = $fieldNameCount > 1 ? $fieldNameCount : NULL;

		$this->columns[] = $column = new Column($this->request, $fieldName, $title, $type, $suffix);

		return $column;
	}


	public function getColumns()
	{
		return $this->columns;
	}





	public function render()
	{
		$perPage = $this->request->input('chgrid-perPage', $this->defaultPerPage);

		$filter = new Filter($this->request, $this, $this->model);
		$model = $filter->getResult();
		$model = $model->paginate($perPage, ['*'], 'chgrid-page')
			->onEachSide($this->onEachSide)
			->withQueryString();

		$this->setColumnsCount();

		return view('camohubLaravelDatagrid::base', [
			'model' => $model,
			'columns' => $this->columns,
			'grid' => $this,
			'request' => $this->request,
		]);
	}


//////////////////////////////////////////////////////////////////////////////////////////////
// PROTECTED ////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

	protected function setColumnsCount()
	{
		foreach ($this->columns as $col)
		{
			if( !$col->hidden )
			{
				$this->columnsCount++;
				if( !$col->filter ) $this->columnsFiltersCount++;
			}
		}
	}

}