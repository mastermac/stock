var SelectedStone;

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
			pinned: 'right'
		},
		{
			headerName: "Delete",
			field: "id",
			filter: false,
			sortable: false,
			cellRenderer: "delButton",
			cellRendererParams: {
				clicked: function (field) {
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
			pinned: 'right'
		},
	],
	defaultColDef: {
		width: 110,
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

//#region PL-Items

var gridOptions_PL_Items = {
	columnDefs: [
		{
			headerName: "S.No",
			field: "sno",
			headerCheckboxSelection: true,
			headerCheckboxSelectionFilteredOnly: true,
			checkboxSelection: true,
			editable: false,
		},
		{ headerName: "Item #", field: "itemcode" },
		{ headerName: "Mewar #", field: "mewarcode" },
		{ headerName: "Qty", field: "qty", width: 15, filter: "agNumberColumnFilter" },
		{ headerName: "Ring Size", field: "ringsize" },
		{ headerName: "M Type", field: "metaltype" },
		{ headerName: "M Color", field: "metalcolor" },
		{ headerName: "Description", field: "description", width: 150 },
		{
			headerName: "Edit",
			field: "id",
			filter: false,
			sortable: false,
			cellRenderer: "editButton",
			cellRendererParams: {
				clicked: function (field) {
					currentItem = field;
					$("#itemDetailModal").modal("show");
				},
			},
			width: 20,
			resizable: false,
		},
		{
			headerName: "Delete",
			field: "id",
			filter: false,
			sortable: false,
			cellRenderer: "delButton",
			cellRendererParams: {
				clicked: function (field) {
					showLoader();
					$.ajax({
						dataType: "json",
						url: url + "../src/scripts/packingList_d.php",
						data: {
							func: "deletePLItem",
							id: field,
						},
					}).done(function (data) {
						hideLoader();
						getPLItems(currentPL);
					});
				},
			},
			width: 20,
			resizable: false,
		},
	],
	defaultColDef: {
		flex: 1,
		width: 40,
		resizable: true,
		editable: true,
		filter: true,
		sortable: true,
		type: "leftAligned",
		enableCellChangeFlash: true,
	},
	animateRows: true,
	suppressRowClickSelection: true,
	rowSelection: "multiple",
	undoRedoCellEditing: true,
	enableFillHandle: true,
	undoRedoCellEditingLimit: 10,
	stopEditingWhenGridLosesFocus: true,
	components: {
		editButton: EditBtnCellRenderer,
		delButton: DelBtnCellRenderer,
	},
	onCellValueChanged: function (event) {
		newData.push(event.data);
		stoneInventory.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};

$("#packingListItemsModal").on("show.bs.modal", ()=>{
	InitPLItemsForm();

	InitPLItemDetailsForm();
});
$("#packingListItemsModal").on("hidden.bs.modal", () => {
	gridOptions_PL_Items.api.destroy();

	metalGridOptions.api.destroy();
	diamondGridOptions.api.destroy();
	stoneGridOptions.api.destroy();
	otherCostGridOptions.api.destroy();
	allGridOptions.api.destroy();
});

function selectChanged(labelId){
	$("#"+labelId).addClass("active");
}

function InitPLItemsForm() {
	$("#PL-Items").trigger("reset");
	$("#PL-Items label").removeClass("active");
	var gridDiv = document.querySelector("#PL_Items_Grid");
	new agGrid.Grid(gridDiv, gridOptions_PL_Items);
	getPLItems(currentPL);
	gridOptions_PL_Items.getRowNodeId = d => {
		return d.id; // return the property you want set as the id.
	};
}

function getPLItems(packingListId) {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_r.php",
		data: {
			func: "getPackingListItems",
			id: packingListId,
		},
	}).done(function (data) {
		hideLoader();
		BindPL_Items(data.data);
	});
}
function createPLItem() {

	var item = new Item();
	console.log(item);

	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_c.php",
		data: {
			func: "createPLItem",
			pid: currentPL,
			itemcode: $("#itemCode").val(),
			mewarcode: $("#itemDesignNo").val(),
			qty: $("#itemQty").val(),
			ringsize: $("#itemSize").val(),
			metaltype: $("#itemMetalType").val(),
			metalcolor: $("#itemMetalColor").val(),
			description: $("#itemDescription").val(),
		},
	}).done(function (data) {
		hideLoader();
		getPLItems(currentPL);
	});
}
function BindPL_Items(data) {
	gridOptions_PL_Items.api.setRowData(data);
	gridOptions_PL_Items.api.sizeColumnsToFit();
}

