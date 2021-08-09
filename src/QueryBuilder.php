<?php

namespace Camohub\LaravelDatagrid;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;


class QueryBuilder
{

	/** @var Builder $request */
	protected $model;

	/** @var array $methodsInUse */
	protected $methodsInUse = [];  // structure: ['join' => [], 'rightJoin' => [], 'orderBy' => [], ...]


	public function __construct( $model )
	{
		$this->model = $model;
	}


	public function __call( $method, $params )
	{
		$key = serialize($params);

		// This if branch stops calling duplicate Eloquent\Builder calls.
		if( isset($this->methodsInUse[$method])
			&& in_array($key, $this->methodsInUse[$method])
		) {
			return $this;
		}

		$key = serialize($params);
		$this->methodsInUse[$method][] = $key;

		$return = $this->model->$method( ...$params );

		is_object($return) ? Log::debug(get_class($return)) : Log::debug($return);

		//  If $return is not instance of Eloquent\QueryBuilder return $result otherwise return $this.
		return $return instanceof Builder ? $this : $return;
	}


	public function __callStatis( $method, $params )
	{
		$return = $this->model->$method( ...$params );

		//  If $return is not instance of Eloquent\QueryBuilder return $result otherwise return $this.
		return $return instanceof Builder ? $this : $return;
	}


	public function __get( $param )
	{
		return $this->model->$param;
	}


	public function __set( $prop, $value )
	{
		$this->model->$prop = $value;

		return $this;
	}


}