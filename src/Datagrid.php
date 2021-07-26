<?php

namespace Camohub\LaravelDatagrid;


use Illuminate\Database\Eloquent\Builder;
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
	public $onEachSide;

	/** @var string $tableClass  */
	public $tableClass = 'table table-striped table-hover table-bordered';

	/** @var array $columns */
	protected $columns;

	/** @var integer $columnsCount */
	public $columnsCount;

	/** @var integer $columnsFiltersCount */
	public $columnsFiltersCount;

	/** @var boolean $javascript */
	public $javascript = TRUE;

	/** @var integer $jsFilterTimeout */
	public $jsFilterTimeout = TRUE;



	public function __construct(
		Builder $model
	) {
		$this->request = request();
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


	public function setTableClass(string $class)
	{
		$this->tableClass = $class;
	}


	public function disableJavascript()
	{
		$this->javascript = FALSE;
	}


	public function setJSFilterTimeout(int $timeout)
	{
		$this->jsFilterTimeout = $timeout;
	}








	public function addColumn($fieldName, $title = '', $type = Column::TYPE_TEXT)
	{
		$this->columns[] = $column = new Column($this->request, $fieldName, $title, $type);

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