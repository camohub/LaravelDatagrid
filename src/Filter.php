<?php

namespace Camohub\LaravelDatagrid;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class Filter
{

	/** @var Request $request */
	protected $request;

	/** @var Datagrid $grid */
	protected $grid;

	/** @var Collection $model */
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
			if( $col->filter && $colFilter = $this->request->input('chgrid-filter-' . $col->filterName, NULL) )
			{
				($col->filter)($this->model, $colFilter);
			}

			if( $col->sort && $sort = $this->request->input('chgrid-sort-' . $col->filterName, NULL) )
			{
				if( is_bool($col->sort) )
				{
					($col->sort)($this->model, $sort);
				}
				else
				{
					$this->model->orderBy($col->filterName, $sort);
				}
			}
		}

		return $this->model;
	}

}