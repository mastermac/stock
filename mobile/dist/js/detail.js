var StringBuilder = function () { this.value = ""; };
StringBuilder.prototype.append = function (value) { this.value += value; };

var badgeNumber = "";
var filter="";
var dataCnt = 0;
var period = 1;
var sort = -1;
function Init() {
    headerName="Dashboard";
    if(ForceRefresh){
        dataCnt = 0;
        pageoffset = 0;
        ForceRefresh=false;
        $("[id$='divOppList']").html("");
        retryOpportunityDetail();
    }
    else{
        currentWidget="ManagerOpportunityWidget";
        loadSkeleton();
        createHeader("header.html");
        localforage.getItem("SortOption-OppDetailView").then(function (value) {
            if (value != null) {
                sort = value;
                sort1 = sortOptions[sort - 1];
                tempsort = sortOptions[sort - 1];
                updateSortIcon();
            }
            else {
                sort = 2;
                sort1 = sortOptions[1];
                tempsort = sortOptions[1];
                updateSortIcon();
            }
            getDetailPage();
        });
    }
} 
function getDetailPage() {
    try {
        assignDefaultsIfEmpty();
            // $("#OpportunityDetailDiv #filterDiv").hide();
            // reassign = true;
            // //SetPageNameForOmniture("OpportunityDetail");
            // $("#OpportunityDetailDiv #retryDiv").html(retryTemplate.replace("{click_method}","retryOpportunityDetail"));
            // localforage.getItem("detail" + "_ManagerHeaderData").then(function (value) {
            //     var d = JSON.parse(JSON.stringify(value));
            //     if (d != null) {
            //         badgeNumber = d.BadgeNumber;
            //         if (userState.SFDCInstance == "LDELL")
            //             getlobList(badgeNumber, false);
            //         $("#totalCnt").val(d.CountValue);
            //         localforage.getItem("fullview_filter").then(function (val) {

            //             if (value) {

            //                 if (val) {
            //                     localforage.getItem("filterdata").then(function (value) {
            //                         initializeFilter(value);
            //                     });
            //                     localforage.setItem("fullview_filter", false);
            //                 }
            //                 else {
            //                     localforage.getItem("detail_filter_data").then(function (value) {
            //                         initializeFilter(value);
            //                     });
            //                     localforage.setItem("fullview_filter", false);
            //                 }

            //             }
            //         });



            //     }


            // });
            initializeFilter(null);
        $(window).scroll(function () {
            if (Offline.state == "up" && !isSortApplied && !isFilterApplied) {
                var a = Math.round($(window).scrollTop());
                var b = $(document).height() - $(window).height();
                if ((a == b || a == b - 1) && dataCnt < parseInt($("#totalCnt").val())) {
                    if (!isSortApplied && !isFilterApplied) {
                        isCallDetails = true;
                        pageoffset += pagesize;
                        showSkeleton("divOppList",1);
                        GetOpportunityDetails(badgeNumber, false);
                    }
                    else
                        isCallDetails = false;
                }
            }
            else
                isCallDetails = false;
        });
    } catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
}
function initializeFilter(value) {
    var d = JSON.parse(JSON.stringify(value));
    if (d != null) {
        //filter=d.filter;
        filter = assignFilterString(d.filter);
        lobfilter = assignProdString(d.lobfilter);
        period = d.period;
        tempquar = assignQuarter(d.period);
        tempstage = [...d.filter];
        temp_selected_product = [...d.lobfilter];
        stage1 = [...tempstage];
        quarter1 = tempquar;
        selected_product = [...d.lobfilter];

    }
    updateFilterIcon();
    GetPipelineData(badgeNumber, false);
    GetOpportunityDetails(badgeNumber, true);
    fullview_filter = false;
}

