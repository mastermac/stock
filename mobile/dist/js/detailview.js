var summaryMetadata = {
    ItemNo : "Item No",
    VendorId: "Vendor ID",
    VendorCode: "Vendor Code",
    Description: "Description",
    itemType: "Item Type",
    Size: "Size",
    GrossWt: "Gross Weight",
    DiaWt: "Diamond Weight",
    CStoneWt: "CStone Weight",
    GoldWt: "Gold Weight",
    Diamonds: "# Diamonds",
    SellPrice: "Sell Price",
    Quantity: "Quantity",
    Timestamp: "DateTime",
    MU: "MU",
    CostPrice: "Cost Price"
};
var SummaryBaseTemplate=`
<div class="card" style="margin: 0.5rem 0rem;">
    {ItemNo}
    {VendorId}
    {VendorCode}
    {Description}
    {itemType}
    {Size}
    {GrossWt}
    {DiaWt}
    {CStoneWt}
    {GoldWt}
    {Diamonds}
    {SellPrice}
    {Quantity}
    {Timestamp}
    {MU}
    {CostPrice}
</div>
`
var SummaryCardTemplate=`<div class="card-body" style="padding: 0.5rem 1.01rem;">
    <div class="row">
        <div class="col-12 text-truncate" style="font-size:14px;">{Header}</div>
        <div style="font-size:14px;font-weight:bold ;" class="col-12">{Data}</div>
    </div>
</div>
<hr style="margin-top: 1px; margin-bottom: 1px;">`;

var HistoryTemplate=`<div class="card" style="margin: 0.5rem 0rem;">
<div class="card-body" style="padding: 1.01rem;">
    <div class="row">
        <div class="col text-truncate" style="color:#007db8;font-weight:bold ;font-size:14px;">{Type}</div>
    </div>
    <div class="row pb-1" style="font-size:10px;">
        <div class="col">{Description}</div>
    </div>
    <hr style="margin-top: 1px; margin-bottom: 1px;">
    <div class="row pt-1" style="font-size:11px;">
        <div class="col">V ID</div>
        <div class="col">Qty</div>
        <div class="col">S.P</div>
        <div class="col">MU</div>
        <div class="col">C.P</div>
    </div>
    <div class="row pb-1" style="font-size:11px;font-weight:bold">
        <div class="col">{VId}</div>
        <div class="col">{Qty}</div>
        <div class="col">{SellPrice}</div>
        <div class="col">{MU}</div>
        <div class="col">{CostPrice}</div>
    </div>
    <div class="row pt-1" style="font-size:11px;">
        <div class="col">G.W</div>
        <div class="col">DiaWt</div>
        <div class="col">CStone</div>
        <div class="col">Gold</div>
        <div class="col"># Dia</div>
    </div>
    <div class="row pb-1" style="font-size:11px;font-weight:bold">
        <div class="col">{GW}</div>
        <div class="col">{DiaWt}</div>
        <div class="col">{CS}</div>
        <div class="col">{Gold}</div>
        <div class="col">{Dia}</div>
    </div>
    <div class="row pt-1" style="font-size:11px;">
        <div class="col-10">Item Type</div>
        <div class="col-2 pl-0">Size</div>
    </div>
    <div class="row pb-1" style="font-size:11px;font-weight:bold">
        <div class="col-10">{ItemType}</div>
        <div class="col-2 pl-0">{Size}</div>
    </div>
</div>
</div>`; 
var HistoryFooterTemplate=`<div class="card" style="margin: 0.5rem 0rem;">
<div class="card-body" style="padding: 1.01rem;">
    <div class="row">
        <div class="col text-truncate" data-i18n='opportunity_total_lbl' style="font-weight:bold ;font-size:15px;">Opportunity Total</div>
        <div class="col d-flex justify-content-end" style="font-weight:bold;font-size:15px;">{opportunitytotal}</div>
    </div>
</div>
</div> `;

