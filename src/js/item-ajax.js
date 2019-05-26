!function(a){function b(a,b,c){if(8==g){var d=j.width(),e=f.debounce(function(){var a=j.width();d!=a&&(d=a,c())},a);j.on(b,e)}else j.on(b,f.debounce(c,a))}function c(a){window.console&&window.console&&window.console.log&&window.console.log(a)}function d(){var b=a('<div style="width:50px;height:50px;overflow-y:scroll;position:absolute;top:-200px;left:-200px;"><div style="height:100px;width:100%"></div>');a("body").append(b);var c=b.innerWidth(),d=a("div",b).innerWidth();return b.remove(),c-d}function e(a){if(a.dataTableSettings)for(var b=0;b<a.dataTableSettings.length;b++){var c=a.dataTableSettings[b].nTable;if(a[0]==c)return!0}return!1}a.floatThead=a.floatThead||{},a.floatThead.defaults={cellTag:null,headerCellSelector:"tr:first>th:visible",zIndex:1001,debounceResizeMs:10,useAbsolutePositioning:!0,scrollingTop:0,scrollingBottom:0,scrollContainer:function(){return a([])},getSizingRow:function(a){return a.find("tbody tr:visible:first>*")},floatTableClass:"floatThead-table",floatWrapperClass:"floatThead-wrapper",floatContainerClass:"floatThead-container",copyTableClass:!0,debug:!1};var f=window._,g=function(){for(var a=3,b=document.createElement("b"),c=b.all||[];a=1+a,b.innerHTML="<!--[if gt IE "+a+"]><i><![endif]-->",c[0];);return a>4?a:document.documentMode}(),h=null,i=function(){if(g)return!1;var b=a("<table><colgroup><col></colgroup><tbody><tr><td style='width:10px'></td></tbody></table>");a("body").append(b);var c=b.find("col").width();return b.remove(),0==c},j=a(window),k=0;a.fn.floatThead=function(l){if(l=l||{},!f&&(f=window._||a.floatThead._,!f))throw new Error("jquery.floatThead-slim.js requires underscore. You should use the non-lite version since you do not have underscore.");if(8>g)return this;if(null==h&&(h=i(),h&&(document.createElement("fthtr"),document.createElement("fthtd"),document.createElement("fthfoot"))),f.isString(l)){var m=l,n=this;return this.filter("table").each(function(){var b=a(this).data("floatThead-attached");if(b&&f.isFunction(b[m])){var c=b[m]();"undefined"!=typeof c&&(n=c)}}),n}var o=a.extend({},a.floatThead.defaults||{},l);return a.each(l,function(b){b in a.floatThead.defaults||!o.debug||c("jQuery.floatThead: used ["+b+"] key to init plugin, but that param is not an option for the plugin. Valid options are: "+f.keys(a.floatThead.defaults).join(", "))}),this.filter(":not(."+o.floatTableClass+")").each(function(){function c(a){return a+".fth-"+y+".floatTHead"}function i(){var b=0;A.find("tr:visible").each(function(){b+=a(this).outerHeight(!0)}),Z.outerHeight(b),$.outerHeight(b)}function l(){var a=z.outerWidth(),b=I.width()||a;if(X.width(b-F.vertical),O){var c=100*a/(b-F.vertical);S.css("width",c+"%")}else S.outerWidth(a)}function m(){C=(f.isFunction(o.scrollingTop)?o.scrollingTop(z):o.scrollingTop)||0,D=(f.isFunction(o.scrollingBottom)?o.scrollingBottom(z):o.scrollingBottom)||0}function n(){var b,c;if(V)b=U.find("col").length;else{var d;d=null==o.cellTag&&o.headerCellSelector?o.headerCellSelector:"tr:first>"+o.cellTag,c=A.find(d),b=0,c.each(function(){b+=parseInt(a(this).attr("colspan")||1,10)})}if(b!=H){H=b;for(var e=[],f=[],g=[],i=0;b>i;i++)e.push('<th class="floatThead-col"/>'),f.push("<col/>"),g.push("<fthtd style='display:table-cell;height:0;width:auto;'/>");f=f.join(""),e=e.join(""),h&&(g=g.join(""),W.html(g),bb=W.find("fthtd")),Z.html(e),$=Z.find("th"),V||U.html(f),_=U.find("col"),T.html(f),ab=T.find("col")}return b}function p(){if(!E){if(E=!0,J){var a=z.width(),b=Q.width();a>b&&z.css("minWidth",a)}z.css(db),S.css(db),S.append(A),B.before(Y),i()}}function q(){E&&(E=!1,J&&z.width(fb),Y.detach(),z.prepend(A),z.css(eb),S.css(eb))}function r(a){J!=a&&(J=a,X.css({position:J?"absolute":"fixed"}))}function s(a,b,c,d){return h?c:d?o.getSizingRow(a,b,c):b}function t(){var a,b=n();return function(){var c=s(z,_,bb,g);if(c.length==b&&b>0){if(!V)for(a=0;b>a;a++)_.eq(a).css("width","");q();var d=[];for(a=0;b>a;a++)d[a]=c.get(a).offsetWidth;for(a=0;b>a;a++)ab.eq(a).width(d[a]),_.eq(a).width(d[a]);p()}else S.append(A),z.css(eb),S.css(eb),i()}}function u(a){var b=I.css("border-"+a+"-width"),c=0;return b&&~b.indexOf("px")&&(c=parseInt(b,10)),c}function v(){var a,b=I.scrollTop(),c=0,d=L?K.outerHeight(!0):0,e=M?d:-d,f=X.height(),g=z.offset(),i=0;if(O){var k=I.offset();c=g.top-k.top+b,L&&M&&(c+=d),c-=u("top"),i=u("left")}else a=g.top-C-f+D+F.horizontal;var l=j.scrollTop(),m=j.scrollLeft(),n=I.scrollLeft();return b=I.scrollTop(),function(k){if("windowScroll"==k?(l=j.scrollTop(),m=j.scrollLeft()):"containerScroll"==k?(b=I.scrollTop(),n=I.scrollLeft()):"init"!=k&&(l=j.scrollTop(),m=j.scrollLeft(),b=I.scrollTop(),n=I.scrollLeft()),!h||!(0>l||0>m)){if(R)r("windowScrollDone"==k?!0:!1);else if("windowScrollDone"==k)return null;g=z.offset(),L&&M&&(g.top+=d);var o,s,t=z.outerHeight();if(O&&J){if(c>=b){var u=c-b;o=u>0?u:0}else o=P?0:b;s=i}else!O&&J?(l>a+t+e?o=t-f+e:g.top>l+C?(o=0,q()):(o=C+l-g.top+c+(M?d:0),p()),s=0):O&&!J?(c>b||b-c>t?(o=g.top-l,q()):(o=g.top+b-l-c,p()),s=g.left+n-m):O||J||(l>a+t+e?o=t+C-l+a+e:g.top>l+C?(o=g.top-l,p()):o=C,s=g.left-m);return{top:o,left:s}}}}function w(){var a=null,b=null,c=null;return function(d,e,f){null==d||a==d.top&&b==d.left||(X.css({top:d.top,left:d.left}),a=d.top,b=d.left),e&&l(),f&&i();var g=I.scrollLeft();J&&c==g||(X.scrollLeft(g),c=g)}}function x(){if(I.length){var a=I.width(),b=I.height(),c=z.height(),d=z.width(),e=d>a?G:0,f=c>b?G:0;F.horizontal=d>a-f?G:0,F.vertical=c>b-e?G:0}}var y=k,z=a(this);if(z.data("floatThead-attached"))return!0;if(!z.is("table"))throw new Error('jQuery.floatThead must be run on a table element. ex: $("table").floatThead();');var A=z.find("thead:first"),B=z.find("tbody:first");if(0==A.length)throw new Error("jQuery.floatThead must be run on a table that contains a <thead> element");var C,D,E=!1,F={vertical:0,horizontal:0},G=d(),H=0,I=o.scrollContainer(z)||a([]),J=o.useAbsolutePositioning;null==J&&(J=o.scrollContainer(z).length);var K=z.find("caption"),L=1==K.length;if(L)var M="top"===(K.css("caption-side")||K.attr("align")||"top");var N=a('<fthfoot style="display:table-footer-group;"/>'),O=I.length>0,P=!1,Q=a([]),R=9>=g&&!O&&J,S=a("<table/>"),T=a("<colgroup/>"),U=z.find("colgroup:first"),V=!0;0==U.length&&(U=a("<colgroup/>"),V=!1);var W=a('<fthrow style="display:table-row;height:0;"/>'),X=a('<div style="overflow: hidden;"></div>'),Y=a("<thead/>"),Z=a('<tr class="size-row"/>'),$=a([]),_=a([]),ab=a([]),bb=a([]);if(Y.append(Z),z.prepend(U),h&&(N.append(W),z.append(N)),S.append(T),X.append(S),o.copyTableClass&&S.attr("class",z.attr("class")),S.attr({cellpadding:z.attr("cellpadding"),cellspacing:z.attr("cellspacing"),border:z.attr("border")}),S.css({borderCollapse:z.css("borderCollapse"),border:z.css("border")}),S.addClass(o.floatTableClass).css("margin",0),J){var cb=function(a,b){var c=a.css("position"),d="relative"==c||"absolute"==c;if(!d||b){var e={paddingLeft:a.css("paddingLeft"),paddingRight:a.css("paddingRight")};X.css(e),a=a.wrap("<div class='"+o.floatWrapperClass+"' style='position: relative; clear:both;'></div>").parent(),P=!0}return a};O?(Q=cb(I,!0),Q.append(X)):(Q=cb(z),z.after(X))}else z.after(X);X.css({position:J?"absolute":"fixed",marginTop:0,top:J?0:"auto",zIndex:o.zIndex}),X.addClass(o.floatContainerClass),m();var db={"table-layout":"fixed"},eb={"table-layout":z.css("tableLayout")||"auto"},fb=z[0].style.width||"";x();var gb,hb=function(){(gb=t())()};hb();var ib=v(),jb=w();jb(ib("init"),!0);var kb=f.debounce(function(){jb(ib("windowScrollDone"),!1)},300),lb=function(){jb(ib("windowScroll"),!1),kb()},mb=function(){jb(ib("containerScroll"),!1)},nb=function(){m(),x(),hb(),ib=v(),(jb=w())(ib("resize"),!0,!0)},ob=f.debounce(function(){x(),m(),hb(),ib=v(),jb(ib("reflow"),!0)},1);O?J?I.on(c("scroll"),mb):(I.on(c("scroll"),mb),j.on(c("scroll"),lb)):j.on(c("scroll"),lb),j.on(c("load"),ob),b(o.debounceResizeMs,c("resize"),nb),z.on("reflow",ob),e(z)&&z.on("filter",ob).on("sort",ob).on("page",ob),z.data("floatThead-attached",{destroy:function(){var a=".fth-"+y;q(),z.css(eb),U.remove(),h&&N.remove(),Y.parent().length&&Y.replaceWith(A),z.off("reflow"),I.off(a),P&&(I.length?I.unwrap():z.unwrap()),J&&z.css("minWidth",""),X.remove(),z.data("floatThead-attached",!1),j.off(a)},reflow:function(){ob()},setHeaderHeight:function(){i()},getFloatContainer:function(){return X},getRowGroups:function(){return E?X.find("thead").add(z.find("tbody,tfoot")):z.find("thead,tbody,tfoot")}}),k++}),this}}(jQuery),function(a){a.floatThead=a.floatThead||{},a.floatThead._=window._||function(){var b={},c=Object.prototype.hasOwnProperty,d=["Arguments","Function","String","Number","Date","RegExp"];return b.has=function(a,b){return c.call(a,b)},b.keys=function(a){if(a!==Object(a))throw new TypeError("Invalid object");var c=[];for(var d in a)b.has(a,d)&&c.push(d);return c},a.each(d,function(){var a=this;b["is"+a]=function(b){return Object.prototype.toString.call(b)=="[object "+a+"]"}}),b.debounce=function(a,b,c){var d,e,f,g,h;return function(){f=this,e=arguments,g=new Date;var i=function(){var j=new Date-g;b>j?d=setTimeout(i,b-j):(d=null,c||(h=a.apply(f,e)))},j=c&&!d;return d||(d=setTimeout(i,b)),j&&(h=a.apply(f,e)),h}},b}()}(jQuery);