function GetOpportunityDetails(badgeNumber, fromFilter) {
    $("#OpportunityDetailDiv #retryDiv").hide();
    $("#OpportunityDetailDiv #filterDiv").show();
    var caller = getFunctionName(arguments.callee.toString());
   
        localforage.getItem(currentWidget+":"+caller + ":" + badgeNumber + ":" + filter + ":" + period + ":" + sort + ":" + lobfilter + ":" + dataCnt).then(function (value) {
            
                

            var data = JSON.parse(value);
            tempstage = [...stage1];
            tempquar = quarter1;
            tempsort = sort1;
            temp_selected_product = [...selected_product];
            BindOpportunityDetails(data.value);
            ChangeHeaderCount(data.value.opportunityCount);
            if (fromFilter == true) {
                setDetailFilterData();
            }
            hideSkeleton("divOppList");
            isCallDetails = false;
            isFilterApplied = false;
            isSortApplied = false;
        }).catch(function (err) {
            var ajaxCall = fetchData(
                {
                    perPage: pagesize,
                    page: pageoffset/pagesize + 1,
                    itemNo: getVal('itemId'),
                    vendor: getVal('vendor'),
                    vendorCode: getVal('vendorCode'),
                    description: getVal('description'),
                    itemTypeCode: getVal('itemTypeCode'),
                    grossWt: getVal('grossWt'),
                    diaWt: getVal('diaWt'),
                    cstoneWt: getVal('cstoneWt'),
                    goldWt: getVal('goldWt'),
                    sellPrice: getVal('sellPrice'),
                    curStock: getVal('curStock'),
                    ringSize: getVal('ringSize'),
                    styleCode: getVal('styleCode'),
                    sdt: '0000-00-00',
                    edt: '0000-00-00',
                    itemNoExt: getVal('itemIdExt'),
                    source: 'page'
                }, $("[id$='divOppList']"), '../../src/scripts/getData.php', false);
            ajaxCall.done(function (data) {
                // if (data.total == 0) $("#myTable > tbody").html("");
                // else {
                //     manageRow(data.data);
                //     is_ajax_fire = 1;
                // }
    
                if (data.total > 0) {
                    var object = { value: data, timestamp: new Date().getTime() };
                    localforage.setItem( currentWidget+":"+ caller + ":" + badgeNumber + ":" + filter + ":" + period + ":" + sort + ":" + lobfilter + ":" + dataCnt, JSON.stringify(object));
                    tempstage = [...stage1];
                    tempquar = quarter1;
                    tempsort = sort1;
                    temp_selected_product = [...selected_product];
                    BindOpportunityDetails(data);
                    ChangeHeaderCount(data.total);
                    if (fromFilter == true) {
                        setDetailFilterData();
                    }
                }
                else {
                    if (fromFilter == true) {
                        tempstage = [...stage1];
                        tempquar = quarter1;
                        tempsort = sort1;
                        temp_selected_product = [...selected_product];
                        $("[id$='divOppList']").html("");
                        $("[id$='divOppList']").append("<div class='errormsg'>" + "No Records Found" + "</div>");
                    }
                    ChangeHeaderCount(0);
                }
                hideSkeleton("divOppList");
                isCallDetails = false;
                isFilterApplied = false;
                isSortApplied = false;
            }).fail(function (xhr, textStatus, errorThrown) {
                hideSkeleton("divOppList");
                $("#OpportunityDetailDiv #retryDiv").show();
                $("#OpportunityDetailDiv #filterDiv").hide();
                $("[id$='pipeLine']").css("display", "none");
                $("[id$='pipeLine']").html("");
                tempstage = [...stage1];
                tempquar = quarter1;
                tempsort = sort1;
                temp_selected_product = [...selected_product];
                if (fromFilter == true) {
                    setDetailFilterData();
                }
                isCallDetails = false;
                isFilterApplied = false;
                isSortApplied = false;
            });
        });
}
function retryOpportunityDetail() {
    $("#OpportunityDetailDiv #retryDiv").hide();
    $("#OpportunityDetailDiv #filterDiv").show();
    showSkeleton("divOppList");
    showSkeleton("pipeLine");
    getDetailPage();
}
function ChangeHeaderCount(dataCnt) {
    localforage.getItem("detail" + "_ManagerHeaderData").then(function (value) {
        var data = value;
        if (data != null) {
            data.CountValue = dataCnt;
            if (data.CountValue == 0)
                data.CountText = "";
            else
                data.CountText = "Opportunities";
            localforage.setItem("detail" + "_ManagerHeaderData", data);
            SetHeaderData(data);
           
        }
    });
}
function setDetailFilterData() {
    localforage.setItem("detail_filter_data", {
        "filter": stage1,
        "period": period,
        "lobfilter": selected_product
    });
}


