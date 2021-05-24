var SelectedStone;
toastr.options = {
	"positionClass": "toast-bottom-right",
};

var manufacturingInventory = {
	columnDefs: [
		{
			headerName: "S.No",
			field: "sno",
			headerCheckboxSelection: true,
			headerCheckboxSelectionFilteredOnly: true,
			checkboxSelection: true,
			editable: false,
			filter: false,
			pinned: 'left',
			width: 90
		},
		{
			headerName: "Type", field: "type", width: 200,
			pinned: 'left',
			cellEditor: 'agSelectCellEditor',
			cellEditorParams: {
				values: ['14k gold diamond earring', '14k gold diamond ring', '14k gold diamond bracelet', '14k gold diamond pendant', '14k gold diamond necklace', '14k gold diamond charms', '14k gold diamond bangle', '18k gold diamond earring', '18k gold diamond ring', '18k gold diamond bracelet', '18k gold diamond pendant', '18k gold diamond necklace', '18k gold diamond bangle', '18k gold diamond charms', '925 silver diamond earring', '925 silver diamond bracelet', '925 silver diamond bangle', '925 silver diamond ring', '925 silver diamond pendant', '925 silver diamond necklace', '925 silver diamond charms'],
			},
		},
		{ headerName: "Comments", field: "comments", width: 200, pinned: 'left' },
		{
			headerName: "M Code",
			field: "mewarCode",
			pinned: 'left',
			filter: "agNumberColumnFilter",
			hide: true
		},
		{ headerName: "Code", field: "vendorCode", width: 110, pinned: 'left' },
		{ headerName: "D/S?", field: "d_or_s", width: 90 },
		{ headerName: "Lot#", field: "lotNo", width: 90 },
		{ headerName: "Stone", field: "stoneName", editable:false, width: 170 },
		{ headerName: "Qty", field: "qty", filter: "agNumberColumnFilter", width: 85 },
		{ headerName: "Dia/St Wt", field: "wt_in_grms", filter: "agNumberColumnFilter", width: 120 },
		{ headerName: "Wt (ct)", field: "wt_in_cts", filter: "agNumberColumnFilter", editable: false },
		{ headerName: "Oth Metal Wt", field: "other_metal_grm", filter: "agNumberColumnFilter", width: 150 },
		{ headerName: "Gold Wt", field: "gold_in_grms", filter: "agNumberColumnFilter", editable: false },
		{ headerName: "Gold (ct)", field: "gold_in_cts", filter: "agNumberColumnFilter", editable: false, hide: true, width: 130 },
		{ headerName: "Gross Wt", field: "grossWt", filter: "agNumberColumnFilter", width: 120 },
		{ headerName: "Dia/St Pcs", field: "dia_stone_pcs", filter: "agNumberColumnFilter", width: 120 },
		{ headerName: "Img", field: "img", hide: true },
		{ headerName: "Size", field: "size", width: 90 },
		{ headerName: "PO #", field: "po", width: 90 },
		{ headerName: "Last Updated", editable: false, field: "timestamp", width: 150, filter: "agDateColumnFilter" },
		{
			headerName: "PL #",
			field: "invoice_id",
			editable: false,
			resizable: false,
			pinned: 'right',
			width: 90
		},
		{
			headerName: "Delete",
			field: "id",
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
					$.ajax({
						dataType: "json",
						url: url + "../src/scripts/manufacturing.php",
						data: {
							func: "delete",
							id: field,
						},
					}).done(function (data) {
						hideLoader();
						getManufacturingList();
					});
				},
			},
			resizable: false,
			width: 70,
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
		manufacturingInventory.api.flashCells({ rowNodes: [event.rowIndex] });
	},
};


function exportData() {
	let params = {
		columnKeys: ['sno', 'lot_no', 'name', 'size', 'shape', 'seller', 'purchased_qty', 'purchased_wt', 'unit', 'current_qty', 'current_wt', 'box', 'cost', 'less', 'rate', 'total_amount', 'current_value', 'description','po']
	};

	let selectedRows = manufacturingInventory.api.getSelectedNodes();

	if (selectedRows.length > 0) {
		params = {
			onlySelected: true,
			columnKeys: ['sno', 'lot_no', 'name', 'size', 'shape', 'seller', 'purchased_qty', 'purchased_wt', 'unit', 'current_qty', 'current_wt', 'box', 'cost', 'less', 'rate', 'total_amount', 'current_value', 'description','po']
		}
	}
	manufacturingInventory.api.exportDataAsCsv(params);
}

