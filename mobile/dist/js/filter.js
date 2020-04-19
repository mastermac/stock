var tempstage = [];
var temp_selected_product = [];
var reassign = false;
var l1BadgeNo = "";
var filter = "";
var lobfilter = "";
var tempquar = "";
var tempsort = "";
var pageoffset = 0;
var pagesize = 100;
var stage = ['Active', 'Plan - 1%', 'Discover - 10%', 'Qualify - 30%', 'Propose - 60%', 'Commit - 90%', 'Win - 100%'];
var product = [];
var selected_product = [];
var quarter = ['Previous Fiscal Quarter', 'Current Fiscal Quarter', 'Next Fiscal Quarter'];
var sortOptions = ['Amount(Low to High)', 'Amount(High to Low)', 'Close Date(Old to Recent)', 'Close Date(Recent to Old)', 'Probability %'];
var emcLOBlist = ['CLIENT', 'CMA', 'DATA COMPUTING', 'DATA PROTECTION', 'DELL', 'DELL STORAGE', 'ENTERPRISE INFRASTRUCTURE', 'MDC', 'NETWORKING', 'PIVOTAL', 'RSA SECURITY', 'SERVER', 'STORAGE', 'UNSTRUCTURED DATA STORAGE', 'VCE', 'VIRTUSTREAM', 'VMWARE']
var stage1 = [];
var quarter1 = "";
var sort1 = "";
var isFilterApplied = false;
var isSortApplied = false;
var isModalOpened = false;
var productList_current = null;
var productList_last = null;
var productList_next = null;
var isCallDetails = false;

function LoadModal() {
    isFilterApplied = false;
    $("body").addClass("lock-body");
    stage1 = [...tempstage];
    selected_product = [...temp_selected_product];
    if (window.location.pathname.includes('/detail.html') && userState.SFDCInstance == "LDELL" && reassign) {
        let intersection = (selected_product).filter(x => product.includes(x));
        temp_selected_product = [...intersection];
        selected_product = [...intersection];
    }
    quarter1 = tempquar;
    if (stage1.length == 0) {
        stage1.push("Active");
        if (!tempstage.includes("Active"))
            tempstage.push("Active");
    }
    if (quarter1 == "") {
        quarter1 = "Current Fiscal Quarter";
        tempquar = "Current Fiscal Quarter";
    }
    $("[id$='modalview']").html(modal);
    showList("Stage");
    $('#filterModal').on('hidden.bs.modal', function () {
        $("body").removeClass("lock-body");
        if (!isFilterApplied) {
            tempstage = [...stage1];
            tempquar = quarter1;
            temp_selected_product = [...selected_product];

        }

    });
}
function LoadSortModal() {
    $("body").addClass("lock-body");
    isSortApplied = false;
    sort1 = tempsort;
    $("[id$='modalview']").html(singleSelectPicker);
    showList("SortOptions");
    $('#singleSelectPicker').on('hidden.bs.modal', function () {
        $("body").removeClass("lock-body");
        if (!isSortApplied) {
            tempsort = sort1;
        }

    });
}
function getlobList(badge, is_manager) {
    var caller = getFunctionName(arguments.callee.toString());
    $("[id$='listView']").html("Please wait for sometime");
    localforage.getItem(currentWidget+":"+caller + ":" + badge + ":" + is_manager).then(function (value) {
        var data = JSON.parse(value);
        productList_current = data.value.productCodeList_current;
        productList_next = data.value.productCodeList_next;
        productList_last = data.value.productCodeList_last;
        localforage.getItem("filterdata").then(function (value) {
            var d = JSON.parse(JSON.stringify(value));
            if (d != null) {
                switch (d.period) {
                    case 1: product = [...productList_current];
                        break;
                    case 2: product = [...productList_last];
                        break;
                    case 3: product = [...productList_next];
                        break;
                    default: product = [...productList_current];
                        break;
                }
            }
            else
                product = [...productList_current];

        });
    }).catch(function (err) {
        var ajaxCall = fetchData({ BadgeNumber: badge, IsManager: is_manager }, null, config[env].getlobList);

        ajaxCall.done(function (data) {
            try {
                if (data.productCodeList_current.length > 0) {
                    var object = { value: data, timestamp: new Date().getTime() };
                    localforage.setItem( currentWidget+":"+ caller + ":" + badge + ":" + is_manager, JSON.stringify(object));
                    productList_current = data.productCodeList_current;
                    productList_next = data.productCodeList_next;
                    productList_last = data.productCodeList_last;
                    localforage.getItem("filterdata").then(function (value) {
                        var d = JSON.parse(JSON.stringify(value));
                        if (d != null) {
                            switch (d.period) {
                                case 1: product = [...productList_current];
                                    break;
                                case 2: product = [...productList_last];
                                    break;
                                case 3: product = [...productList_next];
                                    break;
                                default: product = [...productList_current];
                                    break;
                            }
                        }
                        else
                            product = [...productList_current];

                        if (isModalOpened)
                            bindproduct();

                    });




                }
            } catch (e) {
                console.log(e.message);
                hideLoader();
                logToKibana(e);
            };

        });
    });
}
function bindproduct() {

    var temp = "";
    var x = [];
    x = [...selected_product];
    selected_product = [...temp_selected_product];
    for (i = 0; i < product.length; i++) {
        var sb = filtertext;
        if (selected_product.includes(product[i])) {
            sb = sb.replace("{row}", "row selected");
            sb = sb.replace("{color}", "green");
            sb = sb.replace("{stage}", product[i]);
            sb = sb.replace("{state}", "block");
            sb = sb.replace("{onclick}", "changeProductData(this)");
        }
        else {
            sb = sb.replace("{row}", "row unselected");
            sb = sb.replace("{color}", "black");
            sb = sb.replace("{stage}", product[i]);
            sb = sb.replace("{state}", "none");
            sb = sb.replace("{onclick}", "changeProductData(this)");
        }
        temp += sb;
    }
    selected_product = [...x];
    $("[id$='listView']").html(temp);

}

