var deviceAgent = navigator.userAgent.toLowerCase();
var agentID = deviceAgent.match(/(iphone|ipod|ipad)/);

jQuery.fn.scrollCenter = function (elem, speed) {
    var active = jQuery(this).find(elem);
    var activeWidth = active.width() / 2; // get active width center
    var pos = active.position().left + activeWidth; //get left position of active li + center position
    var elpos = jQuery(this).scrollLeft(); // get current scroll position
    var elW = jQuery(this).width();
    pos = pos + elpos - elW / 2; // for center position if you want adjust then change this

    jQuery(this).animate({
        scrollLeft: pos
    }, speed == undefined ? 1000 : speed);
    return this;
};
function SetL1Badge(badgeNo) {
    try {
        if (badgeNo == "") {
            if (window.location.pathname.includes("dashboard.html"))
                localforage.removeItem("L1BadgeNo");
        }
        else
            localforage.setItem("L1BadgeNo", badgeNo);
    } catch (e) {
        logToKibana(e);
    }
}
/*Header Search popup start*/
var hSearchType = 0;
var hSearchText = "";

window.addEventListener('online',  function(){
    this.setTimeout(function(){
        $("#managerMsg").css("bottom","0rem");
    },3500);
});
window.addEventListener('offline', function(){
    $("#managerMsg").css("bottom","1rem");
});