function moveToInvoice() {
	let selectedRows = manufacturingInventory.api.getSelectedNodes();
	if (selectedRows.length <= 0){
		toastr['warning']("Please select some items!");
		return;
	} 
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
	manufacturingInventory.api.forEachNode(function (rowNode, index) {
		data.push(rowNode.data);
	});
	data.push({ sno: data.length + 1 }, { sno: data.length + 2 }, { sno: data.length + 3 }, { sno: data.length + 4 }, { sno: data.length + 5 }, { sno: data.length + 6 }, { sno: data.length + 7 }, { sno: data.length + 8 }, { sno: data.length + 9 }, { sno: data.length + 10 });
	manufacturingInventory.api.setRowData(data);

}
var monthsShort = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
function getPackingLists() {
	$("#plNameParent").css("display", "none");
	$("#plDateParent").css("display", "none");
	window.start = moment().format("YYYY-MM-DD");
	$("#plDate").daterangepicker(
		{
			singleDatePicker: true,
			opens: "center"
		},
		function (start, end, label) {
			let dateElements = (start._d + "").split(" ");
			window.start = dateElements[3]+"-"+(monthsShort.indexOf(dateElements[1])+1)+"-"+dateElements[2];
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
		// $('#invoiceNames').append(`<option value="other">Other</option>`);
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
	var dt = manufacturingInventory.api.getSelectedNodes();
	let manuInventory = [];
	dt.forEach(row => {
		let firstOccurance = getFirstOccuranceOfItem(row.data.vendorCode);
		if (firstOccurance.sno == row.data.sno) {
			manuInventory.push(row.data);
			manuInventory[manuInventory.length - 1].stones = [];
			manuInventory[manuInventory.length - 1].diamonds = [];
			let customData = {"lotNo": row.data.lotNo, "stoneName": row.data.stoneName, "dia_stone_pcs": row.data.dia_stone_pcs, "wt_in_cts": row.data.wt_in_cts};
			if (row.data.d_or_s.toLowerCase() == "s")
				manuInventory[manuInventory.length - 1].stones.push(customData);
			else if (row.data.d_or_s.toLowerCase() == "d")
				manuInventory[manuInventory.length - 1].diamonds.push(customData);			
		}
		else {
			for (let i = 0; i < manuInventory.length; i++) {
				if (manuInventory[i].vendorCode == row.data.vendorCode) {
					let customData = {"lotNo": row.data.lotNo, "stoneName": row.data.stoneName, "dia_stone_pcs": row.data.dia_stone_pcs, "wt_in_cts": row.data.wt_in_cts};
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
	let packingName = $("#invoiceNames").val();
	if(!packingName)
		packingName = $("#plName").val();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/manufacturing.php",
		data: {
			func: "moveToInvoice",
			packing: packingName,
			data: manuInventory
		},
	}).done(function (data) {
		hideLoader();
		getManufacturingList();
		toastr['success'](data.result);
		$("#invoiceModal").modal("hide");
	});
}

function uploadData() {
	let manuInventory = [];
	manufacturingInventory.api.forEachNode(function (rowNode, index) {
		if (changedRows.indexOf(rowNode.data.sno) >= 0) {
			var firstOccurance = getFirstOccuranceOfItem(rowNode.data.vendorCode);
			if (firstOccurance.sno != rowNode.data.sno) {
				rowNode.data.type = firstOccurance.type;
				rowNode.data.comments = firstOccurance.comments;
				rowNode.data.grossWt = 0;
			}
			if (rowNode.data.id)
				manuInventory.push(rowNode.data);
			else if (!rowNode.data.id && rowNode.data.vendorCode) {
				if (!rowNode.data.type)
					rowNode.data.type = "";
				manuInventory.push(rowNode.data);

			}
		}
	});
	manuInventory = cleanUploadList(manuInventory);
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/manufacturing.php",
		data: {
			func: "upsertData",
			data: manuInventory
		},
	}).done(function (data) {
		changedRows = [];
		hideLoader();
		$("#stReset").click();
		SelectedStone = null;
		getManufacturingList();
		$("#stoneFormModal").modal("hide");
	});
}
var numKeys = ["dia_stone_pcs", "gold_in_grms", "gold_in_cts", "grossWt", "other_metal_grm", "qty", "wt_in_cts", "wt_in_grms"];
function cleanUploadList(inventory){
	let list = [];
	inventory.forEach(function(row, index){
		for (const [key, value] of Object.entries(row)) {
			if(!value){
				if(numKeys.indexOf(key)>=0)
					row[key]=0;
				else
					row[key]="";
			}
		}
		list.push(row);
	});
	return list;
}

var currentPL, currentItem;
var myCellRenderer = function () {
	return '<span style="color: black">Edit</span>';
};
function onQuickFilterChanged() {
	manufacturingInventory.api.setQuickFilter(document.getElementById("quickFilter").value);
}
var newData = new Array();

var changedRows = [];

document.addEventListener("DOMContentLoaded", function () {
	var gridDiv = document.querySelector("#manufacturingInventory");
	$("#manufacturingInventory").css("height", window.innerHeight - 170 + "px");
	new agGrid.Grid(gridDiv, manufacturingInventory);
	getManufacturingList();
	manufacturingInventory.getRowNodeId = d => {
		return d.sno; // return the property you want set as the id.
	};

	manufacturingInventory.onCellValueChanged = function (event) {
		if (!(String(event.oldValue) === event.newValue)) changedRows.push(event.data.sno);
		var firstOccurance = getFirstOccuranceOfItem(event.data.vendorCode);
		pushDependentRows(event.data.sno, event.data.vendorCode);

		if (event.data.wt_in_grms && event.column.colId === "wt_in_grms") {
			console.log(manufacturingInventory.api.getRowNode(event.data.sno));
			manufacturingInventory.api.getRowNode(event.data.sno).setDataValue('wt_in_cts', Math.round(Number(cleanNum(event.data.wt_in_grms)) * 5 * 1000) / 1000);
			
			var goldWt = Math.round(Number(cleanNum(firstOccurance.grossWt)) - Number(cleanNum(getAllWeightInfoForItem(firstOccurance.vendorCode))));
			if(goldWt==-1)	goldWt=0;
			manufacturingInventory.api.getRowNode(firstOccurance.sno).setDataValue('gold_in_grms', goldWt);
		}
		if (event.data.other_metal_grm && event.column.colId === "other_metal_grm") {
			var goldWt = Math.round(Number(cleanNum(firstOccurance.grossWt)) - Number(cleanNum(getAllWeightInfoForItem(firstOccurance.vendorCode))));
			if(goldWt==-1)	goldWt=0;
			manufacturingInventory.api.getRowNode(firstOccurance.sno).setDataValue('gold_in_grms', goldWt);
		}
		if (event.data.lotNo && event.column.colId === "lotNo") {
			setStoneDetails(event.data.lotNo, event.data.sno);
		}
		if (event.data.grossWt && (event.column.colId === "grossWt" || event.column.colId === "wt_in_grms" || event.column.colId === "other_metal_grm")) {
			if (event.data.sno == firstOccurance.sno) {
				var goldWt = Math.round(Number(cleanNum(event.data.grossWt)) - Number(cleanNum(event.data.wt_in_grms)) - Number(cleanNum(event.data.other_metal_grm)) - getWeightInfoForItem(event.data.vendorCode, event.data.sno))
				if(goldWt==-1)	goldWt=0;
				manufacturingInventory.api.getRowNode(event.data.sno).setDataValue('gold_in_grms', goldWt);
			}
		}
	}
});

function pushDependentRows(sno, vendorCode){
	manufacturingInventory.api.forEachNode(function (rowNode, index) {
		if (rowNode.data.vendorCode == vendorCode && rowNode.data.sno!=sno)
			changedRows.push(rowNode.data.sno);
	});
}

function cleanNum(value){
	if(value)
		return value;
	else
		return 0;
}

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
			manufacturingInventory.api.getRowNode(sno).setDataValue('stoneName', data.data.name);
		else
			toastr['error']("No Record found with Lot Id");
	});
}


