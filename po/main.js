var PackingListRates;
toastr.options = {
	"positionClass": "toast-bottom-right",
};
var gridOptions_PL = {
	columnDefs: [
		{
			headerName: "S.No",
			field: "sno",
			headerCheckboxSelection: true,
			headerCheckboxSelectionFilteredOnly: true,
			checkboxSelection: true,
			editable: false,
			pinned: 'left',
		},
		{
			headerName: "PO #",
			field: "id",
			pinned: 'left',
			filter: "agNumberColumnFilter"
		},
		{ headerName: "Cust Code", field: "cust_code", filter: "agNumberColumnFilter", pinned: 'left', width: 130 },
		{ headerName: "Type", field: "type", width: 130 },
		{ headerName: "Entry", field: "entry_date", filter: "agDateColumnFilter" },
		{ headerName: "Order", field: "order_date", filter: "agDateColumnFilter" },
		{ headerName: "Ship", field: "ship_date", filter: "agDateColumnFilter" },
		{ headerName: "Cancel", field: "cancel_date", filter: "agDateColumnFilter" },
		{ headerName: "Note", field: "note", width: 300 },
		{ headerName: "Total Items", field: "item_count", width: 130, filter: "agNumberColumnFilter" },
		{ headerName: "Total", field: "total", filter: "agNumberColumnFilter" },
		{ headerName: "Last Modified", field: "last_modified_date", filter:"agDateColumnFilter", width: 150 },		
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
					if(currentRow.data.status>0)
						hideDeleteOptions = true;
					else
						hideDeleteOptions = false;

					PackingListRates=new PL_Rates(currentRow.data.exchangeRt,currentRow.data.silverRt, currentRow.data.goldRt, currentRow.data.labourRt, currentRow.data.goldLabourRt, currentRow.data.platingRt, currentRow.data.findingsRt, currentRow.data.microDiaSettingRt, currentRow.data.prongDiaSettingRt, currentRow.data.baguetteDiaSettingRt, currentRow.data.roundStoneSettingRt, vendorProfit);
					console.log(PackingListRates);
					$("#packingListItemsModal").modal("show");
				},
			},
			width: 70,
			resizable: false,
			pinned: 'right',
		},
		{
			headerName: "Actions",
			field: "id",
			filter: false,
			sortable: false,
			cellRenderer: "delButton",
			cellRendererParams: {
				clicked: function (field) {
					$("#pid").val(field);
					$("#packingListActionModal").modal("show");
				},
			},
			width: 70,
			resizable: false,
			pinned: 'right',
		},
	],
	defaultColDef: {
		width: 100,
		wrapText: true,
		autoHeight: true,
		resizable: true,
		editable: false,
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
		gridOptions_PL.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};
var nextPurchaseOrder=1001;
var hideDeleteOptions=false;
$("#packingListActionModal").on("shown.bs.modal", () => {
	var pid = $("#pid").val();
	var currentRow=gridOptions_PL.api.getRowNode(pid);
	if(currentRow.data.status=="0")
	{
		$("#lockAction").show();
		$("#unlockAction").hide();
		$("#finalizeAction").hide();
	}
	else if(currentRow.data.status=="1"){
		$("#lockAction").hide();
		$("#unlockAction").show();
		$("#finalizeAction").show();
	}
	else if(currentRow.data.status=="2"){
		$("#deleteAction").hide();
		$("#lockAction").hide();
		$("#unlockAction").hide();
		$("#finalizeAction").hide();
	}
});

function deletePackingList(){
	var r = confirm("Sure about DELETING?");
	if (r == false)
		return;

	showLoader();
	var field=$("#pid").val();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_d.php",
		data: {
			func: "deletePackingList",
			id: field,
		},
	}).done(function (data) {
		hideLoader();
		$("#packingListActionModal").modal("hide");
		$("#pid").val(0);
		getPurchaseOrders();
	});
}