function showList(id) {
    var temp = "";
    if (userState.SFDCInstance != "LDELL")
        $('#Product').html("LOB");
    else
        $('#Product').html("Product Group");

    if (id == 'Stage') {
        var x = [];
        x = [...stage1];
        stage1 = [...tempstage];
        $('#Quarter').css('color', 'black');
        $('#Product').css('color', 'black');
        $('#Stage').css('color', 'green');
        $("#stageimage").attr("src", "img/stage_icon_green.png");
        $("#quarterimage").attr("src", "img/cal_icon_grey.png");
        $("#productimage").attr("src", "img/lob.png");

        for (i = 0; i < stage.length; i++) {
            var sb = filtertext;
            if (stage1.includes(stage[i])) {
                sb = sb.replace("{row}", "row selected");
                sb = sb.replace("{color}", "green");
                sb = sb.replace("{stage}", stage[i]);
                sb = sb.replace("{state}", "block");
                sb = sb.replace("{onclick}", "changeListData(this)");
            }
            else {
                sb = sb.replace("{row}", "row unselected");
                sb = sb.replace("{color}", "black");
                sb = sb.replace("{stage}", stage[i]);
                sb = sb.replace("{state}", "none");
                sb = sb.replace("{onclick}", "changeListData(this)");
            }
            temp += sb;
        }
        stage1 = [...x];
        $("[id$='listView']").html(temp);
    }
    else if (id == 'Quarter') {
        var x = quarter1;
        if (tempquar != "")
            quarter1 = tempquar;
        $('#Quarter').css('color', 'green');
        $('#Stage').css('color', 'black');
        $('#Product').css('color', 'black');
        $("#stageimage").attr("src", "img/stage_icon_grey.png");
        $("#productimage").attr("src", "img/lob.png");
        $("#quarterimage").attr("src", "img/cal_icon_green.png");

        for (i = 0; i < quarter.length; i++) {
            var sb = filtertext;
            if (quarter1.includes(quarter[i])) {
                sb = sb.replace("{row}", "row selected");
                sb = sb.replace("{color}", "green");
                sb = sb.replace("{stage}", quarter[i]);
                sb = sb.replace("{state}", "block");
                sb = sb.replace("{onclick}", "changeQuarterData(this)");
            }
            else {
                sb = sb.replace("{row}", "row unselected");
                sb = sb.replace("{color}", "black");
                sb = sb.replace("{stage}", quarter[i]);
                sb = sb.replace("{state}", "none");
                sb = sb.replace("{onclick}", "changeQuarterData(this)");
            }
            temp += sb;
        }
        if (userState.SFDCInstance == "LDELL")
            temp += "<div style='position: absolute;bottom:9px'><div style='float:left;padding:5px;position: relative;bottom:7px'><img src='img/info.png'  height='15' width='15'></div><div style='font-size: 56%;style='float:left''><b><i>Product Group will get reset on selection of different quarter</b></i></div></div>"
        quarter1 = x;
        $("[id$='listView']").html(temp);
    }
    else if (id == 'Product') {
        isModalOpened = true;
        if (userState.SFDCInstance != "LDELL") {
            product = [...emcLOBlist];
            var x = [];
            x = [...selected_product];
            selected_product = [...temp_selected_product];
            $('#Quarter').css('color', 'black');
            $('#Stage').css('color', 'black');
            $('#Product').css('color', 'green');
            $("#stageimage").attr("src", "img/stage_icon_grey.png");
            $("#quarterimage").attr("src", "img/cal_icon_grey.png");
            $("#productimage").attr("src", "img/lob_active.png");
            bindproduct();

        }
        else {

            if (product.length > 0) {
                $('#Quarter').css('color', 'black');
                $('#Stage').css('color', 'black');
                $('#Product').css('color', 'green');
                $("#stageimage").attr("src", "img/stage_icon_grey.png");
                $("#quarterimage").attr("src", "img/cal_icon_grey.png");
                $("#productimage").attr("src", "img/lob_active.png");
                bindproduct();
            }

            else {
                $('#Quarter').css('color', 'black');
                $('#Stage').css('color', 'black');
                $('#Product').css('color', 'green');
                $("#stageimage").attr("src", "img/stage_icon_grey.png");
                $("#quarterimage").attr("src", "img/cal_icon_grey.png");
                $("#productimage").attr("src", "img/lob_active.png");
                $("[id$='listView']").html("Loading...");
            }

        }





    }
    else if (id == 'SortOptions') {
        var x = sort1;
        if (tempsort != "")
            sort1 = tempsort;

        for (i = 0; i < sortOptions.length; i++) {
            var sb = filtertext;
            if (sort1.includes(sortOptions[i])) {
                sb = sb.replace("{row}", "row selected");
                sb = sb.replace("{color}", "green");
                sb = sb.replace("{stage}", sortOptions[i]);
                sb = sb.replace("{state}", "block");
                sb = sb.replace("{onclick}", "changeSortData(this)");
            }
            else {
                sb = sb.replace("{row}", "row unselected");
                sb = sb.replace("{color}", "black");
                sb = sb.replace("{stage}", sortOptions[i]);
                sb = sb.replace("{state}", "none");
                sb = sb.replace("{onclick}", "changeSortData(this)");
            }
            temp += sb;
        }
        sort1 = x;
        $("[id$='sortListView']").html(temp);
    }

}

