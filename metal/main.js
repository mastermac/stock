var SelectedStone;
var metalInv = {
	columnDefs: [
		{
			headerName: "S.No",
			field: "sno",
			headerCheckboxSelection: true,
			headerCheckboxSelectionFilteredOnly: true,
			checkboxSelection: true,
			editable: false,
			filter: false,
			width: 90
		},
		{ headerName: "Date", editable: true, field: "dt", width: 100, filter: "agDateColumnFilter" },
		{ headerName: "Description", field: "description", width: 280 },
		{ headerName: "G/S?", field: "type", width: 90 },
		{
			headerName: "Purity", field: "purity",
			cellEditor: 'agSelectCellEditor',
			cellEditorParams: {
				values: ['999', '18K', '14K', '925'],
			},
		},
		{ headerName: "Qty", field: "qty", filter: "agNumberColumnFilter", width: 90 },
		{ headerName: "Rate", field: "rate", width: 90 },
		{ headerName: "Amt", field: "amt", width: 100, editable: false },
		{
			headerName: "Delete",
			field: "id",
			filter: false,
			sortable: false,
			editable: false,
			cellRenderer: "delButton",
			cellRendererParams: {
				clicked: function (field) {
					showLoader();
					$.ajax({
						dataType: "json",
						url: url + "../src/scripts/metal.php",
						data: {
							func: "delete",
							id: field,
						},
					}).done(function (data) {
						hideLoader();
						getMetalLists();
					});
				},
			},
			resizable: false,
			width: 80,
		},
	],
	defaultColDef: {
		width: 100,
		wrapText: true,
		autoHeight: true,
		resizable: true,
		editable: true,
		filter: true,
		sortable: true,
		type: "leftAligned",
		enableCellChangeFlash: true,
		cellStyle: { "white-space": "normal" },
		columnsMenuParams: {
			// suppresses updating the layout of columns as they are rearranged in the grid
			suppressSyncLayoutWithGrid: true,
			suppressColumnFilter: true,
			suppressColumnSelectAll: true,
			suppressColumnExpandAll: true,
			contractColumnSelection: true
		},
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
		metalInv.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};

var allowRowEdit=true;

if(usertype==0)
	$("#metalSaveDataButton").show();
else
	allowRowEdit=false;


var metalSoldInv = {
	columnDefs: [
		{
			headerName: "S.No",
			field: "sno",
			headerCheckboxSelection: true,
			headerCheckboxSelectionFilteredOnly: true,
			checkboxSelection: true,
			editable: false,
			filter: false,
			width: 90
		},
		{ headerName: "Date", field: "dt", width: 100, filter: "agDateColumnFilter" },
		{ headerName: "Description", field: "description", width: 200 },
		{ headerName: "G/S?", field: "type", width: 90 },
		{
			headerName: "Purity", field: "purity",
			cellEditor: 'agSelectCellEditor',
			cellEditorParams: {
				values: ['999', '18K', '14K', '925'],
			},
		},
		{ headerName: "Qty", field: "qty", filter: "agNumberColumnFilter", width: 90 },
		{ headerName: "PL #", field: "pl_id", width: 90,  editable: false },
		{ headerName: "Rate", field: "rate", width: 90, hide: true },
		{ headerName: "Amt", field: "amount", width: 100, hide: true, editable: false },
		{
			headerName: "Delete",
			field: "id",
			filter: false,
			sortable: false,
			editable: false,
			hide: !allowRowEdit,
			cellRenderer: "delButton",
			cellRendererParams: {
				clicked: function (field) {
					showLoader();
					$.ajax({
						dataType: "json",
						url: url + "../src/scripts/metal.php",
						data: {
							func: "delete_save",
							id: field,
						},
					}).done(function (data) {
						hideLoader();
						getMetalLists();
					});
				},
			},
			pinned: 'right',
			resizable: false,
			width: 100,
		},
	],
	defaultColDef: {
		width: 100,
		wrapText: true,
		autoHeight: true,
		resizable: true,
		editable: allowRowEdit,
		filter: true,
		sortable: true,
		type: "leftAligned",
		enableCellChangeFlash: true,
		cellStyle: { "white-space": "normal" },
		columnsMenuParams: {
			// suppresses updating the layout of columns as they are rearranged in the grid
			suppressSyncLayoutWithGrid: true,
			suppressColumnFilter: true,
			suppressColumnSelectAll: true,
			suppressColumnExpandAll: true,
			contractColumnSelection: true
		},
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
		metalInv.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};

function Init() {

}




function exportData() {
	let params = {
		columnKeys: ['sno', 'lot_no', 'name', 'size', 'shape', 'seller', 'purchased_qty', 'purchased_wt', 'unit', 'current_qty', 'current_wt', 'box', 'cost', 'less', 'rate', 'total_amount', 'current_value', 'description']
	};

	let selectedRows = metalInv.api.getSelectedNodes();

	if (selectedRows.length > 0) {
		params = {
			onlySelected: true,
			columnKeys: ['sno', 'lot_no', 'name', 'size', 'shape', 'seller', 'purchased_qty', 'purchased_wt', 'unit', 'current_qty', 'current_wt', 'box', 'cost', 'less', 'rate', 'total_amount', 'current_value', 'description']
		}
	}
	metalInv.api.exportDataAsCsv(params);
}

function moveToInvoice() {
	let selectedRows = metalInv.api.getSelectedNodes();
	if (selectedRows.length <= 0) return;
	if (changedRows.length > 0) {
		Swal.fire('Please Save your changes before initiating this request!');
		return;
	}
	let temp = false;
	selectedRows.forEach(row => {
		if (row.data.invoice_id) {
			temp = true;
			return;
		}
	})
	if (temp) {
		Swal.fire('Please unselect already moved items before initiating this request!');
		return;
	}

	$("#invoiceModal").modal('show');
}

$("#invoiceModal").on("show.bs.modal", getPackingLists);


function AddMoreRows() {
	let data = [];
	metalInv.api.forEachNode(function (rowNode, index) {
		data.push(rowNode.data);
	});
	data.push({ sno: data.length + 1 }, { sno: data.length + 2 }, { sno: data.length + 3 }, { sno: data.length + 4 }, { sno: data.length + 5 }, { sno: data.length + 6 }, { sno: data.length + 7 }, { sno: data.length + 8 }, { sno: data.length + 9 }, { sno: data.length + 10 });
	metalInv.api.setRowData(data);

}

function getPackingLists() {
	$("#plNameParent").css("display", "none");
	$("#plDateParent").css("display", "none");
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
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_r.php",
		data: {
			func: "getPackingLists",
		},
	}).done(function (data) {
		hideLoader();
		document.getElementById("invoiceNames").options.length = 0;
		data.data.forEach(pl => {
			let optionText = pl.name;
			let optionValue = pl.id;
			$('#invoiceNames').append(`<option value="${optionValue}">${optionText}</option>`);
		});
		$('#invoiceNames').append(`<option value="other">Other</option>`);
		console.log(data.data);
	});
}
function invoiceChanged(sel) {
	if (sel.value == "other") {
		$("#plNameParent").css("display", "block");
		$("#plDateParent").css("display", "block");
	}
	else {
		$("#plNameParent").css("display", "none");
		$("#plDateParent").css("display", "none");
	}
}
function moveToPackingList() {
	showLoader();
	var dt = metalInv.api.getSelectedNodes();
	let manuInventory = [];
	dt.forEach(row => {
		let firstOccurance = getFirstOccuranceOfItem(row.data.vendorCode);
		if (firstOccurance.sno == row.data.sno) {
			manuInventory.push(row.data);
			manuInventory[manuInventory.length - 1].stones = [];
			manuInventory[manuInventory.length - 1].diamonds = [];
			let customData = { "lotNo": row.data.lotNo, "stoneName": row.data.stoneName, "dia_stone_pcs": row.data.dia_stone_pcs, "wt_in_cts": row.data.wt_in_cts };
			if (row.data.d_or_s.toLowerCase() == "s")
				manuInventory[manuInventory.length - 1].stones.push(customData);
			else if (row.data.d_or_s.toLowerCase() == "d")
				manuInventory[manuInventory.length - 1].diamonds.push(customData);
		}
		else {
			for (let i = 0; i < manuInventory.length; i++) {
				if (manuInventory[i].vendorCode == row.data.vendorCode) {
					let customData = { "lotNo": row.data.lotNo, "stoneName": row.data.stoneName, "dia_stone_pcs": row.data.dia_stone_pcs, "wt_in_cts": row.data.wt_in_cts };
					if (row.data.d_or_s.toLowerCase() == "s")
						manuInventory[i].stones.push(customData);
					else if (row.data.d_or_s.toLowerCase() == "d")
						manuInventory[i].diamonds.push(customData);
					break;
				}
			}
		}
	});
	console.log(manuInventory);
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/manufacturing.php",
		data: {
			func: "moveToInvoice",
			packing: $("#invoiceNames").val(),
			data: manuInventory
		},
	}).done(function (data) {
		hideLoader();
		getMetalLists();
		toastr['success'](data.result);
		$("#invoiceModal").modal("hide");
	});
}

