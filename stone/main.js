var SelectedStone;
$('input[name="dates"]').daterangepicker();

var stoneInventory = {
	columnDefs: [
		{
			headerName: "S.No",
			field: "sno",
			headerCheckboxSelection: true,
			headerCheckboxSelectionFilteredOnly: true,
			checkboxSelection: true,
			editable: false,
			filter: false,
			pinned: 'left'
		},
		{
			headerName: "Lot#",
			field: "lot_no",
			pinned: 'left',
			filter: "agNumberColumnFilter"
		},
		{ headerName: "Name", field: "name", width: 180, pinned: 'left' },
		{ headerName: "Size", field: "size", width: 130 },
		{ headerName: "Shape", field: "shape" },
		{ headerName: "Seller", field: "seller", width: 200 },
		{ headerName: "P Qty", field: "purchased_qty", filter: "agNumberColumnFilter" },
		{ headerName: "P Wt", field: "purchased_wt", filter: "agNumberColumnFilter" },
		{ headerName: "Unit", field: "unit", filter: "agNumberColumnFilter" },
		{ headerName: "C Qty", field: "current_qty", filter: "agNumberColumnFilter"  },
		{ headerName: "C Wt", field: "current_wt", filter: "agNumberColumnFilter" },
		{ headerName: "Box#", field: "box" },
		{ headerName: "Cost", field: "cost", filter: "agNumberColumnFilter" },
		{ headerName: "Less", field: "less", filter: "agNumberColumnFilter"},
		{ headerName: "Rate", field: "rate", filter: "agNumberColumnFilter" },
		{ headerName: "Total Amt", field: "total_amount", width: 130, filter: "agNumberColumnFilter"},
		{ headerName: "Cur Value", field: "current_value", width: 130, filter: "agNumberColumnFilter" },
		{ headerName: "Description", field: "description", width: 280 },
		{ headerName: "Created On", field: "date_of_creation", width: 160, filter: "agDateColumnFilter"},
		{ headerName: "Last Updated", field: "last_update_date", width: 160, filter: "agDateColumnFilter"},
		{
			headerName: "Edit",
			field: "id",
			filter: false,
			sortable: false,
			cellRenderer: "editButton",
			cellRendererParams: {
				clicked: function (field) {
					currentPL = field;
					var currentRow=stoneInventory.api.getRowNode(currentPL);
					SelectedStone=new StoneInventory(currentRow.data.lot_no,currentRow.data.name, currentRow.data.size, currentRow.data.shape, currentRow.data.seller,
						currentRow.data.purchased_qty, currentRow.data.purchased_wt, currentRow.data.current_qty, currentRow.data.current_wt, currentRow.data.unit, 
						currentRow.data.box, currentRow.data.cost, currentRow.data.less, currentRow.data.rate, currentRow.data.description);
					$("#stoneFormModal").modal("show");
				},
			},
			resizable: false,
			pinned: 'right',
			width: 70
		},
		{
			headerName: "Delete",
			field: "id",
			filter: false,
			sortable: false,
			cellRenderer: "delButton",
			cellRendererParams: {
				clicked: function (field) {
					var r = confirm("Sure about DELETING?");
					if (r == false)
						return;
				
					showLoader();
					$.ajax({
						dataType: "json",
						url: url + "../src/scripts/stone.php",
						data: {
							func: "deleteStone",
							id: field,
						},
					}).done(function (data) {
						hideLoader();
						getStoneLists();
					});
				},
			},
			resizable: false,
			pinned: 'right',
			width: 70
		},
	],
	defaultColDef: {
		width: 95,
		wrapText: true,
	    autoHeight: true,
		resizable: true,
		editable: true,
		filter: true,
		sortable: true,
		type: "leftAligned",
		enableCellChangeFlash: true,
		cellStyle: { "white-space": "normal" },
	},
	animateRows: true,
	suppressRowClickSelection: true,
	rowSelection: "multiple",
	undoRedoCellEditing: true,
	enableFillHandle: true,
	onColumnResized: onColumnResized,
	onColumnVisible: onColumnVisible,
	// restricts the number of undo / redo steps to 5
	undoRedoCellEditingLimit: 10,
	stopEditingWhenGridLosesFocus: true,
	// enables flashing to help see cell changes
	components: {
		editButton: EditBtnCellRenderer,
		delButton: DelBtnCellRenderer,
	},
	onCellValueChanged: function (event) {
		newData.push(event.data);
		stoneInventory.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};

function exportData(){
	let params = {
		columnKeys: ['sno', 'lot_no', 'name', 'size','shape','seller','purchased_qty','purchased_wt','unit','current_qty','current_wt','box','cost','less','rate','total_amount','current_value','description']
	};

	let selectedRows = stoneInventory.api.getSelectedNodes();

	if(selectedRows.length>0){
		params={
			onlySelected: true,
			columnKeys: ['sno', 'lot_no', 'name', 'size','shape','seller','purchased_qty','purchased_wt','unit','current_qty','current_wt','box','cost','less','rate','total_amount','current_value','description']
		}
	}
	stoneInventory.api.exportDataAsCsv(params);
}


var currentPL, currentItem;
var myCellRenderer = function () {
	return '<span style="color: black">Edit</span>';
};
function onQuickFilterChanged() {
	stoneInventory.api.setQuickFilter(document.getElementById("quickFilter").value);
}
var newData = new Array();
// setup the grid after the page has finished loading
document.addEventListener("DOMContentLoaded", function () {
	var gridDiv = document.querySelector("#stoneInventory");
	$("#stoneInventory").css("height",window.innerHeight-170+"px");
	new agGrid.Grid(gridDiv, stoneInventory);
	getStoneLists();
	stoneInventory.getRowNodeId = d => {
		return d.id; // return the property you want set as the id.
	};
	// agGrid
	// 	.simpleHttpRequest({
	// 		url: "https://raw.githubusercontent.com/ag-grid/ag-grid/master/grid-packages/ag-grid-docs/src/olympicWinnersSmall.json",
	// 	})
	// 	.then(function (data) {
	// 		stoneInventory.api.setRowData(data);
	// 	});
});
function onColumnResized(params) {
	params.api.resetRowHeights();
  }
  
  function onColumnVisible(params) {
	params.api.resetRowHeights();
  }
//#region Packing Lists

$("#stoneFormModal").on("show.bs.modal", initializeStoneForm);
$("#stoneFormModal").on("hide.bs.modal", destroyStoneForm);
function initializeStoneForm() {
	$("#stoneForm").trigger("reset");
	window.start = moment().format("YYYY-MM-DD");
	if(SelectedStone){
		$("#stoneForm label").removeClass("active");
		$("#stoneFormHeader").html("Edit Stone");
		$("#stoneForm label").removeClass("active");
		$('#lot_no').attr('readonly', true); 
		$('#lot_no').addClass('text-muted');
		$("#lot_no").val(SelectedStone.lot_no).change();
		$("#lot_no").css('disabled', 'disabled');
		$("#name").val(SelectedStone.name).change();
		$("#size").val(SelectedStone.size).change();
		$("#shape").val(SelectedStone.shape).change();
		$("#seller").val(SelectedStone.seller).change();
		$("#purchased_qty").val(SelectedStone.purchased_qty).change();
		$("#purchased_wt").val(SelectedStone.purchased_wt).change();
		$("#current_qty").val(SelectedStone.current_qty).change();
		$("#current_wt").val(SelectedStone.current_wt).change();
		$("#unit").val(SelectedStone.unit).change();
		$("#box").val(SelectedStone.box).change();
		$("#cost").val(SelectedStone.cost).change();
		$("#less").val(SelectedStone.less).change();
		$("#rate").val(SelectedStone.rate).change();
		$("#description").val(SelectedStone.description).change();

		$("#stCreate").html("Update");
	}
	else{
		$('#lot_no').attr('readonly', false); 
		$('#lot_no').removeClass('text-muted');
		$("#stoneFormHeader").html("Add New Stone");
	}
}
function destroyStoneForm(){
	SelectedStone=null;
}
function getJSONFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

$("#stoneForm").on('submit', (function (e) {
    e.preventDefault();
	var formData = getJSONFormData($('#stoneForm'));

	formData['func'] = "addStone";
	formData['date'] = window.start;
	
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/stone.php",
		data: formData,
	}).done(function (data) {
		hideLoader();
		$("#stReset").click();
		SelectedStone=null;
		getStoneLists();
		$("#stoneFormModal").modal("hide");
	});
}))


