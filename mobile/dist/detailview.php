<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
}
$usertype = '';
require('../../src/scripts/db_config.php');
if ($_SESSION['usertype'] == 1)
    $usertype = ' and userid=' . $_SESSION['userid'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <!--<script src="../common-ux/js/metrics.js"></script>
    <script src="//nexus.ensighten.com/dell/externalDev/Bootstrap.js"></script>-->
    <title>MySales Manager | Opportunity Details</title>
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" href="css/mdb.min.css">

    <link rel="stylesheet" href="css/detailview.css" />
    <script> var userNamephp='<?php echo $_SESSION['username']; ?>'; </script>

</head>

<body style="background-color:#EEEEEE">
    <header></header>
    <searchmodel></searchmodel>
    <div class="addSkeleton" template="detailsViewSkeleton">
        <div class="container-fluid pr-0 pl-0 pull-to-refresh-material" style="margin-top:75px;display:none" id="OpportunityDetailViewDiv">
            <div class="pull-to-refresh-material__control">
                <svg class="pull-to-refresh-material__icon" fill="#4285f4" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" />
                    <path d="M0 0h24v24H0z" fill="none" />
                  </svg>
          
                  <svg class="pull-to-refresh-material__spinner" width="24" height="24" viewBox="25 25 50 50">
                    <circle class="pull-to-refresh-material__path" cx="50" cy="50" r="20" fill="none" stroke="#4285f4" stroke-width="4" stroke-miterlimit="10" />
                  </svg>
            </div>
  
            <div class="classic-tabs">
                <ul class="nav tabs-grey" id="details-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link  waves-light active show" id="summary-tab" data-toggle="tab" href="#summary"
                           role="tab" aria-controls="summary" aria-selected="true">Summary</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link waves-light" id="history-tab" data-toggle="tab" href="#history"
                           role="tab" aria-controls="history" aria-selected="false">History</a>
                    </li>
                </ul>
                <div class="tab-content card" id="details-tab-content">
                    <div class="tab-pane fade active show" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                        <div class="col-lg-12 col-md-12 col-xs-8 col-md-auto pr-0 pl-0">
                            <div class="container-fluid pl-0 pr-0" id="divOppList">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                        <div class="col-lg-12 col-md-12 col-xs-8 col-md-auto pr-0 pl-0">
                            <div class="container-fluid pl-0 pr-0" id="divProductList">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="justify-content-center" id="retryDiv" style="margin: 1rem;display:none">
            </div>
        </div>
    </div>
        <!-- Start your project here-->
        <!-- /Start your project here-->
        <!-- SCRIPTS -->
        <script src="js/detailview.js"></script>
        <script src="js/mdb.js"></script>
        <script>
            pullToRefresh({
                container: document.querySelector('.pull-to-refresh-material'),
                animates: ptrAnimatesMaterial,
        
                refresh() {
                    DoForceRefresh(false);
                    return new Promise(resolve => {
                        setTimeout(resolve, 1500);
                    })
                }
            });
        </script>

</body>

</html>