function hideHeaderSearchBar() {
    if (window.location.pathname.includes("dashboard.html")) {
        $("#backButton").hide();
    }
    $("#headerText").show();
    $("#headerImg").show();
    $("#divHBMenuIcon").show();
    $("#liGlobalSearch").hide();
    $("#txtGlobalSearch").val('');
    hSearchType = '';
    hSearchText = '';
}
function clearSearchText() {
    try {
        $("#txtGlobalSearch").val('');
        hSearchText = '';
        searchText = '';
        if (hSearchType == 1 || searchType == 1)
            $("#txtGlobalSearch").attr("placeholder", "Search by Account");
        else if (hSearchType == 2 || searchType == 2)
            $("#txtGlobalSearch").attr("placeholder", "Search by Opportunity");
    } catch (e) {
        logToKibana(e);
    }
}
function showsearch() {
    if (Offline.state == "up") {
        try {
            $("body").addClass("lock-scroll");
            SelectSearchBy();
            $("#headerText").hide();
            $("#headerImg").hide();
            $("#liGlobalSearch").show();
    
            // if ($("#liGlobalSearch").css('display') != 'none') {
                hSearchText = $("#txtGlobalSearch").val();
                if (hSearchText.trim().length == 0) {
                    toastr["warning"]("Enter search key");
                    return;
                }
                else if (hSearchText.trim().length < 3) {
                    toastr["warning"]("Enter 3 character to search");
                    return;
                }
                if (!window.location.pathname.includes("headersearch.html")) {
                    localforage.setItem("HDSearch", {
                        "SearchType": hSearchType,
                        "SearchText": hSearchText,
                    }).then(function () {
                        window.location.assign("../headersearch.html");
                    });
                }
                else {
                    localforage.getItem("HDSearch").then(function (value) {
                        var d = JSON.parse(JSON.stringify(value));
                        if (d != null) {
                            hSearchType = d.SearchType;
                            pageoffset = 0;
                            dataCnt = 0;
                            $("#divSearchList").html("");
                            CallHeaderSearch(hSearchText, hSearchType);
                        }
                    });
                }
            // } 
            // else {
            //     $('#globalSearchModal').on('hidden.bs.modal', function () {
            //          $("body").removeClass("lock-scroll");
            //     });
            //     $("#globalSearchModal").modal("show");
            // }
        } catch (e) {
            console.log(e.message);
            logToKibana(e);
        }
    }
}
function createHeaderSearch(header) {
    try {
        $("searchmodel").load(header.replace("header", "searchby") + " #globalSearchModal", function () {
            if (agentID) {
                if ('ontouchstart' in window) {
                    $(document).on('focus', '#txtGlobalSearch', function () {
                        $('#header').css('position', 'absolute');
                    }).on('blur', '#txtGlobalSearch', function () {
                        $('#header').css('position', '');
                    });
                }
            }
        });

        $("#gSearchOK").click(function () { changeSearchType(); });
    } catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
}
function SelectSearchBy() {
    $("#divGlobalSearchType").find(".search-slide")
        .toggleClass("active inactive");
}
function changeSearchType() {
    try {
        
        if (window.location.pathname.includes("dashboard.html")) {
            $("#backButton").show();
        }
        $("#headerText").hide();
        $("#headerImg").hide();
        $("#liGlobalSearch").show();
        if (window.location.pathname.includes("headersearch.html")) {
            if (hSearchType == 1) {
                $("#divGlobalSearchType").find(".search-slide").removeClass("inactive");
                $("#divGlobalSearchType").find("#searchAccType").addClass("active");

            }
            else if (hSearchType == 2) {
                $("#divGlobalSearchType").find(".search-slide").removeClass("inactive");
                $("#divGlobalSearchType").find("#searchOppType").addClass("active");
            }

        }
        else {
            var actDiv = $("#divGlobalSearchType").find(".active div");
            if (actDiv.length > 0) {
                if (actDiv.html() == "Accounts")
                    hSearchType = 1;
                else if (actDiv.html() == "Opportunity")
                    hSearchType = 2;
            }
        }
        if (hSearchType == "1")
            $("#txtGlobalSearch").attr("placeholder", "Search by Account");
        else if (hSearchType == "2")
            $("#txtGlobalSearch").attr("placeholder", "Search by Opportunity");
        $("#txtGlobalSearch").focus();
        $("#txtGlobalSearch").unbind("keyup");
        $("#txtGlobalSearch").on("keyup", function (event) {
            //var el = $(this)
            //var currentVal = el.val();
            event.preventDefault();
            if (event.keyCode === 13) {
                showsearch();
            }
            //$(this).val(currentVal);
        });
        
        $("#txtGlobalSearch").on("keyup", function (event) {
           ( $("#txtGlobalSearch").val().trim().length > 0) ? $("#divHClearSearchIcon").show() : $("#divHClearSearchIcon").hide();
        });
        if ($("#txtGlobalSearch").val() != null && $("#txtGlobalSearch").val() != "null")
            ($("#txtGlobalSearch").val().trim().length > 0) ? $("#divHClearSearchIcon").show() : $("#divHClearSearchIcon").hide();
    }
    catch (e) {
        alert(e.message);
        logToKibana(e);
    }
}

