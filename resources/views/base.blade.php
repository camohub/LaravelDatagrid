
<form id="chgrid-form" method="get" class="table-responsive camohub-laravel-datagrid">
	<table class="{{ $grid->tableClass }}">

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
										data-jsFilterPattern="{{$column->jsFilterPattern}}"
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
				{{-- If it should be the last param in url needs to be here at the end of the form --}}
				<input type="hidden" name="chgrid-page" id="chgrid-page" value="{{$request->input('chgrid-page', NULL)}}">
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
		var pageInput = document.getElementById('chgrid-page');
		var currentUrl = location.href;
		var filterTimeout = null;
		var formChange = false;
		var deletePageParam = false;

		chGridForm.setAttribute('action', currentUrl);


		filterInputs.forEach( function( input ) {

			input.addEventListener('input', function(e) {
				clearTimeout(filterTimeout);

				filterTimeout = setTimeout(function(input) {
					if( input.getAttribute('data-jsFilterPattern') )
					{
						var regexp = new RegExp(input.getAttribute('data-jsFilterPattern'));
						var value = input.value;

						if( value && !value.match(regexp) )
						{
							input.classList.add('text-danger');
							return;
						}

						input.classList.remove('text-danger');
					}
					deletePageParam = true;
					formSubmit();
				}, {{$grid->jsFilterTimeout}}, this);

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
			deletePageParam = true;
			formSubmit();
		});


		/**
		 * This method disables all empty inputs
		 * so its keys wont be in filter url.
		 * @param form
		 */
		function formSubmit()
		{
			if( deletePageParam ) removePageParam();
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


		function removePageParam()
		{
			pageInput.setAttribute('disabled', true);
		}

	}, false);
</script>
@endif