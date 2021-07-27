
<form id="chgrid-form" method="get" class="table-responsive camohub-laravel-datagrid">
	<table class="{{ $grid->tableClass }}">

		<input type="hidden" name="chgrid-page" value="{{$request->input('chgrid-page', NULL)}}">

		<thead>
			<tr>
				@foreach($columns as $column)
					@if( !$column->hidden )
						<th @if( $column->sort ) class="chgrid-sort {{$column->sortValue}}" data-sort="{{$column->getNextSortValue()}}" data-sort-input-id="{{$column->sortParamName}}" @endif>
							{{$column->title}}
						</th>
					@endif

					@php
						unset($thClass)
					@endphp
				@endforeach
			</tr>
			@if($grid->columnsFiltersCount)
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
										data-jsFilterPatter="{{$column->jsFilterPatter}}"
										type="text"
										class="form-control chgrid-filter">
								@endif

								@if( $column->sort )
									<input name="{{$column->sortParamName}}"
										id="{{$column->sortParamName}}"
										value="{{$column->sortValue}}"
										class="chgrid-sort-input"
										type="hidden">
								@endif
							</th>
						@endif

						@php
							unset($thClass)
						@endphp
					@endforeach
				</tr>
			@endif
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

				{{-- form.submit() does not trigger submit event. --}}
				{{-- Need to click on button if is necessary to catch submit event. --}}
				<input type="submit" id="chgrid-submit" style="display: none;">
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
		var filterInputs = document.querySelectorAll('#chgrid-form .chgrid-filter');
		var sortTHeads = document.querySelectorAll('#chgrid-form .chgrid-sort');
		var sortInputs = document.querySelectorAll('#chgrid-form .chgrid-sort-input');
		var currentUrl = location.href;
		var filterTimeout = null;
		var formChange = false;
		var deletePageFromUrl = false;

		chGridForm.setAttribute('action', currentUrl);


		filterInputs.forEach( function( input ) {

			input.addEventListener('input', function(e) {
				clearTimeout(filterTimeout);

				filterTimeout = setTimeout(function() {
					if( this.getAttribute('data-jsFilterPatter') )
					{
						var regexp = new RegExp(this.getAttribute('data-jsFilterPatter'));
						var value = this.value;

						if( value && !value.match(regexp) )
						{
							this.classList.add('text-danger');
							return;
						}

						this.classList.remove('text-danger');
					}
					deletePageFromUrl = true;
					formSubmit(chGridForm);
				}, {{$grid->jsFilterTimeout}});

			});
		});


		sortTHeads.forEach(function(link) {

			link.addEventListener('click', function(e) {
				clearTimeout(filterTimeout);

				var sort = this.getAttribute('data-sort');
				var inputId = this.getAttribute('data-sort-input-id');
				document.getElementById(inputId).value = sort;
				formSubmit();
			});
		});


		perPageSelect.addEventListener('change', function(e) {
			clearTimeout(filterTimeout);
			deletePageFromUrl = true;
			formSubmit();
		});


		/**
		 * This method disables all empty inputs
		 * so its keys wont be in filter url.
		 * @param form
		 */
		function formSubmit()
		{
			if( deletePageFromUrl ) currentUrl = removePageFromUrl(currentUrl);
			chGridForm.setAttribute('action', currentUrl);

			sortInputs.forEach(function( input ) {
				if( !input.value ) input.setAttribute('disabled', true);
			});

			filterInputs.forEach(function( input ) {
				if( !input.value ) input.setAttribute('disabled', true);
			});

			// form.submit() does not trigger submit event.
			// Need to click on button if is necessary to catch submit event.
			//form.submit();
			document.getElementById('chgrid-submit').click();
		}


		function removePageFromUrl(url)
		{
			return url.replace(/chgrid-page=\d+/, '');
		}


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
</script>
@endif