//#endregion

//#region Edit Item Details
var metalDetailsColDef = [
	{ headerName: "S.No", field: "metal_sno", headerCheckboxSelection: true, headerCheckboxSelectionFilteredOnly: true, checkboxSelection: true, editable: false, filter: false },
	{ headerName: "Metal Wt", field: "metal_wt", filter: "agNumberColumnFilter" },
	{ headerName: "Amount", field: "metal_amt", editable: false},
	{
		headerName: "Delete",
		field: "metal_sno",
		filter: false,
		sortable: false,
		editable: false,
		cellRenderer: "delButton",
		cellRendererParams: {
			clicked: function (field) {
				showLoader();
				var data=[];
				metalGridOptions.api.forEachNode(function(rowNode, index) {
					if(index<field-1){
						data.push(rowNode.data);
					}
					else if(index>field-1){
						var node=rowNode.data;
						node.metal_sno=node.metal_sno-1;
						data.push(node);
					}
				});
				metalGridOptions.api.setRowData(data);
				metalGridOptions.getRowNodeId = d => {
					return d.metal_sno;
				};
				hideLoader();
			},
		},
		width: 20,
		resizable: false,
	},
];

var diamondDetailsColDef = [
	{ headerName: "S.No", field: "dia_sno", headerCheckboxSelection: true, headerCheckboxSelectionFilteredOnly: true, checkboxSelection: true, editable: false },
	{ headerName: "Lot No", field: "dia_lot_id", filter: "agNumberColumnFilter" },
	{ headerName: "Shape/Color/Cut", field: "dia_shape", editable: false },
	{ headerName: "Size", field: "dia_size", editable: false },
	{ headerName: "# Pcs", field: "dia_qty", filter: "agNumberColumnFilter" },
	{ headerName: "Ct Wt", field: "dia_wt", filter: "agNumberColumnFilter" },
	{ headerName: "Rate/ct", field: "dia_rate", filter: "agNumberColumnFilter" },
	{ headerName: "Amount", field: "dia_amt", editable: false },
	{
		headerName: "Delete",
		field: "dia_sno",
		filter: false,
		sortable: false,
		editable: false,
		cellRenderer: "delButton",
		cellRendererParams: {
			clicked: function (field) {
				showLoader();
				var data=[];
				diamondGridOptions.api.forEachNode(function(rowNode, index) {
					if(index<field-1){
						data.push(rowNode.data);
					}
					else if(index>field-1){
						var node=rowNode.data;
						node.dia_sno=node.dia_sno-1;
						data.push(node);
					}
				});
				diamondGridOptions.api.setRowData(data);
				diamondGridOptions.getRowNodeId = d => {
					return d.dia_sno;
				};
				hideLoader();
			},
		},
		width: 20,
		resizable: false,
	},
];

var stoneDetailsColDef = [
	{ headerName: "S.No", field: "stone_sno", headerCheckboxSelection: true, headerCheckboxSelectionFilteredOnly: true, checkboxSelection: true, editable: false },
	{ headerName: "Lot No", field: "stone_lot_id", filter: "agNumberColumnFilter" },
	{ headerName: "Name", field: "stone_name", editable: false },
	{ headerName: "Shape", field: "stone_shape", editable: false },
	{ headerName: "Size", field: "stone_size", editable: false },
	{ headerName: "Pcs", field: "stone_qty", filter: "agNumberColumnFilter" },
	{ headerName: "Ctw.", field: "stone_wt", filter: "agNumberColumnFilter" },
	{ headerName: "Rs/ct", field: "stone_rate", filter: "agNumberColumnFilter" },
	{ headerName: "Amount", field: "stone_amt", filter: "agNumberColumnFilter", editable: false },
	{
		headerName: "Delete",
		field: "stone_sno",
		filter: false,
		sortable: false,
		editable: false,
		cellRenderer: "delButton",
		cellRendererParams: {
			clicked: function (field) {
				showLoader();
				var data=[];
				stoneGridOptions.api.forEachNode(function(rowNode, index) {
					if(index<field-1){
						data.push(rowNode.data);
					}
					else if(index>field-1){
						var node=rowNode.data;
						node.stone_sno=node.stone_sno-1;
						data.push(node);
					}
				});
				stoneGridOptions.api.setRowData(data);
				stoneGridOptions.getRowNodeId = d => {
					return d.stone_sno;
				};
				hideLoader();
			},
		},
		width: 20,
		resizable: false,
	},
];

