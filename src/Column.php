<?php

namespace Camohub\LaravelDatagrid;


use Camohub\LaravelDatagrid\Exceptions\ColumnRenderCallbackException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class Column
{

	const TYPE_TEXT = 'text';

	const TYPE_DATE = 'date';

	const TYPE_CUSTOM = 'custom';


	/** @var Request|null $request */
	public $request = NULL;

	/** @var string $fieldName */
	public $fieldName = NULL;

	/** @var string $fieldNameExplode */
	public $fieldNameExplode = NULL;

	/** @var string $filterParamName */
	public $filterParamName = NULL;

	/** @var string $sortParamName */
	public $sortParamName = NULL;

	/** @var string $type */
	public $type = NULL;

	/** @var string $title */
	public $title = NULL;

	/** @var callable $render */
	public $render = NULL;

	/** @var callable|string $sort */
	public $sort = NULL;

	/** @var string|NULL $sort */
	public $sortValue = NULL;

	/** @var callable $filter */
	public $filter = NULL;

	/** @var string $jsFilterPatter */
	public $jsFilterPattern = NULL;

	/** @var boolean $hidden */
	public $hidden = NULL;

	/** @var boolean $noEscape */
	public $noEscape = NULL;

	/** @var \stdClass $link */
	public $link = NULL;

	/** @var callable $outherClass */
	public $outherClass = NULL;


	public function __construct(
		Request $request,
		$fieldName,  // accepts article.user.roles. Other structures need custom render callback.
		$title = '',
		$type = self::TYPE_TEXT
	) {
		$this->request = $request;
		$this->fieldName = $fieldName;
		$this->fieldNameExplode = explode('.', $fieldName);
		$this->title = $title ?: ucfirst($fieldName);
		$this->type = $type;
		$this->filterParamName = 'chgrid-filter-' . Str::slug($fieldName);
		$this->sortParamName = 'chgrid-sort-' . Str::slug($fieldName);
		$this->filterValue = $request->input($this->filterParamName, NULL);
		$this->sortValue = $request->input($this->sortParamName, NULL);

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


	public function setJSFilterPattern(string $pattern)
	{
		$this->jsFilterPattern = $pattern;

		return $this;
	}


	public function setHidden()
	{
		$this->hidden = TRUE;

		return $this;
	}


	public function setNoEscape()
	{
		$this->noEscape = TRUE;

		return $this;
	}


	public function setLink(string $routeName, string $routeParams)
	{
		$this->link = (object)[
			'routeName' => $routeName,
			'routePrams' => $routeParams,
		];

		return $this;
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

		return $this;
	}


	public function getNextSortValue()
	{
		$currentValue = strtolower($this->sortValue);

		if( !$currentValue ) return 'asc';
		elseif ( $currentValue == 'asc' ) return 'desc';
		elseif ( $currentValue == 'desc' ) return '';
	}


	/*public function getSortUrl()
	{
		$currentValue = strtolower($this->sortValue);
		$currentUrl = $this->request->fullUrl();

		if( !$currentValue )
		{
			return $this->request->fullUrlWithQuery([$this->sortParamName => 'asc']);
		}
		elseif ( $currentValue == 'asc' )
		{
			$newUrl = $this->request->fullUrlWithQuery([$this->sortParamName => 'desc']);
			$search = $this->sortParamName . '=' . $currentValue;
			$newUrl = str_replace([$search . '&', $search], '', $newUrl);
			$newUrl = trim($newUrl, '?/');
			return $newUrl;
		}
		elseif ( $currentValue == 'desc' )
		{
			$search = $this->sortParamName . '=' . $currentValue;
			$newUrl = str_replace([$search . '&', $search], '', $currentUrl);
			$newUrl = trim($newUrl, '?/');
			return $newUrl;
		}
	}*/

}