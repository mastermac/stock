<?php
session_start();
if (!isset($_SESSION['userid'])) {
	header('Location: login.php');
}
$usertype = '';
require('../src/scripts/db_config.php');
if ($_SESSION['usertype'] >= 1)
	$usertype = ' and userid=' . $_SESSION['userid'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>PO | SilverCity</title>
	<link rel="stylesheet" href="../metal/mdb.min.css" />
	<link rel="stylesheet" href="../metal/toastr.min.css" />
	<link rel="stylesheet" href="../src/css/daterangepicker.css" />
	<script src="ag-grid-community.min.js"></script>
	<script src="item-class.js"></script>
	<link rel="stylesheet" href="../metal/style.css" />
	<style>
		.specialControl{
			float: left;
	    	margin-left: 1rem;
    		width: 10%;
    		line-height: 20px;
    		font-size: 12px;
		}
		.specialText{
			padding: .14em .75em !important;
		}
	</style>
	<script type="text/javascript">
		//var url = "http://silvercityonline.com/silvercity/";
		var main = "<?php echo $_SERVER['DOCUMENT_ROOT'] ?>";
		var vendorProfit = Number("<?php echo $_SESSION['vendorProfit'] ?>");
		var url = "";
		if (main == "C:/wamp64/www" || main == "C:/wamp/www")
			url = "http://localhost:8080/stock/pack/";
		else
			url = "";
		var usertype = <?php echo $_SESSION['usertype'] ?>;
		var userid = <?php echo $_SESSION['userid'] ?>;
	</script>
</head>

<body>
	<div class="ajax-loader">
		<img src="../src/images/ajax.gif" class="img-responsive" />
	</div>
	<?php include_once('../header.php'); ?>

	<div class="container-fluid text-center mt-4">
		<div class="mb-2 row">
			<div class="col-4 start">
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" data-mdb-toggle="modal" data-mdb-target="#packingListItemsModal">New PO</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" disabled id="updatePackingData" onclick="updatePLTable()" style="display: none;">Update Data</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" data-mdb-toggle="modal" data-mdb-target="#packingListModal" style="display: none;">New List</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" data-mdb-toggle="modal" data-mdb-target="#settingsModal" style="display: none;">Settings</button>
			</div>
			<div class="col-4">
				<span class="center"><b>Purchase Orders</b></span>
			</div>
			<input type="hidden" id="pid" name="pid" value="0">

			<!-- <div class="col-4">
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="moveToInvoice()">Move to Packing List</button>
			</div> -->
		</div>

		<div id="packingLists" class="ag-theme-balham mx-auto" style="height: 500px;width: 95%;text-align:left!important;"></div>

		<div class="row mt-2" style="font-size: 14px;">
			<div class="col float-start">
				Dia: <b id="totalDiamonds"></b>&emsp;&emsp;&emsp;
				Stone: <b id="totalStones"></b>&emsp;&emsp;&emsp;
				Metal: <b id="totalMetal"></b>&emsp;&emsp;&emsp;
				Net Worth: <b id="totalNetWorth"></b>
			</div>
		</div>

		<!--Table-->
	</div>

	<!--Modal: Packing List Actions-->
	<div class="modal fade" id="packingListActionModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered cascading-modal modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header" style="padding-bottom: 0px; border-bottom: 0px;">
					<h5 class="modal-title">Select an Action</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="newPackingList" name="newPackingList" enctype="multipart/form-data" method="GET">
						<div class="row">
							<button onclick="generateSalesOrder()" id="finalizeAction" type="button" class="btn btn-outline-success my-1" data-mdb-ripple-color="dark">Generate SalesOrder</button>
							<button onclick="generatePurchaseOrder()" id="finalizeAction" type="button" class="btn btn-outline-success my-1" data-mdb-ripple-color="dark">Generate PO</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!--Modal: Create/Edit Packing List-->
	<div class="modal fade" id="packingListModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Create New Packing List</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="newPackingList" name="newPackingList" enctype="multipart/form-data" method="GET">
						<div class="row">
							<!-- <div class="col-2 mb-0">
										<input autocomplete=false required type="text" id="plId" name="plId" class="form-control" />
										<label for="plId">ID <span class="text-danger">*</span></label>
									</div> -->
							<div class="col-6 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="plName" name="plName" class="form-control" />
									<label class="form-label" for="plName">Name/Description <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-4 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="plDate" name="plDate" class="form-control" />
									<label class="form-label" class="active" for="plDate">Date <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">
								<button type="button" onclick="createPackingList()" name="plCreate" id="plCreate" class="btn btn-outline-success compact-btn" data-mdb-ripple-color="dark">Create</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!--Modal: Settings-->
	<div class="modal fade" id="settingsModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Update Settings</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<form id="newPackingList" name="newPackingList" enctype="multipart/form-data">
						<div class="row">
							<div class="col mb-3 pe-0">
								<div class="form-outline"> <input required type="number" step="0.01" id="exchangeRt" name="exchangeRt" class="form-control" />
									<label class="form-label" for="exchangeRt">Exchange Rt <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="silverRt" name="silverRt" class="form-control" />
									<label class="form-label" for="silverRt">Silver/grm Rt</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="goldRt" name="goldRt" class="form-control" />
									<label class="form-label" for="goldRt">Gold/grm Rt</label>
								</div>
							</div>
							<div class="col">
								<div class="form-outline"> <input type="number" step="0.01" id="labourRt" name="labourRt" class="form-control" />
									<label class="form-label" for="labourRt">Silver Labour/grm Rt</label>
								</div>
							</div>
							<div class="w-100"></div>
							<div class="col mb-3 pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="goldLabourRt" name="goldLabourRt" class="form-control" />
									<label class="form-label" for="goldLabourRt">Gold Labour/grm Rt</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="platingRt" name="platingRt" class="form-control" />
									<label class="form-label" for="platingRt">Plating/grm Rt</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="findingsRt" name="findingsRt" class="form-control" />
									<label class="form-label" for="findingsRt">Findings Rt</label>
								</div>
							</div>
							<div class="col">
								<div class="form-outline"> <input type="number" step="0.01" id="microDiaRt" name="microDiaRt" class="form-control" />
									<label class="form-label" for="microDiaRt">Micro Dia Setting Rt</label>
								</div>
							</div>
							<div class="w-100"></div>
							<div class="col mb-3 pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="prongDiaRt" name="prongDiaRt" class="form-control" />
									<label class="form-label" for="prongDiaRt">Prong Diamond Setting Rt</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="baguetteDiaRt" name="baguetteDiaRt" class="form-control" />
									<label class="form-label" for="baguetteDiaRt">Baguette Diamond Setting Rt</label>
								</div>
							</div>
							<div class="col">
								<div class="form-outline"> <input type="number" step="0.01" id="roundStoneRt" name="roundStoneRt" class="form-control" />
									<label class="form-label" for="roundStoneRt">Round Stone Setting Rt</label>
								</div>
							</div>
							<div class="w-100"></div>
							<div class="col mb-3 pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="currentDrawbackRt" name="currentDrawbackRt" class="form-control" />
									<label class="form-label" for="currentDrawbackRt">Current Draw back</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline"> <input type="number" step="0.01" id="gstRt" name="gstRt" class="form-control" />
									<label class="form-label" for="gstRt">GST % on Gold</label>
								</div>
							</div>
							<div class="col">
								<button type="button" onclick="updateSettings()" class="btn btn-outline-success compact-btn" data-mdb-ripple-color="dark">Update</button>
								<button type="button" onclick="getSettings()" class="btn btn-outline-danger compact-btn" data-mdb-ripple-color="dark">Reset</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	<!--Modal: Create/Edit Packing List Items-->
	<div class="modal fade" id="packingListItemsModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-fluid" role="document" style="max-width: 95%;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add/Modify Sales Order Details</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<form id="UpsertPurchaseOrder" name="UpsertPurchaseOrder" method="GET">
						<div class="row">
							<input type="hidden" id="id" name="id" />
							<div class="col-1 pe-0">
								<div class="form-outline">
									<input type="text" id="po_id" name="po_id" class="form-control" disabled />
									<label class="form-label" class="active" for="po_id">S.O. Code<span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-1 mb-3 pe-0">
								<div class="form-outline"><input type="text" id="cust_code" name="cust_code" class="form-control" />
									<label class="form-label" for="cust_code">Cust Code</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="entry_date" name="entry_date" class="form-control datepick" />
									<label class="form-label" class="active" for="entry_date">Entry Date</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="order_date" name="order_date" class="form-control datepick" />
									<label class="form-label" class="active" for="order_date">Order Date</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="ship_date" name="ship_date" class="form-control datepick" />
									<label class="form-label" class="active" for="ship_date">Ship Date</label>
								</div>
							</div>
							<div class="col pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="cancel_date" name="cancel_date" class="form-control datepick" />
									<label class="form-label" class="active" for="cancel_date">Cancel Date</label>
								</div>
							</div>
							<div class="col pe-0">
								<fieldset style="padding: 0px !important;border: 0px solid !important; margin-top: -1rem !important;">
									<legend style="font-size: small !important;">Type</legend>
									<select id="type" name="type" style="width: 100%; padding: 0.3rem;">
										<option value="Retail Ship">Retail Ship</option>
										<option value="Retail Show">Retail Show</option>
										<option value="Retail Store">Retail Store</option>
										<option value="Ship">Ship</option>
										<option value="Show">Show</option>
										<option value="Store">Store</option>

									</select>
								</fieldset>
							</div>
							<div class="col-3">
								<div class="form-outline"> <input type="text" id="note" name="note" class="form-control" />
									<label class="form-label" for="note">Note</label>
								</div>
							</div>
						</div>
						<div class="card-body-cascade text-center">
							<div class="row">
								<div class="col-11 pe-0">
									<ul class="nav nav-pills" id="pills-tab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="diamondDetails-tab" data-mdb-toggle="pill" href="#diamondDetails" role="tab" aria-controls="diamondDetails" aria-selected="true">Item Details</a>
										</li>
									</ul>
									<div class="tab-content" id="pills-tabContent">
										<div class="tab-pane fade show active" id="diamondDetails" role="tabpanel" aria-labelledby="diamondDetails-tab">
											<div id="DiamondDetailsGrid" class="ag-theme-balham mx-auto" style="height: 350px;width: 100%;text-align:left!important;"></div>
										</div>
									</div>
								</div>
								<div class="col-1">

									<div class="pe-0" style="margin-top: 48px;">
										<img src="../pics/noImage.jpeg" id="galleryDiv" onerror="this.src='../pics/noImage.jpeg';" height="100px" style="border: 1px dashed;">
										<div id="galleryId" style="font-size: 14px;"></div>
									</div>
									<div class="mb-4 ps-2 pe-2" style="height: 40px;">
										<img src="pics/arrow.png" height="24px" style="float: left;" onclick="loadImage(-1)">
										<img src="pics/arrow.png" height="24px" style="float: right; transform: rotate(180deg);" onclick="loadImage(1)">
									</div>

									<div class="mt-4 pt-4 mb-3 pe-0">
										<div class="form-outline"><input type="number" id="discount" name="discount" class="form-control" min=0/>
											<label class="form-label" for="discount">Discount %</label>
										</div>
									</div>
									<div class="mb-3 pe-0">
										<div class="form-outline"><input type="number" id="poTotal" name="poTotal" class="form-control" disabled/>
											<label class="form-label" for="poTotal">Total</label>
										</div>
									</div>

								</div>
							</div>
							<div class="mt-3">
								<div class="form-outline specialControl" style="margin-left: 0px;">
									<input type="text" id="entered_by" name="entered_by" class="form-control specialText" />
									<label class="form-label" for="entered_by">Entered By</label>
								</div>
								<div class="form-outline specialControl">
									<input type="text" id="ship_via" name="ship_via" class="form-control specialText" />
									<label class="form-label" for="ship_via">Ship Via</label>
								</div>
								<div class="form-outline specialControl">
									<input type="text" id="customer_ref" name="customer_ref" class="form-control specialText" />
									<label class="form-label" for="customer_ref">Customer Ref</label>
								</div>
								<button type="button" name="PL_Items_Reset" id="PL_Items_Reset" onclick="resetPLItem()" class="btn btn-sm btn-outline-danger" data-mdb-ripple-color="dark" style="float: right;">Reset</button>
								<button type="submit" name="SavePurchaseOrder" id="SavePurchaseOrder" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" style="float: right;margin-right: 1rem;">Save</button>
								<button type="button" onclick="AddMoreRows()" class="btn btn-sm btn-outline-info" data-mdb-ripple-color="dark" style="float: right;margin-right: 1rem;">Add More Rows</button>
							</div>
						</div>
					</form>
					<form method="PUT" enctype="multipart/form-data" id="changePicForm" name="changePicForm">
						<input type="file" id="changeItemPic" name="changeItemPic" style="display: none;" accept="image/*"/>
					</form>
				</div>

				<!-- <div id="PL_Items_Grid" class="ag-theme-balham mx-auto" style="margin-top: 15px; margin-bottom: 10px; height: 260px;width: 98%;text-align:left!important;"></div> -->
			</div>
		</div>
	</div>

	<!--Modal: Add Item Details-->
	<div class="modal fade" id="itemDetailModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-fluid" role="document" style="max-width: 90%;">
			<div class="modal-content">
				<div class="card card-cascade narrower mt-0">
					<div class="view view-cascade gradient-card-header blue-gradient">
						<h2 class="card-header-title mb-0">Modify Item Details</h2>
						<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close" style="margin-top: -3.5rem;">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

	<script src="../metal/jquery-3.6.0.min.js"></script>
	<script src="../metal/mdb.min.js"></script>
	<script src="../metal/common.js"></script>
	<script src="../metal/toastr.min.js"></script>
	<script src="../src/js/moment.js"></script>
	<script src="../src/js/daterangepicker.js"></script>
	<script src="img-cell-renderer.js"></script>
	<script src="edit-btn-cell-renderer.js"></script>
	<script src="del-btn-cell-renderer.js"></script>
	<script src="main.js"></script>
	<script>
		$('input[name="dates"]').daterangepicker();
	</script>
</body>

</html>