function uploadMetalAvailableData() {
	let manuInventory = [];
	metalInv.api.forEachNode(function (rowNode, index) {
		if (changedRows.indexOf(rowNode.data.sno) >= 0)
			manuInventory.push(rowNode.data);
	});
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/metal.php",
		data: {
			func: "upsertData",
			data: manuInventory
		},
	}).done(function (data) {
		changedRows = [];
		hideLoader();
		$("#stReset").click();
		SelectedStone = null;
		getMetalLists();
		$("#stoneFormModal").modal("hide");
	});
}

function uploadMetalSaveData() {
	let manuInventory = [];
	metalSoldInv.api.forEachNode(function (rowNode, index) {
		if (changedSoldRows.indexOf(rowNode.data.sno) >= 0)
			manuInventory.push(rowNode.data);
	});
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/metal.php",
		data: {
			func: "upsertSoldData",
			data: manuInventory
		},
	}).done(function (data) {
		changedSoldRows = [];
		hideLoader();
		$("#stReset").click();
		SelectedStone = null;
		getMetalLists();
		$("#stoneFormModal").modal("hide");
	});
}

function resetAvailableFilters(){
	metalInv.api.setFilterModel(null);
	metalInv.api.onFilterChanged();
}
function resetSoldFilters(){
	metalSoldInv.api.setFilterModel(null);
	metalSoldInv.api.onFilterChanged();
}

