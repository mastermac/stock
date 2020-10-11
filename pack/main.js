var PackingListRates;
var gridOptions_PL = {
	columnDefs: [
		{
			headerName: "S.No",
			field: "sno",
			headerCheckboxSelection: true,
			headerCheckboxSelectionFilteredOnly: true,
			checkboxSelection: true,
			editable: false,
		},
		{
			headerName: "Name",
			field: "name",
			width: 150,
		},
		{ headerName: "Date", field: "dt", width: 60, filter: "agDateColumnFilter" },
		{ headerName: "Exchange", field: "exchangeRt", filter: "agNumberColumnFilter" },
		{ headerName: "Silver", field: "silverRt", filter: "agNumberColumnFilter" },
		{ headerName: "Gold", field: "goldRt", filter: "agNumberColumnFilter" },
		{ headerName: "Labour", field: "labourRt", filter: "agNumberColumnFilter" },
		{ headerName: "G Labour", field: "goldLabourRt", filter: "agNumberColumnFilter" },
		{ headerName: "Plating", field: "platingRt", filter: "agNumberColumnFilter" },
		{ headerName: "Findings", field: "findingsRt", filter: "agNumberColumnFilter" },
		{ headerName: "Micro Dia", field: "microDiaSettingRt", hide: true },
		{ headerName: "Prong Dia", field: "prongDiaSettingRt", hide: true },
		{ headerName: "Baguette Dia", field: "baguetteDiaSettingRt", hide: true },
		{ headerName: "Round Stone", field: "roundStoneSettingRt", hide: true },
		{
			headerName: "Edit",
			field: "id",
			filter: false,
			sortable: false,
			cellRenderer: "editButton",
			cellRendererParams: {
				clicked: function (field) {
					currentPL = field;
					var currentRow=gridOptions_PL.api.getRowNode(currentPL);
					PackingListRates=new PL_Rates(currentRow.data.exchangeRt,currentRow.data.silverRt, currentRow.data.goldRt, currentRow.data.labourRt, currentRow.data.goldLabourRt, currentRow.data.platingRt, currentRow.data.findingsRt, currentRow.data.microDiaSettingRt, currentRow.data.prongDiaSettingRt, currentRow.data.baguetteDiaSettingRt, currentRow.data.roundStoneSettingRt);
					console.log(PackingListRates);
					$("#packingListItemsModal").modal("show");
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
							func: "deletePackingList",
							id: field,
						},
					}).done(function (data) {
						hideLoader();
						getPackingLists();
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
		gridOptions_PL.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};
var currentPL, currentItem;
var myCellRenderer = function () {
	return '<span style="color: black">Edit</span>';
};
function onQuickFilterChanged() {
	gridOptions_PL.api.setQuickFilter(document.getElementById("quickFilter").value);
}
var newData = new Array();
// setup the grid after the page has finished loading
document.addEventListener("DOMContentLoaded", function () {
	var gridDiv = document.querySelector("#packingLists");
	new agGrid.Grid(gridDiv, gridOptions_PL);
	getPackingLists();
	gridOptions_PL.getRowNodeId = d => {
		return d.id; // return the property you want set as the id.
	};
	// agGrid
	// 	.simpleHttpRequest({
	// 		url: "https://raw.githubusercontent.com/ag-grid/ag-grid/master/grid-packages/ag-grid-docs/src/olympicWinnersSmall.json",
	// 	})
	// 	.then(function (data) {
	// 		gridOptions_PL.api.setRowData(data);
	// 	});
});

//#region Packing Lists

$("#packingListModal").on("show.bs.modal", initializePackingListForm);
function initializePackingListForm() {
	$("#newPackingList").trigger("reset");
	$("#newPackingList label").removeClass("active");
	window.start = moment().format("YYYY-MM-DD");
	$("#plDate").daterangepicker(
		{
			singleDatePicker: true,
			opens: "center",
		},
		function (start, end, label) {
			window.start = start.format("YYYY-MM-DD");
		}
	);
}


function createPackingList() {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_c.php",
		data: {
			func: "createPackingList",
			date: window.start,
			name: $("#plName").val(),
		},
	}).done(function (data) {
		hideLoader();
		getPackingLists();
		$("#packingListModal").modal("hide");
	});
}


function getPackingLists() {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_r.php",
		data: {
			func: "getPackingLists",
		},
	}).done(function (data) {
		hideLoader();
		BindPackingLists(data.data);
	});
}
function BindPackingLists(data) {
	gridOptions_PL.api.setRowData(data);
	gridOptions_PL.api.sizeColumnsToFit();
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
					showLoader();
					currentItem = field;
					$.ajax({
						dataType: "json",
						url: url + "../src/scripts/packingList_r.php",
						data: {
							func: "getPackingListItemById",
							id: currentPL,
							itemId: currentItem
						},
					}).done(function (data) {
						resetPLItem();
						hideLoader();
					});
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
		gridOptions_PL.api.flashCells({ rowNodes: [event.rowIndex] });
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
	AllDetailsTabClicked(true);

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
			metals: metalGridData,
			diamonds: diamondGridData,
			stones: stoneGridData,
			others: otherCostGridData
		},
	}).done(function (data) {
		hideLoader();
		resetPLItem();
	});
}

