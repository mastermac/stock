<?php
session_start();
if (isset($_SESSION['userid'])) {
  header('Location: panel.php');
}
$_SESSION['msg'] = 0;
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Shubham Gupta">
  <link rel="icon" href="">

  <title>SilverApp | Signin</title>
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.2/css/all.css">
  <link href="css/mdb.min.css" rel="stylesheet">
  <link href="css/modules/animations-extended.css" rel="stylesheet">
  <style>
    .md-form .form-control{
      padding: 0.3rem 2px 0.55rem 1px !important;
    }
    
    </style>

</head>

<body>
  <div class="container-fluid">
    <div class="col-xl-4 pt-2" id="formParentDiv">
      <div class="card card-cascade narrower">

        <div class="view view-cascade gradient-card-header purple-gradient">

          <!-- Title -->
          <h1 class="card-header-title" onclick='window.location.href = "index.html";' style="cursor: pointer;">St<i class="fas fa-search" style="font-size: 0.6em;"></i>ck</h1>
          <!-- Subtitle -->
          <h5 class="mb-0 pb-1">Please Sign-In!</h5>
        </div>

        <div class="card-body card-body-cascade px-lg-5 pt-0 pb-0">

          <form name="lgform" id="lgform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="md-form mb-0" style="color: #757575;">
            <div class="form-row" id="newTransaction">
              <div class="col-12">
                <div class="md-form">
                <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
                <label for="email">Email address</label>
                </div>
              </div>
              <div class="col-12">
                <div class="md-form">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <label for="password">Password</label>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="col-12">
                <div class="md-form mt-0">
                  <button class="btn blue-gradient btn-block btn-rounded hoverable" type="submit" id="sherlocked">
                    Sign In
                  </button>
                </div>
              </div>
              <div class="col-12">
                <div class="md-form mt-0">
                  <button class="btn peach-gradient btn-block btn-rounded hoverable" type="button" onclick="location.reload();">
                    Reset
                  </button>
                </div>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="js/mdb.js"></script>
</body>
<script>
  $(document).ready(function(e) {
    $("#lgform").on('submit', (function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      formData.append('fromLogin', 'key1234');
      $.ajax({
        url: "../../src/scripts/checkLogin.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(data) {
          var resp = JSON.parse(data);
          if (resp.error) {
            toastr.error('Please check your email or password!', 'Access Denied', {
              timeOut: 5000,
              closeButton: true,
              progressBar: true
            });
          } else if (!resp.error) {
            window.location.href = "panel.php";
          }
        }
      });
    }));
  });
</script>
<?php
if (isset($_GET["fromPanel"]) && $_GET["fromPanel"]) {
?>

  <script>
    toastr.success('You are successfully logged out!', 'Thank You!', {
      timeOut: 5000
    });
  </script>
<?php
}
?>

</html>