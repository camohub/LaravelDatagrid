
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
		var resetBtn = document.getElementById('chgrid-reset');
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


		resetBtn.addEventListener('click', function(e)
		{
			reset();
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


		function reset()
		{
			filterInputs.forEach(function(input) { input.value = ''; });
			sortInputs.forEach(function(input) { input.value = ''; });
			removePageParam();
			formSubmit();
		}

	//}, false);
</script>