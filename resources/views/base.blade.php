@section('sort-arrows')
	<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
		<path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/>
	</svg>
	<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
		<path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
	</svg>
	<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
		<path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
	</svg>
@endsection

<style>
	.bi-arrow-down, .bi-arrow-up, .bi-arrow-down-up {
		position: relative;
		top: -2px;
	}

	.chgrid-sort .bi-arrow-down-up {
		display: inline-block;
	}

	.chgrid-sort .bi-arrow-down, .chgrid-sort .bi-arrow-up {
		display: none;
	}

	.chgrid-sort.asc .bi-arrow-up {
		display: inline-block;
	}

	.chgrid-sort.asc .bi-arrow-down, .chgrid-sort.asc .bi-arrow-down-up {
		display: none;
	}

	.chgrid-sort.desc .bi-arrow-down {
		display: inline-block;
	}

	.chgrid-sort.desc .bi-arrow-up, .chgrid-sort.desc .bi-arrow-down-up {
		display: none;
	}
</style>



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
		var deletePageParam = false;
		var gridSubmitOnEnter = {{$grid->submitOnEnter}}

		chGridForm.setAttribute('action', currentUrl);


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