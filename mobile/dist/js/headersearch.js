/*Header Search Script Start*/
var pageoffset = 0;
var pagesize = 20;
var dataCnt = 0;
var isLoadMore = 1;
var searchType = 1;
var searchText = "";
var headerSearchRetryType = "";
function InitSearch() {
    
    loadSkeleton();
    createHeader("../common-ux/header.html");
    PageLoadSearch();
}
function PageLoadSearch() {
    if (Offline.state == "up") {
        try {
            assignDefaultsIfEmpty();
        
            //$("#txtGlobalSearch").val("");
            localforage.getItem("HDSearch").then(function (value) {
                var d = JSON.parse(JSON.stringify(value));
                if (d != null) {
                    searchType = d.SearchType;
                    searchText = d.SearchText;
                    $("#txtGlobalSearch").val(searchText);
                    changeSearchType();
                    if (searchText.trim().length > 0) {
                        if (searchType == 1) {
                            //SetPageNameForOmniture("HeaderAccountSearchView");
                            HeaderAccountSearch(searchText);
                        }
                        else if (searchType == 2) {
                            //SetPageNameForOmniture("HeaderOpportunitySearchView");
                            HeaderOpportunitySearch(searchText);
                        }
                    }
                }
                //changeSearchType();
            });
            $(window).scroll(function () {
                if (Offline.state == "up") {
                    var a = Math.round($(window).scrollTop());
                    var b = $(document).height() - $(window).height();
                    if ((a == b || a == b - 1) && isLoadMore == 1) {
                        pageoffset += pagesize;
                        if (searchText.trim().length > 0) {
                            showSkeleton("divSearchList",1);
                            if (searchType == 1)
                                HeaderAccountSearch();
                            else if (searchType == 2)
                                HeaderOpportunitySearch();
                        }
                    }
                }
                else{
                    callRetryPage("PageLoadSearch");
                }
            });
        } catch (e) {
            alert(e.message);
            logToKibana(e);
            hideLoader();
        }
    }
    else{
        callRetryPage("PageLoadSearch");
    }
};
function CallHeaderSearch(sText, sType) {
    showSkeleton("divSearchList");
    searchType = sType;
    searchText = sText;
    if (sType == 1)
        HeaderAccountSearch();
    else if (sType == 2)
        HeaderOpportunitySearch();
}
function HeaderAccountSearch() {
    if (Offline.state == "up") {
        var caller = getFunctionName(arguments.callee.toString());
        
        localforage.getItem(caller + ":" + userState.BadgeNumber + ":" + searchText + ":" + dataCnt).then(function (value) {
            $("#txtGlobalSearch").val(searchText);
            var data = JSON.parse(value);
            BindSearchDetails(data.value);
            hideSkeleton("divSearchList");
        }).catch(function (err) {
            var ajaxCall = fetchData({ BadgeNumber: userState.BadgeNumber, pageoffset: pageoffset, pagesize: pagesize, SearchText: searchText }, $("[id$='divSearchList']"), config[env].HeaderAccountSearch);
            ajaxCall.done(function (data) {
                try {
                    if (Offline.state == "up") {
                        if (data.accountData != null && data.accountData.length > 0) {
                            var object = { value: data, timestamp: new Date().getTime() };
                            localforage.setItem(caller + ":" + userState.BadgeNumber + ":" + searchText + ":" + dataCnt, JSON.stringify(object));
                            BindSearchDetails(data);
                        }
                        else {
                            isLoadMore = 0;
                            if (pageoffset == 0 && $(".errormsg").length == 0)
                                $("[id$='divSearchList']").append("<div class='errormsg'>" + "No Records" + "</div>");
                        }
                        $("#txtGlobalSearch").val(searchText);
                        changeSearchType();
                        hideSkeleton("divSearchList");
                    }
                    else{
                        callRetryPage("HeaderAccountSearch");
                    }
                } catch (e) {
                    alert(e.message);
                    logToKibana(e);
                } finally {
                    hideSkeleton("divSearchList");
                };
            }).fail(function (xhr, textStatus, errorThrown) {
                callRetryPage("HeaderAccountSearch");
            });
        });
    }
    else{
        callRetryPage("HeaderAccountSearch");
    }
};

