/*Header Script start*/
$(document).click(function (event) {
    var sideNav = document.getElementById('sidenav-overlay');
    if (sideNav != null) {
        if ($(sideNav).hasClass('velocity-animating')) {
            $('body').removeClass("lock-scroll");
        } else {
            $('body').addClass("lock-scroll");
        }
    }
}); 
 
function InitializeHeaderData() {
    var objHeaderData = null;
    var PageName = window.location.pathname;
    PageName = PageName.substring(PageName.lastIndexOf('/') + 1);
    var KeyName = PageName.replace('.html', '') + '_ManagerHeaderData';
    //console.log('HeaderLog ' + KeyName);

    $("#headerText").width($("#headerSection").width() - ($("#headerbackButton").width() + $("#headerImg").width() + $("#hamburgerSection").width() + 15));

    localforage.getItem(KeyName).then(function (value) {
        //console.log('HeaderLog ' + value);
        if (value == null) {
            localforage.getItem('ManagerHeaderData').then(function (value1) {
                //console.log('HeaderLog ' + value1);
                if (value1 != null) { 
                    SetHeaderData(value1);
                }
                else { 
                    localforage.getItem('ManagerUserState').then(function (value2) {
                        if (value != null) {
                            var userStateValue = JSON.parse(value2);
                            $("#header_row1").html(userStateValue.FirstName + " " + userStateValue.LastName);
                        }
                    });
                    InitializeHeaderDefaultData();
                }
            });
        }
        else {
            SetHeaderData(value);
        }
    });
}

function InitializeHeaderDefaultData() {
    var ajaxCall = fetchData({ BadgeNumber: userState.BadgeNumber }, null, config[env].InitializeHeaderDefaultData, false);
    ajaxCall.done(function (data) {
        try {
            //console.log('HeaderLog Response' + data);
            if (data != null && data.name.length > 0) {
                var myData = {
                    "ImageBytes": data.profilePhoto,
                    "UserName": data.name,
                    "Designation": data.title,
                    "CountValue": "0",
                    "CountText": ""
                };
                //console.log('HeaderLog myData ' + myData);

                localforage.setItem('ManagerHeaderData', myData).then(function (value5) {
                    // Do other things once the value has been saved.
                    //console.log('HeaderLog ' + value5);
                    if (!window.location.pathname.includes("headersearch.html"))
                        InitializeHeaderData();
                    else
                        changeSearchType();
                }).catch(function (err) {
                    // This code runs if there were any errors
                    console.log(err);
                });
            }

        } catch (e) {
            console.log('HeaderLog Error 1 ' + e.message);

        }
    });
}

function logoutClick() {
    localforage.clear().then(function () {
        localStorage.removeItem('fromXamarin');
        console.log("Cache Cleared");
        window.location.assign("http://logout");
    }).catch(function (err) {
        // This code runs if there were any errors
        console.log(err);
    });
}

function submitFeedback(source) {
    var ratingValue = $("input[name='rating']:checked").val();
    var feedbackValue= $('#feedbackComments').val();
    if(ratingValue==undefined){
        toastr["warning"]("Please select a rating...");
    }
    else if(feedbackValue.length==0){
        toastr["warning"]("Please provide some comments...");
    }
    else{
        var queryParams;
        if(source=="AE View"){
            queryParams={
                Rating: ratingValue,
                Feedback: feedbackValue,
                Username: aeUserState.FirstName+" "+aeUserState.LastName,
                BadgeNumber: aeUserState.BadgeNumber,
                AppVersion: aeUserState.AppVersion,
                Instance: aeUserState.SFDCInstance,
                Source: source,
                Timestamp: 0
            };
        }
        else{
            queryParams={
                Rating: ratingValue,
                Feedback: feedbackValue,
                Username: userState.FirstName+" "+userState.LastName,
                BadgeNumber: userState.BadgeNumber,
                AppVersion: userState.AppVersion,
                Instance: userState.SFDCInstance,
                Source: source,
                Timestamp: 0
            };
        }

        var ajaxCall = fetchAttainmentData(null, config[env].AddFeedback+"?"+$.param(queryParams), true);
        ajaxCall.done(function(data){
            if(source=="AE View"){
                localforage.removeItem('AEUserState');
                window.location.href = "http://closefeedbackpopuponsuccess#";
                //toastr["success"]("Thank You for your feedback!");            
                //setTimeout(function () {  }, 1000); 
            }else{
                closeFeedbackForm();
                toastr["success"]("Thank You for your feedback!");
            }
        });
    }
}