function lockPackingList(){
	var r = confirm("Sure about LOCKING the packing list? \n\nOnce It is locked, you won't be able to edit/delete any item details within the given packing list.");
	if (r == false)
		return;

	showLoader();
	var field=$("#pid").val();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_u.php",
		data: {
			func: "lock",
			id: field,
		},
	}).done(function (data) {
		hideLoader();
		if(data.error)
			toastr['error'](data.error);
		else
			getPurchaseOrders();
		$("#packingListActionModal").modal("hide");
		$("#pid").val(0);
	});
}

function unlockPackingList(){
	var r = confirm("Sure about UN-LOCKING the packing list? \n\nOnce It is un-locked, your respective metal, diamond and stone inventories will be restored and You will be able to edit/delete any item details within the given packing list.");
	if (r == false)
		return;
	showLoader();
	var field=$("#pid").val();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_u.php",
		data: {
			func: "unlock",
			id: field,
		},
	}).done(function (data) {
		hideLoader();
		getPurchaseOrders();
		$("#packingListActionModal").modal("hide");
		$("#pid").val(0);
	});
}


function finalizePackingList(){
	var r = confirm("Sure about FINALIZING the packing list? \n\nOnce It is done, Stock Panel's Inventory will be updated and You won't be able to edit/delete any details within the given packing list.");
	if (r == false)
		return;
	showLoader();
	var field=$("#pid").val();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_u.php",
		data: {
			func: "finalize",
			id: field,
		},
	}).done(function (data) {
		hideLoader();
		$("#packingListActionModal").modal("hide");
		$("#pid").val(0);
		getPurchaseOrders();
	});
}

function onColumnResized(params) {
	params.api.resetRowHeights();
}

function onColumnVisible(params) {
	params.api.resetRowHeights();
}
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
	getPurchaseOrders();
	gridOptions_PL.getRowNodeId = d => {
		return d.id; // return the property you want set as the id.
	};
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
	if(!$("#plName").val())
	{
		toastr['warning']("Please enter a name for packing list!");
		return;
	}
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
		getPurchaseOrders();
		$("#packingListModal").modal("hide");
	});
}


function getPurchaseOrders() {
	showLoader();
	getPurchaseOrdersCount();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/po.php",
		data: {
			func: "getPurchaseOrders",
		},
	}).done(function (data) {
		hideLoader();
		BindPurchaseOrders(data.data);
	});
}