function getStoneLists() {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/stone.php",
		data: {
			func: "getStoneLists",
		},
	}).done(function (data) {
		hideLoader();
		BindPackingLists(data.data);
	});
}
function BindPackingLists(data) {
	stoneInventory.api.setRowData(data);
	// stoneInventory.api.forEachNode(node=> node.rowIndex ? 0 : node.setSelected(true));
	// stoneInventory.api.resetRowHeights();
	// stoneInventory.api.sizeColumnsToFit();
}

//#endregion

//#region Helper Functions
function showLoader() {
	$(".ajax-loader").css("visibility", "visible");
}
function hideLoader() {
	$(".ajax-loader").css("visibility", "hidden");
}
//#endregion

//#region Overrides

//#endregion






$("#importForm").on('submit', (function (e) {
	e.preventDefault();
	var formData = new FormData(this);
	$('.ajax-loader').css("visibility", "visible");
	formData.append("src", "importForm");
	$.ajax({
		type: 'POST',
		url: url + '../src/scripts/stone_import.php',
		data: formData,
		contentType: false,
		processData: false,
	}).done(function (data) {
		$('.ajax-loader').css("visibility", "hidden");
		refreshCache();
		var resp = JSON.parse(data);
		lastOperationCount = resp['total'];
		if (resp['success'] == 1) {
			if (resp['impact'] > 0) {
				toastr.success(resp['impact'] + ' records inserted!', 'Data Imported', {
					timeOut: 0,
					closeButton: true
				});
			}
			if (resp['update'] > 0) {
				toastr.warning(resp['update'] + ' records updated!', 'Data Imported', {
					timeOut: 0,
					closeButton: true
				});
			}
			manageData();
		}
		else if (resp['success'] == 0) {
			toastr.error(resp['msg'], 'Import Failed', {
				timeOut: 0,
				closeButton: true
			});
			if (resp['impact'] > 0) {
				toastr.success(resp['impact'] + ' records inserted!', 'Data Imported', {
					timeOut: 0,
					closeButton: true
				});
			}
			if (resp['update'] > 0) {
				toastr.warning(resp['update'] + ' records updated!', 'Data Imported', {
					timeOut: 0,
					closeButton: true
				});
			}
		}
		$('#undo').attr("disabled", false);
		$('#importForm')[0].reset();
		$(".modal").modal('hide');
	});
}));


function resetFilters(){
	stoneInventory.api.setFilterModel(null);
	stoneInventory.api.onFilterChanged();
}