var currentPL, currentItem;
var myCellRenderer = function () {
	return '<span style="color: black">Edit</span>';
};
function onQuickFilterChanged() {
	metalInv.api.setQuickFilter(document.getElementById("quickFilter").value);
}
var newData = new Array();
// setup the grid after the page has finished loading

var changedRows = [];

var changedSoldRows = [];

document.addEventListener("DOMContentLoaded", function () {
	var gridDiv = document.querySelector("#metalInventory");
	$("#metalInventory").css("height", window.innerHeight - 170 + "px");
	new agGrid.Grid(gridDiv, metalInv);

	var gridDiv = document.querySelector("#metalSoldInventory");
	$("#metalSoldInventory").css("height", window.innerHeight - 170 + "px");
	new agGrid.Grid(gridDiv, metalSoldInv);

	getMetalLists();
	metalInv.getRowNodeId = d => {
		return d.sno; // return the property you want set as the id.
	};
	metalSoldInv.getRowNodeId = d => {
		return d.sno; // return the property you want set as the id.
	};

	metalSoldInv.onCellValueChanged = function (event) {
		if (!(String(event.oldValue) === event.newValue)) changedSoldRows.push(event.data.sno);
	}

	metalInv.onCellValueChanged = function (event) {
		if (!(String(event.oldValue) === event.newValue)) changedRows.push(event.data.sno);

		if (event.data.type && event.data.purity && event.data.qty && event.data.rate){
			if(event.column.colId === "type" || event.column.colId === "purity" || event.column.colId === "qty" || event.column.colId === "rate"){
				let multiplier = 1;
				if(event.data.type.toLowerCase()=="g"){
					if(event.data.purity.toLowerCase()==="18k")
						multiplier = 0.76;
					else if(event.data.purity.toLowerCase()==="14k")
						multiplier = 0.59;
				}
				metalInv.api.getRowNode(event.data.sno).setDataValue('amt', Math.round(event.data.qty * event.data.rate * multiplier));
			}
		}
	}
});

function setStoneDetails(lotid, sno) {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_r.php",
		data: {
			func: "getStoneById",
			lotId: lotid
		},
	}).done(function (data) {
		hideLoader();
		if (data.data)
			metalInv.api.getRowNode(sno).setDataValue('stoneName', data.data.name);
		else
			toastr['error']("No Record found with Lot Id");
	});
}


function getFirstOccuranceOfItem(vendorCode) {
	var data = 0;
	metalInv.api.forEachNode(function (rowNode, index) {
		if (rowNode.data.vendorCode == vendorCode && data == 0)
			data = rowNode.data;
	});
	return data;
}

