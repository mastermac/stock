<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
}
$usertype = '';
require('../../src/scripts/db_config.php');
if ($_SESSION['usertype'] >= 1)
    $usertype = ' and userid=' . $_SESSION['userid'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SilverApp | Stock List</title>
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="../../src/css/jquery.fancybox.min.css" />

    <link rel="stylesheet" href="css/fullview.css" />
</head>

<body id="OpportunityDetailDiv">
    <style>
        #filterModal .modal-content,
        #singleSelectPicker .modal-content {
            align-self: flex-end;
            top: 1rem;
            height: 400px;
        }

        #filterModal .modal-dialog,
        #singleSelectPicker .modal-dialog {
            margin: 0rem;
        }
    </style>
    <script> var userNamephp='<?php echo $_SESSION['username']; ?>'; </script>
    <header>
    </header>
    <searchmodel></searchmodel>
    <input id="pageoffset" type="hidden" value="0" />
    <input id="pagesize" type="hidden" value="20" />
    <input id="totalCnt" type="hidden" value="0" />
    <div class="container-fluid pr-0 pl-0 pull-to-refresh-material" style="margin-top:75px;">
        <div class="pull-to-refresh-material__control">
            <svg class="pull-to-refresh-material__icon" fill="#4285f4" width="24" height="24" viewBox="0 0 24 24">
                <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" />
                <path d="M0 0h24v24H0z" fill="none" />
            </svg>

            <svg class="pull-to-refresh-material__spinner" width="24" height="24" viewBox="25 25 50 50">
                <circle class="pull-to-refresh-material__path" cx="50" cy="50" r="20" fill="none" stroke="#4285f4" stroke-width="4" stroke-miterlimit="10" />
            </svg>
        </div>

        <div class="addSkeleton" template="pipelineSkeleton">
            <div id="pipeLine" class="col-xs-8 col-md-12 col-lg-12 pipeline" style="display:none;"></div>
        </div>

        <div class="card">
            <input type="hidden" id="opportunityAppLink" name="opportunityAppLink" value="../opportunity-ux/">
            <div style="background-color: #EEE; width: 100%; padding: 0.25rem;" id="filterDiv">
                <span id="filter" onclick='LoadModal()' data-toggle='modal' data-target='#filterModal'>
                    <img src='img/filter_default.png' height='25' width='25' style='float:right; margin-right: 0.5rem;' />
                </span>
                <span id="sort" onclick='LoadSortModal()' data-toggle='modal' data-target='#singleSelectPicker'>
                    <img src='img/sort_default.png' height='25' width='25' style='float:right; margin-right: 0.5rem;' />
                </span>
            </div>

            <div class="col-lg-12 col-md-12 col-xs-8 col-md-auto pr-0 pl-0">
                <div class="addSkeleton" template="detailsListSkeleton">
                    <div class="container-fluid pl-0 pr-0" id="divOppList" style="display:none"></div>
                </div>
            </div>
            <div id="modalview"></div>
            <div class="justify-content-center" id="retryDiv" style="margin: 1rem;display:none">
            </div>
        </div>
    </div>
    <!-- SCRIPTS -->
    <script src="js/filter.js"></script>
    <script src="js/detail.js"></script>
    <script src="js/mdb.js"></script>
    <script src="../../src/js/jquery.fancybox.min.js"></script>
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
        $.fancybox.defaults.animationEffect = "circular";
        // $.fancybox.defaults.arrows = true;
        $.fancybox.defaults.idleTime = 60;
        $.fancybox.defaults.buttons = [
            // "zoom",
            "share",
            // "slideShow",
            "fullScreen",
            "download",
            "thumbs",
            // "arrowLeft",
            // "arrowRight",
            "close",
        ];

    </script>
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