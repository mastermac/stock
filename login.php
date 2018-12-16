<?php
  session_start();
  if(isset($_SESSION['userid'])){
    header('Location: panel.php');
  }
  $_SESSION['msg']=0;
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
    <link href="src/css/bootstrap.min.css" rel="stylesheet">
    <link href="src/css/login.css" rel="stylesheet">
    <link href="src/css/toastr.min.css" rel="stylesheet">
    <script type="text/javascript" src="src/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="src/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="src/js/toastr.min.js"></script>

  </head>

  <body>

    <div class="container">
      <form class="form-signin" name="lgform" id="lgform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        <div class="checkbox" style="display: none;">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div>
  </body>
  <script>
  $(document).ready(function (e) {
	$("#lgform").on('submit',(function(e) {
    e.preventDefault();
    var formData=new FormData(this);
    formData.append('fromLogin','key1234');
    $.ajax({
      url: "src/scripts/checkLogin.php",
			type: "POST",
			data:  formData,
			contentType: false,
			processData:false,
			success: function(data)
		    {
          var resp=JSON.parse(data);
          if(resp.error){
            toastr.error('Please check your email or password!', 'Access Denied', {timeOut: 5000, closeButton: true, progressBar: true});
          }
          else if(!resp.error){
            window.location.href = "panel.php";
          }
		    }});
    }));
  });

  </script>
  <?php
  if(isset($_GET["fromPanel"])&&$_GET["fromPanel"]){
    ?>
    <script>
                toastr.success('You are successfully logged out!', 'Thank You!', {timeOut: 5000});
               
</script>
<?php 
  }
  ?>
</html>
