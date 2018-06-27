jQuery(document).ready(function() {
	
	function split(val) {
		return val.split(/,\s*/);
	}

	function extractLast(term) {
		return term;
	}	
			
	var ajaxCache = {};
	jQuery("input.auto_complete")
		.bind("keydown", function(event) {
			if (event.keyCode === jQuery.ui.keyCode.TAB && jQuery( this ).data("autocomplete").menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function(request, response) {	
				var query_type = $(this).attr('element').attr('name');
				var url_src = $(this).attr('element').attr('src');
				var cachedTerm = (request.term + '' + query_type) . toLowerCase();
				if (ajaxCache[cachedTerm] != undefined && ajaxCache[cachedTerm].length < 13) {
					response(jQuery.map(ajaxCache[cachedTerm], function(item) {
						return {
							label: item.value+" ("+item.aantal+")",
							value: item.value
						}
					}));
				}
				else {
					jQuery.ajax({
						url: url_src,
						dataType: "json",
						data: {
							query_type: query_type,
							q: extractLast(request.term)/*,
							ogPrijsType: $(this).attr('element').attr('id')*/,
							ogType: $(this).attr('element').attr('data-ogType')
						},
						success: function(data) {
							//cache the data for later
							ajaxCache[cachedTerm] = data;
							//map the data into a response that will be understood by the autocomplete widget
							response(jQuery.map(data, function(item) {
								return {
									label: item.value+" ("+item.aantal+")",
									value: item.value
								}
							}));
						}
					});
				}
			},
			minLength: 2,
			select: function(event, ui) {
				var terms = split( this.value );
				terms.pop();
				terms.push( ui.item.value );
				terms.push( "" );
				this.value = terms.join("");
				return false;
				
				this.close
			},
			search: function() {
				var term = extractLast(this.value);
				if (term.length < 1) {
					return false;
				}
			},
			focus: function() {
				return false;
			}
		});	
});