function BindOpportunityDetails(data) {
    $('#filter').css('display', 'block');
    try {
        var sb = new StringBuilder();
        var obj = data.data;
        $("#totalCnt").val(data.total);
        var amt = 0; var usd = "";
        var i=0;
        for (; i < pagesize; i++) {
            if(obj[i]==null) {
                --i;
                break;
            }
            var usd = "$ ";
            
            sb.append("<div class='card' style='margin: 0.5rem 0rem;'>");
            sb.append("<div class='card-body' style= 'padding: 0.4rem; padding-right: 1rem;'>");
            sb.append("<div class='row'>");
            sb.append("<div class='col-3'>");
            sb.append('<a data-fancybox="gallery" href="../../pics/'+obj[i].itemNo+'.JPG" data-caption="14YN1730">');
            sb.append('<img class="lazy img-responsive" src="../../pics/'+obj[i].itemNo+'.JPG" onerror="this.src=\'../../pics/noImage.jpeg\';" alt="" border="3" style="width: 100%;"></a>');
            sb.append("</div>");
//            <a data-fancybox="gallery" href="pics/14YN1730.JPG" data-caption="14YN1730">
            sb.append("<div class='col-9 pl-0 openFullView'>");
            sb.append("<input type='hidden' id='OpportunityId' value='" + obj[i].itemNo + "'>");
            sb.append("<div class='row'>");
            sb.append(
                "<div class='col text-truncate' style='color:#007db8;font-weight:bold ;font-size:14px;' id='OpportunityName'>" +
                obj[i].description +
                "</div>");
            sb.append("</div>");
            sb.append("<div class='row pb-1' style='font-size:10px;'><div class='col'></div></div>");
            sb.append(
                "<hr style='margin-top: 1px; margin-bottom: 1px;'>");
            sb.append("<div class='row pt-1' style='font-size:11px;'>");
            sb.append("<div class='col-4 pr-0'>ItemNo</div>");
            sb.append("<div class='col-3'>V ID</div>");
            sb.append("<div class='col-2 pr-0'>Qty</div>");
            sb.append("<div class='col-3 pr-0'>Sell Price</div>");
            sb.append("</div >");
            sb.append("<div class='row pb-1' style='font-size:11px;font-weight:bold'>");
            sb.append("<div class='col-4 pr-0'>"+ obj[i].itemNo + "</div >");
            sb.append("<div class='col-3'>" + obj[i].vendor + "</div >");
            sb.append("<div class='col-2 pr-0'>"+ obj[i].curStock + "</div >");
            sb.append("<div class='col-3 pr-0'>" + usd + obj[i].sellPrice + "</div >");
            sb.append("</div></div></div>");
            sb.append("</div></div>");
        }
        dataCnt += i;
        $("[id$='divOppList']").append(sb.value);
        $("[id$='divOppList']").css("display","");

        $("[id$='divOppList']").find(".openFullView").click(function () {
            ShowAEOpportunityDetails(this);
        });
    }
    catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
}