function BindSearchDetails(data) {
    try {
        var temp = "";
        var obj = data.accountData;
        for (var i = 0; i < obj.length; i++) {
            var sb = aeAccDetailTemplate;
            sb = sb.replace("{name}", obj[i].accountName);
            sb = sb.replace("{address}", NullCheckNA(NullCheck(obj[i].billingStreet) + ' ' + NullCheck(obj[i].billingState) + ' ' + NullCheck(obj[i].billingCountry)))
            sb = sb.replace("{visited}", ConvertDate(obj[i].lastVisited));
            sb = sb.replace("{industry}", NullCheckNA(obj[i].industry));
            sb = sb.replace("{ucid}", NullCheckNA(obj[i].ucid));
            sb = sb.replace("{id}", obj[i].accountID);
            sb = sb.replace("{owner}", NullCheckNA(obj[i].accountOwner));
            sb = sb.replace("{img}", "./img/ae_icon_account.png");

            temp = temp + sb;
        }
        dataCnt += obj.length;
        $("#divSearchList").append(temp);
    } catch (e) {
        logToKibana(e);
        hideSkeleton("divSearchList");
    }
}
function HeaderOpportunitySearch() {
    if (Offline.state == "up") {
        var caller = getFunctionName(arguments.callee.toString());

        localforage.getItem(caller + ":" + userState.BadgeNumber + ":" + searchText + ":" + dataCnt).then(function (value) {
            $("#txtGlobalSearch").val(searchText);
            var data = JSON.parse(value);
            BindOppoSearchDetails(data.value);
            hideSkeleton("divSearchList");
        }).catch(function (err) {
            var ajaxCall = fetchData({ BadgeNumber: userState.BadgeNumber, pageoffset: pageoffset, pagesize: pagesize, SearchText: searchText }, $("[id$='divSearchList']"), config[env].HeaderOpportunitySearch);
            ajaxCall.done(function (data) {
                try {
                    if (Offline.state == "up") {
                        console.log("opportunity search");
                        if (data.opportunityData != null && data.opportunityData.length > 0) {
                            var object = { value: data, timestamp: new Date().getTime() };
                            localforage.setItem(caller + ":" + userState.BadgeNumber + ":" + searchText + ":" + dataCnt, JSON.stringify(object));
                            BindOppoSearchDetails(data);
                        }
                        else {
                            isLoadMore = 0;
                            if (pageoffset == 0 && $(".errormsg").length == 0)
                                $("[id$='divSearchList']").append("<div class='errormsg'>" + "No Records" + "</div>");
                        }
                        $("#txtGlobalSearch").val(searchText);
                        changeSearchType();
                        hideSkeleton("divSearchList");
                    }
                    else{
                        callRetryPage("HeaderOpportunitySearch");
                    }
                } catch (e) {
                    alert(e.message);
                    logToKibana(e);
                } finally {
                    hideSkeleton("divSearchList");
                };
            }).fail(function (xhr, textStatus, errorThrown) {
                callRetryPage("HeaderOpportunitySearch");
            });
        });
    }
    else{
        callRetryPage("HeaderOpportunitySearch");
    }
}


function BindOppoSearchDetails(data) {
    try {
        //Binding search data
        var temp = "";
        var usd = "USD ";
        var amt = 0;
        var obj = data.opportunityData;
        for (var i = 0; i < obj.length; i++) {
            //Calling Currency Converter
            if (typeof obj[i].amount !== undefined && obj[i].amount) {
                obj[i].amount = obj[i].amount.split(' ');
                if (obj[i].amount.length > 1) {
                    var usd = obj[i].amount[0];
                    var amt = obj[i].amount[1].replace(/,/g, '');
                    amt = CurrencyConverter(amt);
                } else {
                    usd = "USD ";
                    amt = obj[i].amount[0].replace(/,/g, '');
                    amt = CurrencyConverter(amt);
                }
            }
            else {
                amt = obj[i].amount;
            }
            var sb = aeOppoDetailTemplate;
            sb = sb.replace("{id}", obj[i].id);
            sb = sb.replace("{opportunityName}", obj[i].opportunityName);
            sb = sb.replace("{accountName}", obj[i].accountName);
            sb = sb.replace("{stage}", obj[i].stage);
            sb = sb.replace("{amount}", usd + ' ' + amt);
            sb = sb.replace("{closedDate}", obj[i].closeDate);
            sb = sb.replace("{stageColor}", getStageColorClassName(obj[i].stage));
            temp = temp + sb;
           

        }
        dataCnt += obj.length;
        $("#divSearchList").append(temp);
        $("[id$='divSearchList']").find(".card").click(function () {
            ShowAEOpportunityDetails(this);
        });
    } catch (e) {
        logToKibana(e);
        hideSkeleton("divSearchList");
    }
}

function ShowAEOpportunityDetails(accCard) {
    if (Offline.state == "up") {
        try {
            var OpportunityId = $(accCard).find("#OpportunityId")[0].value;
            //var OpportunityName = $(accCard).find("#OpportunityName")[0].innerHTML;
            localforage.setItem("detailview" + "_ManagerHeaderData", {
                "ImageBytes": "Hide_Image",
                "UserName": 'Opportunity Details',
                "Designation": "",
                "CountValue": '',
                "CountText": "",
                "BadgeNumber": userState.BadgeNumber,
                "OpportunityId": OpportunityId
            }).then(function () {
                window.location.href = "../opportunity-ux/detailview.html";
            });
        } catch (e) {
            console.log(e.message);
            hideSkeleton("divSearchList");
            logToKibana(e);
        }
    }
}

function retryHeaderSearch() {
    $("#headerSearchDiv #retryDiv").hide();
    showSkeleton("divSearchList");
    if (headerSearchRetryType == "HeaderOpportunitySearch") {
        HeaderOpportunitySearch();
    }
    else if (headerSearchRetryType == "HeaderAccountSearch") {
        HeaderAccountSearch();
    }
    else if( headerSearchRetryType == "PageLoadSearch"){
        PageLoadSearch();
    }
}

function callRetryPage(type){
    headerSearchRetryType = type;
    $("#headerSearchDiv #retryDiv").show();
    hideSkeleton("divSearchList");
}



/*Header Search Script End*/