function changeListData(list) {
    var listText = list.innerText.trim();
    if (listText == "Active") {
        tempstage = [];
        $("#listView").find(".selected").removeClass("selected");
        $("#listView").find(".row").addClass("unselected");
        $("#listView").find(".col-10").css({ color: "black" });
        $("#listView").find(".filterimg").css({ display: "none" });
        $(list).removeClass("unselected");
        $(list).addClass("selected");
        $(list).find("#text").css({ color: "green" });
        $(list).find(".filterimg").css({ display: "block" });

        tempstage.push(listText);

    } else {
        if ($(list).hasClass("unselected")) {
            if (tempstage.includes("Active")) {
                $("#listView").find(".selected").removeClass("selected");
                $("#listView").find(".col-10").css({ color: "black" });
                $("#listView").find(".filterimg").css({ display: "none" });
                var index = tempstage.indexOf("Active");
                tempstage.splice(index, 1);
            }
            $(list).removeClass("unselected");
            $(list).addClass("selected");
            $(list).find("#text").css({ color: "green" });
            $(list).find(".filterimg").css({ display: "block" });


            if (!tempstage.includes(listText))
                tempstage.push(listText);
        }
        else {
            $(list).removeClass("selected");
            $(list).addClass("unselected");
            $(list).find("#text").css({ color: "black" });
            $(list).find(".filterimg").css({ display: "none" });
            var index = tempstage.indexOf(listText);
            tempstage.splice(index, 1);
            if (tempstage.length == 0) {
                if (!tempstage.includes("Active"))
                    tempstage.push("Active");

                $("#listView").find(".row").eq(0).addClass("selected");
                $("#listView").find("#text").css({ color: "green" });
                $("#listView").find("#imgselected").css({ display: "block" });

            }
        }
    }
}
function changeQuarterData(list) {
    tempquar = "";
    $("#listView").find(".selected").removeClass("selected");
    $("#listView").find(".col-10").css({ color: "black" });
    $("#listView").find(".filterimg").css({ display: "none" });
    $(list).removeClass("unselected");
    $(list).addClass("selected");
    $(list).find("#text").css({ color: "green" });
    $(list).find(".filterimg").css({ display: "block" });
    tempquar = list.innerText.trim();
    if (userState.SFDCInstance == "LDELL") {
        switch (tempquar) {
            case 'Current Fiscal Quarter':
                product = [...productList_current];
                temp_selected_product = [];
                break;
            case 'Previous Fiscal Quarter':
                product = [...productList_last];
                temp_selected_product = [];
                break;
            case 'Next Fiscal Quarter':
                product = [...productList_next];
                temp_selected_product = [];
                break;
            default:
                product = [...productList_current];
                temp_selected_product = [];
                break;
        }
    }


}
function changeProductData(list) {
    var listText = list.innerText.trim();
    if ($(list).hasClass("unselected")) {
        $(list).removeClass("unselected");
        $(list).addClass("selected");
        $(list).find("#text").css({ color: "green" });
        $(list).find(".filterimg").css({ display: "block" });
        if (!temp_selected_product.includes(listText))
            temp_selected_product.push(listText);
    }
    else {
        $(list).removeClass("selected");
        $(list).addClass("unselected");
        $(list).find("#text").css({ color: "black" });
        $(list).find(".filterimg").css({ display: "none" });
        var index = temp_selected_product.indexOf(listText);
        temp_selected_product.splice(index, 1);


    }
}



