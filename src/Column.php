<?php

namespace Camohub\LaravelDatagrid;


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

	/** @var string|NULL $filter */
	public $filterValue = NULL;

	/** @var callable $filterRender */
	public $filterRender = NULL;

	/** @var string $jsFilterPatter */
	public $jsFilterPattern = NULL;

	/** @var boolean $submitOnEnter */
	public $submitOnEnter = FALSE;

	/** @var boolean $hidden */
	public $hidden = NULL;

	/** @var boolean $noEscape */
	public $noEscape = NULL;

	/** @var \stdClass $link */
	public $link = NULL;

	/** @var callable $outherClass */
	public $outherClass = NULL;

	/** @var string $outherClass */
	public $outherTitleClass = NULL;


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


	public function setFilterRender(callable $callback)
	{
		$this->filterRender = $callback;

		return $this;
	}


	public function setJSFilterPattern(string $pattern)
	{
		$this->jsFilterPattern = $pattern;

		return $this;
	}


	public function setSubmitOnEnter()
	{
		$this->submitOnEnter = TRUE;

		return $this;
	}


	public function setFilterOnEnter()
	{
		$this->submitOnEnter = TRUE;

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


	/**
	 * Callback to add e.g. conditional class to the parent td element.
	 */
	public function setOutherClass(callable $callback)
	{
		$this->outherClass = $callback;

		return $this;
	}


	/**
	 * string
	 */
	public function setOutherTitleClass(string $class)
	{
		$this->outherTitleClass = $class;

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