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