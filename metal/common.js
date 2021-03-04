$("#logout").click(function(e) {
    $('.ajax-loader').css("visibility", "visible");
    $.ajax({
        type: "POST",
        url: url + '../src/scripts/checkLogin.php',
        data: {
            fromLogout: "key5678",
            fromLogin: "key12"
        }
    }).done(function(data) {
        $('.ajax-loader').css("visibility", "hidden");
        window.location.href = "/stock/login.php?fromPanel=true";
    });
});