var otherCostsDetailsColDef = [
	{ headerName: "S.No", field: "other_sno", headerCheckboxSelection: true, headerCheckboxSelectionFilteredOnly: true, checkboxSelection: true, editable: false },
	{ headerName: "Description", field: "other_desc", width: 200 },
	{ headerName: "Amount", field: "other_amt", filter: "agNumberColumnFilter" },
	{
		headerName: "Delete",
		field: "other_sno",
		filter: false,
		sortable: false,
		editable: false,
		cellRenderer: "delButton",
		cellRendererParams: {
			clicked: function (field) {
				showLoader();
				var data=[];
				otherCostGridOptions.api.forEachNode(function(rowNode, index) {
					if(index<field-1){
						data.push(rowNode.data);
					}
					else if(index>field-1){
						var node=rowNode.data;
						node.other_sno=node.other_sno-1;
						data.push(node);
					}
				});
				otherCostGridOptions.api.setRowData(data);
				otherCostGridOptions.getRowNodeId = d => {
					return d.other_sno;
				};
				hideLoader();
			},
		},
		width: 20,
		resizable: false,
	},
];

var LabourDetailsColDef = [
	{ headerName: "S.No", field: "sno", headerCheckboxSelection: true, headerCheckboxSelectionFilteredOnly: true, checkboxSelection: true, editable: false },
	{ headerName: "CPF", field: "cpf", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Setting", field: "setting", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Plating", field: "plating", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Findings Cost", field: "findings", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Mount Total", field: "total", filter: "agNumberColumnFilter", editable: false },
];

