<?php

namespace Camohub\LaravelDatagrid;


use Camohub\LaravelDatagrid\Exceptions\ColumnRenderCallbackException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class Column
{

	/** @var string $fieldName */
	public $fieldName = NULL;

	/** @var string $type */
	public $type = NULL;

	/** @var string $title */
	public $title = NULL;

	/** @var callable $render */
	public $render = NULL;

	/** @var callable|string $sort */
	public $sort = NULL;

	/** @var callable $filter */
	public $filter = NULL;

	/** @var callable $numberFormat */
	public $numberFormat = NULL;

	/** @var boolean $hidden */
	public $hidden = NULL;

	/** @var boolean $noEscape */
	public $noEscape = NULL;

	/** @var \stdClass $link */
	public $link = NULL;

	/** @var callable $outherClass */
	public $outherClass = NULL;



	public function __construct(
		$fieldName,  // accepts article.user.roles. Other structures need custom render callback.
		$type = 'text',
		$title = ''
	) {
		$this->fieldName = explode('.', $fieldName);
		$this->type = $type;
		$this->title = $title ?: $fieldName;

		return $this;
	}


	public function setTitle(string $title)
	{
		$this->title = $title;

		return $this;
	}


	public function setRender(callable $callback)
	{
		if( !is_callable($callback) ) throw new ColumnRenderCallbackException("Column $this->fieldName render callback is not calleble");

		$this->render = $callback;

		return $this;
	}


	public function setSort(callable $callback = NULL)
	{
		$this->sort = $callback ?: TRUE;

		return $this;
	}


	public function setFilter(callable $callback)
	{
		$this->filter = $callback;

		return $this;
	}


	/**
	 * Nemeric values only
	 */
	public function setNumberFormat(int $decimals, string $point = '.', string $thousants = ' ')
	{
		$this->numberFormat = [$decimals, $point, $thousants];
	}


	public function setHidden()
	{
		$this->hidden = TRUE;
	}


	public function setNoEscape()
	{
		$this->noEscape = TRUE;
	}


	public function setLink(string $routeName, string $routeParams)
	{
		$this->link = (object)[
			'routeName' => $routeName,
			'routePrams' => $routeParams,
		];
	}


	/**
	 * Callback to add e.g. conditional class to the parent td element.
	 */
	public function setOutherClass($callback)
	{
		if( is_string($callback) )
		{
			$this->outherClass = function() use ($callback) {
				return $callback;
			};
		}
		$this->outherClass = $callback;
	}

}