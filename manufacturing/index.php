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
	<title>Manufacturing | SilverCity</title>
	<link rel="stylesheet" href="../src/css/daterangepicker.css" />
	<link rel="stylesheet" href="../src/css/sweetalert2.min.css" />
	<link rel="stylesheet" href="../metal/mdb.min.css" />
	<link rel="stylesheet" href="../metal/toastr.min.css" />
	<script src="ag-grid-community.min.js"></script>
	<script src="item-class.js"></script>
	<link rel="stylesheet" href="../metal/style.css" />
	<script type="text/javascript">
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

		<div class="mb-2 row">
			<div class="col-4 start">
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="AddMoreRows()">Add Empty Rows</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="resetFilters()">Reset Filters</button>
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="uploadData()">Save Data</button>
			</div>
			<div class="col-4">
				<span class="center"><b>Items in Manufacturing</b></span>
			</div>
			<div class="col-4">
				<button type="button" class="btn btn-sm btn-outline-success" data-mdb-ripple-color="dark" onclick="moveToInvoice()">Move to Packing List</button>
			</div>
		</div>

		<div id="manufacturingInventory" class="ag-theme-balham mx-auto" style="width: 100%;text-align:left!important;"></div>
		<!--Table-->
	</div>

	<!--Modal: Move to Invoice-->
	<div class="modal fade" id="invoiceModal" name="invoiceModal" tabindex="-1" data-mdb-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog cascading-modal" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Move Items to Packing List</h5>
					<button type="button" class="close" data-mdb-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="PL-Items" name="PL-Items" enctype="multipart/form-data" method="post">
						<div class="row ms-2">
							Select Packing List: 
							<select id="invoiceNames" name="invoiceNames" class="ms-2" onchange="invoiceChanged(this);" style="padding: 0.25rem; width: 170px"></select>
							<button type="button" name="plCreate" id="plCreate" class="btn btn-sm btn-outline-success ms-2" data-mdb-ripple-color="dark" onclick="moveToPackingList()" style="width: 120px;">Move Items</button>

							<div class="col-5 pe-0" id="plNameParent" name="plNameParent" style="display: none;">
								<div class="form-outline">
									<input autocomplete=false type="text" id="plName" name="plName" class="form-control" />
									<label class="form-label" for="plName">Name/Description <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-3 pe-0 pb-3" id="plDateParent" name="plDateParent" style="display: none;">
								<div class="form-outline">
									<input autocomplete=false type="text" id="plDate" name="plDate" class="form-control" />
									<label class="form-label" for="plDate">Date <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="ms-4" style="text-align: center;">
							</div>
						</div>
					</form>

				</div>

			</div>
		</div>
	</div>



	<script src="../metal/jquery-3.6.0.min.js"></script>
	<script src="../metal/mdb.min.js"></script>
	<script src="../metal/common.js"></script>
	<script src="../metal/toastr.min.js"></script>
	<script src="../src/js/sweetalert2.min.js"></script>
	<script src="../src/js/moment.js"></script>
	<script src="../src/js/daterangepicker.js"></script>
	<script src="edit-btn-cell-renderer.js"></script>
	<script src="del-btn-cell-renderer.js"></script>
	<script src="main.js"></script>
	<script>
		// $('.mdb-select').materialSelect();

		$('input[name="dates"]').daterangepicker();
	</script>
</body>

</html>