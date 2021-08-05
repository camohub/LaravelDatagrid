
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