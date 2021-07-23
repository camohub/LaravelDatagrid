
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
									$value = $item->{$column->fieldName};
									if( $f = $column->numberFormat )
									{
										$value = number_format($column, $f[0], $f[1], $f[2]);
									}
								@endphp
								@if($column->render)
									{{ $column->render($value) }}
								@else
									{{ $value }}
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