/* Header Search popup end */
/* Ajax Call Start*/
var errorMsg = "Error while processing";
function fetchData(queryData, msgCtr, dataURL, includeToken) {
    dataURL=rootUri+dataURL;
    //try {
        if (includeToken === undefined) 
            includeToken = true;
        if (includeToken) { 
            queryData["SfdcInstance"] = SFDCInstance;
            queryData["ShouldProtoSerialize"] = false;
            queryData["SalesforceUrl"] = managerTokens.SFDCInstanceUrl;
            queryData["SalesforceAccessToken"] = managerTokens.SFDCToken;
        }

        // Return the $.ajax promise
        var dfd = $.Deferred();
        $.ajax({
            data: queryData,
            dataType: 'json',
            url: dataURL,
            tryCount: 0,
            retryLimit: 3,
            success: function(data){
                dfd.resolve(data);
            },
            error: function (jqXHR, exception) {
                if (this.tryCount == 0) {
                    logAjaxErrorToKibana(jqXHR, exception, dataURL, "");
                }
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) { 
                    if(jqXHR.status == 401){
                        console.log("401 Error");
                        //token not yet renewed then wait for 2s and then set new token
                        if (queryData["SalesforceAccessToken"] == managerTokens.SFDCToken) {
                            setTimeout(function () {
                                queryData["SalesforceAccessToken"] = managerTokens.SFDCToken;
                            }, 2000);
                        }
                        else {
                            queryData["SalesforceAccessToken"] = managerTokens.SFDCToken;
                        }
                    }
                    $.ajax(this);
                }
                else{
                    dfd.reject(jqXHR);
                }
            }
        });
        return dfd.promise();
}
function fetchOppData(queryData, msgCtr, dataURL, includeToken) {
    dataURL=rootUri+dataURL;
    //try {
    if (includeToken === undefined)
        includeToken = true;
    if (includeToken) {
        queryData["SfdcInstance"] = SFDCInstance;
        queryData["ShouldProtoSerialize"] = false;
        queryData["SalesforceUrl"] = managerTokens.SFDCInstanceUrl;
        queryData["SalesforceAccessToken"] = managerTokens.SFDCToken;
    }
    // Return the $.ajax promise
    var dfd = $.Deferred();
    $.ajax({
        url: dataURL,
        headers: {
            "Content-Type": "application/json"
        },
        method: "POST",
        data: JSON.stringify(queryData),
        tryCount: 0,
        retryLimit: 3,
        success: function (data) {
            dfd.resolve(data);
        },
        error: function (jqXHR, exception) {
            if (this.tryCount == 0) {
                logAjaxErrorToKibana(jqXHR, exception, dataURL, "");
            }
            this.tryCount++;
            if (this.tryCount <= this.retryLimit) {
                if (jqXHR.status == 401) {
                    console.log("401 Error");
                    //token not yet renewed then wait for 2s and then set new token
                    if (queryData["SalesforceAccessToken"] == managerTokens.SFDCToken) {
                        setTimeout(function () {
                            queryData["SalesforceAccessToken"] = managerTokens.SFDCToken;
                        }, 2000);
                    }
                    else {
                        queryData["SalesforceAccessToken"] = managerTokens.SFDCToken;
                    }
                }
                $.ajax(this);
            }
            else {
                dfd.reject(jqXHR);
            }
        }
    });
    return dfd.promise();
}
function fetchAttainmentData(queryData, dataURL,asyncType) {
    dataURL= rootUri+dataURL;
    //try {
        // Return the $.ajax promise
        var dfd = $.Deferred();
        $.ajax({
            async:asyncType,
            url:dataURL,
            headers: {
                "Content-Type": "application/json"
            },
            method:"POST",
            data:queryData,
            tryCount : 0,
            retryLimit : 3, 
            success: function (data) {
                dfd.resolve(data);
            },
            error: function (jqXHR, exception) {
                if (this.tryCount == 0) {
                    logAjaxErrorToKibana(jqXHR, exception, dataURL, JSON.stringify(queryData));
                }
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    //return;
                }
                else {
                    dfd.reject(jqXHR);
                }
                //return;
            }
    });
    return dfd.promise();
    //} catch (e) {
    //    console.log(e.message);
    //    logToKibana(e);
    //} 
}
function logAjaxErrorToKibana(jqXHR, exception, dataURL, queryData) {
    if (localStorage.getItem("fromXamarin") == null)
        return;
    var errorMsg = '';
    if (jqXHR.status === 0) {
        errorMsg = 'Not connect.\n Verify Network.';
    } else if (jqXHR.status == 404) {
        errorMsg = 'Requested page not found. [404]';
    } else if (jqXHR.status == 500) {
        errorMsg = 'Internal Server Error [500].';
    } else if (exception === 'parsererror') {
        errorMsg = 'Requested JSON parse failed.';
    } else if (exception === 'timeout') {
        errorMsg = 'Time out error.';
    } else if (exception === 'abort') {
        errorMsg = 'Ajax request aborted.';
    } else {
        errorMsg = 'Uncaught Error.\n' + jqXHR.responseText;
    }
    var msg=""
    if (queryData == "") {
        msg = "URL: " + dataURL + " ERROR: " + errorMsg;
       
    }
    else {
        msg = "URL: " + dataURL + "DATA:" + queryData + " ERROR: " + errorMsg;
    }
    window.location.href = "http://kibanalog#" + msg;
}
/* Ajax Call End*/

