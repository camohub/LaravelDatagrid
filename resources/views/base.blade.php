
<form id="chgrid-form" method="get" class="table-responsive camohub-laravel-datagrid">
	<table class="{{ $grid->tableClass }}">

		<thead>
			<tr>
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
			</tr>
			<tr>
				@foreach($columns as $column)
					@if(!$column->hidden)
						@php
							$thClass = $column->filter ? 'filtering' : '';
						@endphp
						<th class="{{$thClass}}">
							@if( $column->filter )
								<input name="{{$column->filterParamName}}"
									id="{{$column->filterParamName}}"
									value="{{$column->filterValue}}"
									type="text"
									class="form-control chgrid-filter">
							@endif
						</th>
					@endif

					@php
						unset($thClass)
					@endphp
				@endforeach
			</tr>
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
			<td colspan="{{$grid->columnsCount - 1}}">
				{{ $model->links() }}
			</td>
			<td>
				<select name="{{'chgrid-perPage'}}" class="form-control" id="chgrid-perPage">
					@foreach($grid->perPage as $pP)
						<option value="{{$pP}}"
								@if( $request->input('chgrid-perPage', $grid->defaultPerPage) == $pP ) selected @endif
						>{{$pP}}</option>
					@endforeach
				</select>
			</td>
		</tr>
		</tfoot>
	</table>
</form>


@if($grid->javascript)
<script>
	// without jQuery (doesn't work in older IEs)
	// https://stackoverflow.com/questions/9899372/pure-javascript-equivalent-of-jquerys-ready-how-to-call-a-function-when-t
	document.addEventListener('DOMContentLoaded', function() {

		var chGridForm = document.getElementById('chgrid-form');
		var perPageSelect = document.getElementById('chgrid-perPage');
		var filterInputs = document.querySelectorAll('.chgrid-filter');
		var currentUrl = location.href;
		var filterTimeout = null;

		chGridForm.setAttribute('action', currentUrl);


		filterInputs.forEach( function( input ) {

			input.addEventListener('keyup', function(e) {
				clearTimeout(filterTimeout);

				filterTimeout = setTimeout(function() {
					currentUrl = removePageFromUrl(currentUrl);
					chGridForm.setAttribute('action', currentUrl);

					formSubmit(chGridForm);

				}, {{$grid->jsFilterTimeout}});

			});
		});


		perPageSelect.addEventListener('change', function(e) {
			clearTimeout(filterTimeout);

			filterTimeout = setTimeout(function() {
				currentUrl = removePageFromUrl(currentUrl);
				chGridForm.setAttribute('action', currentUrl);

				formSubmit(chGridForm);

			}, {{$grid->jsFilterTimeout}});
		});




		/*var perPageSelect = document.getElementById('chgrid-perPage');
		var filterInputs = document.querySelectorAll('.chgrid-filter');

		var href = location.href;

		perPageSelect.addEventListener('change', function(e) {

			href = href.replace(/chgrid-page=\d+/, '');
			var urlParam = 'chgrid-perPage=' + this.value;

			if( href.match(/chgrid-perPage=\d+/) )
			{
				href = href.replace(/chgrid-perPage=\d+/, urlParam);
			}
			else
			{
				if( href.match(/\?.+$/) ) href = href + '&' + urlParam;
				else if( href.match(/\?$/) ) href = href + urlParam;
				else href = href + '?' + urlParam;
			}
			href = href.replace(/&&/, '&');
			href = href.replace(/&$/, '');

			location.href = href;
		});

		filterInputs.forEach(function( item )
		{
			item.addEventListener('keyup', function(e) {

				href = href.replace(/chgrid-page=\d+/, '');
				var name = this.getAttribute('name');
				urlParam = name + '=' + this.value;

				var regexp = new RegExp(name + '=[^&]*');  // including empty value

				if( href.match(regexp) )
				{
					href = this.value
						? href.replace(regexp, name + '=' + this.value)
						: href.replace(regexp, '');
				}
				else
				{
					if( href.match(/\?.+$/) ) href = href + '&' + urlParam;
					else if( href.match(/\?$/) ) href = href + urlParam;
					else href = href + '?' + urlParam;
				}
				href = href.replace(/&&/, '&');
				href = href.replace(/&$/, '');

				location.href = href;
			});
		});*/
	}, false);


	function removePageFromUrl(url)
	{
		return url.replace(/chgrid-page=\d+/, '');
	}


	/**
	 * This method disables all empty inputs
	 * so its keys wont be in filter url.
	 * @param form
	 */
	function formSubmit(form)
	{
		var input = null;
		@foreach($columns as $col)
			@if( !$col->filter && !$col->sort )
				input = document.getElementById({{$col->filterParamName}});
				if( !input.value ) input.setAttribute('disabled', true);
			@endif
		@endforeach

		form.submit();
	}
</script>
@endif