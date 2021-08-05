@include('camohubLaravelDatagrid::arrows')



<form id="chgrid-form" method="get" class="table-responsive camohub-laravel-datagrid">

	{{-- In most cases getParams are not neccessary --}}
	@foreach($grid->getParams as $getParam)
		<input type="hidden" name="{{$getParam}}" value="{{$request->input($getParam, NULL)}}" class="chgrid-getPrams">
	@endforeach

	<table class="{{ $grid->tableClass }}">
		@include('camohubLaravelDatagrid::thead')

		@include('camohubLaravelDatagrid::tbody')

		@include('camohubLaravelDatagrid::tfoot')
	</table>
</form>


@include('camohubLaravelDatagrid::javascript')
