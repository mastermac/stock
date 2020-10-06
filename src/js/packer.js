$("#newPackingList").submit(function (e) {
	e.preventDefault();
	let formData = getFormData("#newPackingList");
	console.log(formData);
});

function getFormData(form) {
	let unindexed_array = $(form).serializeArray();
	let indexed_array = {};

	$.map(unindexed_array, function (n, i) {
		indexed_array[n["name"]] = n["value"];
	});

	return indexed_array;
}
function initializePackingListForm() {
	$("#newPackingList").trigger("reset");
	$("#newPackingList label").removeClass("active");
	$("#plDate").daterangepicker(
		{
			singleDatePicker: true,
			opens: "center",
		},
		function (start, end, label) {}
	);
}
$("#packingListModal").on("show.bs.modal", initializePackingListForm);
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
});

var gridOptions, grid;
//Ag-Grid
function initializeDiamondDetails() {
	var gridOptions = {
		columnDefs: [
			{
				headerName: "Athlete",
				field: "athlete",
				minWidth: 180,
				headerCheckboxSelection: true,
				headerCheckboxSelectionFilteredOnly: true,
				checkboxSelection: true,
				sortable: true,
				filter: true,
			},
			{ field: "age" },
			{ field: "country", minWidth: 150 },
			{ field: "year" },
			{ field: "date", minWidth: 150 },
			{ field: "sport", minWidth: 150 },
			{ field: "gold" },
			{ field: "silver" },
			{ field: "bronze" },
			{ field: "total" },
		],
		defaultColDef: {
			flex: 1,
			minWidth: 100,
			resizable: true,
			editable: true,
		},
		suppressRowClickSelection: true,
		rowSelection: "multiple",
		undoRedoCellEditing: true,

		// restricts the number of undo / redo steps to 5
		undoRedoCellEditingLimit: 10,

		// enables flashing to help see cell changes
		enableCellChangeFlash: true,
	};

	function onQuickFilterChanged() {
		gridOptions.api.setQuickFilter(document.getElementById("quickFilter").value);
	}
	$("#myGrid").html("");
	var gridDiv = document.querySelector("#myGrid");
	//grid = new agGrid.Grid(gridDiv, gridOptions);

	agGrid
		.simpleHttpRequest({
			url: "https://raw.githubusercontent.com/ag-grid/ag-grid/master/grid-packages/ag-grid-docs/src/olympicWinnersSmall.json",
		})
		.then(function (data) {
			gridOptions.api.setRowData(data);
		});
}
function getSelectedRows() {
	var selectedNodes = gridOptions.api.getSelectedNodes();
	var selectedData = selectedNodes.map(function (node) {
		return node.data;
	});
	var selectedDataStringPresentation = selectedData
		.map(function (node) {
			return node.make + " " + node.model;
		})
		.join(", ");
	alert("Selected nodes: " + selectedDataStringPresentation);
}

function onFirstDataRendered(params) {
	//params.api.sizeColumnsToFit();
}
