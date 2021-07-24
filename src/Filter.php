<?php

namespace Camohub\LaravelDatagrid;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class Filter
{

	/** @var Request $request */
	protected $request;

	/** @var Datagrid $grid */
	protected $grid;

	/** @var Builder $model */
	protected $model;


	public function __construct(
		Request $request,
		Datagrid $grid,
		$model
	) {
		$this->request = $request;
		$this->grid = $grid;
		$this->model = $model;
	}


	public function getResult()
	{
		$colmns = $this->grid->getColumns();

		foreach ($colmns as $col)
		{
			// Filter
			if( $col->filter && $colFilter = $col->filterValue )
			{
				($col->filter)($this->model, $colFilter);
			}

			// Sort
			if( $col->sort && $col->sortValue )
			{
				if( is_callable($col->sort) )
				{
					($col->sort)($this->model, $col->sortValue);
				}
				else if( is_string($col->sortValue) )
				{
					$this->model->orderBy($col->fieldName, $col->sortValue);
				}
			}
		}

		return $this->model;
	}

}