function changeSortData(list) {
    tempsort = "";
    $("#sortListView").find(".selected").removeClass("selected");
    $("#sortListView").find(".col-10").css({ color: "black" });
    $("#sortListView").find(".filterimg").css({ display: "none" });
    $(list).removeClass("unselected");
    $(list).addClass("selected");
    $(list).find("#text").css({ color: "green" });
    $(list).find(".filterimg").css({ display: "block" });
    tempsort = list.innerText.trim();
}

function updateList() {
    isFilterApplied = true;
    var x = [...stage1];
    var y = quarter1;
    var z = [...selected_product];
    stage1 = [...tempstage];
    quarter1 = tempquar;
    selected_product = [...temp_selected_product];

    updateFilterIcon();

    filter = assignFilterString(stage1);
    lobfilter = assignProdString(selected_product);

    period = assignPeriod(quarter1);
    showLoader();
    tempstage = [...x];
    tempquar = y;
    temp_selected_product = [...z];

    if (window.location.pathname.includes('/fullview.html')) {
        showSkeleton("teamAE");
        GetTeamAEForOpportunity(l1BadgeNo, true);
        GetPipelineData(l1BadgeNo, true);
    }

    else {
        showSkeleton("divOppList");
        $("[id$='divOppList']").html("");
        pageoffset = 0;
        dataCnt = 0;
        GetOpportunityDetails(badgeNumber, true);
        GetPipelineData(badgeNumber, false);
    }
}
function updateFilterIcon() {
    if(stage1.length>0){
        if (quarter1 != quarter[1] || stage1[0]!=stage[0] || selected_product.length > 0)
            $("#filter img").attr("src", "img/filter_applied.png");
        else
            $("#filter img").attr("src", "img/filter_default.png");
    }
    else{
        if(quarter1.length>0 && (quarter1 != quarter[1] || selected_product.length > 0))
            $("#filter img").attr("src", "img/filter_applied.png");
        else
            $("#filter img").attr("src", "img/filter_default.png");
    }
}