function logToKibana(error) {
    if (localStorage.getItem("fromXamarin") == null)
        return;
    var msg = error;
    if (error instanceof Error && typeof value.message !== 'undefined')
        msg = "error: " + error + "message: " + error.message + "stack: " + error.stack;
    window.location.href = "http://kibanalog#" + msg;
}

function trackEvent(eventName, value){    
    if(eventName)
        window.location.href="https://trackEvent#"+eventName+":"+value;
}

toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-bottom-center",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "500",
    "timeOut": "2000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };

function initializeHeader() {
    if ($('.menu-icon').length) {
        $(".menu-icon").sideNav({
            edge: 'right'
        });
        var sideNavScrollbar = document.querySelector('.custom-scrollbar');
        var ps = new PerfectScrollbar(sideNavScrollbar);
        if ((window.location.href).includes("backbutton=false"))
            $('#backButton').hide();

        document.getElementById("version").innerHTML = userState.AppVersion;
        document.getElementById("headerText").innerHTML = headerName;
        document.getElementById("username").innerHTML = userNamephp;
    }
}

var userState,aeUserState, managerTokens;
var SFDCToken = "";
var SFDCInstanceUrl = "";
var BadgeNumber = "";
var SFDCInstance = "";
var config, env = "ARR", fromXamarin = false;
var cacheTimeoutTime = 1000 * 60 * 60 * 8, renewTokensTime= 1000 * 60 * 55, quotaCacheTimeoutTime = 1000 * 60 * 60 * 3;
var immediateRefreshRequired = false;
var ForceRefresh=false;
var currentWidget="", headerName="";
function createHeader(header) {
    $("header").load(header + " #header", function () {
        initializeHeader();
        createHeaderSearch(header);
    });
}

function performXamarinAction(url) {
    window.location.href = url;
}
var rootUri="";

function InitializeApp() {
    getConfigFile();
}

function StartAppFromXamarin(managerUserState, managerTokens) {
    console.log("ManagerUserState: " + managerUserState);
    console.log("ManagerTokens: " + managerTokens);
    localforage.setItem('ManagerUserState', managerUserState).then(function (value) {
        localforage.setItem('ManagerTokens', managerTokens).then(function (value) {
            if (localStorage.getItem("fromXamarin") == null) {
                localStorage.setItem('fromXamarin', '1');
                getConfigFile();
            }
        }).catch(function (err) {
            console.log("Manager Tokens Error: " + err);
            logToKibana(err);
        });
    }).catch(function (err) {
        console.log("Manager User State Error: " + err);
        logToKibana(err);
    }); 
}
function SetAEUserStateFromXamarin(aeUserState) { 
    console.log("AEUserState: " + aeUserState);
    localforage.setItem('AEUserState', aeUserState).then(function (value) {
        // localforage.setItem('ManagerTokens', managerTokens).then(function (value) {
        //     if (localStorage.getItem("fromXamarin") == null) {
        //         localStorage.setItem('fromXamarin', '1');
        //         getConfigFile(); 
        //     }
        // }).catch(function (err) {
        //     console.log("Manager Tokens Error: " + err);
        //     logToKibana(err);
        // });
        getAEUserState();
    }).catch(function (err) {
        console.log("AE User State Error: " + err);
        logToKibana(err);
    }); 
}

