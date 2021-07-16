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
	<title>Packer | SilverCity</title>
	<link rel="stylesheet" href="../metal/mdb.min.css" />
	<link rel="stylesheet" href="../metal/toastr.min.css" />
	<link rel="stylesheet" href="../src/css/daterangepicker.css" />
	<script src="ag-grid-community.min.js"></script>
	<script src="item-class.js"></script>
	<link rel="stylesheet" href="../metal/style.css" />
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
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" disabled id="updatePackingData" onclick="updatePLTable()">Update Data</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" data-mdb-toggle="modal" data-mdb-target="#packingListModal">New List</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" data-mdb-toggle="modal" data-mdb-target="#settingsModal">Settings</button>
			</div>
			<div class="col-4">
				<span class="center"><b>Packing Lists</b></span>
			</div>
			<input type="hidden" id="pid" name="pid" value="0">

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
							<button onclick="deletePackingList()" id="deleteAction" type="button" class="btn btn-outline-danger my-1" data-mdb-ripple-color="dark">Delete</button>
							<button onclick="lockPackingList()" id="lockAction" type="button" class="btn btn-outline-warning my-1" style="display: none;" data-mdb-ripple-color="dark">Lock</button>
							<button onclick="unlockPackingList()" id="unlockAction" type="button" class="btn btn-outline-warning my-1" style="display: none;" data-mdb-ripple-color="dark">Un-Lock</button>
							<button onclick="finalizePackingList()" id="finalizeAction" type="button" class="btn btn-outline-success my-1" style="display: none;" data-mdb-ripple-color="dark">Finalise</button>
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
								<button type="button" onclick="getSettings()" class="btn btn-outline-danger compact-btn" data-mdb-ripple-color="dark" >Reset</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	<!--Modal: Create/Edit Packing List Items-->
	<div class="modal fade" id="packingListItemsModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-fluid" role="document" style="max-width: 90%;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Items to Packing List</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<form id="PL-Items" name="PL-Items" method="POST" enctype="multipart/form-data">
						<fieldset style="border-color: dodgerblue !important;">
							<legend>Add More Items</legend>
							<div class="row">
								<input type="hidden" id="itemid" name="itemid" />
								<div class="col-1 mb-3 pe-0">
									<div class="form-outline"><input required type="text" id="itemcode" name="itemcode" class="form-control" />
										<label class="form-label" for="itemcode">Item Code <span class="text-danger">*</span></label>
									</div>
								</div>
								<div class="col-1 pe-0">
									<div class="form-outline">
										<input required type="text" id="mewarcode" name="mewarcode" class="form-control" />
										<label class="form-label" class="active" for="mewarcode">Mewar # <span class="text-danger">*</span></label>
									</div>
								</div>
								<div class="col-1 pe-0">
									<div class="form-outline"> <input required type="number" id="qty" name="qty" class="form-control" min=1 />
										<label class="form-label" for="qty">Qty <span class="text-danger">*</span></label>
									</div>
								</div>
								<div class="col-1 pe-0">
									<div class="form-outline"> <input type="text" id="ringsize" name="ringsize" class="form-control" />
										<label class="form-label" for="ringsize">Ring Size</label>
									</div>
								</div>
								<div class="col-1 pe-0">
									<div class="form-outline"> <input type="text" id="dimensions" name="dimensions" class="form-control" />
										<label class="form-label" for="dimensions">Dimensions</label>
									</div>
								</div>
								<div class="col-1 pe-0">
									<fieldset style="padding: 0px !important;border: 0px solid !important; margin-top: -1rem !important;">
										<legend style="font-size: small !important;">Metal Type</legend>
										<select id="metaltype" name="metaltype" onchange="selectChanged('metaltypeLabel')" style="width: 100%; padding: 0.3rem;">
											<option value="10K">10K</option>
											<option value="14K">14K</option>
											<option value="18K">18K</option>
											<option value="925">925</option>
											<option value="1">Other</option>
										</select>
									</fieldset>
								</div>
								<div class="col-1 pe-0">
									<div class="form-outline"> <input type="text" id="metalcolor" name="metalcolor" class="form-control" />
										<label class="form-label" for="metalcolor">Metal Color</label>
									</div>
								</div>
								<div class="col-3 pe-0">
									<div class="form-outline"> <input required type="text" id="description" name="description" class="form-control" />
										<label class="form-label" for="description">Description <span class="text-danger">*</span></label>
									</div>
								</div>
								<div class="col-1 pe-0">
									<fieldset style="padding: 0px !important;border: 0px solid !important; margin-top: -1rem !important;">
										<legend style="font-size: small !important; ">Item Pic</legend>
										<div class="custom-file">
											<input type="file" class="custom-file-input" name="itemPic" id="itemPic" aria-describedby="inputGroupFileAddon01">
										</div>
									</fieldset>
								</div>
							</div>
							<div class="card-body-cascade text-center">
								<ul class="nav nav-pills" id="pills-tab" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="metalDetails-tab" data-mdb-toggle="pill" href="#metalDetails" role="tab" aria-controls="metalDetails" aria-selected="true">Metal Details</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="diamondDetails-tab" data-mdb-toggle="pill" href="#diamondDetails" role="tab" aria-controls="diamondDetails" aria-selected="false">Diamond Details</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="stoneDetails-tab" data-mdb-toggle="pill" href="#stoneDetails" role="tab" aria-controls="stoneDetails" aria-selected="false">Stone Details</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="otherDetails-tab" data-mdb-toggle="pill" href="#otherDetails" role="tab" aria-controls="otherDetails" aria-selected="false">Other Costs Details</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="allDetails-tab" data-mdb-toggle="pill" href="#allDetails" role="tab" aria-controls="allDetails" aria-selected="false" onclick="AllDetailsTabClicked(false)">All Totals</a>
									</li>
								</ul>
								<div class="tab-content" id="pills-tabContent">
									<div class="tab-pane fade show active" id="metalDetails" role="tabpanel" aria-labelledby="metalDetails-tab">
										<div id="MetalDetailsGrid" class="ag-theme-balham mx-auto" style="height: 200px;width: 100%;text-align:left!important;"></div>
									</div>
									<div class="tab-pane fade" id="diamondDetails" role="tabpanel" aria-labelledby="diamondDetails-tab">
										<div id="DiamondDetailsGrid" class="ag-theme-balham mx-auto" style="height: 200px;width: 100%;text-align:left!important;"></div>
									</div>
									<div class="tab-pane fade" id="stoneDetails" role="tabpanel" aria-labelledby="stoneDetails-tab">
										<div id="StoneDetailsGrid" class="ag-theme-balham mx-auto" style="height: 200px;width: 100%;text-align:left!important;"></div>
									</div>
									<div class="tab-pane fade" id="otherDetails" role="tabpanel" aria-labelledby="otherDetails-tab">
										<div id="OtherCostsDetailsGrid" class="ag-theme-balham mx-auto" style="height: 200px;width: 100%;text-align:left!important;"></div>
									</div>
									<div class="tab-pane fade" id="allDetails" role="tabpanel" aria-labelledby="allDetails-tab">
										<div id="AllDetailsGrid" class="ag-theme-balham mx-auto" style="height: 200px;width: 100%;text-align:left!important;"></div>
									</div>
								</div>
								<div class="mt-2">
									<button type="button" onclick="AddMoreRows()" class="btn btn-sm btn-outline-info" data-mdb-ripple-color="dark" style="float: left;">Add More Rows</button>
									<button type="button" name="PL_Items_Reset" id="PL_Items_Reset" onclick="resetPLItem()" class="btn btn-sm btn-outline-danger" data-mdb-ripple-color="dark" style="float: right;">Reset Item</button>
									<button type="submit" name="PL_Items_Create" id="PL_Items_Create" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" style="float: right;margin-right: 1rem;">Save Item</button>
								</div>

							</div>
						</fieldset>
					</form>

				</div>

				<div id="PL_Items_Grid" class="ag-theme-balham mx-auto" style="margin-top: 15px; margin-bottom: 10px; height: 260px;width: 98%;text-align:left!important;"></div>
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