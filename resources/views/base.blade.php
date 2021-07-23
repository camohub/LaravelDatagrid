
<div class="table-responsive">
	<table class="{{ $grid->tableClass }}">
		<thead>
		@foreach($columns as $column)
			@if(!$column->hidden)
				<th>
					{{$column->title}}
				</th>
			@endif
		@endforeach
		</thead>
		<tbody>
		@foreach($model as $item)
			<tr>
				@foreach($columns as $column)
					@if( !$column->hidden )

						@php
							if( $column->type != \Camohub\LaravelDatagrid\Column::TYPE_CUSTOM )
							{
								// fieldName comes from explode() of path as user.role.name.
								// Every iteration adds new object level.
								foreach ($column->fieldName as $path)
								{
									$fieldValue = !isset($fieldValue) ? $item->{$path} : $fieldValue->{$path};
								}

								// Has to be here to have access to raw value.
								$outherClass = $column->outherClass ? ($column->outherClass)($fieldValue, $item) : '';

								if( $f = $column->numberFormat )
								{
									$fieldValue = number_format($column, $f[0], $f[1], $f[2]);
								}
							}
							else
							{
								$fieldValue = '';
							}
						@endphp

						<td class="{{ $outherClass }}">

							@if($column->render)
								@if($column->noEscape){!! ($column->render)($fieldValue, $item) !!}
								@else {{ ($column->render)($fieldValue, $item) }}
								@endif
							@else
								@if($column->noEscape){!! $fieldValue !!}
								@else {{ $fieldValue }}
								@endif
							@endif

							@php
								unset($fieldValue);  // Remove old value
							@endphp

						</td>
					@endif
				@endforeach
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>

	<div class="mt-2">
		{{ $model->links() }}
	</div>
</div>
