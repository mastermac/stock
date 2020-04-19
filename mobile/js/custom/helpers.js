function getFunctionName(name) {
    name = name.substr('function '.length);
    name = name.substr(0, name.indexOf('('));
    return name;
}  
function showLoader() {
    $('#loadingDiv').css("display", "flex");
}
function hideLoader() {
    $('#loadingDiv').fadeOut("slow");
}

function CheckAndRepairNulls(value){
    if(value==null)
        return 0;
    else
        return value;
}

function showSkeleton(actualDiv, isScroll = 0) {
    var skeletonDiv = actualDiv + "-skeleton"
    if ($(skeletonDiv).length > 0) {
        $(skeletonDiv).css("display", "");
    }
    if (isScroll == 0) {
        if (actualDiv != null && $('#' + actualDiv).length > 0) {
            $('#' + actualDiv).css("display", "none");
        }
    }
}
function hideSkeleton(actualDiv, isScroll = 0) {
    var skeletonDiv = actualDiv + "-skeleton"
    if ($(skeletonDiv).length > 0) {
        $(skeletonDiv).css("display", "none");
    }
    if (isScroll == 0) {
        if (actualDiv != null && $('#' + actualDiv).length > 0) {
            $('#' + actualDiv).css("display", "");
        }
    }
} 
function GetSkeletonTemplate(templateType) {
    switch (templateType) {
        case "opportunityWidgetSkeleton": return opportunityWidgetSkeleton; break;
        case "accountWidgetSkeleton": return accountWidgetSkeleton; break;
        case "accountSkeleton": return accountSkeleton; break;
        case "pipelineSkeleton": return pipelineSkeleton; break;
        case "teamSkeleton": return teamSkeleton; break;
        case "detailsListSkeleton": return detailsListSkeleton; break;
        case "attainmentSkeleton": return attainmentSkeleton; break;
        case "attainmentWidgetSkeleton": return attainmentWidgetSkeleton; break;
        case "detailsViewSkeleton": return detailsViewSkeleton; break;
        default: return detailsListSkeleton;
            break;
    }
}
function loadSkeleton() {
    $('div.addSkeleton').each(function (index, value) {
        if ($(this).children().length == 1) {
            var template = GetSkeletonTemplate($(this).attr("template"));
            $(this).append('<' + $(this).children().first().attr('id') + '-skeleton>' + template + '</' + $(this).children().first().attr('id') + '-skeleton>');
        }
    });
}
function getVal(id) {
    return document.getElementById('s_' + id)==null?'':document.getElementById('s_' + id).value;
}

//EOF