function closeFeedbackForm(closefrom){
    if(closefrom=="AE View"){
        localforage.removeItem('AEUserState');
        window.location.href = "http://closefeedbackpopup#";
    }
    else {
        $('body').removeClass("lock-scroll");
        $('#feedbackComments').val('');
        $("#feedbackCommentsLabel").removeClass("active");
        $("#feedbackModal .rating > input:checked").prop('checked', false);
        $('#feedbackModal').modal('hide');
    }
} 

function showFeedbackForm() { 
    feedbackTemplate = feedbackTemplate.replace("{source}","'Manager View'")
    feedbackTemplate = feedbackTemplate.replace("{closefrom}","'Manager View'")
    $("#feedbackDiv").html(feedbackTemplate);
    $("#feedbackModal .rating>label").removeAttr('style');
    document.getElementById("slide-out").style.transform = "translateX(100%)";
    $("#sidenav-overlay").remove();

    $('#feedbackModal').modal({
        backdrop: false
    });
}

function backButtonClick() {
    console.log("Back Button Pressed");
    console.log("Current Url: " + window.location.href);
    if (window.location.href.includes("dashboard.html") && $("#liGlobalSearch").is(":visible")) {
        hideHeaderSearchBar();
    }
    else if (window.location.href.toString().indexOf("#")>0) {
        $("#backButton").prop("onclick", null);
        var count = window.location.href.toString().split("#").length; 
        window.location.assign("http://goBack#"+count);
    }
    // else if (window.location.href.toString().endsWith("#")) {
    //     console.log("Current Url Changed to GoBack#Twice");
    //     window.location.assign("http://goBack#2");
    // }
    else {
        $("#backButton").prop("onclick", null);
        console.log("Current Url Changed to GoBack");
        window.location.assign("http://goBack");
    }
}

function SetHeaderData(objHeaderData) {
    //console.log('HeaderLog ' + 'SetHeaderData - ')

    objHeaderData = JSON.parse(JSON.stringify(objHeaderData));
    //console.log(objHeaderData);
    //console.log('HeaderLog ' + 'SetHeaderData - ' + objHeaderData.UserName + ' ' + objHeaderData.Designation + ' ' + objHeaderData.ImageBytes);

    $("#header_row1").removeClass("headerskeletonsLine");
    $("#header_row2").removeClass("headerskeletonsLine");

    $("#header_row1").html(objHeaderData.UserName);
    $("#header_row2").html(objHeaderData.Designation);
    $("#header_row31").html(objHeaderData.CountValue);
    $("#header_row32").html(objHeaderData.CountText);
    if (objHeaderData.CountValue == null || objHeaderData.CountValue == '0')
        $("#header_row31").html("");
    if (objHeaderData.CountText == null || objHeaderData.CountText.length == 0)
        $("#header_row32").html("");
    var HideProfileImg = false;
    if (objHeaderData.HideProfileImg != null)
        HideProfileImg = objHeaderData.HideProfileImg;

    var imgProfile = $('#imgProfile');

    if (objHeaderData.ImageBytes != null && objHeaderData.ImageBytes == "Hide_Image") {
        $("#headerImg").hide();
    }
    else if (!HideProfileImg) {

        if (!objHeaderData.ImageBytes || objHeaderData.ImageBytes == "null" || objHeaderData.ImageBytes.includes("SalesForceDefaultImage")) {
            imgProfile.attr("src", "../common-ux/img/SalesForceDefaultImage.png");
        }
        else {
            imgProfile.attr("src", 'data:image/jpeg;base64,' + objHeaderData.ImageBytes);
        }
    }
    else {
        imgProfile.hide();
    }
}
function settingsClick() {
    localforage.setItem("settings" + "_ManagerHeaderData", {
        "ImageBytes": "",
        "UserName": "Settings",
        "Designation": "",
        "CountValue": "",
        "CountText": "",
        "HideProfileImg": true
    }).then(function () {
        window.location.href = "../common-ux/settings.html";
    });


}

/*Header Script end*/