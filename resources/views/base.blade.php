
<div class="table-responsive camohub-laravel-datagrid">
	<table class="{{ $grid->tableClass }}">

		<thead>
		@foreach($columns as $column)
			@if(!$column->hidden)
				@php
					$thClass = $column->sortValue ? 'sorting' : '';
				@endphp
				<th class="{{$thClass}}">
					@if( $column->sort )
						<a href="{{$column->getSortUrl()}}">{{$column->title}}</a>
					@else
						{{$column->title}}
					@endif
				</th>
			@endif

			@php
				unset($thClass)
			@endphp
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
								// fieldExplodeName comes from explode() of path as user.role.name.
								// Every iteration adds new object level.
								foreach ($column->fieldNameExplode as $path)
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
		<tr>
			<td colspan="{{$grid->columnsCount - 2}}">
				{{ $model->links() }}
			</td>
			<td colspan="2">
				<form action="" method="get" id="chgrid-perPageForm">
					<select name="{{'chgrid-perPage'}}" class="form-control" id="chgrid-perPage">
						@foreach($grid->perPage as $pP)
							<option value="{{$pP}}"
									@if( $request->input('chgrid-perPage', $grid->defaultPerPage) == $pP ) selected @endif
							>{{$pP}}</option>
						@endforeach
					</select>
				</form>
			</td>
		</tr>
		</tfoot>
	</table>
</div>

<script>
	// without jQuery (doesn't work in older IEs)
	// https://stackoverflow.com/questions/9899372/pure-javascript-equivalent-of-jquerys-ready-how-to-call-a-function-when-t
	document.addEventListener('DOMContentLoaded', function() {

		var perPageSelect = document.getElementById('chgrid-perPage');
		var perPageForm = document.getElementById('chgrid-perPageForm');

		perPageSelect.addEventListener('change', function(e) {
			var href = location.href;

			href = href.replace(/chgrid-page=\d+/, '');

			if( href.match(/chgrid-perPage=\d+/) )
			{
				href = href.replace(/chgrid-perPage=\d+/, 'chgrid-perPage=' + this.value);
			}
			else
			{
				if( href.match(/\?.+$/) ) href = href + '&chgrid-perPage=' + this.value;
				else if( href.match(/\?$/) ) href = href + 'chgrid-perPage=' + this.value;
				else href = href + '?chgrid-perPage=' + this.value;
			}
			href = href.replace(/&&/, '&');

			location.href = href;
		});
	}, false);
</script>
