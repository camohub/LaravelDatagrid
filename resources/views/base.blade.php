
<div class="table-responsive">
	<table class="table">
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
							<td>
								@php
									// fieldName comes from explode() of path as user.role.name.
									// Every iteration adds new object level.
									foreach ($column->fieldName as $path) $value = $item->{$path};

									if( $f = $column->numberFormat )
									{
										$value = number_format($column, $f[0], $f[1], $f[2]);
									}
								@endphp

								@if($column->render)
									@if($column->noEscape){!! $column->render($value) !!}
									@else {{ $column->render($value) }}
									@endif
								@else
									@if($column->noEscape){!! $value !!}
									@else {{ $value }}
									@endif
								@endif
							</td>
						@endif
					@endforeach
				</tr>
			@endforeach
		</tbody>
		<tfoot>
			{{ $model->links() }}
		</tfoot>
	</table>

	<div class="mt-3">
		{{ $g }}
	</div>
</div>