function getDetailViewPage() {
    try {
        assignDefaultsIfEmpty();
        //SetPageNameForOmniture("AEOpportunityDetails");
        $("#OpportunityDetailViewDiv #retryDiv").html(retryTemplate.replace("{click_method}","retryOpportunityDetailView"));
        localforage.getItem("detailview" + "_ManagerHeaderData").then(function (value) {
            var d = JSON.parse(JSON.stringify(value));
            if (d != null) {
                badgeNumber = d.BadgeNumber;
                GetAEOpportunityDetails(badgeNumber, d.OpportunityId);

            }
        });
    } catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
}
function Init() {
    headerName="Item Details";
    if(ForceRefresh){
        ForceRefresh=false;
        retryOpportunityDetailView();
    }
    else{
        currentWidget="ManagerOpportunityWidget";
        loadSkeleton();
        createHeader("header.html");
        getDetailViewPage();
    }
}
function GetAEOpportunityDetails(badgeNumber,opportunityId) {
    $("#OpportunityDetailViewDiv #retryDiv").hide();
    var caller = getFunctionName(arguments.callee.toString());
    localforage.getItem(currentWidget+":"+caller  +":"+badgeNumber+ ":" +opportunityId).then(function (value) {
        var data = JSON.parse(value);
        BindSummaryPage(data.value);
        GetHistory(opportunityId);
        hideSkeleton("OpportunityDetailViewDiv");
    }).catch(function (err) {
        var ajaxCall = fetchData(
            {
                perPage: 100,
                page: 1,
                itemNo: opportunityId,
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
            if (data.total > 0 && data.data !=null) {
                var object = { value: data.data[0], timestamp: new Date().getTime() };
                localforage.setItem( currentWidget+":"+ caller  +":"+badgeNumber+ ":" +opportunityId, JSON.stringify(object));
                BindSummaryPage(data.data[0]);
                GetHistory(opportunityId);
            }
            else{
                $("[id$='divOppList']").html("");
                $("[id$='divOppList']").append("<div class='errormsg'>" + "No Records Found" + "</div>");
            }
            hideSkeleton("OpportunityDetailViewDiv");
        }).fail(function (xhr, textStatus, errorThrown) {
            $("#OpportunityDetailViewDiv #retryDiv").show();
            hideSkeleton("OpportunityDetailViewDiv");
        });;
    });    
}
function GetHistory(itemId){
    var caller = getFunctionName(arguments.callee.toString());
    localforage.getItem(caller  +":"+userNamephp+ ":" +itemId).then(function (value) {
        var data = JSON.parse(value);
        BindProductPage(data.value);
        hideSkeleton("OpportunityDetailViewDiv");
    }).catch(function (err) {
        var ajaxCall = fetchData(
            {
                itemNo: itemId
            }, $("[id$='divOppList']"), '../../src/scripts/getHistoryData.php', false);
        ajaxCall.done(function (data) {
            if (data.total > 0 && data.data !=null) {
                var object = { value: data, timestamp: new Date().getTime() };
                localforage.setItem(caller  +":"+userNamephp+ ":" +itemId, JSON.stringify(object));
                BindProductPage(data);
            }
            else{
                $("[id$='divProductList']").html("");
                $("[id$='divProductList']").append("<div class='errormsg'>" + "No Records Found" + "</div>");
            }
            hideSkeleton("OpportunityDetailViewDiv");
        }).fail(function (xhr, textStatus, errorThrown) {
            $("#OpportunityDetailViewDiv #retryDiv").show();
            hideSkeleton("OpportunityDetailViewDiv");
        });;
    });    

}
function retryOpportunityDetailView() {
    $("#OpportunityDetailViewDiv #retryDiv").hide();
    showSkeleton("OpportunityDetailViewDiv");
    getDetailViewPage();
}
function fillSubSummaryTemplate(header,data){
    if(data!=null && data!=""){
        var card=SummaryCardTemplate;
        card = card.replace("{Header}", header);
        card = card.replace("{Data}", data);
        return card;
    }
    else{
        return "";
    }
}
function BindSummaryPage(data) {
    var temp = "";
        var sb=SummaryBaseTemplate;
        sb = sb.replace("{ItemNo}", fillSubSummaryTemplate(summaryMetadata.ItemNo,data.itemNo)); 
        sb = sb.replace("{VendorId}", fillSubSummaryTemplate(summaryMetadata.VendorId,data.vendor)); 
        sb = sb.replace("{VendorCode}", fillSubSummaryTemplate(summaryMetadata.VendorCode,data.vendorCode)); 
        sb = sb.replace("{Description}", fillSubSummaryTemplate(summaryMetadata.Description,data.description)); 
        sb = sb.replace("{itemType}", fillSubSummaryTemplate(summaryMetadata.itemType,data.itemTypeCode)); 
        sb = sb.replace("{Size}", fillSubSummaryTemplate(summaryMetadata.Size, data.Size));
        sb = sb.replace("{GrossWt}", fillSubSummaryTemplate(summaryMetadata.GrossWt, data.grossWt)); 
        sb = sb.replace("{DiaWt}", fillSubSummaryTemplate(summaryMetadata.DiaWt,data.diaWt)); 
        sb = sb.replace("{CStoneWt}", fillSubSummaryTemplate(summaryMetadata.CStoneWt,data.cstoneWt)); 
        sb = sb.replace("{GoldWt}", fillSubSummaryTemplate(summaryMetadata.GoldWt,data.goldWt)); 
        sb = sb.replace("{Diamonds}", fillSubSummaryTemplate(summaryMetadata.Diamonds,data.noOfDia)); 
        sb = sb.replace("{SellPrice}", fillSubSummaryTemplate(summaryMetadata.SellPrice,"USD "+data.sellPrice)); 
        sb = sb.replace("{Quantity}", fillSubSummaryTemplate(summaryMetadata.Quantity,data.curStock)); 
        sb = sb.replace("{Timestamp}", fillSubSummaryTemplate(summaryMetadata.Timestamp,data.dt)); 
        sb = sb.replace("{MU}", fillSubSummaryTemplate(summaryMetadata.MU,data.mu));
        sb = sb.replace("{CostPrice}", fillSubSummaryTemplate(summaryMetadata.CostPrice,"USD "+data.costPrice));
        temp+=sb;
    $("[id$='divOppList']").html(temp);
}
function BindProductPage(data){
    var temp = "";
        if (data != null) {
            for (i = 0; i < data.total; i++) {
                var sb = HistoryTemplate;
                sb = sb.replace("{Type}",data.data[i].action.toUpperCase()+" | "+data.data[i].timestamp);
                sb = sb.replace("{Description}",data.data[i].description);
                sb = sb.replace("{VId}",data.data[i].vendor);
                sb = sb.replace("{Qty}",data.data[i].curStock);
                sb = sb.replace("{SellPrice}","$ " + data.data[i].sellPrice);
                sb = sb.replace("{MU}",NullCheckNA(data.data[i].mu));
                sb = sb.replace("{CostPrice}","$ "+NullCheckNA(data.data[i].costPrice));
                sb = sb.replace("{Size}",data.data[i].ringSize);
                sb = sb.replace("{GW}",data.data[i].grossWt);
                sb = sb.replace("{DiaWt}",data.data[i].diaWt);
                sb = sb.replace("{CS}",data.data[i].cstoneWt);
                sb = sb.replace("{Gold}",data.data[i].goldWt);
                sb = sb.replace("{Dia}",data.data[i].noOfDia);
                sb = sb.replace("{ItemType}",data.data[i].itemTypeCode);
                temp += sb;
            }
            // var footersb = HistoryFooterTemplate;
            // footersb = footersb.replace("{opportunitytotal}", NullCheckNA(data.amount));
            // temp += footersb;
            $("[id$='divProductList']").html(temp);
        }
        else {
            $("[id$='divProductList']").html("");
            $("[id$='divProductList']").append("<div class='errormsg'>" + "No Records Found" + "</div>");
        }
}