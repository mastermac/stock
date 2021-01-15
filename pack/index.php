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
	<link rel="stylesheet" href="mdb.css" />
	<link rel="stylesheet" href="../src/css/daterangepicker.css" />
	<script src="ag-grid-community.min.js"></script>
	<script src="item-class.js"></script>
	<style>
		.md-form label {
			padding-left: 1rem;
		}

		legend {
			text-align: initial !important;
			display: block;
			padding-left: 2px;
			padding-right: 2px;
			border: none;
			width: auto !important;
			font-size: initial !important;
		}

		fieldset {
			min-width: initial !important;
			padding: 25px !important;
			margin: initial !important;
			border: 1px solid !important;
		}

		fieldset .md-form {
			margin-top: 0px !important;
			padding-bottom: 0px !important;
		}

		.pt-3-half {
			padding-top: 1.4rem;
		}

		.compact-btn {
			padding: 0.4rem 0.85rem !important;
		}

		.xcompact-btn {
			padding: 0.2rem 0.6rem !important;
		}

		.md-form .form-control {
			padding-top: 0.3rem !important;
			/* padding-bottom: 0.3rem !important; */
		}

		.ajax-loader {
			visibility: hidden;
			background-color: rgba(255, 255, 255, 0.7);
			position: absolute;
			z-index: +2500 !important;
			width: 100%;
			height: 100%;
		}

		.ajax-loader img {
			position: relative;
			top: 30%;
			left: 35%;
		}

		.modal-dialog.cascading-modal {
			margin-top: 50px !important;
		}

		.tab-content {
			padding: 0px !important;
		}

		#PL-Items .md-form {
			margin-bottom: 1rem !important;
		}
		.select-wrapper + label{
			top: 0.65rem !important;
			font-size: 1rem !important;
		}
		.select-wrapper + label{
			top: 0.65rem !important;
			font-size: 1rem !important;
		}
		.select-wrapper + label.active{
			font-size: 0.8rem !important;
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

	<div class="container-fluid text-center">
		<!-- Header -->
		<div class="btn-group mb-4 mt-4" role="group" aria-label="Basic example">
			<button type="button" class="btn btn-outline-deep-purple btn-rounded waves-effect" disabled id="updatePackingData" onclick="updatePLTable()">Update Data</button>
			<button type="button" class="btn btn-outline-deep-purple btn-rounded waves-effect" data-toggle="modal" data-target="#packingListModal">New List</button>
			<button type="button" class="btn btn-outline-deep-purple btn-rounded waves-effect" data-toggle="modal" data-target="#settingsModal">Settings</button>
		</div>

		<!--Table-->
		<div id="packingLists" class="ag-theme-alpine mx-auto" style="height: 500px;width: 95%;text-align:left!important;"></div>
		<!--Table-->
	</div>

	<!--Modal: Create/Edit Packing List-->
	<div class="modal fade" id="packingListModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-lg" role="document">
			<div class="modal-content">
				<div class="card card-cascade narrower mt-0">
					<div class="view view-cascade gradient-card-header blue-gradient">
						<h2 class="card-header-title mb-0">Create New Packing List</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -3.5rem;">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="card-body card-body-cascade text-center">
						<form id="newPackingList" name="newPackingList" enctype="multipart/form-data">
							<div class="row">
								<!-- <div class="md-form col-2 mb-0">
										<input autocomplete=false required type="text" id="plId" name="plId" class="form-control" />
										<label for="plId">ID <span class="text-danger">*</span></label>
									</div> -->
								<div class="md-form col-6 mb-0">
									<input autocomplete=false required type="text" id="plName" name="plName" class="form-control" />
									<label for="plName">Name/Description <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-4 mb-0">
									<input autocomplete=false required type="text" id="plDate" name="plDate" class="form-control" />
									<label class="active" for="plDate">Date <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 pr-0 pl-0 mb-0">
									<!-- <button type="reset" name="plReset" id="plReset" class="btn btn-outline-danger waves-effect compact-btn">Reset</button> -->
									<button type="button" onclick="createPackingList()" name="plCreate" id="plCreate" class="btn btn-outline-success waves-effect compact-btn">Create</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--Modal: Settings-->
	<div class="modal fade" id="settingsModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-lg" role="document">
			<div class="modal-content">
				<div class="card card-cascade narrower mt-0">
					<div class="view view-cascade gradient-card-header blue-gradient">
						<h2 class="card-header-title mb-0">Update Settings</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -3.5rem;">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="card-body card-body-cascade text-center">
						<form id="newPackingList" name="newPackingList" enctype="multipart/form-data">
							<div class="row">
								<div class="md-form col mb-0">
									<input required type="number" step="0.01" id="exchangeRt" name="exchangeRt" class="form-control" />
									<label for="exchangeRt">Exchange Rt <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="silverRt" name="silverRt" class="form-control" />
									<label for="silverRt">Silver/grm Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="goldRt" name="goldRt" class="form-control" />
									<label for="goldRt">Gold/grm Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="labourRt" name="labourRt" class="form-control" />
									<label for="labourRt">Silver Labour/grm Rt</label>
								</div>
								<div class="w-100"></div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="goldLabourRt" name="goldLabourRt" class="form-control" />
									<label for="goldLabourRt">Gold Labour/grm Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="platingRt" name="platingRt" class="form-control" />
									<label for="platingRt">Plating/grm Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="findingsRt" name="findingsRt" class="form-control" />
									<label for="findingsRt">Findings Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="microDiaRt" name="microDiaRt" class="form-control" />
									<label for="microDiaRt">Micro Dia Setting Rt</label>
								</div>
								<div class="w-100"></div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="prongDiaRt" name="prongDiaRt" class="form-control" />
									<label for="prongDiaRt">Prong Diamond Setting Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="baguetteDiaRt" name="baguetteDiaRt" class="form-control" />
									<label for="baguetteDiaRt">Baguette Diamond Setting Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="roundStoneRt" name="roundStoneRt" class="form-control" />
									<label for="roundStoneRt">Round Stone Setting Rt</label>
								</div>
								<div class="w-100"></div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="currentDrawbackRt" name="currentDrawbackRt" class="form-control" />
									<label for="currentDrawbackRt">Current Draw back</label>
								</div>
								<div class="md-form col mb-0">
									<input type="number" step="0.01" id="gstRt" name="gstRt" class="form-control" />
									<label for="gstRt">GST % on Gold</label>
								</div>
								<div class="md-form col mb-0 mt-1 pt-3">
									<button type="button" onclick="updateSettings()" class="btn btn-outline-success waves-effect compact-btn">Update</button>
									<button type="button" onclick="getSettings()" class="btn btn-outline-danger waves-effect compact-btn">Reset</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!--Modal: Create/Edit Packing List Items-->
	<div class="modal fade" id="packingListItemsModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-fluid" role="document" style="max-width: 90%;">
			<div class="modal-content">
				<div class="card card-cascade narrower mt-0">
					<div class="view view-cascade gradient-card-header blue-gradient">
						<h2 class="card-header-title mb-0">Add Items to Packing List</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -3.5rem;">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="card-body card-body-cascade">
						<form id="PL-Items" name="PL-Items" method="POST" enctype="multipart/form-data">
							<fieldset style="border-color: dodgerblue !important;">
								<legend>Add More Items</legend>
								<div class="row">
									<input type="hidden" id="itemid" name="itemid"/>
									<div class="md-form col">
										<input required type="text" id="itemcode" name="itemcode" class="form-control" />
										<label for="itemcode">Item Code <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col">
										<input required type="text" id="mewarcode" name="mewarcode" class="form-control" />
										<label class="active" for="mewarcode">Mewar # <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col-1">
										<input required type="number" id="qty" name="qty" class="form-control" min=1 />
										<label for="qty">Qty <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col-1">
										<input type="text" id="ringsize" name="ringsize" class="form-control" />
										<label for="ringsize">Ring Size</label>
									</div>
									<div class="md-form col-1">
										<select class="mdb-select md-form colorful-select  dropdown-primary" id="metaltype" name="metaltype"  onchange="selectChanged('metaltypeLabel')">
											<option value="10K">10K</option>
											<option value="14K">14K</option>
											<option value="18K">18K</option>
											<option value="925">925</option>
											<option value="1">Other</option>
										</select>
										<label  class="mdb-main-label" id="metaltypeLabel">Metal Type</label>
									</div>
									<div class="md-form col-1">
										<input type="text" id="metalcolor" name="metalcolor" class="form-control" />
										<label for="metalcolor">Metal Color</label>
									</div>
									<div class="md-form col-3" style="margin-bottom: 0px;">
										<input required type="text" id="description" name="description" class="form-control" />
										<label for="description">Description <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col input-group">
										<div class="custom-file">
											<input type="file" class="custom-file-input" name="itemPic" id="itemPic" aria-describedby="inputGroupFileAddon01">
											<label class="custom-file-label" for="itemPic" style="margin-top: -10px;">Item Pic</label>
										</div>
									</div>
								</div>
								<div class="card-body-cascade text-center">
									<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="metalDetails-tab" data-toggle="pill" href="#metalDetails" role="tab" aria-controls="metalDetails" aria-selected="true">Metal Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="diamondDetails-tab" data-toggle="pill" href="#diamondDetails" role="tab" aria-controls="diamondDetails" aria-selected="false">Diamond Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="stoneDetails-tab" data-toggle="pill" href="#stoneDetails" role="tab" aria-controls="stoneDetails" aria-selected="false">Stone Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="otherDetails-tab" data-toggle="pill" href="#otherDetails" role="tab" aria-controls="otherDetails" aria-selected="false">Other Costs Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="allDetails-tab" data-toggle="pill" href="#allDetails" role="tab" aria-controls="allDetails" aria-selected="false" onclick="AllDetailsTabClicked(false)">All Totals</a>
										</li>
									</ul>
									<div class="tab-content" id="pills-tabContent">
										<div class="tab-pane fade show active" id="metalDetails" role="tabpanel" aria-labelledby="metalDetails-tab">
											<div id="MetalDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
										</div>
										<div class="tab-pane fade" id="diamondDetails" role="tabpanel" aria-labelledby="diamondDetails-tab">
											<div id="DiamondDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
										</div>
										<div class="tab-pane fade" id="stoneDetails" role="tabpanel" aria-labelledby="stoneDetails-tab">
											<div id="StoneDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
										</div>
										<div class="tab-pane fade" id="otherDetails" role="tabpanel" aria-labelledby="otherDetails-tab">
											<div id="OtherCostsDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
										</div>
										<div class="tab-pane fade" id="allDetails" role="tabpanel" aria-labelledby="allDetails-tab">
											<div id="AllDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
										</div>
									</div>
									<div class="mt-2">
										<button type="button" onclick="AddMoreRows()" class="btn btn-sm btn-outline-info waves-effect" style="float: left;">Add More Rows</button>
										<button type="button" name="PL_Items_Reset" id="PL_Items_Reset" onclick="resetPLItem()" class="btn btn-sm btn-outline-danger waves-effect" style="float: right;">Reset Item</button>
										<button type="submit" name="PL_Items_Create" id="PL_Items_Create" class="btn btn-sm btn-outline-success waves-effect" style="float: right;">Save Item</button>
									</div>

								</div>
							</fieldset>
						</form>

					</div>


					<!--Table-->
					<div id="PL_Items_Grid" class="ag-theme-alpine mx-auto" style="margin-top: 15px; margin-bottom: 10px; height: 260px;width: 98%;text-align:left!important;"></div>
					<!--Table-->

				</div>
			</div>
		</div>
	</div>
	</div>

	<!--Modal: Add Item Details-->
	<div class="modal fade" id="itemDetailModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-fluid" role="document" style="max-width: 90%;">
			<div class="modal-content">
				<div class="card card-cascade narrower mt-0">
					<div class="view view-cascade gradient-card-header blue-gradient">
						<h2 class="card-header-title mb-0">Modify Item Details</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -3.5rem;">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

	<script src="mdb.js"></script>
	<script type="text/javascript" src="src/js/toastr.min.js"></script>
	<script src="../src/js/moment.js"></script>
	<script src="../src/js/daterangepicker.js"></script>
	<script src="img-cell-renderer.js"></script>
	<script src="edit-btn-cell-renderer.js"></script>
	<script src="del-btn-cell-renderer.js"></script>
	<script src="main.js"></script>
	<script>
		$('.mdb-select').materialSelect();

		$('input[name="dates"]').daterangepicker();
	</script>
</body>

</html>