function getPurchaseOrdersCount(){
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/packingList_r.php",
		data: {
			func: "getPackingListCounts",
		},
	}).done(function (data) {
		hideLoader();
		$("#totalDiamonds").html(data.data.dia_total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
		$("#totalStones").html(data.data.stone_total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
		$("#totalMetal").html(data.data.metal_total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
		$("#totalNetWorth").html("INR "+abbreviateNumber(Number(data.data.item_total)));
	});
}

function abbreviateNumber(value) {
    var newValue = value;
    if (value >= 1000) {
        var suffixes = ["", "K", "M", "B","T"];
        var suffixNum = Math.floor( (""+value).length/3 );
        var shortValue = '';
        for (var precision = 3; precision >= 1; precision--) {
            shortValue = parseFloat( (suffixNum != 0 ? (value / Math.pow(1000,suffixNum) ) : value).toPrecision(precision));
            var dotLessShortValue = (shortValue + '').replace(/[^a-zA-Z 0-9]+/g,'');
            if (dotLessShortValue.length <= 3) { break; }
        }
        if (shortValue % 1 != 0)  shortValue = shortValue.toFixed(2);
        newValue = shortValue+suffixes[suffixNum];
    }
    return newValue;
}

function BindPurchaseOrders(data) {
	if(data)
		nextPurchaseOrder = data[data.length-1].id+1;
	else
		nextPurchaseOrder = 1001;
	gridOptions_PL.api.setRowData(data);
	// gridOptions_PL.api.sizeColumnsToFit();
}

//#endregion

//#region PL-Items


var isEditingItem=false;
$("#packingListItemsModal").on("show.bs.modal", ()=>{
	// InitPLItemsForm();

	InitPLItemDetailsForm();
	$("#metalDetails-tab").click();
	$("#po_id").val(nextPurchaseOrder).change();
	$(".datepick").daterangepicker(
		{
			singleDatePicker: true,
			opens: "center",
		},
		function (start, end, label) {
			window.start = start.format("YYYY-MM-DD");
		}
	);
});
$("#packingListItemsModal").on("hidden.bs.modal", () => {
	poGridOptions.api.destroy();
});

function selectChanged(labelId){
	$("#"+labelId).addClass("active");
}

function InitPLItemsForm() {
	$("#UpsertPurchaseOrder").trigger("reset");
	$("#UpsertPurchaseOrder label").removeClass("active");
	var gridDiv = document.querySelector("#PL_Items_Grid");
	new agGrid.Grid(gridDiv, gridOptions_PL_Items);
	getPLItems(currentPL);
	gridOptions_PL_Items.getRowNodeId = d => {
		return d.id; // return the property you want set as the id.
	};
	gridOptions_PL_Items.columnApi.setColumnVisible('delete', !hideDeleteOptions);
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

$("#UpsertPurchaseOrder").on('submit', (function (e) {
	e.preventDefault();
	showLoader();
	var formData = new FormData(this);
	var form_action = $("#create-item").find("form").attr("action");
	$('.ajax-loader').css("visibility", "visible");
	AllDetailsTabClicked(true);
	let funcType='createPLItem';
	let urlEnd='packingList_c.php';
	if(isEditingItem){
		funcType='updatePLItem';
		urlEnd='packingList_u.php';
	}
	formData.append('func', funcType);
	formData.append('pid', currentPL);
	formData.append('diamonds', JSON.stringify(poItemsGridData));
	console.log(formData);
	$.ajax({
		type: 'POST',
		dataType: "json",
		url: url + "../src/scripts/"+urlEnd,
		data: formData,
		contentType: false,
		processData: false,
	}).done(function (data) {
		hideLoader();
		if(isEditingItem)
			toastr['success']("Item updated successfully.");
		else
			toastr['success']("Item added successfully.");
		updateStoneInventory();
		resetPLItem();
	});
}));

function updateStoneInventory(){

}

function resetPLItem(){
	gridOptions_PL_Items.api.destroy();
	poGridOptions.api.destroy();
	InitPLItemsForm();
	InitPLItemDetailsForm();
	$("#metalDetails-tab").click();
	isEditingItem=false;
	$("#PL_Items_Create").show();
	$('#metalDetails-tab').tab('show');
}
function BindPL_Items(data) {
	gridOptions_PL_Items.api.setRowData(data);
	gridOptions_PL_Items.api.sizeColumnsToFit();
}

//#endregion

//#region Edit Item Details
var poDetailsColDef = [
	{ headerName: "S.No", field: "sno", editable: false, width: 90 },
	{ headerName: "Id", field: "id", hide: true },
	{ headerName: "Item No", field: "itemNo"},
	{ headerName: "In Stk", field: "curStock", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "On Ordr", field: "onOrder", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Qty", field: "po_qty", filter: "agNumberColumnFilter" },
	{ headerName: "Price", field: "sellPrice", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Discount", field: "discount", filter: "agNumberColumnFilter" },
	{ headerName: "Unit Price", field: "unit_price", filter: "agNumberColumnFilter", editable: false },
	{ headerName: "Note", field: "note", width: 250 },
	{ headerName: "DESC", field: "description", width: 250 },
	{
		headerName: "Del",
		field: "sno",
		colId: "delete",
		width: 60,
		filter: false,
		sortable: false,
		editable: false,
		cellRenderer: "delButton",
		cellRendererParams: {
			clicked: function (field) {
				var r = confirm("Sure about DELETING?");
				if (r == false)
					return;
			
				showLoader();
				var data=[];
				poGridOptions.api.forEachNode(function(rowNode, index) {
					if(index<field-1){
						data.push(rowNode.data);
					}
					else if(index>field-1){
						var node=rowNode.data;
						node.sno=node.sno-1;
						data.push(node);
					}
				});
				poGridOptions.api.setRowData(data);
				poGridOptions.getRowNodeId = d => {
					return d.sno;
				};
				hideLoader();
			},
		},
		resizable: false,
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
		width: 120,
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
	immutableData: false,
	animateRows: true,
	suppressRowClickSelection: true,
	undoRedoCellEditing: true,
	enableFillHandle: true,
	undoRedoCellEditingLimit: 10,
	stopEditingWhenGridLosesFocus: true,
	onColumnResized: onColumnResized,
	onColumnVisible: onColumnVisible,
	components: {
		editButton: EditBtnCellRenderer,
		delButton: DelBtnCellRenderer,
	},
	onCellValueChanged: function (event) {
		newData.push(event.data);
	},
};

function onColumnResized(params) {
	params.api.resetRowHeights();
}
  
function onColumnVisible(params) {
	params.api.resetRowHeights();
}

$("#itemDetailModal").on("show.bs.modal", InitPLItemDetailsForm);
$("#itemDetailModal").on("hide.bs.modal", () => {
	poGridOptions.api.destroy();
});

var poGridOptions = Object.create(commonGridOptions);
var dummyData=[{sno: 1},{sno: 2},{sno: 3},{sno: 4},{sno: 5},{sno: 6},{sno: 7},];

function getStoneMultiplier(loss){
	let multiplier = 1;
	switch($("#metaltype").val().toLowerCase()){
		case "14k": multiplier = 0.59 * (1+(loss*.01)) * PackingListRates.gold; break;
		case "18k": multiplier = 0.76 * (1+(loss*.01)) * PackingListRates.gold; break;
		case "10k": multiplier = 0.42 * (1+(loss*.01)) * PackingListRates.gold; break;
		case "925": multiplier = 1 * PackingListRates.silver; break;
		case "other": multiplier = 1; break;
	}
	return multiplier;
}

const debounce = (func, delay) => {
    let debounceTimer
    return function() {
        const context = this
        const args = arguments
            clearTimeout(debounceTimer)
                debounceTimer
            = setTimeout(() => func.apply(context, args), delay)
    }
}

document.getElementById("discount").addEventListener('keyup', debounce(function() {
	let globalDiscount = Number.parseFloat($("#discount").val());
	if(isNaN(globalDiscount)) globalDiscount = 0;

	poGridOptions.api.forEachNode(function(rowNode, index) {
		if(rowNode.data.itemNo){
			poGridOptions.api.getRowNode(rowNode.data.sno).setDataValue('discount', Number.parseFloat(globalDiscount));
			if(rowNode.data.po_qty)
				poGridOptions.api.getRowNode(rowNode.data.sno).setDataValue('unit_price', Number.parseFloat(rowNode.data.sellPrice * (1 - (globalDiscount*0.01))).toFixed(2));
		}
	});
	calculateTotal();
}, 750));
var currentImageNo=0;
function getStockDetails(){
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/po.php",
		data: {
			func: "getStockById",
			itemNo: currentStoneLotId
		},
	}).done(function (data) {
		if(data.data.itemNo){
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('curStock', data.data.curStock);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('sellPrice', data.data.sellPrice);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('description', data.data.description);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('onOrder', data.data.onOrder);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('po_qty', 1);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('discount', 0);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('unit_price', data.data.sellPrice);
		}
		else{
			toastr['error']("No Record found with Lot Id");
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('curStock', null);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('sellPrice', null);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('description', null);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('onOrder', null);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('po_qty', null);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('discount', null);
			poGridOptions.api.getRowNode(currentStoneSno).setDataValue('unit_price', null);
		}
		hideLoader();
		if(currentStoneSno==1){
			currentImageNo = currentStoneSno;
			$("#galleryId").html(currentStoneLotId);
			$("#galleryDiv").attr("src","../pics/"+currentStoneLotId+".jpg");
		}
		currentDiaSno=0;
		currentDiaLotId=0;
		currentStoneType="";
		poGridOptions.api.resetRowHeights();
	});
}

function loadImage(delta){
	if(currentImageNo+delta >= 1){
		let row = poGridOptions.api.getRowNode(currentImageNo+delta).data;
		if(row.itemNo){
			currentImageNo = row.sno;
			$("#galleryId").html(row.itemNo);
			$("#galleryDiv").attr("src","../pics/"+row.itemNo+".jpg");
		}
	}
}

function calculateTotal(){
	let total = 0;
	poGridOptions.api.forEachNode(function(rowNode, index) {
		if(rowNode.data.itemNo && rowNode.data.po_qty && rowNode.data.curStock)
			total += Number.parseFloat(rowNode.data.po_qty * rowNode.data.unit_price);
	});
	$("#poTotal").val(total).change();
	$("#poTotal").addClass("active");
}

var currentStoneSno=0;
var currentStoneLotId=0;
var currentStoneType="";

function InitPLItemDetailsForm() {
	$("#UpsertPurchaseOrder").trigger("reset");
	$("#UpsertPurchaseOrder label").removeClass("active");

	new agGrid.Grid(document.querySelector("#DiamondDetailsGrid"), poGridOptions);
	poGridOptions.getRowNodeId = d => {
		return d.sno;
	};
	poGridOptions.api.setRowData([{sno: 1},{sno: 2},{sno: 3},{sno: 4},{sno: 5},{sno: 6},{sno: 7},{sno: 8},{sno: 9},{sno: 10},{sno: 11},{sno: 12},]);
	poGridOptions.api.setColumnDefs(poDetailsColDef);
	poGridOptions.onCellValueChanged = function(event){
		if(event.data.po_qty && ( event.column.colId === "po_qty" ||  event.column.colId === "discount")){
			poGridOptions.api.getRowNode(event.data.sno).setDataValue('unit_price', Number.parseFloat(event.data.sellPrice * (1 - (event.data.discount*0.01))).toFixed(2));
			calculateTotal();
		}
		if(event.colDef.field=="itemNo"){
			currentStoneSno = event.data.sno;
			currentStoneLotId = event.data.itemNo;
			getStockDetails();
		}
	}
}

var poItemsGridData=[];

function AllDetailsTabClicked(ignoreEmpty=false){
	GetAllGridData(ignoreEmpty);
	for(var i=0;i<poItemsGridData.length;i++){
		if(poItemsGridData[i].lot_id){
			let settingRate=10;
			if(poItemsGridData[i].setting=="Micro")	settingRate = PackingListRates.microDiamondSetting;
			else if(poItemsGridData[i].setting=="Prong")	settingRate = PackingListRates.prongDiamondSetting;
			else if(poItemsGridData[i].setting=="Baguette")	settingRate = PackingListRates.baguetteDiamondSetting;
		}
	}
}
function AddMoreRows(){
	GetAllGridData();
	poItemsGridData.push({sno: poItemsGridData.length+1},{sno: poItemsGridData.length+2},{sno: poItemsGridData.length+3},{sno: poItemsGridData.length+4},{sno: poItemsGridData.length+5});
	poGridOptions.api.setRowData(poItemsGridData);
}
function GetAllGridData(ignoreEmpty=false){
	poItemsGridData=[];
	poGridOptions.api.forEachNode(function(rowNode, index) {
		if((ignoreEmpty && rowNode.data.lot_id) || !ignoreEmpty)
			poItemsGridData.push(rowNode.data);		
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
		$("#currentDrawbackRt").val(data.data[0].currentDrawback).change();
		$("#gstRt").val(data.data[0].gst).change();
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
			currentDrawback: $("#currentDrawbackRt").val(),
			gst: $("#gstRt").val()
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