var stageArr = ["Plan", "Discover", "Qualify", "Propose", "Commit", "Win"];
// Start Ajax Call for Getting the Pipeline data
function GetPipelineData(badgeNo, isManager) {
    $("#OpportunityDetailDiv #retryDiv").hide();
    $("#OpportunityDetailDiv #filterDiv").show();
    var caller = getFunctionName(arguments.callee.toString());
    localforage.getItem(currentWidget+":"+caller + ":" + badgeNo + ":" + period).then(function (value) {
        
            
        var data = JSON.parse(value);
        BindOpportunityPipeline(data.value);
        hideSkeleton("pipeLine");
    }).catch(function (err) {
        try {
            var ajaxCall = fetchData({ BadgeNumber: badgeNo, Quarter: period, IsManager: isManager }, null, config[env].GetPipelineData);
            ajaxCall.done(function (data) {
                try {
                    if (data.pipelineData.length > 0) {
                        var object = { value: data, timestamp: new Date().getTime() };
                        localforage.setItem(currentWidget+":"+ caller + ":" + badgeNo + ":" + period, JSON.stringify(object));
                        BindOpportunityPipeline(data);
                        hideSkeleton("pipeLine");
                    }
                    else {
                        hideSkeleton("pipeLine");
                        $("[id$='pipeLine']").css("display", "none");
                        $("[id$='pipeLine']").html("");
                    }
                    
                } catch (e) {
                    console.log(e.message);
                    logToKibana(e);
                };
            }).fail(function (xhr, textStatus, errorThrown) {
                $("#OpportunityDetailDiv #retryDiv").show();
                $("#OpportunityDetailDiv #filterDiv").hide();
                hideSkeleton("pipeLine");
                $("[id$='pipeLine']").css("display", "none");
                $("[id$='pipeLine']").html("");
                tempstage = [...stage1];
                tempquar = quarter1;
                tempsort = sort1;
                temp_selected_product = [...selected_product];
                if (fromFilter == true) {
                    setDetailFilterData();
                }
            });

        } catch (e) {
            console.log(e.message);
            logToKibana(e);
        }
    });
};
// End Ajax Call for Getting the Pipeline data
function BindOpportunityPipeline(data) {
    try {
        if (data.pipelineData != null && data.pipelineData.length > 0) {
            var sb = new StringBuilder();
            var amt = 0;
            var cnt = 0;
            var stagename = "";
            var obj = data.pipelineData;
            var quarName = assignQuarter(period);
            sb.append("<div class='quarter'>");
            sb.append("<span class='current-text'>" + quarName + "</span>");
            sb.append("<span class='q1'><i class='fa fa-calendar' aria-hidden='true'></i></span></div>");
            sb.append("<div class='imagesstyle'>");
            for (var i = 0; i < stageArr.length; i++) {
                // Fetching name based on stagename condition pipeline
                stagename = stageArr[i];
                var s = obj.find(x => x.stageName.includes(stagename));
                if (s != null) {
                    amt = s.total_Amount;
                    cnt = s.total_Count;
                    //Calling Currency Converter
                    amt = CurrencyConverter(amt);
                    //Calling Currency Converter
                }
                else {
                    amt = 0;
                    cnt = 0;
                }
                if(amt==null)
                    amt=0;
                // start graph row 
                sb.append("<div class='row graphrow g" + (i + 1) + "'>");
                sb.append("<div class='col-xs-2'> </div>");
                sb.append("<div class='col-xs-7 center grid-framework'>" + stagename + " (<span id='plan_span'>" + cnt + "</span>)</div>");
                sb.append("<div class='col-xs-3 left dollar' id='plan'>$ " + amt + "</div>");
                sb.append("</div>");
                // end graph row 


            }
        }
        sb.append("</div>");
        $("[id$='pipeLine']").css("display", "");
        $("[id$='pipeLine']").html(sb.value);
    } catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
};

function ShowAEOpportunityDetails(accCard) {
    if (Offline.state == "up") {
        try {
            var OpportunityId = $(accCard).find("#OpportunityId")[0].value;
            //var OpportunityName = $(accCard).find("#OpportunityName")[0].innerHTML;
            localforage.setItem("detailview" + "_ManagerHeaderData", {
                "ImageBytes": "",
                "UserName": 'Item Details',
                "Designation": "",
                "CountValue": '',
                "CountText": "",
                "HideProfileImg": true,
                "OpportunityId": OpportunityId
            }).then(function () {
                window.location.href = "detailview.php";
            });
        } catch (e) {
            console.log(e.message);
            logToKibana(e);
        }
    }
}