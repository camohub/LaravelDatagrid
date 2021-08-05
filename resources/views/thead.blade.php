
<thead>
	<tr>
		@foreach($columns as $column)
			@if( !$column->hidden )
				@php
					$outherTitleClass = $column->outherTitleClass ?: '';
				@endphp
				{{-- SORT --}}
				{{-- SORT --}}
				{{-- SORT --}}
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
	{{-- FILTERS --}}
	{{-- FILTERS --}}
	{{-- FILTERS --}}
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
						@elseif( $column->selectFilter)
							<select name="{{$column->filterParamName}}"
									id="{{$column->filterParamName}}"
									data-submitOnEnter="{{$column->submitOnEnter}}"
									class="form-control chgrid-filter">
								@if($column->selectFilterPrompt)
									<option value="">{{$column->selectFilterPrompt}}</option>
								@endif
								@foreach( $column->selectFilter as $key => $value )
									<option value="{{$key}}" @if( $column->filterValue === (string)$key ) selected="selected" @endif>{{$value}}</option>
								@endforeach
							</select>
						@elseif( $column->filter )
							<input name="{{$column->filterParamName}}"
								id="{{$column->filterParamName}}"
								value="{{$column->filterValue}}"
								data-jsFilterPattern="{{$column->jsFilterPattern}}"
								data-submitOnEnter="{{$column->submitOnEnter}}"
								type="text"
								class="form-control chgrid-filter">
						@endif
						{{-- SORT HIDDEN --}}
						{{-- SORT HIDDEN --}}
						{{-- SORT HIDDEN --}}
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