var lastOperationCount=0;
$(document).ready(function() {
    var page = 1;
    var vendorPage = 1;
    var vendorcurrent_page = 1;
    var vendortotal_page = 0;
    var vendoris_ajax_fire = 0;
    var vendorsno = 1;
    var vendorvisible = 10;
    var vendorperPage = 100;
    var current_page = 1;
    var total_page = 0;
    var is_ajax_fire = 0;
    var sno = 1;
    var visible = 10;
    var perPage = 50;
    var start = '0000-00-00';
    var end = '0000-00-00';
    var pd=null;
    var pdCopy=null;
    var opt= {shouldSort: true, threshold: 0.4, location: 0, distance: 100, maxPatternLength: 32, minMatchCharLength: 1, keys: ["itemNo"]};
    var fuse=null;
    var fuseResult=null;
    localforage.setDriver([localforage.INDEXEDDB,localforage.WEBSQL,localforage.LOCALSTORAGE]);
    refreshCache();
    function refreshCache(){
    	$.ajax({
            url: url + 'src/scripts/getD.php'
        }).done(function(data) {
 		localforage.setItem("pd", data, function(){
 			localforage.getItem("pd").then(function(readValue) {
    				pd= JSON.parse(readValue);
    			});
 		});	
	});
    }
    function searchCache(){
  $('.ajax-loader').css("visibility", "visible");
  pdCopy=pd;
  var e_dt = "";
        var s_dt = "";
        s_dt = window.start;
        e_dt = window.end;
        
  if(hasSomeValue(getVal('itemId'))){
    performSearch("itemId", "itemNo");
  }
  if(hasSomeValue(getVal('vendor'))){
    performSearch("vendor", "vendor");
  }
  if(hasSomeValue(getVal('vendorCode'))){
    performSearch("vendorCode", "vendorCode");
  }
  if(hasSomeValue(getVal('description'))){
    performSearch("description", "description");
  }
  if(hasSomeValue(getVal('itemTypeCode'))){
    performSearch("itemTypeCode", "itemTypeCode");
  }
  if(hasSomeValue(getVal('grossWt'))){
    performSearch("grossWt", "grossWt");
  }
  if(hasSomeValue(getVal('diaWt'))){
    performSearch("diaWt", "diaWt");
  }
  if(hasSomeValue(getVal('cstoneWt'))){
    performSearch("cstoneWt", "cstoneWt");
  }
  if(hasSomeValue(getVal('goldWt'))){
    performSearch("goldWt", "goldWt");
  }
  if(hasSomeValue(getVal('goldWt'))){
    performSearch("goldWt", "goldWt");
  }
  if(hasSomeValue(getVal('goldWt'))){
    performSearch("goldWt", "goldWt");
  }
  if(hasSomeValue(getVal('sellPrice'))){
    performSearch("sellPrice", "sellPrice");
  }
  if(hasSomeValue(getVal('curStock'))){
    performSearch("curStock", "curStock");
  }
  if(hasSomeValue(getVal('ringSize'))){
    performSearch("ringSize", "ringSize");
  }
  if(hasSomeValue(getVal('styleCode'))){
    performSearch("styleCode", "styleCode");
  }
  if(hasSomeValue(s_dt) && hasSomeValue(e_dt)){
  var startDate=new Date(s_dt);
  var endDate=new Date(e_dt);
  var tempArray=new Array();
  var pdLen=pdCopy.length;
  var dt=null;
	for(i = 0; i < pdLen; i++){
		dt=new Date(pdCopy[i].dt.substr(0,10));
		if(dt >=startDate && dt <= endDate) {
   			tempArray.push(pdCopy[i]);
		}
	}
	pdCopy=tempArray;
  }
  if(hasSomeValue(getVal('itemIdExt'))){
  	performSpecialSearch("itemIdExt");
  }
  total_page = Math.ceil(pdCopy.length / perPage);
  if(total_page>=1){
            current_page = page;
            visible = total_page;
            if (total_page > 8) visible = 7;
            $('#pagination').empty();
            $('#pagination').removeData("twbs-pagination");
            $('#pagination').unbind("page");
            $('#pagination').twbsPagination({
                totalPages: total_page,
                visiblePages: visible,
                initiateStartPageClick: false,
                onPageClick: function(event, pageL) {
                    	page = pageL;
                    if (is_ajax_fire != 0) {
                        searchCache();
                    }
                }
            });
            page=current_page;
  if(pdCopy.length<=perPage)
  	manageRow(pdCopy);
  else          
  	manageRow(pdCopy.slice(((page - 1) * perPage),((page - 1) * perPage)+perPage));
  is_ajax_fire = 1;
  
  }
            
  $('.ajax-loader').css("visibility", "hidden");     
}
function performSearch(id, name){
    opt={shouldSort: true, threshold: 0.0, location: 0, distance: 100, maxPatternLength: 32, minMatchCharLength: 1, keys: [name]};
    if(name=="description" || name=="itemTypeCode")
    	opt={shouldSort: true,tokenize: true,  matchAllTokens: true,  findAllMatches: true, threshold: 0.0, location: 0, distance: 100, maxPatternLength: 32, minMatchCharLength: 1, keys: [name]};
    
    fuse = new Fuse(pdCopy, opt); // "list" is the item array
    pdCopy= fuse.search(getVal(id));
}
function performSpecialSearch(id){
    opt={shouldSort: true, threshold: 0.0, location: 0, distance: 100, maxPatternLength: 32, minMatchCharLength: 1, keys: ["itemNo"]};
    var res = getVal(id).split(",");
    fuse = new Fuse(pdCopy, opt); 
    var tmpArray=new Array();
    var iterativeValue;
    var notFound="";
    var c=0;
    for(var i=0;i<res.length;i++){
    	iterativeValue=fuse.search(res[i]);
    	if(iterativeValue.length>0){
    		tmpArray[c++]=iterativeValue[0];
        }
        else{
            notFound+=res[i]+", ";
        }
    }
    pdCopy= tmpArray;
    if(notFound.length>0)
    {
        toastr.error(notFound, 'Some ItemCodes Not Found!', {
            timeOut: 10000
        });
    }
}

    
   
    $(".sticky-header").floatThead({
        scrollingTop: 0
    });
    $(".floatThead-container.sticky-header").removeClass("table-bordered");
    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').fadeIn();
        }
        else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function() {
        $('#back-to-top').fadeOut();
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
    $('#back-to-top').fadeOut();
    (function($) {
        $.fn.extend({
            donetyping: function(callback, timeout) {
                timeout = timeout || 500; // 1 second default timeout
                var timeoutReference,
                    doneTyping = function(el) {
                        if (!timeoutReference) return;
                        timeoutReference = null;
                        callback.call(el);
                    };
                return this.each(function(i, el) {
                    var $el = $(el);
                    // Chrome Fix (Use keyup over keypress to detect backspace)
                    $el.is(':input') && $el.on('keyup keypress paste change', function(e) {
                        // This catches the backspace button in chrome, but also prevents
                        // the event from triggering too preemptively. Without this line,
                        // using tab/shift+tab will make the focused element fire the callback.
                        if (e.type == 'keyup' && e.keyCode != 8) return;
                        // Check if timeout has been set. If it has, "reset" the clock and
                        // start over again.
                        if (timeoutReference) clearTimeout(timeoutReference);
                        timeoutReference = setTimeout(function() {
                            // if we made it here, our timeout has elapsed. Fire the
                            // callback
                            doneTyping(el);
                        }, timeout);
                    }).on('blur', function() {
                        // If we can, fire the event since we're leaving the field
                        doneTyping(el);
                    });
                });
            }
        });
    })(jQuery);

    function cb(start, end) {
        if (start == '0000-00-00') {
            $('#s_daterange').val('');
            window.start = '0000-00-00';
            window.end = '0000-00-00';
        }
        else {
            $("#myTable > tbody").html("");
            window.start = start.format('YYYY-MM-DD');
            window.end = end.format('YYYY-MM-DD');
            var st = start.format("MMM Do YY");
            var ed = end.format("MMM Do YY");
            $('#s_daterange').val(st + ' - ' + ed);
            searchCache();
        }
    }
    $('#s_daterange').daterangepicker({
        autoUpdateInput: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
        }
    }, cb);
    cb(start, end);
    manageData();

    function getVal(id) {
        return document.getElementById('s_' + id).value;
    }

    function hasSomeValue(val){
    	if(val=="" || val=="0000-00-00")
    		return false;
    	return true;
    }
    function manageData() {
        $sno = 1;
        var e_dt = "";
        var s_dt = "";
        s_dt = window.start;
        e_dt = window.end;
        $('.ajax-loader').css("visibility", "visible");
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/getData.php',
            data: {
                perPage: perPage,
                page: page,
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
                sdt: s_dt,
                edt: e_dt,
                itemNoExt: getVal('itemIdExt'),
                source: 'page'
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            total_page = Math.ceil(data.total / perPage);
            current_page = page;
            visible = total_page;
            if (total_page > 8) visible = 7;
            $('#pagination').empty();
            $('#pagination').removeData("twbs-pagination");
            $('#pagination').unbind("page");
            $('#pagination').twbsPagination({
                totalPages: total_page,
                visiblePages: visible,
                initiateStartPageClick: false,
                onPageClick: function(event, pageL) {
                    page = pageL;
                    if (is_ajax_fire != 0) {
                        getPageData();
                    }
                }
            });
            if (data.total == 0) $("#myTable > tbody").html("");
            else {
                manageRow(data.data);
                is_ajax_fire = 1;
            }
        });
    }

    function getPageData() {
        $('.ajax-loader').css("visibility", "visible");
        s_dt = window.start;
        e_dt = window.end;
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/getData.php',
            data: {
                perPage: perPage,
                page: page,
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
                sdt: s_dt,
                edt: e_dt,
                itemNoExt: getVal('itemIdExt'),
                source: 'page'
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            manageRow(data.data);
        });
    }

    function getVendorPageData() {
        $('.ajax-loader').css("visibility", "visible");
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/getVendorData.php',
            data: {
                perPage: vendorperPage,
                page: vendorPage,
                vid: ''
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            manageVendorRow(data.data);
        });
    }

    function loadVendorTable() {
        $('.ajax-loader').css("visibility", "visible");
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/getVendorData.php',
            data: {
                perPage: vendorperPage,
                page: vendorPage,
                vid: ''
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            vendortotal_page = 1;
            vendorcurrent_page = vendorPage;
            vendorvisible = vendortotal_page;
            if (vendortotal_page > 8) vendorvisible = 7;
            $('#pagination1').empty();
            $('#pagination1').removeData("twbs-pagination");
            $('#pagination1').unbind("page");
            $('#pagination1').twbsPagination({
                totalPages: vendortotal_page,
                visiblePages: vendorvisible,
                onPageClick: function(event, pageL) {
                    vendorPage = pageL;
                    if (vendoris_ajax_fire != 0) {
                        getVendorPageData();
                    }
                }
            });
            manageVendorRow(data.data);
            vendoris_ajax_fire = 1;
        });
    }

    function ImageExist(url) {
        var img = new Image();
        img.src = url;
        return img.height != 0;
    }

    function manageRow(data) {
        var rows = '';
        sno = 1;
        $.each(data, function(key, value) {
            if (typeof value.sno != 'undefined') {
                rows = rows + '<tr>';
                rows = rows + '<td>' + ((page - 1) * perPage + sno++) + '</td>';
                rows = rows + '<td>' + value.itemNo + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.vendor + '</td>';
                rows = rows + '<td>' + value.vendorCode + '</td>';
                rows = rows + '<td><a data-fancybox="gallery" href="pics/' + value.itemNo + '.JPG" data-caption="' + value.itemNo + '"><img class="lazy" src="pics/' + value.itemNo + '.JPG" onerror="this.src=\'pics/noImage.jpeg\';" alt="" border=3 height=75 width=75></img></a></td>';
                rows = rows + '<td>' + value.description + '</td>';
                rows = rows + '<td>' + value.itemTypeCode + '</td>';
                rows = rows + '<td>' + value.ringSize + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.grossWt + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.diaWt + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.cstoneWt + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.goldWt + '</td>';
                rows = rows + '<td style="text-align: center;">' + value.noOfDia + '</td>';
                rows = rows + '<td style="text-align: right;">$' + value.sellPrice + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.curStock + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.dt + '</td>';
                rows = rows + '<td data-id="' + value.itemNo + '">';
                rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-xs btn-warning edit-item"></button> ';
                if (usertype == 0) rows = rows + '<button class="btn btn-xs btn-danger remove-item"></button>';
                rows = rows + '</td></tr>';
            }
            else
             return false; 
        });
        $("#productData").html(rows);
    }

    function manageVendorRow(data) {
        var rows = '';
        vendorsno = 1;
        $.each(data, function(key, value) {
            if (typeof value.vid != 'undefined') {
                if (value.type == "1") value.type = "Normal";
                else value.type = "Admin";
                if (value.canExport == "1") value.canExport = "Yes";
                else value.canExport = "No";
                if (value.enabled == "1") value.enabled = "True";
                else value.enabled = "False";
                if (value.email == null) value.email = "";
                if (value.pwd == null) value.pwd = "";
                rows = rows + '<tr><td>' + ((vendorPage - 1) * vendorperPage + vendorsno++) + '</td>';
                rows = rows + '<td>' + value.code + '</td>';
                rows = rows + '<td>' + value.name + '</td>';
                rows = rows + '<td style="text-align: right;">' + value.tot + '</td>';
                rows = rows + '<td>' + value.email + '</td>';
                rows = rows + '<td>' + value.pwd + '</td>';
                rows = rows + '<td>' + value.type + '</td>';
                rows = rows + '<td>' + value.series + '</td>';
                rows = rows + '<td>' + value.canExport + '</td>';
                rows = rows + '<td>' + value.enabled + '</td>';
                rows = rows + '<td data-id="' + value.vid + '">';
                rows = rows + '<button class="btn btn-xs btn-warning edit-vendor"></button> ';
                rows = rows + '<button class="btn btn-xs btn-danger remove-vendor"></button>';
                rows = rows + '</td></tr>';
            }
        });
        $("#vendorDataTable").html(rows);
    }
    $("body").on("click", ".remove-item", function() {
        var id = $(this).parent("td").data('id');
        var c_obj = $(this).parents("tr");
        var r = confirm("Sure about deleting?");
        if (r == true) {
            $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: url + 'src/scripts/delete.php',
                data: {
                    id: id
                }
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                refreshCache();
                c_obj.remove();
                toastr.success('Product ' + id + ' deleted successfully.', 'Item Deleted', {
                    timeOut: 5000
                });
                getPageData();
                lastOperationCount=1;
                $('#undo').attr("disabled", false);
            });
        }
    });
    $("body").on("click", ".remove-vendor", function() {
        var id = $(this).parent("td").data('id');
        var c_obj = $(this).parents("tr");
        var r = confirm("Sure about deleting?");
        if (r == true) {
            $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: url + 'src/scripts/deleteVendor.php',
                data: {
                    id: id
                }
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                c_obj.remove();
                toastr.success('Vendor ' + id + ' deleted successfully.', 'Item Deleted', {
                    timeOut: 3000
                });
                loadVendorTable();
                if ($("#vid").val() == id) {
                    $('#addVendorForm')[0].reset();
                    $("#vendorId").removeAttr('readonly');
                    manageData();
                    $(".newUser").hide();
                }
            });
        }
    });
    $("body").on("click", ".edit-item", function() {
        var id = $(this).parent("td").data('id');
        $('.ajax-loader').css("visibility", "visible");
        s_dt = window.start;
        e_dt = window.end;
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/getData.php',
            data: {
                perPage: perPage,
                page: '1',
                itemNo: id,
                vendor: '',
                vendorCode: '',
                description: '',
                itemTypeCode: '',
                grossWt: '',
                diaWt: '',
                cstoneWt: '',
                goldWt: '',
                sellPrice: '',
                curStock: '',
                ringSize: '',
                styleCode: '',
                sdt: '0000-00-00',
                edt: '0000-00-00',
                itemNoExt: '',
                source: 'edit'
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            $("#edit_id").val(data.data[0].itemNo);
            $("#edit_itemId").val(data.data[0].itemNo);
            $("#edit_vendor").val(data.data[0].vendor);
            $("#edit_vendorCode").val(data.data[0].vendorCode);
            $("#edit_description").val(data.data[0].description);
            $("#edit_itemTypeCode").val(data.data[0].itemTypeCode);
            $("#edit_grossWt").val(data.data[0].grossWt);
            $("#edit_diaWt").val(data.data[0].diaWt);
            $("#edit_cstoneWt").val(data.data[0].cstoneWt);
            $("#edit_goldWt").val(data.data[0].goldWt);
            $("#edit_sellPrice").val(data.data[0].sellPrice);
            $("#edit_curStock").val(data.data[0].curStock);
            $("#edit_noOfDia").val(data.data[0].noOfDia);
            $("#edit_ringSize").val(data.data[0].ringSize);
            $("#edit_styleCode").val(data.data[0].styleCode);
            $("#edit_comments").val(data.data[0].comments);
        });
    });
    $("body").on("click", ".edit-vendor", function() {
        var id = $(this).parent("td").data('id');
        $('.ajax-loader').css("visibility", "visible");
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/getVendorData.php',
            data: {
                perPage: vendorperPage,
                page: vendorPage,
                vid: id
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            $("#vendorAction").val("edit");
            $("#vid").val(data.data[0].vid);
            $("#vendorId").attr('readonly', '');
            $("#vendorName").val(data.data[0].name);
            $("#vendorId").val(data.data[0].code);
            if (data.data[0].enabled == "1") $("#accountActive").attr('checked', 'true');
            else $("#accountActive").removeAttr('checked');
            if (data.data[0].canExport == "1") $("#canExport").attr('checked', 'true');
            else $("#canExport").removeAttr('checked');
            if (data.data[0].email == null || data.data[0].pwd == null || data.data[0].email == "" || data.data[0].pwd == "") {
                $("#newAccount").removeAttr('checked');
                $(".newUser").hide(500);
                $("#vendorEmail").val(data.data[0].email);
                $("#vendorPwd").val(data.data[0].pwd);
                $("#vendorType").val(data.data[0].type);
                $("#vendorSeries").val(data.data[0].series);
            }
            else {
                $("#newAccount").attr('checked', 'true');
                $(".newUser").show(500);
                $("#vendorEmail").val(data.data[0].email);
                $("#vendorPwd").val(data.data[0].pwd);
                $("#vendorType").val(data.data[0].type);
                $("#vendorSeries").val(data.data[0].series);
            }
        });
    });
    $(document).ready(function(e) {
        $(".extention").hide();
        $("#s_itemIdExt").val('');
        $("#s_itemIdExt").attr('readonly', '');
        $(".multiId").on('click', (function(e) {
            //        alert("Link");
            $(".extention").toggle(500);
            if ($("#s_itemId").val() != "" || $("#s_itemIdExt").val() != "") {
                $("#s_itemId").val('');
                $("#s_itemIdExt").val('');
                //manageData();
                searchCache();
            }
            var attr = $("#s_itemIdExt").attr('readonly');
            if (typeof attr !== typeof undefined && attr !== false) {
                $("#s_itemIdExt").removeAttr('readonly');
                $("#s_itemId").attr('readonly', '');
            }
            else {
                $("#s_itemIdExt").attr('readonly', '');
                $("#s_itemId").removeAttr('readonly', '');
            }
        }));
        $(".newUser").hide();
        $("#frm_edit").on('submit', (function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                url: url + 'src/scripts/update.php',
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    $('.ajax-loader').css("visibility", "hidden");
                    if (data == "0") toastr.error('Please check your entries and try again!', 'Update Error!', {
                        timeOut: 10000
                    });
                    else if (data == "1") {
                    refreshCache();
                        $('#frm_edit')[0].reset();
                        toastr.success('Record has been updated', 'Update Done!', {
                            timeOut: 10000
                        });
                        $(".modal").modal('hide');
                        manageData();
                        lastOperationCount=1;
                        $('#undo').attr("disabled", false);
                    }
                }
            });
        }));
        $('#newAccount').change(function() {
            $(".newUser").toggle(500);
        });
        $("#itemTypeCode").on('input', function() {
            var val = this.value;
            if ($('#itemTypeCodeList option').filter(function() {
                    return this.value === val;
                }).length) {
                $("#itemTypeError").remove();
                $('.ajax-loader').css("visibility", "visible");
                $.ajax({
                    type: "POST",
                    data: {
                        itemtype: val
                    },
                    url: url + 'src/scripts/getStyleCode.php',
                    success: function(data) {
                        $('.ajax-loader').css("visibility", "hidden");
                        $("#styleCode").val(data);
                    }
                });
            }
            else {
                $("#itemTypeError").remove();
                $('#itemTypeCodeDiv').append('<div id="itemTypeError" style="color: #dc3545;">Please select a value from list.</div>');
            }
        });
        $("#vendorId").on('input', function() {
            var val = this.value;
            if ($('#vendorList option').filter(function() {
                    return this.value === val;
                }).length) {
                $("#vendorModalError").remove();
                $('#vendorModalDiv').append('<div id="vendorModalError" style="color: #dc3545;">Id exists.</div>');
                $('#vendorAdd').prop('disabled', true);
            }
            else {
                $("#vendorModalError").remove();
                $('#vendorAdd').prop('disabled', false);
            }
        });
        $("#vendor").on('input', function() {
            var val = this.value;
            var title = "";
            if ($('#vendorList option').filter(function() {
                    return this.value === val;
                }).length) {
                $("#vendorError").remove();
                $("#vendorList").find("option").each(function() {
                    if ($(this).val() == val) {
                        title = $(this).attr("title");
                    }
                });
                $('#vendor').attr('title', title);
            }
            else {
                $('#vendor').attr('title', '');
                $("#vendorError").remove();
                $('#vendorDiv').append('<div id="vendorError" style="color: #dc3545;">Please select a value from list.</div>');
            }
        });
        $("#edit_itemTypeCode").on('input', function() {
            var val = this.value;
            if ($('#itemTypeCodeList option').filter(function() {
                    return this.value === val;
                }).length) {
                $("#edit_itemTypeError").remove();
                $('.ajax-loader').css("visibility", "visible");
                $.ajax({
                    type: "POST",
                    data: {
                        itemtype: val
                    },
                    url: url + 'src/scripts/getStyleCode.php',
                    success: function(data) {
                        $('.ajax-loader').css("visibility", "hidden");
                        $("#edit_styleCode").val(data);
                    }
                });
            }
            else {
                $("#edit_itemTypeError").remove();
                $('#edit_itemTypeCodeDiv').append('<div id="edit_itemTypeError" style="color: #dc3545;">Please select a value from list.</div>');
            }
        });
        $("#edit_vendor").on('input', function() {
            var val = this.value;
            var title = "";
            if ($('#vendorList option').filter(function() {
                    return this.value === val;
                }).length) {
                $("#edit_vendorError").remove();
                $("#vendorList").find("option").each(function() {
                    if ($(this).val() == val) {
                        title = $(this).attr("title");
                    }
                });
                $('#vendor').attr('title', title);
            }
            else {
                $('#vendor').attr('title', '');
                $("#edit_vendorError").remove();
                $('#edit_vendorDiv').append('<div id="edit_vendorError" style="color: #dc3545;">Please select a value from list.</div>');
            }
        });
        $("#add_product").on('submit', (function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var form_action = $("#create-item").find("form").attr("action");
            $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                type: 'POST',
                url: url + form_action,
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                var resp = JSON.parse(data);
                if (resp['success'] == 1) {
                    $('#add_product')[0].reset();
                    $(".modal").modal('hide');
                    toastr.success('Product entered Successfully.', 'Success!', {
                        timeOut: 7000
                    });
                    manageData();
                    refreshCache();
                    lastOperationCount=1;
                    $('#undo').attr("disabled", false);
                }
                else {
                    toastr.error('Product with same Item No already exist!', 'Error', {
                        timeOut: 7000
                    });
                }
            });
        }));
        $("#addVendorForm").on('submit', (function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                type: 'POST',
                url: url + "src/scripts/addVendor.php",
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                var resp = JSON.parse(data);
                if (resp['success'] == 1) {
                    if ($('#vendorAction').val() == "add") {
                        toastr.success('Vendor entered Successfully.', 'Success!', {
                            timeOut: 5000
                        });
                    }
                    else if ($('#vendorAction').val() == "edit") {
                        toastr.success('Vendor updated Successfully.', 'Success!', {
                            timeOut: 3000
                        });
                        $("#vendorId").removeAttr('readonly');
                    }
                    $('#addVendorForm')[0].reset();
                    manageData();
                    loadVendorTable();
                    $(".newUser").hide();
                    $("#vendorList").append('<option>' + resp['id'] + '</option>');
                }
                else {
                    toastr.error('Something went wrong. Please try again later!', 'Error', {
                        timeOut: 5000
                    });
                }
            });
        }));
        $("#uploadForm").on('submit', (function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                type: 'POST',
                url: url + 'src/scripts/upload.php',
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                refreshCache();
                var resp = JSON.parse(data);
                if (resp['scount'] > 0) {
                    $('#uploadForm')[0].reset();
                    $(".modal").modal('hide');
                    toastr.success(resp['scount'] + " files uploaded!", 'Success!', {
                        timeOut: 7000
                    });
                }
                if (resp['ecount'] > 0) {
                    toastr.error(resp['err'], 'Error', {
                        timeOut: 10000
                    });
                }
                manageData();
                $(".modal").modal('hide');
            });
        }));
        $("#importForm").on('submit', (function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('.ajax-loader').css("visibility", "visible");
            formData.append("src", "importForm");
            $.ajax({
                type: 'POST',
                url: url + 'src/scripts/import.php',
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                refreshCache();
                var resp = JSON.parse(data);
                lastOperationCount=resp['total'];
                if (resp['success'] == 1) {
                    if (resp['impact'] > 0) {
                        toastr.success(resp['impact'] + ' records inserted!', 'Data Imported', {
                            timeOut: 0,
                            closeButton: true
                        });
                    }
                    if (resp['update'] > 0) {
                        toastr.warning(resp['update'] + ' records updated!', 'Data Imported', {
                            timeOut: 0,
                            closeButton: true
                        });
                    }
                    manageData();
                }
                else if (resp['success'] == 0) {
                    toastr.error(resp['msg'], 'Import Failed', {
                        timeOut: 0,
                        closeButton: true
                    });
                    if (resp['impact'] > 0) {
                        toastr.success(resp['impact'] + ' records inserted!', 'Data Imported', {
                            timeOut: 0,
                            closeButton: true
                        });
                    }
                    if (resp['update'] > 0) {
                        toastr.warning(resp['update'] + ' records updated!', 'Data Imported', {
                            timeOut: 0,
                            closeButton: true
                        });
                    }
                }
                $('#undo').attr("disabled", false);
                $('#importForm')[0].reset();
                $(".modal").modal('hide');
            });
        }));
        $("#updateForm").on('submit', (function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('.ajax-loader').css("visibility", "visible");
            formData.append("src", "updateForm");
            $.ajax({
                type: 'POST',
                url: url + 'src/scripts/import.php',
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                refreshCache();
                var resp = JSON.parse(data);
                lastOperationCount=resp['total'];
                if (resp['success'] == 1) {
                    if (resp['update'] > 0) {
                        toastr.warning(resp['update'] + ' records updated!', 'Data Imported', {
                            timeOut: 0,
                            closeButton: true
                        });
                    }
                    manageData();
                }
                else if (resp['success'] == 0) {
                    toastr.error(resp['msg'], 'Update Failed', {
                        timeOut: 0,
                        closeButton: true
                    });
                    if (resp['update'] > 0) {
                        toastr.warning(resp['update'] + ' records updated!', 'Data Imported', {
                            timeOut: 0,
                            closeButton: true
                        });
                    }
                }
                $('#undo').attr("disabled", false);
                $('#updateForm')[0].reset();
                $(".modal").modal('hide');
            });
        }));
    });
    $("#resetFilter").click(function(event) {
        $(this).closest('form').get(0).reset();
        $("#myTable > tbody").html("");
        page = 1;
        manageData();
    });
    $(window).keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    $(".searchField").keyup(function(e) {
        if (e.keyCode == 13) {
            $("#myTable > tbody").html("");
            page = 1;
            //alert("Search");
            //manageData();
            searchCache();
        }
    });
    $('#perPage').change(function(e) {
        perPage = $(this).val();
        page = 1;
        manageData();
    });
    $("#excelExport").click(function(e) {
        var r = confirm("Are you sure you want to export this data into Excel?");
        if (r == true) {
        $('.ajax-loader').css("visibility", "visible");
        s_dt = window.start;
        e_dt = window.end;
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/export.php',
            data: {
                page: page,
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
                sdt: s_dt,
                edt: e_dt,
                itemNoExt: getVal('itemIdExt')
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            window.open('src/scripts/export.xlsx');
        });
        }
    });
    $("#pdfExport").click(function(e) {
        $('.ajax-loader').css("visibility", "visible");
        s_dt = window.start;
        e_dt = window.end;
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/pdfexport.php',
            data: {
                page: page,
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
                sdt: s_dt,
                edt: e_dt,
                itemNoExt: getVal('itemIdExt')
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            window.open('src/scripts/test.pdf');
        });
    });
    
    $("#noImageCodes").click(function(e) {
        $('.ajax-loader').css("visibility", "visible");
        $.ajax({
            dataType: 'json',
            url: url + 'src/scripts/noImageCodes.php',
            data: {
                origin: 'fromAjax',
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            window.open('src/scripts/noImageCodes.xlsx');
        });
    });
    /*$('#nextCodes').on('show.bs.modal', function (event) {
      var resp="<table><thead><tr><th>Item Type<th>Last Code<th>Style Code</th></tr></thead><tbody>";
      $.ajax({
                
                url: url + 'src/scripts/lastItemCode.php',
                success: function(response){
                      resp=resp+response+"</tbody></table>";
                      var modal = $(this);
                      modal.find('.modal-body').append(resp);

            }});
        

    });*/
    $('#addVendorModal').on('show.bs.modal', function(event) {
        $('#addVendorForm')[0].reset();
        $("#vendorId").removeAttr('readonly');
        loadVendorTable();
        $("#newAccount").removeAttr('checked');
        $(".newUser").hide();
    });
    $('#lastCodesButton').on('click', function(event) {
        $('#nextCodesDiv').empty();
        var resp = "<table class='table table-striped table-bordered' style='width:100%;'><thead><tr><th style='width: 200px;'>Item Type</th><th style='width: 80px;'>Last Code</th><th style='width: 100px;'>Style Code</th></tr></thead><tbody>";
        $('.ajax-loader').css("visibility", "visible");
        $.ajax({
            url: url + 'src/scripts/lastItemCode.php',
            success: function(response) {
                $('.ajax-loader').css("visibility", "hidden");
                resp = resp + response + "</tbody></table>";
                $('#nextCodesDiv').append(resp);
                $('#nextCodes').modal('toggle');
            }
        });
    });

    $("#logout").click(function(e) {
        $('.ajax-loader').css("visibility", "visible");
        $.ajax({
            type: "POST",
            url: url + 'src/scripts/checkLogin.php',
            data: {
                fromLogout: "key5678",
                fromLogin: "key12"
            }
        }).done(function(data) {
            $('.ajax-loader').css("visibility", "hidden");
            window.location.href = "login.php?fromPanel=true";
        });
    });
    $("#undo").click(function(e) {
        if(confirm("Are you sure that you want to UNDO your last operation?")){
            $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                dataType: 'json',
                url: url + 'src/scripts/undo.php',
                data: {
                    txn: lastOperationCount,
                }
            }).done(function(data) {
                $('.ajax-loader').css("visibility", "hidden");
                $('#undo').attr("disabled", true);
                refreshCache();
                manageData();
            });
        }
    });
    lightbox.option({
        'wrapAround': true,
        'disableScrolling': false,
        'fitImagesInViewport': true
    });
});

