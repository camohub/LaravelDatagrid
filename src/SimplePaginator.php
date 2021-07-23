<?php

namespace Camohub\Paginator;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class SimplePaginator
{

	/** @var  Request $request */
	public $request;

	/** @var  Collection  $model */
	public $model;

	/** @var  String $routeName */
	public $routeName;

	/** @var  array $routeName */
	public $routeParams;

	/** @var  string $routeName */
	public $pageParam;

	/** @var  int */
	public $totalCount;

	/** @var  int */
	public $skip;

	/** @var  Collection */
	public $items;


	public $prev;


	public $next;


	public function __construct(
		Request $request,
		$model,
		$routeName,
		$routeParams = [],
		$perPage = 15,
		$pageParam = 'page',
		$prev = '&lsaquo; Prev',
		$next = 'Next &rsaquo;'
	) {
		$this->request = $request;
		$this->model = $model;
		$this->routeName = $routeName;
		$this->routeParams = $routeParams;
		$this->pageParam = $pageParam;
		$this->perPage = $perPage;
		$this->prev = $prev;
		$this->next = $next;

		$this->currentPage = $request->route()->parameter($pageParam) ?: ($request->query($pageParam) ?: 1);

		$this->totalCount = $this->model->count();
		$this->skip = $this->perPage * $this->currentPage - $this->perPage;
		$this->items = $this->model->skip($this->skip)->take($this->perPage)->get();
	}


	public function render()
	{
		$pageCount = $lastPage = (int) ceil($this->totalCount / $this->perPage);

		if ($pageCount < 2)
		{
			$steps = [];
		}
		else
		{
			$min = max(1, $this->currentPage - 1);
			$max = min($pageCount, $this->currentPage + 1);
			$steps = [$min, $max];
		}

		return view('camohubPaginator::simple', [
			'steps' => $steps,
			'pageCount' => $pageCount,
			'currentPage' => $this->currentPage,
			'routeName' => $this->routeName,
			'routeParams' => $this->routeParams,
			'pageParam' => $this->pageParam,
			'prev' => $this->prev,
			'next' => $this->next,
		]);
	}


	public function getItems()
	{
		return $this->items;
	}

}