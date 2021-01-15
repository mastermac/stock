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
			<button type="button" class="btn btn-outline-deep-purple btn-rounded waves-effect" data-toggle="modal" data-target="#importModal">Import</button>
			<button type="button" class="btn btn-outline-deep-purple btn-rounded waves-effect" data-toggle="modal" data-target="#stoneFormModal">Add New</button>
			<button type="button" class="btn btn-outline-deep-purple btn-rounded waves-effect" onclick="exportData()">Export</button>
		</div>

		<!--Table-->
		<div id="stoneInventory" class="ag-theme-alpine mx-auto" style="width: 100%;text-align:left!important;"></div>
		<!--Table-->
	</div>


    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Import Data From .XLSX / .XLS File</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><b>Please be sure that your .xlsx file has data in correct format.</b> <a href="stone_importFormat.xlsx" target="_blank" class="tooltip-test" title="Tooltip">Download</a> the .xlsx sample file format if you don't have it!</p>
            <form id="importForm" name="importForm" action="src/scripts/stone_import.php" method="POST" enctype="multipart/form-data">
              <input type="file" class="form-control-file" id="importFile" accept=".xlsx" name="importFile">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="importFileButton" name="importFileButton">Upload Data</button>
            </form>
          </div>
        </div>
      </div>
    </div>

	<!--Modal: Create/Edit Stone-->
	<div class="modal fade" id="stoneFormModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-lg" role="document">
			<div class="modal-content">
				<div class="card card-cascade narrower mt-0">
					<div class="view view-cascade gradient-card-header blue-gradient">
						<h2 class="card-header-title mb-0" id="stoneFormHeader">Add New Stone</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -3.5rem;">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="card-body card-body-cascade text-center">
						<form id="stoneForm" name="stoneForm" enctype="multipart/form-data" method="post">
							<div class="row">
								<!-- <div class="md-form col-2 mb-0">
										<input autocomplete=false required type="text" id="plId" name="plId" class="form-control" />
										<label for="plId">ID <span class="text-danger">*</span></label>
									</div> -->
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="number" id="lot_no" name="lot_no" class="form-control" />
									<label for="lot_no">Lot No <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-3 mb-0">
									<input autocomplete=false required type="text" id="name" name="name" class="form-control" />
									<label for="name">Name <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="text" id="size" name="size" class="form-control" />
									<label for="size">Size <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="text" id="shape" name="shape" class="form-control" />
									<label for="shape">Shape <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-3 mb-0">
									<input autocomplete=false required type="text" id="seller" name="seller" class="form-control" />
									<label for="seller">Seller Name <span class="text-danger">*</span></label>
								</div>

								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="number" id="purchased_qty" name="purchased_qty" class="form-control" />
									<label for="purchased_qty">P Qty <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="number" id="purchased_wt" name="purchased_wt" class="form-control" />
									<label for="purchased_wt">P Wt <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="text" id="unit" name="unit" class="form-control" value="cts" />
									<label for="unit">Unit <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="number" id="current_qty" name="current_qty" class="form-control" />
									<label for="current_qty">C Qty <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="number" id="current_wt" name="current_wt" class="form-control" />
									<label for="current_wt">C Wt <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="text" id="box" name="box" class="form-control" />
									<label for="box">Box # <span class="text-danger">*</span></label>
								</div>

								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="number" id="cost" name="cost" class="form-control" />
									<label for="cost">Cost <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-2 mb-0">
									<input autocomplete=false required type="number" step="0.01" id="less" name="less" class="form-control" />
									<label for="less">Less <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col-8 mb-0">
									<input autocomplete=false required type="text" id="description" name="description" class="form-control" />
									<label for="description">Description <span class="text-danger">*</span></label>
								</div>


								<!-- <div class="md-form col-4 mb-0">
									<input autocomplete=false required type="text" id="plDate" name="plDate" class="form-control" />
									<label class="active" for="plDate">Date <span class="text-danger">*</span></label>
								</div> -->
								<div class="md-form col-12 pr-0 pl-0 mb-0">
									<button type="reset" name="stReset" id="stReset" class="btn btn-outline-danger waves-effect compact-btn">Reset</button>
									<button type="submit" name="stCreate" id="stCreate" class="btn btn-outline-success waves-effect compact-btn">Create</button>
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
									<input required type="text" id="exchangeRt" name="exchangeRt" class="form-control" />
									<label for="exchangeRt">Exchange Rt <span class="text-danger">*</span></label>
								</div>
								<div class="md-form col mb-0">
									<input type="text" id="silverRt" name="silverRt" class="form-control" />
									<label for="silverRt">Silver/grm Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="text" id="goldRt" name="goldRt" class="form-control" />
									<label for="goldRt">Gold/grm Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="text" id="labourRt" name="labourRt" class="form-control" />
									<label for="labourRt">Labour/grm Rt</label>
								</div>
								<div class="w-100"></div>
								<div class="md-form col mb-0">
									<input type="text" id="platingRt" name="platingRt" class="form-control" />
									<label for="platingRt">Plating/grm Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="text" id="findingsRt" name="findingsRt" class="form-control" />
									<label for="findingsRt">Findings Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="text" id="microDiaRt" name="microDiaRt" class="form-control" />
									<label for="microDiaRt">Micro Diamond Setting Rt</label>
								</div>
								<div class="w-100"></div>
								<div class="md-form col mb-0">
									<input type="text" id="prongDiaRt" name="prongDiaRt" class="form-control" />
									<label for="prongDiaRt">Prong Diamond Setting Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="text" id="baguetteDiaRt" name="baguetteDiaRt" class="form-control" />
									<label for="baguetteDiaRt">Baguette Diamond Setting Rt</label>
								</div>
								<div class="md-form col mb-0">
									<input type="text" id="roundStoneRt" name="roundStoneRt" class="form-control" />
									<label for="roundStoneRt">Round Stone Setting Rt</label>
								</div>
								<div class="w-100"></div>
								<div class="md-form col mb-0 mt-1">
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
						<form id="PL-Items" name="PL-Items" enctype="multipart/form-data">
							<fieldset style="border-color: dodgerblue !important;">
								<legend>Add More Items</legend>
								<div class="row">
									<div class="md-form col">
										<input required type="text" id="itemCode" name="itemCode" class="form-control" />
										<label for="itemCode">Item Code <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col">
										<input required type="text" id="itemDesignNo" name="itemDesignNo" class="form-control" />
										<label class="active" for="itemDesignNo">Mewar # <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col-1">
										<input required type="number" id="itemQty" id="itemQty" class="form-control" min=1 />
										<label for="itemQty">Qty <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col-1">
										<input type="text" id="itemSize" name="itemSize" class="form-control" />
										<label for="itemSize">Ring Size</label>
									</div>
									<div class="md-form col-1">
										<select class="mdb-select md-form colorful-select  dropdown-primary" id="itemMetalType" name="itemMetalType"  onchange="selectChanged('itemMetalTypeLabel')">
											<option value="14K">14K</option>
											<option value="28K">18K</option>
											<option value="925">925</option>
											<option value="1">Other</option>
										</select>
										<label  class="mdb-main-label" id="itemMetalTypeLabel">Metal Type</label>
									</div>
									<div class="md-form col-1">
										<input type="text" id="itemMetalColor" name="itemMetalColor" class="form-control" />
										<label for="itemMetalColor">Metal Color</label>
									</div>
									<div class="md-form col-3" style="margin-bottom: 0px;">
										<input required type="text" id="itemDescription" name="itemDescription" class="form-control" />
										<label for="itemDescription">Description <span class="text-danger">*</span></label>
									</div>
									<div class="md-form col input-group">
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="itemPic" aria-describedby="inputGroupFileAddon01">
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
											<a class="nav-link" id="oneDetails-tab" data-toggle="pill" href="#stoneDetails" role="tab" aria-controls="stoneDetails" aria-selected="false">Stone Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="otherDetails-tab" data-toggle="pill" href="#otherDetails" role="tab" aria-controls="otherDetails" aria-selected="false">Other Costs Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="allDetails-tab" data-toggle="pill" href="#allDetails" role="tab" aria-controls="allDetails" aria-selected="false" onclick="AllDetailsTabClicked()">All Totals</a>
										</li>
									</ul>
									<div class="tab-content" id="pills-tabContent">
										<div class="tab-pane fade show active" id="metalDetails" role="tabpanel" aria-labelledby="metalDetails-tab">
											<div id="MetalDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
										</div>
										<div class="tab-pane fade" id="diamondDetails" role="tabpanel" aria-labelledby="diamondDetails-tab">
											<div id="DiamondDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
										</div>
										<div class="tab-pane fade" id="oneDetails" role="tabpanel" aria-labelledby="stoneDetails-tab">
											<div id="oneDetailsGrid" class="ag-theme-alpine mx-auto" style="height: 260px;width: 100%;text-align:left!important;"></div>
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
										<button type="reset" name="PL_Items_Reset" id="PL_Items_Reset" class="btn btn-sm btn-outline-danger waves-effect" style="float: right;">Reset Item</button>
										<button type="button" name="PL_Items_Create" id="PL_Items_Create" onclick="createPLItem()" class="btn btn-sm btn-outline-success waves-effect" style="float: right;">Save Item</button>
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
	<script src="../src/js/moment.js"></script>
	<script src="../src/js/daterangepicker.js"></script>
	<script src="edit-btn-cell-renderer.js"></script>
	<script src="del-btn-cell-renderer.js"></script>
	<script src="main.js"></script>
	<script>
		$('.mdb-select').materialSelect();

		$('input[name="dates"]').daterangepicker();
	</script>
</body>

</html>