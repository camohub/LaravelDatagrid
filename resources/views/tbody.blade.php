
<tbody>
	@foreach($model as $row)
		<tr>
			@foreach($columns as $column)
				@if( !$column->hidden )

					@php
						if( $column->type != \Camohub\LaravelDatagrid\Column::TYPE_CUSTOM )
						{
							// fieldExplodeName comes from explode() of path as user.role.name.
							// Every iteration adds new object level.
							foreach ($column->fieldNameExplode as $path)
							{
								$fieldValue = !isset($fieldValue) ? $row->{$path} : $fieldValue->{$path};
							}

							// Has to be here to have access to raw value. OR NOT???
							//$outherClass = $column->outherClass ? ($column->outherClass)($fieldValue, $row) : '';
						}
						else
						{
							$fieldValue = '';
						}

						// Has to be here to have access to raw value.
						$outherClass = $column->outherClass ? ($column->outherClass)($fieldValue, $row) : '';
					@endphp

					<td class="{{ $outherClass }}">

						@if($column->render)
							@if($column->noEscape){!! ($column->render)($fieldValue, $row) !!}
							@else {{ ($column->render)($fieldValue, $row) }}
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