var FactoryDetailsColDef = [
	{ headerName: "S.No", field: "sno", headerCheckboxSelection: true, headerCheckboxSelectionFilteredOnly: true, checkboxSelection: true, editable: false },
	{ headerName: "Per Piece Cost", field: "ppc", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Selling Price", field: "sp", filter: "agNumberColumnFilter", editable: false },
];

var allDetailsColDef = [
	{ headerName: "G. Wt", field: "gross_wt", width: 75 },
	{
		headerName: "Metal Details",
		children: [
			{ headerName: "Metal Wt", field: "metal_wt" },
			{ headerName: "Amt", field: "metal_amt"},
		],
	},
	{
		headerName: "Diamond Details",
		children: [
			{ headerName: "# Pcs", field: "dia_qty", width: 75 },
			{ headerName: "Ct Wt", field: "dia_wt", width: 75 },
			{ headerName: "Amt", field: "dia_amt" },
		],
	},
	{
		headerName: "Stone Details",
		children: [
			{ headerName: "Pcs", field: "stone_qty", width: 75 },
			{ headerName: "Ctw.", field: "stone_wt", width: 75 },
			{ headerName: "Amt", field: "stone_amt" }
		],
	},
	{
		headerName: "Oth Costs",
		children: [
			{ headerName: "Total Amt", field: "other_amt" },
		],
	},
	{
		headerName: "Labour Details",
		children: [
			{ headerName: "CPF", field: "labour_cpf" },
			{ headerName: "Setting", field: "labour_setting" },
			{ headerName: "Plating", field: "labour_plating" },
			{ headerName: "Findings Cost", field: "labour_findings", width: 130 },
			{ headerName: "Total", field: "labour_total" },
		],
	},
	{
		headerName: "Factory Details",
		children: [
			{ headerName: "PPC (in Rs)", field: "fac_ppc" },
			{ headerName: "SP (in $)", field: "fac_sp" },
		],
	},
];
var allDetailsGO={
	defaultColDef: {
		width: 100,
		resizable: true,
		editable: false,
		filter: false,
		sortable: false,
		type: "leftAligned",
		enableCellChangeFlash: true,
	},
	animateRows: true,
	suppressRowClickSelection: true,
	enableFillHandle: true,
	stopEditingWhenGridLosesFocus: true,
}
var commonGridOptions = {
	defaultColDef: {
		flex: 1,
		
		width: 40,
		resizable: true,
		editable: true,
		filter: true,
		sortable: true,
		type: "leftAligned",
		enableCellChangeFlash: true,
	},
	immutableData: false,
	animateRows: true,
	suppressRowClickSelection: true,
	rowSelection: "multiple",
	undoRedoCellEditing: true,
	enableFillHandle: true,
	undoRedoCellEditingLimit: 10,
	stopEditingWhenGridLosesFocus: true,
	components: {
		editButton: EditBtnCellRenderer,
		delButton: DelBtnCellRenderer,
	},
	onCellValueChanged: function (event) {
		newData.push(event.data);
	},
};

$("#itemDetailModal").on("show.bs.modal", InitPLItemDetailsForm);
$("#itemDetailModal").on("hide.bs.modal", () => {
	metalGridOptions.api.destroy();
	diamondGridOptions.api.destroy();
	stoneGridOptions.api.destroy();
	otherCostGridOptions.api.destroy();
	allGridOptions.api.destroy();
	// labourGridOptions.api.destroy();
	// factoryGridOptions.api.destroy();
});

var metalGridOptions = Object.create(commonGridOptions);
var diamondGridOptions = Object.create(commonGridOptions);
var stoneGridOptions = Object.create(commonGridOptions);
var otherCostGridOptions = Object.create(commonGridOptions);
var labourGridOptions = Object.create(commonGridOptions);
var factoryGridOptions = Object.create(commonGridOptions);
var allGridOptions = Object.create(allDetailsGO);
var dummyData=[{sno: 1},{sno: 2},{sno: 3},{sno: 4},{sno: 5},{sno: 6},{sno: 7},];
function InitPLItemDetailsForm() {
	$("#PL-Items").trigger("reset");
	$("#PL-Items label").removeClass("active");

	new agGrid.Grid(document.querySelector("#MetalDetailsGrid"), metalGridOptions);
	metalGridOptions.getRowNodeId = d => {
		return d.metal_sno;
	};
	metalGridOptions.api.setRowData([{metal_sno: 1},{metal_sno: 2},{metal_sno: 3},{metal_sno: 4},{metal_sno: 5},{metal_sno: 6},{metal_sno: 7},]);
	
	metalGridOptions.api.setColumnDefs(metalDetailsColDef);
	metalGridOptions.onCellValueChanged = function(event){
		if(event.data.metal_wt && event.column.colId === "metal_wt"){
			metalGridOptions.api.getRowNode(event.data.metal_sno).setDataValue('metal_amt', PackingListRates.silver * event.data.metal_wt);
		}
	}

	new agGrid.Grid(document.querySelector("#DiamondDetailsGrid"), diamondGridOptions);
	diamondGridOptions.getRowNodeId = d => {
		return d.dia_sno;
	};
	diamondGridOptions.api.setRowData([{dia_sno: 1},{dia_sno: 2},{dia_sno: 3},{dia_sno: 4},{dia_sno: 5},{dia_sno: 6},{dia_sno: 7},]);
	diamondGridOptions.api.setColumnDefs(diamondDetailsColDef);
	diamondGridOptions.onCellValueChanged = function(event){
		if(event.data.dia_wt && event.data.dia_rate && ( event.column.colId === "dia_wt" ||  event.column.colId === "dia_rate")){
			diamondGridOptions.api.getRowNode(event.data.dia_sno).setDataValue('dia_amt', event.data.dia_wt * event.data.dia_rate);
		}
	}

	new agGrid.Grid(document.querySelector("#StoneDetailsGrid"), stoneGridOptions);
	stoneGridOptions.getRowNodeId = d => {
		return d.stone_sno;
	};
	stoneGridOptions.api.setRowData([{stone_sno: 1},{stone_sno: 2},{stone_sno: 3},{stone_sno: 4},{stone_sno: 5},{stone_sno: 6},{stone_sno: 7},]);
	stoneGridOptions.api.setColumnDefs(stoneDetailsColDef);
	stoneGridOptions.onCellValueChanged = function(event){
		if(event.data.stone_wt && event.data.stone_rate && ( event.column.colId === "stone_wt" ||  event.column.colId === "stone_rate")){
			stoneGridOptions.api.getRowNode(event.data.stone_sno).setDataValue('stone_amt', event.data.stone_wt * event.data.stone_rate);
		}
	}

	new agGrid.Grid(document.querySelector("#OtherCostsDetailsGrid"), otherCostGridOptions);
	otherCostGridOptions.getRowNodeId = d => {
		return d.other_sno;
	};
	otherCostGridOptions.api.setRowData([{other_sno: 1},{other_sno: 2},{other_sno: 3},{other_sno: 4},{other_sno: 5},{other_sno: 6},{other_sno: 7},]);
	otherCostGridOptions.api.setColumnDefs(otherCostsDetailsColDef);

	new agGrid.Grid(document.querySelector("#AllDetailsGrid"), allGridOptions);
	allGridOptions.getRowNodeId = d => {
		return d.metal_sno;
	};
	allGridOptions.api.setRowData([{metal_sno: 1},{metal_sno: 2},{metal_sno: 3},{metal_sno: 4},{metal_sno: 5},{metal_sno: 6},{metal_sno: 7},]);
	allGridOptions.api.setColumnDefs(allDetailsColDef);

}

var metalGridData=[];
var diamondGridData=[];
var stoneGridData=[];
var otherCostGridData=[];
var allTotalGridData=[];

function AllDetailsTabClicked(){
	GetAllGridData();
	allTotalGridData=[
		{
			gross_wt: 0,
			metal_wt:0,
			metal_amt: 0,
			dia_qty: 0,
			dia_wt: 0,
			dia_amt: 0,
			stone_qty: 0,
			stone_wt: 0,
			stone_amt: 0,
			other_amt: 0,
			labour_cpf: 0,
			labour_setting: 0,
			labour_plating: 0,
			labour_findings: 0,
			labour_total: 0,
			fac_ppc: 0,
			fac_sp: 0
		}
	];
	for(var i=0;i<metalGridData.length;i++){
		if(metalGridData[i].metal_wt){
			allTotalGridData[0].metal_wt+= Number(metalGridData[i].metal_wt);
			allTotalGridData[0].metal_amt+= (Number(metalGridData[i].metal_wt)*PackingListRates.silver);
			allTotalGridData[0].labour_cpf+= (Number(metalGridData[i].metal_wt) * PackingListRates.labour);
			allTotalGridData[0].labour_plating+= (Number(metalGridData[i].metal_wt) * PackingListRates.plating);
		}
	}

	for(var i=0;i<diamondGridData.length;i++){
		if(diamondGridData[i].dia_lot_id){
			allTotalGridData[0].dia_qty+= Number(diamondGridData[i].dia_qty);
			allTotalGridData[0].dia_wt+= Number(diamondGridData[i].dia_wt);
			allTotalGridData[0].dia_amt+= (Number(diamondGridData[i].dia_wt) * Number(diamondGridData[i].dia_qty));
			allTotalGridData[0].labour_setting+=Number(diamondGridData[i].dia_qty)*10;
		}
	}
	for(var i=0;i<stoneGridData.length;i++){
		if(stoneGridData[i].stone_lot_id){
			allTotalGridData[0].stone_qty+= Number(stoneGridData[i].stone_qty);
			allTotalGridData[0].stone_wt+= Number(stoneGridData[i].stone_wt);
			allTotalGridData[0].stone_amt+= (Number(stoneGridData[i].stone_wt) * Number(stoneGridData[i].stone_qty));
			allTotalGridData[0].labour_setting+=Number(stoneGridData[i].stone_qty)*6;
		}
	}
	for(var i=0;i<otherCostGridData.length;i++){
		if(otherCostGridData[i].other_amt && !otherCostGridData[i].other_desc.toLowerCase().includes("finding"))
			allTotalGridData[0].other_amt+= Number(otherCostGridData[i].other_amt);
		else if(otherCostGridData[i].other_amt && otherCostGridData[i].other_desc.toLowerCase().includes("finding"))
			allTotalGridData[0].labour_findings+= Number(otherCostGridData[i].other_amt);
	}
	allTotalGridData[0].labour_total = allTotalGridData[0].labour_cpf + allTotalGridData[0].labour_setting + allTotalGridData[0].labour_plating + allTotalGridData[0].labour_findings;
	allTotalGridData[0].gross_wt = allTotalGridData[0].metal_wt + (allTotalGridData[0].dia_wt + allTotalGridData[0].stone_wt)*0.2;
	allTotalGridData[0].fac_ppc = (allTotalGridData[0].metal_amt + allTotalGridData[0].labour_total + allTotalGridData[0].dia_amt + allTotalGridData[0].stone_amt + allTotalGridData[0].other_amt)/Number($("#itemQty").val());
	var factoryProfitIncludedPPC =allTotalGridData[0].fac_ppc + (allTotalGridData[0].fac_ppc*PackingListRates.factoryProfit*.01);
	var priceInUSD = factoryProfitIncludedPPC / PackingListRates.exchange;
	allTotalGridData[0].fac_sp = Math.round(priceInUSD * 3.5);
	allGridOptions.api.setRowData(allTotalGridData);
}
function AddMoreRows(){
	GetAllGridData();
	metalGridData.push({metal_sno: metalGridData.length+1},{metal_sno: metalGridData.length+2},{metal_sno: metalGridData.length+3},{metal_sno: metalGridData.length+4},{metal_sno: metalGridData.length+5});
	diamondGridData.push({dia_sno: diamondGridData.length+1},{dia_sno: diamondGridData.length+2},{dia_sno: diamondGridData.length+3},{dia_sno: diamondGridData.length+4},{dia_sno: diamondGridData.length+5});
	stoneGridData.push({stone_sno: stoneGridData.length+1},{stone_sno: stoneGridData.length+2},{stone_sno: stoneGridData.length+3},{stone_sno: stoneGridData.length+4},{stone_sno: stoneGridData.length+5});
	otherCostGridData.push({other_sno: otherCostGridData.length+1},{other_sno: otherCostGridData.length+2},{other_sno: otherCostGridData.length+3},{other_sno: otherCostGridData.length+4},{other_sno: otherCostGridData.length+5});

	metalGridOptions.api.setRowData(metalGridData);
	diamondGridOptions.api.setRowData(diamondGridData);
	stoneGridOptions.api.setRowData(stoneGridData);
	otherCostGridOptions.api.setRowData(otherCostGridData);
}
function GetAllGridData(){
	metalGridData=[];
	diamondGridData=[];
	stoneGridData=[];
	otherCostGridData=[];
	allTotalGridData=[];

	metalGridOptions.api.forEachNode(function(rowNode, index) {
		metalGridData.push(rowNode.data);	
	});
	diamondGridOptions.api.forEachNode(function(rowNode, index) {
		diamondGridData.push(rowNode.data);		
	});
	stoneGridOptions.api.forEachNode(function(rowNode, index) {
		stoneGridData.push(rowNode.data);		
	});
	otherCostGridOptions.api.forEachNode(function(rowNode, index) {
		otherCostGridData.push(rowNode.data);		
	});
}

//#endregion

//#region Settings

$("#settingsModal").on("show.bs.modal", getSettings);

function getSettings() {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_r.php",
		data: {
			func: "getSettings",
		},
	}).done(function (data) {
		hideLoader();
		$("#newPackingList label").removeClass("active");
		$("#exchangeRt").val(data.data[0].exchangeRt).change();
		$("#silverRt").val(data.data[0].silverRt).change();
		$("#goldRt").val(data.data[0].goldRt).change();
		$("#labourRt").val(data.data[0].labourRt).change();
		$("#platingRt").val(data.data[0].platingRt).change();
		$("#findingsRt").val(data.data[0].findingsRt).change();

		$("#microDiaRt").val(data.data[0].microDiaSettingRt).change();
		$("#prongDiaRt").val(data.data[0].prongDiaSettingRt).change();
		$("#roundStoneRt").val(data.data[0].roundStoneSettingRt).change();
		$("#baguetteDiaRt").val(data.data[0].baguetteDiaSettingRt).change();

	});
}

function updateSettings() {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_u.php",
		data: {
			func: "updateSettings",
			exchangeRt: $("#exchangeRt").val(),
			silverRt: $("#silverRt").val(),
			goldRt: $("#goldRt").val(),
			labourRt: $("#labourRt").val(),
			platingRt: $("#platingRt").val(),
			findingsRt: $("#findingsRt").val(),
			microDiaRt: $("#microDiaRt").val(),
			prongDiaRt: $("#prongDiaRt").val(),
			baguetteDiaRt: $("#baguetteDiaRt").val(),
			roundStoneRt: $("#roundStoneRt").val(),

		},
	}).done(function (data) {
		hideLoader();
		toastr["success"]("Settings Updated!");
		$("#settingsModal").modal("hide");
	});
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


