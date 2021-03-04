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
	<title>Stone | SilverCity</title>
	<link rel="stylesheet" href="../metal/mdb.min.css" />
	<link rel="stylesheet" href="../src/css/daterangepicker.css" />
	<script src="ag-grid-community.min.js"></script>
	<script src="item-class.js"></script>
	<link rel="stylesheet" href="../metal/style.css" />
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

	<?php include_once('../header.php'); ?>

	<div class="container-fluid text-center mt-4">
		<!-- Header -->
		<!--Table-->
		<div class="mb-2 row">
			<div class="col-4 start">
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="resetFilters()">Reset Filters</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="uploadMetalAvailableData()" style="display: none;">Save Data</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" data-mdb-toggle="modal" data-mdb-target="#stoneFormModal">Add New</button>
			</div>
			<div class="col-4">
				<span class="center"><b>Purchased Stones</b></span>
			</div>
			<div class="col-4">
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" data-mdb-toggle="modal" data-mdb-target="#importModal">Import</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="exportData()">Export</button>
			</div>
		</div>
		<div id="stoneInventory" class="ag-theme-balham" style="width: 100%;text-align:left!important;"></div>
		<!--Table-->
	</div>

	<!-- Import Modal -->
	<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Import Data From .XLSX / .XLS File</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p><b>Please be sure that your .xlsx file has data in correct format.</b> <a href="stone_importFormat.xlsx" target="_blank" class="tooltip-test" title="Tooltip">Download</a> the .xlsx sample file format if you don't have it!</p>
					<form id="importForm" name="importForm" action="src/scripts/stone_import.php" method="POST" enctype="multipart/form-data">
						<input type="file" class="form-control-file" id="importFile" accept=".xlsx" name="importFile">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" id="importFileButton" name="importFileButton">Upload Data</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!--Modal: Create/Edit Stone-->
	<div class="modal fade" id="stoneFormModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="stoneFormHeader">Add New Stone</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="stoneForm" name="stoneForm" enctype="multipart/form-data" method="post">
						<div class="row">
							<div class="col-2 pe-0 pb-3">
								<div class="form-outline">
									<input autocomplete=false required type="number" id="lot_no" name="lot_no" class="form-control" />
									<label class="form-label" for="lot_no">Lot No <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-3 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="name" name="name" class="form-control" />
									<label class="form-label" for="name">Name <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="size" name="size" class="form-control" />
									<label class="form-label" for="size">Size <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="shape" name="shape" class="form-control" />
									<label class="form-label" for="shape">Shape <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-3">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="seller" name="seller" class="form-control" />
									<label class="form-label" for="seller">Seller Name <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0 pb-3">
								<div class="form-outline">
									<input autocomplete=false required type="number" id="purchased_qty" name="purchased_qty" class="form-control" />
									<label class="form-label" for="purchased_qty">P Qty <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="number" id="purchased_wt" name="purchased_wt" class="form-control" />
									<label class="form-label" for="purchased_wt">P Wt <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">

								<div class="form-outline">
									<input autocomplete=false required type="text" id="unit" name="unit" class="form-control" value="cts" />
									<label class="form-label" for="unit">Unit <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="number" id="current_qty" name="current_qty" class="form-control" />
									<label class="form-label" for="current_qty">C Qty <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="number" id="current_wt" name="current_wt" class="form-control" />
									<label class="form-label" for="current_wt">C Wt <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="box" name="box" class="form-control" />
									<label class="form-label" for="box">Box # <span class="text-danger">*</span></label>
								</div>
							</div>

							<div class="col-2 pe-0 pb-3">
								<div class="form-outline">
									<input autocomplete=false required type="number" id="cost" name="cost" class="form-control" />
									<label class="form-label" for="cost">Cost <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-2 pe-0">
								<div class="form-outline">
									<input autocomplete=false required type="number" step="0.01" id="less" name="less" class="form-control" />
									<label class="form-label" for="less">Less <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-8">
								<div class="form-outline">
									<input autocomplete=false required type="text" id="description" name="description" class="form-control" />
									<label class="form-label" for="description">Description <span class="text-danger">*</span></label>
								</div>
							</div>

							<div class="col-12 pr-0 pl-0" style="text-align: end;">
								<button type="reset" name="stReset" id="stReset" class="btn btn-outline-danger" data-mdb-ripple-color="dark">Reset</button>
								<button type="submit" name="stCreate" id="stCreate" class="btn btn-outline-success" data-mdb-ripple-color="dark">Create</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script src="../metal/jquery-3.6.0.min.js"></script>
	<script src="../metal/mdb.min.js"></script>
	<!-- <script src="mdb.js"></script> -->
	<script src="../src/js/moment.js"></script>
	<script src="../src/js/daterangepicker.js"></script>
	<script src="edit-btn-cell-renderer.js"></script>
	<script src="del-btn-cell-renderer.js"></script>
	<script src="main.js"></script>
</body>

</html>