function SetAEUserStateFromXamarin(aeUserState) {
    console.log("AEUserState: " + aeUserState);
    localforage.setItem('AEUserState', aeUserState).then(function (value) {
        getAEUserState();
    }).catch(function (err) {
        console.log("AE User State Error: " + err);
        logToKibana(err);
    });
}
function getConfigFile() {
    localforage.getItem('Configs').then(function (value) {
        if (value != null) {
            console.log("Config Data From Localforage");
            config = JSON.parse(value);
            getManagerUserState();
        }
        else {
            $.getJSON("config.json", function (data) {
                console.log("Config Data From AJAX");
                //logToKibana("config data from ajax");
                localforage.setItem('Configs', JSON.stringify(data));
                config = JSON.parse(JSON.stringify(data));
                getManagerUserState();
            });
        }
    });
}

function renewTokens(id) {
    console.log("Requesting For New Tokens");
    //switch (id) {
    //    case 0: console.log("RenewToken Due to Timeout");
    //        break;
    //    case 1: console.log("RenewToken SET for Timeout");
    //        break;
    //    case 2: console.log("RenewToken Due to Timeout From XAMARIN");
    //        break;
    //    case 3: console.log("RenewToken SET to Timeout From XAMARIN");
    //        break;
    //}
    window.location.href = "http://renewtokens";
}

function getManagerUserState() {
    localforage.getItem('ManagerUserState').then(function (value) {
        if (value != null) {
            console.log("ManagerUserState Data From Localforage");
            userState = JSON.parse(value);
            getManagerTokens();
        }
        else {
            userState = {
                FirstName: 'Shubham',
                LastName: 'Gupta',
                BadgeNumber: '929839',
                Environment: 'prod',
                AppVersion: '1.0.0',
                SFDCInstance: 'LDELL',
                timestamp: new Date().getTime(),
                IsNotificationEnabled: false,
                IsBioAuthAvailable: false,
                IsBioAuthEnabled: false,
                SelectedBioAuthSetting: 0
            };
            console.log("ManagerUserState Data From Default");
            getManagerTokens();
            localforage.setItem('ManagerUserState', JSON.stringify(userState));
        }

        if (userState.SFDCInstance == "LEMC")
            SFDCInstance = "1";
        else
            SFDCInstance = "0";
    });
}
function getAEUserState() {
    localforage.getItem('AEUserState').then(function (value) {
        if (value != null) {
            console.log("AEUserState Data From Localforage");
            aeUserState = JSON.parse(value);
        }
        else {
            aeUserState = {
                FirstName: 'Shubham',
                LastName: 'Gupta',
                BadgeNumber: '929839',
                Environment: 'prod',
                AppVersion: '0.0.1',
                SFDCInstance: 'LDELL',
                timestamp: new Date().getTime()
            };
            console.log("AEUserState Data From Default");
            localforage.setItem('AEUserState', JSON.stringify(aeUserState));
        }
    });
}

function getManagerTokens() {
    Init();
}

function clearExpiredCache() {
    localforage.iterate(function (value, key, iterationNumber) {
        if (key.toString().indexOf(":") > 0) { 
            var timeDiff = (new Date().getTime()) - (JSON.parse(value)).timestamp;
            if(timeDiff >= quotaCacheTimeoutTime){
                if(key.toString().toLowerCase().indexOf("attainment")>0)
                    localforage.removeItem(key); 
            }
            if (timeDiff >= cacheTimeoutTime) {
                if(key.toString().toLowerCase().indexOf("accountwidgetdata")>0 || key.toString().toLowerCase().indexOf("opportunitywidgetdata")>0){
                    console.log("Not Removing Widget Item " + key);
                }
                else{
                    localforage.removeItem(key);
                    console.log("Removed Item " + key);
                }
            }
        }
    }).then(function () {
        console.log('Iteration has completed');
        setTimeout(clearExpiredCache, cacheTimeoutTime);
    }).catch(function (err) {
        // This code runs if there were any errors
        console.log(err);
        logToKibana(err);
    });
    SetL1Badge("");
}

