@include('camohubLaravelDatagrid::arrows')



<form id="chgrid-form" method="get" class="table-responsive camohub-laravel-datagrid">

	{{-- In most cases getParams are not neccessary --}}
	@foreach($grid->getParams as $getParam)
		<input type="hidden" name="{{$getParam}}" value="{{$request->input($getParam, NULL)}}" class="chgrid-getPrams">
	@endforeach

	<table class="{{ $grid->tableClass }}">

		<thead>
			<tr>
				@foreach($columns as $column)
					@if( !$column->hidden )
						@php
							$outherTitleClass = $column->outherTitleClass ?: '';
						@endphp
						<th @if( $column->sort )
								class="chgrid-sort {{$column->sortValue}} {{$outherTitleClass}}"
								data-sort="{{$column->getNextSortValue()}}"
								data-sort-input-id="{{$column->sortParamName}}"
							@else
								class="{{$outherTitleClass}}"
							@endif>
							{{$column->title}} @if( $column->sort ) @yield('sort-arrows') @endif
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
								@if( $column->filterRender )
									{!! ($column->filterRender)($column) !!}
								@elseif( $column->filter )
									<input name="{{$column->filterParamName}}"
										id="{{$column->filterParamName}}"
										value="{{$column->filterValue}}"
										data-jsFilterPattern="{{$column->jsFilterPattern}}"
										data-submitOnEnter="{{$column->submitOnEnter}}"
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


<script>
	// without jQuery (doesn't work in older IEs)
	// https://stackoverflow.com/questions/9899372/pure-javascript-equivalent-of-jquerys-ready-how-to-call-a-function-when-t
	// DOMContentLoaded is commented cause it blocks ajax requests and it seems it is not needed.
	//document.addEventListener('DOMContentLoaded', function() {

		var chGridForm = document.getElementById('chgrid-form');
		var filterInputs = document.querySelectorAll('#chgrid-form .chgrid-filter');
		var sortTHeads = document.querySelectorAll('#chgrid-form .chgrid-sort');
		var sortInputs = document.querySelectorAll('#chgrid-form .chgrid-sort-input');
		var pageInput = document.getElementById('chgrid-page');
		var paginationLinks = document.querySelectorAll('.pagination a');
		var perPageSelect = document.getElementById('chgrid-perPage');
		var currentUrl = location.href;
		var filterTimeout = null;
		var deletePageParam = false;
		var gridSubmitOnEnter = {{$grid->submitOnEnter}}

		chGridForm.setAttribute('action', currentUrl);

		setFocus();


		filterInputs.forEach( function( input ) {
			var submitOnEnter = input.getAttribute('data-submitOnEnter');
			var jsFilterPattern = input.getAttribute('data-jsFilterPattern');

			input.addEventListener('input', function(e) {
				clearTimeout(filterTimeout);

				filterTimeout = setTimeout(function(input) {
					if( jsFilterPattern )
					{
						var regexp = new RegExp(jsFilterPattern);
						var value = input.value;

						if( value && !value.match(regexp) )
						{
							input.classList.add('text-danger');
							return;
						}

						input.classList.remove('text-danger');
					}
					deletePageParam = true;

					saveFocusSelector(input);

					if(!submitOnEnter && !gridSubmitOnEnter) formSubmit();
				}, {{$grid->jsFilterTimeout}}, this);

			});
		});


		sortTHeads.forEach(function(link) {

			link.addEventListener('click', function(e) {
				clearTimeout(filterTimeout);

				sortInputs.forEach(function(item)
				{
					item.value = '';
				});

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


		paginationLinks.forEach(function(item) {
			item.addEventListener('click', function(e) {
				e.preventDefault();
				var href = e.target.getAttribute('href');
				var page = href.match(/chgrid-page=(\d+)/)[1];
				pageInput.value = page;
				formSubmit();
			});
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

			if( !pageInput.value || pageInput.value == '1' ) pageInput.setAttribute('disabled', true);

			// form.submit() does not trigger submit event.
			// Need to click on button if is necessary to catch submit event.
			//form.submit();
			document.getElementById('chgrid-submit').click();
		}


		function removePageParam()
		{
			pageInput.setAttribute('disabled', true);
		}


		function saveFocusSelector(input)
		{
			localStorage.setItem('chgrid-focus-name', input.getAttribute('name'));
		}


		function setFocus()
		{
			var selector = localStorage.getItem('chgrid-focus-name');
			var input = document.querySelector('input[name="'+selector+'"]');
			if( input )
			{
				input.focus();
				// setSelectionRange() because focus() sets cursor at the beginning of the text.
				var valueLen = input.value.length * 2;
				input.setSelectionRange(valueLen, valueLen);
			}
		}

	//}, false);
</script>