function getWeightInfoForItem(vendorCode, sno) {
	var wt = 0;
	metalInv.api.forEachNode(function (rowNode, index) {
		if (rowNode.data.sno != sno && rowNode.data.vendorCode == vendorCode)
			wt += Number(rowNode.data.wt_in_grms) + Number(rowNode.data.other_metal_grm);
	});
	return wt;
}
function getAllWeightInfoForItem(vendorCode) {
	var wt = 0;
	metalInv.api.forEachNode(function (rowNode, index) {
		if (rowNode.data.vendorCode == vendorCode)
			wt += Number(rowNode.data.wt_in_grms) + Number(rowNode.data.other_metal_grm);
	});
	return wt;
}


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
	if (SelectedStone) {
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
	else {
		$('#lot_no').attr('readonly', false);
		$('#lot_no').removeClass('text-muted');
		$("#stoneFormHeader").html("Add New Stone");
	}
}
function destroyStoneForm() {
	SelectedStone = null;
}
function getJSONFormData($form) {
	var unindexed_array = $form.serializeArray();
	var indexed_array = {};

	$.map(unindexed_array, function (n, i) {
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
		url: url + "../src/scripts/manufacturing.php",
		data: formData,
	}).done(function (data) {
		hideLoader();
		$("#stReset").click();
		SelectedStone = null;
		getMetalLists();
		$("#stoneFormModal").modal("hide");
	});
}))


function getMetalLists() {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/metal.php",
		data: {
			func: "getMetalList",
		},
	}).done(function (data) {
		BindMetalLists(data.data, metalInv);
		calculateTotals(data.data, "Available");		
	});
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/metal.php",
		data: {
			func: "getSoldMetalList",
		},
	}).done(function (data) {
		hideLoader();
		BindMetalLists(data.data, metalSoldInv);
		calculateTotals(data.data, "Sold");		
	});
}

function calculateTotals(data, type) {
	let gTot = 0, gWt = 0, sTot = 0, sWt = 0, multiplier = 1;
	data.forEach(row => {
		if (row.id) {
			if (row.type.toLowerCase() == "g") {
				if (row.purity.toLowerCase() == "999")
					multiplier = 1;
				else if (row.purity.toLowerCase() == "18k")
					multiplier = 0.76;
				else if (row.purity.toLowerCase() == "14k")
					multiplier = 0.59;

				gTot += (row.qty * row.rate * multiplier)
				gWt += (row.qty * multiplier)
			}
			if (row.type.toLowerCase() == "s") {
				sTot += (row.qty * row.rate)
				sWt += row.qty
			}
		}
	});

	$("#totalGoldWt"+type).html(gWt.toLocaleString(undefined, { minimumFractionDigits: 2 }));
	$("#totalSilverWt"+type).html(sWt.toLocaleString(undefined, { minimumFractionDigits: 2 }));

	$("#totalGoldAmt"+type).html(gTot.toLocaleString(undefined, { minimumFractionDigits: 2 }));
	$("#totalSilverAmt"+type).html(sTot.toLocaleString(undefined, { minimumFractionDigits: 2 }));

	if (gWt == 0) gWt = 1;
	if (sWt == 0) sWt = 1;

	$("#avgGoldPrice"+type).html(Math.round(gTot / gWt).toLocaleString(undefined, { minimumFractionDigits: 2 }));
	$("#avgSilverPrice"+type).html(Math.round(sTot / sWt).toLocaleString(undefined, { minimumFractionDigits: 2 }));
}

function BindMetalLists(data, Inv) {
	data.push({ sno: data.length + 1 }, { sno: data.length + 2 }, { sno: data.length + 3 }, { sno: data.length + 4 }, { sno: data.length + 5 });//, { sno: data.length + 6 }, { sno: data.length + 7 }, { sno: data.length + 8 }, { sno: data.length + 9 }, { sno: data.length + 10 });
	Inv.api.setRowData(data);
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
		metalInv.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};