function RefreshTokenFromXamarin(managerTokens) {
    console.log("Received Tokens From Xamarin");
    console.log("ManagerTokens: " + managerTokens);
    localforage.setItem('ManagerTokens', managerTokens).then(function (value) {
        managerTokens = JSON.parse(value);

        if (localStorage.getItem("fromXamarin") != null) {
            var timeElapsed = (new Date().getTime()) - managerTokens.timestamp;
            if (timeElapsed >= renewTokensTime) {
                renewTokens(2);
            }
            else {
                console.log("Time Remaining for renewTokens from Xamarin: " + (renewTokensTime - timeElapsed));
                setTimeout(renewTokens, renewTokensTime - timeElapsed,3);
            }
        }
        if (immediateRefreshRequired) {
            immediateRefreshRequired = false;
            console.log("Calling Init");
            if (window.location.pathname.includes('/headersearch.html'))
                InitSearch();
            else if (window.location.pathname.includes('/settings.html'))
                InitSettings();
            else
                Init();
        }
    }).catch(function (err) { 
        console.log("Refresh Manager Tokens Error: " + err);
        logToKibana(err);
    });
}

function AddManagerTypeMsg() {
    var caller = getFunctionName(arguments.callee.toString());
    var badgeno = userState.BadgeNumber;
    localforage.getItem(caller + ":" + badgeno ).then(function (value) {
        var data = JSON.parse(value);
        showMsg(data.value)
    }).catch(function (err) {
        var ajaxCall = fetchData({ BadgeNumber: badgeno }, null, config[env].GetManagerType, false);

        ajaxCall.done(function (data) {
            try {
                if (data != null) {
                    var object = { value: data, timestamp: new Date().getTime() };
                    console.log("data: " + data)
                    localforage.setItem(caller + ":" + badgeno , JSON.stringify(object));
                    showMsg(data);
                }
            } catch (e) {
                //alert(e.message);
                logToKibana(e);
            } finally {

            };
        }).fail(function (xhr, textStatus, errorThrown) {
        });
    });
};
//
function showMsg(value) {
    if (value)
    return;
    var managerMsgTemplate = `
        <div id="managerMsg" style='width:100%;font-size:12px;font-family: Roboto,sans-serif;display:none;background-color:#EEE;color:#444;text-align:center;position:fixed;z-index:1;bottom:0;padding:8px;font-weight: 400;'>
            MySales works best for managers and directors. If there is data inconsistency please contact 
            <strong>
                <u>mysales.mobile.l2@emc.com</u>
            </strong>
        </div>
    `;
    var sb = managerMsgTemplate;
    $(sb).appendTo('body');
    $('#managerMsg').show();
}
function DoForceRefresh(closeRefreshSpinner=false, callback=null){
    console.log("Calling Force Refresh.");
    ForceRefresh=true;
    clearWidgetCache(closeRefreshSpinner, callback);
    // window.location.href=window.location.href+"?=refresh";
}
function clearWidgetCache(closeRefreshSpinner, callback){
    localforage.iterate(function (value, key, iterationNumber) {
        if (isCacheClearRequired(key.toString()))
            localforage.removeItem(key); 
    }).then(function () {
        if(callback){
            callback();
        }
        else{
            Init();
        }
    }).catch(function (err) {
        console.log("error");
    });
}
function isCacheClearRequired(key){
    //Pull To Refresh available for all widgets but not for pages under common-ux
    if(window.location.href.indexOf("common-ux")==-1){
        if(key.indexOf(currentWidget)>=0)
            return true;
    }
    return false;
}
function clearCacheFromPN(){
    var count=0;
    localforage.iterate(function (value, key, iterationNumber) {
        if (key.toString().toLowerCase().indexOf("attainment")>=0){
            localforage.removeItem(key);
            count++;
        }
    });
    console.log("Cleared "+count+" keys...");
}
  

InitializeApp();
