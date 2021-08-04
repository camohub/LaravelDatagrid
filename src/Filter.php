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
		$callDefaultSort = TRUE;

		foreach ($colmns as $col)
		{
			// Filter
			if( $col->filter && !is_null($col->filterValue) && $col->filterValue !== '' )
			{
				$this->model = ($col->filter)($this->model, $col->filterValue);
			}

			// Sort
			if( $col->sort && $col->sortValue )
			{
				if( is_callable($col->sort) )
				{
					$this->model = ($col->sort)($this->model, $col->sortValue);
					$callDefaultSort = FALSE;
				}
				else if( is_string($col->sortValue) )
				{
					$this->model->orderBy($col->fieldName, $col->sortValue);
					$callDefaultSort = FALSE;
				}
			}
		}

		if( $callDefaultSort && $this->grid->defaultSort ) $this->model = ($this->grid->defaultSort)($this->model);

		return $this->model;
	}

}