$("#packingListItemsModal").on("show.bs.modal", () => {
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
	{ headerName: "Amount", field: "metal_amt", editable: false },
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
				var data = [];
				metalGridOptions.api.forEachNode(function (rowNode, index) {
					if (index < field - 1) {
						data.push(rowNode.data);
					}
					else if (index > field - 1) {
						var node = rowNode.data;
						node.metal_sno = node.metal_sno - 1;
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
				var data = [];
				diamondGridOptions.api.forEachNode(function (rowNode, index) {
					if (index < field - 1) {
						data.push(rowNode.data);
					}
					else if (index > field - 1) {
						var node = rowNode.data;
						node.dia_sno = node.dia_sno - 1;
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
				var data = [];
				stoneGridOptions.api.forEachNode(function (rowNode, index) {
					if (index < field - 1) {
						data.push(rowNode.data);
					}
					else if (index > field - 1) {
						var node = rowNode.data;
						node.stone_sno = node.stone_sno - 1;
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
				var data = [];
				otherCostGridOptions.api.forEachNode(function (rowNode, index) {
					if (index < field - 1) {
						data.push(rowNode.data);
					}
					else if (index > field - 1) {
						var node = rowNode.data;
						node.other_sno = node.other_sno - 1;
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
			{ headerName: "Amt", field: "metal_amt" },
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
var allDetailsGO = {
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
var dummyData = [{ sno: 1 }, { sno: 2 }, { sno: 3 }, { sno: 4 }, { sno: 5 }, { sno: 6 }, { sno: 7 },];
function InitPLItemDetailsForm() {
	$("#PL-Items").trigger("reset");
	$("#PL-Items label").removeClass("active");

	new agGrid.Grid(document.querySelector("#MetalDetailsGrid"), metalGridOptions);
	metalGridOptions.getRowNodeId = d => {
		return d.metal_sno;
	};
	metalGridOptions.api.setRowData([{ metal_sno: 1 }, { metal_sno: 2 }, { metal_sno: 3 }, { metal_sno: 4 }, { metal_sno: 5 }, { metal_sno: 6 }, { metal_sno: 7 },]);

	metalGridOptions.api.setColumnDefs(metalDetailsColDef);
	metalGridOptions.onCellValueChanged = function (event) {
		if (event.data.metal_wt && event.column.colId === "metal_wt") {
			metalGridOptions.api.getRowNode(event.data.metal_sno).setDataValue('metal_amt', PackingListRates.silver * event.data.metal_wt);
		}
	}

	new agGrid.Grid(document.querySelector("#DiamondDetailsGrid"), diamondGridOptions);
	diamondGridOptions.getRowNodeId = d => {
		return d.dia_sno;
	};
	diamondGridOptions.api.setRowData([{ dia_sno: 1 }, { dia_sno: 2 }, { dia_sno: 3 }, { dia_sno: 4 }, { dia_sno: 5 }, { dia_sno: 6 }, { dia_sno: 7 },]);
	diamondGridOptions.api.setColumnDefs(diamondDetailsColDef);
	diamondGridOptions.onCellValueChanged = function (event) {
		if (event.data.dia_wt && event.data.dia_rate && (event.column.colId === "dia_wt" || event.column.colId === "dia_rate")) {
			diamondGridOptions.api.getRowNode(event.data.dia_sno).setDataValue('dia_amt', event.data.dia_wt * event.data.dia_rate);
		}
	}

	new agGrid.Grid(document.querySelector("#StoneDetailsGrid"), stoneGridOptions);
	stoneGridOptions.getRowNodeId = d => {
		return d.stone_sno;
	};
	stoneGridOptions.api.setRowData([{ stone_sno: 1 }, { stone_sno: 2 }, { stone_sno: 3 }, { stone_sno: 4 }, { stone_sno: 5 }, { stone_sno: 6 }, { stone_sno: 7 },]);
	stoneGridOptions.api.setColumnDefs(stoneDetailsColDef);
	stoneGridOptions.onCellValueChanged = function (event) {
		if (event.data.stone_wt && event.data.stone_rate && (event.column.colId === "stone_wt" || event.column.colId === "stone_rate")) {
			stoneGridOptions.api.getRowNode(event.data.stone_sno).setDataValue('stone_amt', event.data.stone_wt * event.data.stone_rate);
		}
	}

	new agGrid.Grid(document.querySelector("#OtherCostsDetailsGrid"), otherCostGridOptions);
	otherCostGridOptions.getRowNodeId = d => {
		return d.other_sno;
	};
	otherCostGridOptions.api.setRowData([{ other_sno: 1 }, { other_sno: 2 }, { other_sno: 3 }, { other_sno: 4 }, { other_sno: 5 }, { other_sno: 6 }, { other_sno: 7 },]);
	otherCostGridOptions.api.setColumnDefs(otherCostsDetailsColDef);

	new agGrid.Grid(document.querySelector("#AllDetailsGrid"), allGridOptions);
	allGridOptions.getRowNodeId = d => {
		return d.metal_sno;
	};
	allGridOptions.api.setRowData([{ metal_sno: 1 }, { metal_sno: 2 }, { metal_sno: 3 }, { metal_sno: 4 }, { metal_sno: 5 }, { metal_sno: 6 }, { metal_sno: 7 },]);
	allGridOptions.api.setColumnDefs(allDetailsColDef);

}

var metalGridData = [];
var diamondGridData = [];
var stoneGridData = [];
var otherCostGridData = [];
var allTotalGridData = [];

function AllDetailsTabClicked() {
	GetAllGridData();
	allTotalGridData = [
		{
			gross_wt: 0,
			metal_wt: 0,
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
	for (var i = 0; i < metalGridData.length; i++) {
		if (metalGridData[i].metal_wt) {
			allTotalGridData[0].metal_wt += Number(metalGridData[i].metal_wt);
			allTotalGridData[0].metal_amt += (Number(metalGridData[i].metal_wt) * PackingListRates.silver);
			allTotalGridData[0].labour_cpf += (Number(metalGridData[i].metal_wt) * PackingListRates.labour);
			allTotalGridData[0].labour_plating += (Number(metalGridData[i].metal_wt) * PackingListRates.plating);
		}
	}

	for (var i = 0; i < diamondGridData.length; i++) {
		if (diamondGridData[i].dia_lot_id) {
			allTotalGridData[0].dia_qty += Number(diamondGridData[i].dia_qty);
			allTotalGridData[0].dia_wt += Number(diamondGridData[i].dia_wt);
			allTotalGridData[0].dia_amt += (Number(diamondGridData[i].dia_wt) * Number(diamondGridData[i].dia_qty));
			allTotalGridData[0].labour_setting += Number(diamondGridData[i].dia_qty) * 10;
		}
	}
	for (var i = 0; i < stoneGridData.length; i++) {
		if (stoneGridData[i].stone_lot_id) {
			allTotalGridData[0].stone_qty += Number(stoneGridData[i].stone_qty);
			allTotalGridData[0].stone_wt += Number(stoneGridData[i].stone_wt);
			allTotalGridData[0].stone_amt += (Number(stoneGridData[i].stone_wt) * Number(stoneGridData[i].stone_qty));
			allTotalGridData[0].labour_setting += Number(stoneGridData[i].stone_qty) * 6;
		}
	}
	for (var i = 0; i < otherCostGridData.length; i++) {
		if (otherCostGridData[i].other_amt && !otherCostGridData[i].other_desc.toLowerCase().includes("finding"))
			allTotalGridData[0].other_amt += Number(otherCostGridData[i].other_amt);
		else if (otherCostGridData[i].other_amt && otherCostGridData[i].other_desc.toLowerCase().includes("finding"))
			allTotalGridData[0].labour_findings += Number(otherCostGridData[i].other_amt);
	}
	allTotalGridData[0].labour_total = allTotalGridData[0].labour_cpf + allTotalGridData[0].labour_setting + allTotalGridData[0].labour_plating + allTotalGridData[0].labour_findings;
	allTotalGridData[0].gross_wt = allTotalGridData[0].metal_wt + (allTotalGridData[0].dia_wt + allTotalGridData[0].stone_wt) * 0.2;
	allTotalGridData[0].fac_ppc = (allTotalGridData[0].metal_amt + allTotalGridData[0].labour_total + allTotalGridData[0].dia_amt + allTotalGridData[0].stone_amt + allTotalGridData[0].other_amt) / Number($("#itemQty").val());
	var factoryProfitIncludedPPC = allTotalGridData[0].fac_ppc + (allTotalGridData[0].fac_ppc * PackingListRates.factoryProfit * .01);
	var priceInUSD = factoryProfitIncludedPPC / PackingListRates.exchange;
	allTotalGridData[0].fac_sp = Math.round(priceInUSD * 3.5);
	allGridOptions.api.setRowData(allTotalGridData);
}
function GetAllGridData() {
	metalGridData = [];
	diamondGridData = [];
	stoneGridData = [];
	otherCostGridData = [];
	allTotalGridData = [];

	metalGridOptions.api.forEachNode(function (rowNode, index) {
		metalGridData.push(rowNode.data);
	});
	diamondGridOptions.api.forEachNode(function (rowNode, index) {
		diamondGridData.push(rowNode.data);
	});
	stoneGridOptions.api.forEachNode(function (rowNode, index) {
		stoneGridData.push(rowNode.data);
	});
	otherCostGridOptions.api.forEachNode(function (rowNode, index) {
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