function getFirstOccuranceOfItem(vendorCode) {
	var data = 0;
	manufacturingInventory.api.forEachNode(function (rowNode, index) {
		if (rowNode.data.vendorCode == vendorCode && data == 0)
			data = rowNode.data;
	});
	return data;
}

function getWeightInfoForItem(vendorCode, sno) {
	var wt = 0;
	manufacturingInventory.api.forEachNode(function (rowNode, index) {
		if (rowNode.data.sno != sno && rowNode.data.vendorCode == vendorCode)
			wt += Number(cleanNum(rowNode.data.wt_in_grms)) + Number(cleanNum(rowNode.data.other_metal_grm));
	});
	return wt;
}
function getAllWeightInfoForItem(vendorCode) {
	var wt = 0;
	manufacturingInventory.api.forEachNode(function (rowNode, index) {
		if (rowNode.data.vendorCode == vendorCode)
			wt += Number(cleanNum(rowNode.data.wt_in_grms)) + Number(cleanNum(rowNode.data.other_metal_grm));
	});
	return wt;
}


function onColumnResized(params) {
	params.api.resetRowHeights();
}

function onColumnVisible(params) {
	params.api.resetRowHeights();
}

function getJSONFormData($form) {
	var unindexed_array = $form.serializeArray();
	var indexed_array = {};

	$.map(unindexed_array, function (n, i) {
		indexed_array[n['name']] = n['value'];
	});

	return indexed_array;
}

function getManufacturingList() {
	showLoader();
	$.ajax({
		dataType: "json",
		url: url + "../src/scripts/manufacturing.php",
		data: {
			func: "getManufacturingList",
		},
	}).done(function (data) {
		hideLoader();
		BindListData(data.data);
	});
}
function BindListData(data) {
	data.push({ sno: data.length + 1 }, { sno: data.length + 2 }, { sno: data.length + 3 }, { sno: data.length + 4 }, { sno: data.length + 5 }, { sno: data.length + 6 }, { sno: data.length + 7 }, { sno: data.length + 8 }, { sno: data.length + 9 }, { sno: data.length + 10 });
	manufacturingInventory.api.setRowData(data);
}

function showLoader() {
	$(".ajax-loader").css("visibility", "visible");
}
function hideLoader() {
	$(".ajax-loader").css("visibility", "hidden");
}

function resetFilters(){
	manufacturingInventory.api.setFilterModel(null);
	manufacturingInventory.api.onFilterChanged();
}