function updateSortList() {
    isSortApplied = true;
    var y = sort1;
    sort1 = tempsort;
    updateSortIcon();
    sort = assignSort(sort1);
    localforage.setItem("SortOption-OppDetailView", sort);
    showLoader();
    CallDetails();
}
var callCnt = 0;
function updateSortIcon() {
    if (sort1 != sortOptions[1]) {
        $("#sort img").attr("src", "img/sort_applied.png");
    }
    else {
        $("#sort img").attr("src", "img/sort_default.png");
    }
}

function CallDetails() {
    
    if (isCallDetails && callCnt < 3) {
        callCnt++;
        setTimeout(CallDetails, 2000);
    }
    else {
        callCnt = 0;
        isCallDetails = false;
    $("[id$='divOppList']").html("");
    pageoffset = 0;
    dataCnt = 0;
    GetOpportunityDetails(badgeNumber, true);
    }
}
function assignQuarter(per) {
    var quar;
    switch (per) {
        case 1: quar = 'Current Fiscal Quarter';
            break;
        case 2: quar = 'Previous Fiscal Quarter';
            break;
        case 3: quar = 'Next Fiscal Quarter';
            break;
        default: quar = 'Current Fiscal Quarter';
            break;
    }
    return quar;
}
function assignPeriod(quar) {
    var per;
    switch (quar) {
        case 'Current Fiscal Quarter': per = 1;
            break;
        case 'Previous Fiscal Quarter': per = 2;
            break;
        case 'Next Fiscal Quarter': per = 3;
            break;
        default: per = 1;
            break;
    }
    return per;
}


function assignSort(sort) {
    var per;
    switch (sort) {
        case 'Amount(Low to High)': per = 1;
            break;
        case 'Amount(High to Low)': per = 2;
            break;
        case 'Close Date(Old to Recent)': per = 3;
            break;
        case 'Close Date(Recent to Old)': per = 4;
            break;
        case 'Probability %': per = 5;
            break;
        default: per = 2;
            break;
    }
    return per;
}

function assignFilterString(stageArray) {
    var str = "";
    for (var i = 0; i < stageArray.length; i++) {
        if (stageArray[0] == "Active") {
            str = "";
            break;
        }
        str += "'" + stageArray[i] + "'";
        if (i != stageArray.length - 1)
            str += ","
    }
    return str;
}
function assignProdString(prodArray) {
    var str = "";
    for (var i = 0; i < prodArray.length; i++) {
        str += "'" + prodArray[i] + "'";
        if (i != prodArray.length - 1)
            str += ","
    }
    return str;
}


