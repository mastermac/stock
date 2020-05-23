<?php
session_start();
if (!isset($_SESSION['userid'])) {
  header('Location: login.php');
}
$usertype = '';
require('src/scripts/db_config.php');
if ($_SESSION['usertype'] == 1)
  $usertype = ' and userid=' . $_SESSION['userid'];
?>
<!DOCTYPE html>
<html>

<head>
  <title>SilverApp | Dashboard</title>
  <!-- <meta name="viewport" content="width=device-width, initial-scale=0.9"> -->
  <link href="src/css/jquery-ui.min.css" rel="stylesheet">
  <link href="src/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="src/css/jquery.fancybox.min.css" />
  <link href="src/css/toastr.min.css" rel="stylesheet">
  <link href="src/css/daterangepicker.css" rel="stylesheet">
  <style type="text/css">
    .modal-dialog,
    .modal-content {
      z-index: 1051;
    }
  </style>
  <script type="text/javascript" src="src/js/jquery.min.js"></script>
  <script src="src/js/jquery-ui.min.js"></script>
  <script type="text/javascript" src="src/js/jquery.lazy.js"></script>
  <script type="text/javascript" src="src/js/moment.js"></script>
  <script type="text/javascript" src="src/js/daterangepicker.js"></script>
  <script src="src/js/tether.min.js"></script>
  <script type="text/javascript" src="src/js/popper.min.js"></script>
  <script type="text/javascript" src="src/js/bootstrap.min.js"></script>
  <script src="src/js/ion.rangeSlider.min.js"></script>
  <script type="text/javascript" src="src/js/jquery.twbsPagination.js"></script>
  <script type="text/javascript" src="src/js/toastr.min.js"></script>
  <script src="src/js/jquery.fancybox.min.js"></script>
  <script src="https://cdn.rawgit.com/mozilla/localForage/master/dist/localforage.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fuse.js/3.0.4/fuse.min.js"></script>
  <script type="text/javascript">
    //var url = "http://silvercityonline.com/silvercity/";
    var main = "<?php echo $_SERVER['DOCUMENT_ROOT'] ?>";
    var url = "";
    if (main == "C:/wamp64/www" || main == "C:/wamp/www")
      url = "http://localhost:8080/stock/";
    else
      url = "";
    var usertype = <?php echo $_SESSION['usertype'] ?>;
    var userid = <?php echo $_SESSION['userid'] ?>;
  </script>

  <style>
    .productTable tr td:nth-child(1),
    .productTable tr th:nth-child(1) {
      width: 30px;
    }

    .productTable tr td:nth-child(2),
    .productTable tr th:nth-child(2) {
      width: 120px;
    }

    .productTable tr td:nth-child(3),
    .productTable tr th:nth-child(3) {
      width: 70px;
    }

    .productTable tr td:nth-child(4),
    .productTable tr th:nth-child(4) {
      width: 90px;
    }

    .productTable tr td:nth-child(5),
    .productTable tr th:nth-child(5) {
      width: 85px;
    }

    .productTable tr td:nth-child(6),
    .productTable tr th:nth-child(6) {
      width: 250px;
    }

    .productTable tr td:nth-child(7),
    .productTable tr th:nth-child(7) {
      width: 240px;
    }

    .productTable tr td:nth-child(8),
    .productTable tr th:nth-child(8) {
      width: 70px;
    }

    .productTable tr td:nth-child(9),
    .productTable tr th:nth-child(9) {
      width: 80px;
    }

    .productTable tr td:nth-child(10),
    .productTable tr th:nth-child(10) {
      width: 90px;
    }

    .productTable tr td:nth-child(11),
    .productTable tr th:nth-child(11) {
      width: 90px;
    }

    .productTable tr td:nth-child(12),
    .productTable tr th:nth-child(12) {
      width: 70px;
    }

    .productTable tr td:nth-child(13),
    .productTable tr th:nth-child(13) {
      width: 70px;
    }

    .productTable tr td:nth-child(14),
    .productTable tr th:nth-child(14) {
      width: 90px;
    }

    .productTable tr td:nth-child(15),
    .productTable tr th:nth-child(15) {
      width: 50px;
    }

    .productTable tr td:nth-child(16),
    .productTable tr th:nth-child(16) {
      width: 100px;
    }

    @media (min-width: 768px) {
      _grid.scss:42 .col-md-1 {
        -ms-flex: 0 0 8.333333%;
        flex: 0 0 8.333333%;
        max-width: 7.5%;
      }

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

    table.floatThead-table {
      border-top: none;
      border-bottom: none;
      background-color: #fff;
      margin-top: -50px;
    }

    .back-to-top {
      cursor: pointer;
      position: fixed;
      bottom: 20px;
      right: 20px;
      display: none;
    }

    /*******************************
* MODAL AS LEFT/RIGHT SIDEBAR
* Add "left" or "right" in modal parent div, after class="modal".
* Get free snippets on bootpen.com
*******************************/
    .modal-dialog-slideout {
      min-height: 100%;
      margin: 0 0 0 auto;
      background: #fff;
    }

    .modal.fade .modal-dialog.modal-dialog-slideout {
      -webkit-transform: translate(100%, 0)scale(1);
      transform: translate(100%, 0)scale(1);
    }

    .modal.fade.show .modal-dialog.modal-dialog-slideout {
      -webkit-transform: translate(0, 0);
      transform: translate(0, 0);
      display: flex;
      align-items: stretch;
      -webkit-box-align: stretch;
      height: 100%;
    }

    .modal.fade.show .modal-dialog.modal-dialog-slideout .modal-body {
      overflow-y: auto;
      overflow-x: hidden;
    }

    .modal-dialog-slideout .modal-content {
      border: 0;
    }

    .modal-dialog-slideout .modal-header,
    .modal-dialog-slideout .modal-footer {
      height: 69px;
      display: block;
    }

    .modal-dialog-slideout .modal-header h5 {
      float: left;
    }

    .btn {
      cursor: pointer !important;
    }

    .ui-slider .ui-btn-inner {
      padding: 4px 0 0 0 !important;
    }

    .ui-slider-popup {
      position: absolute !important;
      width: 64px;
      height: 64px;
      text-align: center;
      font-size: 36px;
      padding-top: 14px;
      z-index: 100;
      opacity: 0.8;
    }
    input[button]{
      cursor: pointer;
    }
  </style>
  <link rel="stylesheet" href="src/css/ion.rangeSlider.min.css" />
</head>

<body>
  <div class="ajax-loader">
    <img src="src/images/ajax.gif" class="img-responsive" />
  </div>

  <div class="container-fluid" style="zoom: 90%;">
    <div class="row" style="margin:25px 0px;">
      <div class="col-sm-12 text-center">
        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
          <button type="button" class="btn btn-outline-secondary">Welcome <?php echo $_SESSION['username']; ?></button>
          <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#create-item">Add Product</button>
          <?php
          if ($_SESSION['usertype'] == 0) {
            echo '<button type="button" id="addVendor" name="addVendor" class="btn btn-outline-success" data-toggle="modal" data-target="#addVendorModal" data-backdrop="static" data-keyboard="false">Manage Vendors</button>';
            echo '<button type="button" id="noImageCodes" name="noImageCodes" class="btn btn-outline-primary">Item Without Images</button>';
            echo '<button type="button" class="btn btn-outline-primary"  data-toggle="modal" data-target="#updateModal" data-keyboard="false">Update Stock Data</button>';
          }

          ?>
          <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#importModal">Import Data</button>
          <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#importPics">Upload Pics</button>
          <?php
          if ($_SESSION['canExport'] == 1)
            echo '<button type="button" id="excelExport" name="excelExport" class="btn btn-outline-danger">Export</button>';
          ?>
          <?php
          if ($_SESSION['canExport'] == 1)
            echo '<button type="button" id="pdfExport" name="pdfExport" class="btn btn-outline-danger">PDF Export</button>';
          ?>
          <button type="button" id="lastCodesButton" name="lastCodesButton" class="btn btn-outline-danger">Last Codes</button>
          <div class="btn-group" role="group">
            <select id="perPage" name="perPage" class="btn btn-outline-dark">
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="250">250</option>
              <option value="500">500</option>
              <option value="1000">1000</option>
            </select>
          </div>
          <button type="button" class="btn btn-outline-info" id="undo" name="undo" disabled>Undo</button>
          <button type="button" class="btn btn-outline-info" id="logout" name="logout">Log Out</button>
        </div>
      </div>
    </div>
    <div >
        <form method="POST" id="filterForm">
          <div class="row">
        <div class="form-group col-md-1" style="min-width: 9%;max-width: 9%;padding-left: 15px;padding-right: 5px;">
          <label for="itemId">Item Id&nbsp;</label><?php if($_SESSION['usertype']==0){ echo '<a class="multiId" style="cursor: pointer; color: blue;">( Multi? )</a>'; }
           ?>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_itemId" name="s_itemId"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 6%;max-width: 6%;padding-left: 5px;padding-right: 5px;">
          <label for="vendor">V Id:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_vendor" name="s_vendor"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 9%;max-width: 9%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">vCode:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_vendorCode" name="s_vendorCode"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 15%;max-width: 15%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">Description:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_description" name="s_description"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 10%;max-width: 15%;padding-left: 5px;padding-right: 5px;display:none;">
          <label for="itemId">Item Type:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_itemTypeCode" name="s_itemTypeCode"/>
        </div>
        <!-- <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">G.W.</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_grossWt" name="s_grossWt"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 3.5%;max-width: 3.5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">Dia</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_diaWt" name="s_diaWt"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">cstone</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_cstoneWt" name="s_cstoneWt"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">Gold</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_goldWt" name="s_goldWt"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">sellPrice:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_sellPrice" name="s_sellPrice"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">Stock:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_curStock" name="s_curStock"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">Size:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_ringSize" name="s_ringSize"/>
        </div> -->
        <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">Style:</label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_styleCode" name="s_styleCode"/>
        </div>
        <div class="form-group col-md-1" style="min-width: 15%;max-width: 15%;padding-left: 5px;padding-right: 5px;">
          <label for="itemId">Date:</label>
          <input type="text" autocomplete="off" class="form-control searchField" id="s_daterange" id="s_daterange" style="padding: .375rem .15rem;"  value="01/01/2015 - 01/31/2015" />
        </div>
        <div class="form-group col-md-1" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 15px;">
          <label for="itemId">&nbsp;</label>
          <button type="button" class="btn btn-xs btn-outline-danger reset-item" id="resetFilter" name="resetFilter">RESET</button>
        </div>
        <div class="form-group col-md-2" style="min-width: 5%;max-width: 5%;padding-left: 5px;padding-right: 15px;">
          <label for="itemId">&nbsp;</label>
          <button type="button" class="btn btn-outline-info" id="filter" name="filter" data-toggle="modal" data-target="#filterModal">More Filters</button>
        </div>
        <div class="form-group col-md-12 extention" style="min-width: 94.5%;max-width: 94.5%;padding-left: 15px;padding-right: 5px;">
          <label for="itemIdExt">Enter Multiple Item IDs here separated by <b>comma ,</b></label>
          <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_itemIdExt" name="s_itemIdExt"/>
        </div>
      </div>
      </form>
      </div>

    <table class="table sticky-header productTable table-sm table-bordered table-hover table-striped" width="100%" cellspacing="0" id="myTable">
      <thead>
        <tr>
          <th data-column-id="SNo">S.No.</th>
          <th data-column-id="itemNo" data-identifier="true" class="text-center">Item No</th>
          <th data-column-id="vendor" class="text-center">V Id</th>
          <th data-column-id="vendorCode" class="text-center">VCode</th>
          <th data-column-id="itemPic" class="text-center">Item Pic</th>
          <th data-column-id="description" class="text-center">Description</th>
          <th data-column-id="itemTypeCode" class="text-center">Item Type</th>
          <th data-column-id="ringSize" class="text-center">Size</th>
          <th data-column-id="grossWt" class="text-center">G.W.</th>
          <th data-column-id="diaWt" class="text-center">Dia</th>
          <th data-column-id="cstoneWt" class="text-center">Cstone</th>
          <th data-column-id="goldWt" class="text-center">Gold</th>
          <th data-column-id="noOfDia" class="text-center">Dia</th>
          <th data-column-id="sellPrice" class="text-center" style="text-align: right;">Price</th>
          <th data-column-id="curStock" class="text-center" style="text-align: right;">Stock</th>
          <th data-column-id="dt" class="text-center" style="text-align: right;">Date</th>
          <th style="width:80px;">Action</th>
        </tr>
      </thead>
      <tbody id="productData">
      </tbody>
    </table>
    <ul id="pagination" class="pagination"></ul>
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
            <p><b>Please be sure that your .xlsx file has data in correct format.</b> <a href="src/import/importFormat.xlsx" target="_blank" class="tooltip-test" title="Tooltip">Download</a> the .xlsx sample file format if you don't have it!</p>
            <form id="importForm" name="importForm" action="src/scripts/import.php" method="POST" enctype="multipart/form-data">
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
    <!-- Update Stock Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Update Stock Data From .XLSX / .XLS File</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><b>Please be sure that your .xlsx file has data in correct format.</b> <a href="src/import/importFormat.xlsx" target="_blank" class="tooltip-test" title="Tooltip">Download</a> the .xlsx sample file format if you don't have it!</p>
            <form id="updateForm" name="updateForm" action="src/scripts/import.php" method="POST" enctype="multipart/form-data">
              <input type="file" class="form-control-file" id="updateFile" accept=".xlsx" name="updateFile">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="updateFileButton" name="updateFileButton">Update Data</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Add Vendor -->
    <div class="modal fade" id="addVendorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" style="max-width: 1200px;" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Manage Vendors</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="addVendorForm" action="src/scripts/addVendor.php" method="POST" enctype="multipart/form-data">
              <div class="row" id="vendorRow">
                <input type="hidden" value="add" name="vendorAction" id="vendorAction">
                <input type="hidden" value="" name="vid" id="vid">
                <div class="form-group col-md-4">
                  <label for="itemId">Vendor Name:</label>
                  <input type="text" style="padding: .375rem .15rem;" class="form-control" id="vendorName" name="vendorName" required />
                </div>
                <div class="form-group col-md-2" id="vendorModalDiv">
                  <label for="vendor" class="control-label">V ID:</label>
                  <input type="text" style="padding: .375rem .15rem;" class="form-control" id="vendorId" name="vendorId" />
                </div>
                <div class="form-group col-md-2">
                  <label for="vendor" class="control-label"></label>
                  <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                    <input type="checkbox" id="accountActive" name="accountActive" class="custom-control-input" checked>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Make Account Active?</span>
                  </label>
                </div>
                <div class="form-group col-md-2">
                  <label for="vendor" class="control-label"></label>
                  <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                    <input type="checkbox" id="canExport" name="canExport" class="custom-control-input">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Give Export Option?</span>
                  </label>
                </div>
                <div class="form-group col-md-2">
                  <label for="vendor" class="control-label"></label>
                  <label class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">
                    <input type="checkbox" id="newAccount" name="newAccount" class="custom-control-input">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Create an Account?</span>
                  </label>
                </div>
                <div class="form-group col-md-4 newUser">
                  <label for="itemId">Email:</label>
                  <input type="Email" class="form-control" id="vendorEmail" name="vendorEmail" />
                </div>
                <div class="form-group col-md-3 newUser">
                  <label for="itemId">Password:</label>
                  <input type="Password" class="form-control" id="vendorPwd" name="vendorPwd" />
                </div>
                <div class="form-group col-md-3 newUser">
                  <label for="itemId">User Type:</label>
                  <select class="form-control" id="vendorType" name="vendorType">
                    <option value="1">Normal User</option>
                    <option value="0">Super User</option>
                  </select>
                </div>
                <div class="form-group col-md-2 newUser">
                  <label for="itemId">Series (if Any):</label>
                  <input type="text" class="form-control" id="vendorSeries" name="vendorSeries" />
                </div>
              </div>
              <button type="submit" id="vendorAdd" class="btn col-md-12 vendor-submit btn-success">Save</button>
            </form>
            <hr>
            <table class="table table-responsive table-sm table-bordered table-hover table-striped" width="100%" cellspacing="0" id="vendorTable">
              <thead>
                <tr>
                  <th onclick="sortTable(0, 'vendorTable', true)" data-column-id="vendorTableSNo" style="width: 80px;">S.No.</th>
                  <th onclick="sortTable(1, 'vendorTable', true)" data-column-id="vendorTableID" data-identifier="true" class="text-center" style="width: 70px;">ID</th>
                  <th onclick="sortTable(2, 'vendorTable')" data-column-id="vendorTableName" class="text-center" style="width: 210px;">Name</th>
                  <th onclick="sortTable(3, 'vendorTable', true)" data-column-id="vendorTableCount" class="text-center" style="width: 100px;text-align: right;">Products</th>
                  <th data-column-id="vendorTableEmail" class="text-center" style="width: 210px;">Email</th>
                  <th data-column-id="vendorTablePwd" class="text-center" style="width: 150px;">Password</th>
                  <th data-column-id="vendorTableType" class="text-center" style="width: 100px;">Type</th>
                  <th data-column-id="vendorTableSeries" class="text-center" style="width:70px;">Series</th>
                  <th data-column-id="vendorTableExport" class="text-center" style="width: 70px;">Export</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">Enabled</th>
                  <th style="width:80px;">Action</th>
                </tr>
              </thead>
              <tbody id="vendorDataTable">
              </tbody>
            </table>
            <ul id="pagination1" class="pagination"></ul>

          </div>
        </div>
      </div>
    </div>
    <!-- Add Vendor -->

    <!-- Product History -->
    <div class="modal fade" id="productHistoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" style="max-width: 90%" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="productHistoryModalName">Product History</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-responsive table-sm table-bordered table-hover table-striped" width="100%" cellspacing="0" id="vendorTable">
              <thead>
                <tr>
                  <th data-column-id="vendorTableSNo" style="width: 60px;">S.No.</th>
                  <th data-column-id="vendorTableSNo" style="width: 70px;">Type</th>
                  <th data-column-id="vendorTableSNo" style="width: 180px;">Date-Time</th>
                  <th data-column-id="vendorTableID" data-identifier="true" class="text-center" style="width: 100px;">Item No</th>
                  <th data-column-id="vendorTableName" class="text-center" style="width: 70px;">V Id</th>
                  <th data-column-id="vendorTableCount" class="text-center" style="width: 70px;text-align: right;">VCode</th>
                  <th data-column-id="vendorTableEmail" class="text-center" style="width: 250px;">Description</th>
                  <th data-column-id="vendorTablePwd" class="text-center" style="width: 220px;">Item Type</th>
                  <th data-column-id="vendorTableType" class="text-center" style="width: 100px;">Size</th>
                  <th data-column-id="vendorTableSeries" class="text-center" style="width:70px;">G.W.</th>
                  <th data-column-id="vendorTableExport" class="text-center" style="width: 70px;">Dia</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">Cstone</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">Gold</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">Dia</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">Price</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">Stock</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">MU</th>
                  <th data-column-id="vendorTableEnabled" class="text-center" style="width: 70px;">Cost</th>
                </tr>
              </thead>
              <tbody id="productHistoryDataTable">
              </tbody>
            </table>
            <ul id="pagination1" class="pagination"></ul>

          </div>
        </div>
      </div>
    </div>
    <!-- Product History -->


    <!-- Last Codes -->
    <div class="modal fade" id="nextCodes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Last Codes Entered</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div id="nextCodesDiv" class="modal-body">
          </div>
        </div>
      </div>
    </div>
    <!-- END LAST CODES -->
    <!-- Import Pics Modal -->
    <div class="modal fade" id="importPics" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Import Pics of Products</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Select all the pics which you want to upload <b>(Max 20 at a time)</b>.</p>
            <form id="uploadForm" name="uploadForm" action="src/scripts/upload.php" method="POST" enctype="multipart/form-data">
              <input type="file" class="form-control-file" id="uploadFile" accept=".jpeg, .jpg, .png, .gif" multiple="multiple" name="files[]">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="importFileButton" name="importFileButton">Upload Data</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Create Item Modal -->
    <div class="modal fade" id="create-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Product</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="add_product" action="src/scripts/create.php" method="POST" enctype="multipart/form-data">
              <div class="row">
                <input type="hidden" value="add" name="action" id="action">
                <div class="form-group col-md-3">
                  <label for="itemId">Item Id:</label>
                  <input type="text" class="form-control" id="itemId" name="itemId" required />
                </div>

                <div class="form-group col-md-3" id="vendorDiv">
                  <label for="vendor" class="control-label">Vendor ID:</label>
                  <input type="text" class="form-control" id="vendor" name="vendor" list="vendorList" />
                  <datalist id="vendorList">
                    <?php
                    $mysqli = getConn();
                    if ($_SESSION['usertype'] == 1)
                      $sql = "SELECT code, name from vendor where vid='" . $_SESSION['userid'] . "' order by code;";
                    else
                      $sql = "SELECT code, name from vendor order by code;";
                    $result = $mysqli->query($sql);
                    $vendorList = "";
                    while ($row = $result->fetch_assoc()) {
                      $vendorList = $vendorList . "<option title='" . $row["name"] . "'>" . $row["code"] . "</option>";
                    }
                    echo $vendorList;
                    ?>
                  </datalist>
                </div>
                <div class="form-group col-md-2">
                  <label for="vendorCode">Vendor Code:</label>
                  <input type="text" class="form-control" id="vendorCode" name="vendorCode" />
                </div>

                <div class="form-group col-md-4">
                  <label for="itemPic" class="control-label">Item Pic:</label>
                  <input type="file" class="form-control-file" id="itemPic" name="itemPic">
                </div>
                <div class="form-group col-md-6">
                  <label for="description" class="control-label">Description</label>
                  <textarea class="form-control" id="description" name="description" row="2"></textarea>
                </div>
                <div class="form-group col-md-6" id="itemTypeCodeDiv">
                  <label for="itemTypeCode" class="control-label">Item Type</label>
                  <input type="text" class="form-control" id="itemTypeCode" name="itemTypeCode" list="itemTypeCodeList" />
                  <datalist id="itemTypeCodeList">
                    <?php
                    $sql = "SELECT itype,purity,category FROM itemtype";
                    $result1 = $mysqli->query($sql);
                    $itemType = "";
                    while ($row1 = $result1->fetch_assoc()) {
                      $itemType = $itemType . "<option value='" . $row1["purity"] . ' ' . $row1["category"] . ' ' . $row1["itype"] . "'>" . $row1["purity"] . ' ' . $row1["category"] . ' ' . $row1["itype"] . "</option>";
                    }
                    echo $itemType;
                    ?>
                  </datalist>
                </div>
                <div class="form-group col-md-3">
                  <label for="grossWt" class="control-label">Gross Wt</label>
                  <input type="number" step="any" class="form-control" id="grossWt" min="0" value="0" name="grossWt" />
                </div>
                <div class="form-group col-md-3">
                  <label for="diaWt" class="control-label">Dia Wt</label>
                  <input type="number" step="any" class="form-control" id="diaWt" min="0" value="0" name="diaWt" />
                </div>
                <div class="form-group col-md-3">
                  <label for="cstoneWt" class="control-label">cstone Wt</label>
                  <input type="number" step="any" class="form-control" id="cstoneWt" min="0" value="0" name="cstoneWt" />
                </div>
                <div class="form-group col-md-3">
                  <label for="goldWt" class="control-label">Gold Wt</label>
                  <input type="number" step="any" step="any" class="form-control" id="goldWt" min="0" value="0" name="goldWt" />
                </div>
                <div class="form-group col-md-2">
                  <label for="noOfDia" class="control-label">Total Dia</label>
                  <input type="number" step="1" class="form-control" id="noOfDia" min="0" value="0" name="noOfDia" />
                </div>
                <div class="form-group col-md-2">
                  <label for="sellPrice" class="control-label">Sell Price</label>
                  <input type="number" class="form-control" id="sellPrice" min="0" name="sellPrice" required />
                </div>
                <div class="form-group col-md-2">
                  <label for="curStock" class="control-label">Current Stock</label>
                  <input type="number" class="form-control" id="curStock" min="0" name="curStock" value="1" />
                </div>
                <div class="form-group col-md-3">
                  <label for="ringSize" class="control-label">Ring Size</label>
                  <input type="text" class="form-control" id="ringSize" name="ringSize" />
                </div>
                <div class="form-group col-md-3">
                  <label for="styleCode" class="control-label">Style Code</label>
                  <input type="number" readonly class="form-control" id="styleCode" name="styleCode" required="" />

                </div>
                <div class="form-group col-md-8">
                  <label for="comments" class="control-label">Comments</label>
                  <textarea class="form-control" id="comments" name="comments" row="2"></textarea>
                </div>
                <div class="form-group col-md-2">
                  <label for="mu" class="control-label">MU</label>
                  <input type="text" class="form-control" id="mu" name="mu" />
                </div>
                <div class="form-group col-md-2">
                  <label for="costPrice" class="control-label">Cost Price</label>
                  <input type="text" class="form-control" id="costPrice" name="costPrice" />
                </div>

              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="btn_add" class="btn crud-submit btn-success">Save</button>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Edit Item Modal -->
  <div class="modal fade" id="edit-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Product</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="src/scripts/update.php" method="put" id="frm_edit" name="frm_edit" enctype="multipart/form-data">
            <input type="hidden" name="id" class="edit-id">
            <div class="row">
              <input type="hidden" value="edit" name="action" id="action">
              <input type="hidden" value="0" name="edit_id" id="edit_id">
              <div class="form-group col-md-3">
                <label for="edit_itemId">Item Id:</label>
                <input type="text" class="form-control" id="edit_itemId" name="edit_itemId" required />
              </div>
              <div class="form-group col-md-3" id="edit_vendorDiv">
                <label for="edit_vendor" class="control-label">Vendor</label>
                <input type="text" class="form-control" id="edit_vendor" name="edit_vendor" list="vendorList" />
              </div>
              <div class="form-group col-md-2">
                <label for="edit_vendorCode">Vendor Code:</label>
                <input type="text" class="form-control" id="edit_vendorCode" name="edit_vendorCode" data-error="Please enter Item Id." />
              </div>

              <div class="form-group col-md-4">
                <label for="edit_itemPic" class="control-label">Item Pic:</label>
                <input type="file" class="form-control-file" id="edit_itemPic" name="edit_itemPic">
              </div>
              <div class="form-group col-md-6">
                <label for="edit_description" class="control-label">Description</label>
                <textarea class="form-control" id="edit_description" name="edit_description" row="2"></textarea>
              </div>
              <div class="form-group col-md-6" id=edit_itemTypeCodeDiv>
                <label for="edit_itemTypeCode" class="control-label">Item Type</label>
                <input type="text" class="form-control" id="edit_itemTypeCode" name="edit_itemTypeCode" list="itemTypeCodeList" />
              </div>
              <div class="form-group col-md-3">
                <label for="edit_grossWt" class="control-label">Gross Wt</label>
                <input type="number" step="any" class="form-control" id="edit_grossWt" name="edit_grossWt" />
              </div>
              <div class="form-group col-md-3">
                <label for="edit_diaWt" class="control-label">Dia Wt</label>
                <input type="number" step="any" class="form-control" id="edit_diaWt" name="edit_diaWt" />
              </div>
              <div class="form-group col-md-3">
                <label for="edit_cstoneWt" class="control-label">cstone Wt</label>
                <input type="number" step="any" class="form-control" id="edit_cstoneWt" name="edit_cstoneWt" />
              </div>
              <div class="form-group col-md-3">
                <label for="edit_goldWt" class="control-label">Gold Wt</label>
                <input type="number" step="any" class="form-control" id="edit_goldWt" name="edit_goldWt" />
              </div>
              <div class="form-group col-md-2">
                <label for="noOfDia" class="control-label">Total Dia</label>
                <input type="number" step="1" class="form-control" id="edit_noOfDia" name="edit_noOfDia" />
              </div>
              <div class="form-group col-md-2">
                <label for="edit_sellPrice" class="control-label">Sell Price</label>
                <input type="number" class="form-control" id="edit_sellPrice" name="edit_sellPrice" required />
              </div>
              <div class="form-group col-md-2">
                <label for="edit_curStock" class="control-label">Current Stock</label>
                <input type="number" class="form-control" id="edit_curStock" name="edit_curStock" />
              </div>
              <div class="form-group col-md-3">
                <label for="edit_ringSize" class="control-label">Ring Size</label>
                <input type="text" class="form-control" id="edit_ringSize" name="edit_ringSize" />
              </div>
              <div class="form-group col-md-3">
                <label for="edit_styleCode" class="control-label">Style Code</label>
                <input type="number" readonly class="form-control" id="edit_styleCode" name="edit_styleCode" required />
              </div>
              <div class="form-group col-md-8">
                <label for="edit_comments" class="control-label">Comments</label>
                <textarea class="form-control" id="edit_comments" name="edit_comments" row="2"></textarea>
              </div>
              <div class="form-group col-md-2">
                <label for="edit_mu" class="control-label">MU</label>
                <input type="text" class="form-control" id="edit_mu" name="edit_mu" />
              </div>
              <div class="form-group col-md-2">
                <label for="edit_costPrice" class="control-label">Cost Price</label>
                <input type="text" class="form-control" id="edit_costPrice" name="edit_costPrice" />
              </div>

            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" id="btn_edit" name="btn_edit" class="btn btn-warning">UPDATE</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  </div>

  <!-- Filter Modal -->
  <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-slideout" role="document" style="max-width: 400px !important;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Extra Filters</h5>
          <button type="button" class="close" data-dismiss="modal" onclick="restoreFilterForm()" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" id="advFilterForm" name="advFilterForm">
            <div class="row">
              <!-- <div class="form-group col">
                <label for="itemId">Date:</label>
                <input type="text" autocomplete="off" class="form-control searchField" id="s_daterange" id="s_daterange" autocomplete="false" style="padding: .375rem .15rem;" value="01/01/2015 - 01/31/2015" />
              </div>
              <div class="w-100"></div> -->
              <!-- <div class="form-group col">
                <label for="itemId">Item Id&nbsp;</label>
                <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_itemId" name="s_itemId" />
              </div>
              <div class="form-group col">
                <label for="vendor">V Id:</label>
                <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_vendor" name="s_vendor" />
              </div>
              <div class="w-100"></div>
              <div class="form-group col extention">
                <label for="itemIdExt">Enter Multiple Item IDs <b>(comma separated)</b></label>
                <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_itemIdExt" name="s_itemIdExt" />
              </div>
              <div class="w-100"></div>
              <div class="form-group col">
                <label for="itemId">Item Type:</label>
                <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_itemTypeCode" name="s_itemTypeCode" />
              </div>
              <div class="w-100"></div>
              <div class="form-group col">
                <label for="itemId">Description:</label>
                <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_description" name="s_description" />
              </div>
              <div class="w-100"></div> -->
              <div class="form-group col" style="padding-right: 0px;">
                <label for="itemId">Dia</label>
                <input type="text" class="form-control" style="padding: .375rem .15rem;" id="s_diaWt" name="s_diaWt" />
              </div>
              <div class="form-group col" style="padding-right: 0px;">
                <label for="itemId">cstone</label>
                <input type="text" class="form-control" style="padding: .375rem .15rem;" id="s_cstoneWt" name="s_cstoneWt" />
              </div>
              <div class="form-group col" style="padding-right: 0px;">
                <label for="itemId">Gold</label>
                <input type="text" class="form-control" style="padding: .375rem .15rem;" id="s_goldWt" name="s_goldWt" />
              </div>
              <div class="form-group col">
                <label for="itemId">Size:</label>
                <input type="text" class="form-control" style="padding: .375rem .15rem;" id="s_ringSize" name="s_ringSize" />
              </div>
              <div class="w-100"></div>
              <!-- <div class="form-group col">
                <label for="itemId">vCode:</label>
                <input type="text" class="form-control" style="padding: .375rem .15rem;" id="s_vendorCode" name="s_vendorCode" />
              </div> -->
              <!-- <div class="form-group col">
                <label for="itemId">Style:</label>
                <input type="text" class="form-control searchField" style="padding: .375rem .15rem;" id="s_styleCode" name="s_styleCode" />
              </div> -->
              <div class="w-100"></div>
              <div class="form-group col" style="margin-bottom: 0px;">
                <label for="itemId">Gross Weight:</label>
              </div>
              <div class="w-100"></div>
              <div class="form-group col-2" style="padding-right: 0rem;">
                <input oninput="fromInputTf(this)" type="text" id="from_grossWt" name="from_grossWt" data-slider="grossWt" value="" style="width: 100%;font-size: 0.8rem;"/>
                <input type="hidden" id="s_grossWt" name="s_grossWt" value=""/>
              </div>
              <div class="form-group col-8" style="padding: 0rem 0.5rem;margin-top: -1.5rem;">
                <input type="text" class="js-range-slider" id="grossWtSlider" name="grossWtSlider" value=""/>
              </div>
              <div class="form-group col-2" style="padding-left: 0rem;">
                <input oninput="toInputTf(this)" type="text" id="to_grossWt" name="to_grossWt" data-slider="grossWt" value="" style="width: 100%;font-size: 0.8rem;"/>
              </div>
              <div class="w-100"></div>
              <div class="form-group col" style="margin-bottom: 0px;">
                <label for="itemId">Sell Price:</label>
              </div>
              <div class="w-100"></div>
              <div class="form-group col-2" style="padding-right: 0rem;">
                <input oninput="fromInputTf(this)" type="text" id="from_sellPrice" name="from_sellPrice" data-slider="sellPrice" value="" style="width: 100%;font-size: 0.8rem;"/>
                <input type="hidden" id="s_sellPrice" name="s_sellPrice" value=""/>
              </div>
              <div class="form-group col-8" style="padding: 0rem 0.5rem;margin-top: -1.5rem;">
                <input type="text" class="js-range-slider" id="sellPriceSlider" name="sellPriceSlider" value=""/>
              </div>
              <div class="form-group col-2" style="padding-left: 0rem;">
                <input oninput="toInputTf(this)" type="text" id="to_sellPrice" name="to_sellPrice" data-slider="sellPrice" value="" style="width: 100%;font-size: 0.8rem;"/>
              </div>
              <div class="w-100"></div>
              <div class="form-group col" style="margin-bottom: 0px;">
                <label for="itemId">Available Qty:</label>
              </div>
              <div class="w-100"></div>
              <div class="form-group col-2" style="padding-right: 0rem;">
                <input oninput="fromInputTf(this)" type="text" id="from_curStock" name="from_curStock" data-slider="curStock" value="" style="width: 100%;font-size: 0.8rem;"/>
                <input type="hidden" id="s_curStock" name="s_curStock" value=""/>
              </div>
              <div class="form-group col-8" style="padding: 0rem 0.5rem;margin-top: -1.5rem;">
                <input type="text" class="js-range-slider" id="curStockSlider" name="curStockSlider" value=""/>
              </div>
              <div class="form-group col-2" style="padding-left: 0rem;">
                <input oninput="toInputTf(this)" type="text" id="to_curStock" name="to_curStock" data-slider="curStock" value="" style="width: 100%;font-size: 0.8rem;"/>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-danger reset-item" id="advFilterReset" name="advFilterReset">Reset</button>
          <button type="button" class="btn btn-outline-success" id="applyFilterButton" name="applyFilterButton">Apply</button>
        </div>
      </div>
    </div>
  </div>


  <a id="back-to-top" href="#" class="btn btn-primary btn-md back-to-top" role="button" title="Click to return on the top page" data-toggle="tooltip" data-placement="left">^</a>
  </div>

  <script>
    $(window).on('load', function() {
      $('.lazy').Lazy();
    });
  </script>
  <script src="src/js/item-ajax.js"></script>

  <?php
  if ($_SESSION["msg"] == 1) {
  ?>
    <script>
      toastr.success('You are successfully logged in!', 'Welcome :)', {
        timeOut: 5000,
        closeButton: true
      });
    </script>
  <?php
    $_SESSION["msg"] = 0;
  }
  ?>
</body>

</html>