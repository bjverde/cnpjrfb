function kanban_start_board(id, stageDropAction)
{
	$('#'+id).sortable({
		revert: 200,
		placeholder: 'kanban-stage-placeholder',
		forcePlaceholderSize: true,
		helper: function(event, element){
		    return $(element).clone().addClass('kanban-dragging');
		},
		start: function (e, ui) {
		    ui.item.show().addClass('kanban-ghost')
		},
		stop: function (e, ui) {
		    ui.item.show().removeClass('kanban-ghost')
		},
		update: function (e, ui) {
			var stageId   = $(ui.item).attr('stage_id');
			var positions = [];
			$(".kanban-stage").each( function(){
			    positions.push($(this).attr('stage_id'));
			});
    		var order = positions.join('&order[]=');
    		__adianti_load_page(stageDropAction+"&key="+stageId+"&id="+stageId+"&stage_id="+stageId+"&order[]="+order);
		},
		cursor:'move',
		tolerance:'pointer',
		cancel: '.kanban-stage-actions, .kanban-item, .kanban-shortcuts'
	});
}

function kanban_start_item(classItem, itemDropAction)
{
	$( '.'+classItem ).sortable({
		revert: 200,
		placeholder: 'kanban-item-placeholder',
		forcePlaceholderSize: true,
		connectWith: ".kanban-item-sortable",
		helper: function(event, element){
		    return $(element).clone().addClass('kanban-dragging');
		},
		start:  function (e, ui) {
		    ui.item.show().addClass('kanban-ghost')
		},
		stop:   function (e, ui) {
		    ui.item.show().removeClass('kanban-ghost')
		},
		update: function( event, ui ) {
			if (itemDropAction && this === ui.item.parent()[0]) {
				var itemId    = $(ui.item).attr('item_id');
				var stageId   = $(this).attr('stage_id')
				var positions = [];
				$("[stage_id="+stageId+"] .kanban-item").each(function(){
				    positions.push($(this).attr('item_id'));
				});
	    		// Remove possible "" value in array
				var item = "";
				var index = positions.indexOf(item);
				if (index !== -1) positions.splice(index, 1);
				
	    		var order = positions.join('&order[]=');
	    		__adianti_load_page(itemDropAction+"&key="+itemId+"&id="+itemId+"&stage_id="+stageId+"&order[]="+order);
    		}
		},
		cursor:'move',
		tolerance:'pointer',
		cancel: '.kanban-item-content, .kanban-item-actions'
    });
}