function resetPLItem(){
	gridOptions_PL_Items.api.destroy();
	metalGridOptions.api.destroy();
	diamondGridOptions.api.destroy();
	stoneGridOptions.api.destroy();
	otherCostGridOptions.api.destroy();
	allGridOptions.api.destroy();
	InitPLItemsForm();
	InitPLItemDetailsForm();
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

function getStoneMultiplier(){
	let multiplier = 1;
	switch($("#itemMetalType").val().toLowerCase()){
		case "14k": multiplier = 0.59 * 1.1 * PackingListRates.gold; break;
		case "18k": multiplier = 0.76 * 1.1 * PackingListRates.gold; break;
		case "10k": multiplier = 0.42 * 1.1 * PackingListRates.gold; break;
		case "925": multiplier = 1 * PackingListRates.silver; break;
		case "other": multiplier = 1; break;
	}
	return multiplier;
}

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
			metalGridOptions.api.getRowNode(event.data.metal_sno).setDataValue('metal_amt', Math.round(getStoneMultiplier() * event.data.metal_wt));
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
			diamondGridOptions.api.getRowNode(event.data.dia_sno).setDataValue('dia_amt', Math.round(event.data.dia_wt * event.data.dia_rate));
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

function AllDetailsTabClicked(ignoreEmpty=false){
	GetAllGridData(ignoreEmpty);
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
			allTotalGridData[0].metal_amt+= (Number(metalGridData[i].metal_wt) * getStoneMultiplier());
			let labourRt = PackingListRates.labour;
			let platingRt = 0;

			if($("#itemMetalType").val().toLowerCase()!="925")	labourRt=PackingListRates.goldLabour;
			if($("#itemMetalColor").val().toLowerCase()=="wg" || $("#itemMetalColor").val().toLowerCase()=="rh" || $("#itemMetalColor").val().toLowerCase()=="yp")
				platingRt = PackingListRates.plating;

			allTotalGridData[0].labour_cpf+= (Number(metalGridData[i].metal_wt) * labourRt);
			allTotalGridData[0].labour_plating+= (Number(metalGridData[i].metal_wt) * platingRt);
		}
	}

	for(var i=0;i<diamondGridData.length;i++){
		if(diamondGridData[i].dia_lot_id){
			allTotalGridData[0].dia_qty+= Number(diamondGridData[i].dia_qty);
			allTotalGridData[0].dia_wt+= Number(diamondGridData[i].dia_wt);
			allTotalGridData[0].dia_amt+= (Math.round(Number(diamondGridData[i].dia_wt) * Number(diamondGridData[i].dia_rate)));
			allTotalGridData[0].labour_setting+=Number(diamondGridData[i].dia_qty)*10;
		}
	}
	for(var i=0;i<stoneGridData.length;i++){
		if(stoneGridData[i].stone_lot_id){
			allTotalGridData[0].stone_qty+= Number(stoneGridData[i].stone_qty);
			allTotalGridData[0].stone_wt+= Number(stoneGridData[i].stone_wt);
			allTotalGridData[0].stone_amt+= (Number(stoneGridData[i].stone_wt) * Number(stoneGridData[i].stone_rate));
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
	allTotalGridData[0].fac_ppc = Math.round(allTotalGridData[0].metal_amt + allTotalGridData[0].labour_total + allTotalGridData[0].dia_amt + allTotalGridData[0].stone_amt + allTotalGridData[0].other_amt)/Number($("#itemQty").val(),0);
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
function GetAllGridData(ignoreEmpty=false){
	metalGridData=[];
	diamondGridData=[];
	stoneGridData=[];
	otherCostGridData=[];
	allTotalGridData=[];

	metalGridOptions.api.forEachNode(function(rowNode, index) {
		if((ignoreEmpty && rowNode.data.metal_wt) || !ignoreEmpty)
			metalGridData.push(rowNode.data);	
	});
	diamondGridOptions.api.forEachNode(function(rowNode, index) {
		if((ignoreEmpty && rowNode.data.dia_lot_id) || !ignoreEmpty)
			diamondGridData.push(rowNode.data);		
	});
	stoneGridOptions.api.forEachNode(function(rowNode, index) {
		if((ignoreEmpty && rowNode.data.stone_lot_id) || !ignoreEmpty)
			stoneGridData.push(rowNode.data);		
	});
	otherCostGridOptions.api.forEachNode(function(rowNode, index) {
		if((ignoreEmpty && rowNode.data.other_amt) || !ignoreEmpty)
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
		$("#goldLabourRt").val(data.data[0].goldLabourRt).change();
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
			goldLabourRt: $("#goldLabourRt").val(),
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
