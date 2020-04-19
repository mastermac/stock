/*Metrics Script start*/
function InitializeOmniture() {
    console.log("Initializing Omniture");
    Dell = window.Dell || {};
    Dell.Metrics = Dell.Metrics || {};
    Dell.Metrics.sc = Dell.Metrics.sc || {};
    Dell.Metrics.sc.country = "us";         /***dynamic value based on site***/
    Dell.Metrics.sc.language = "en";      /***dynamic value based on site***/
    Dell.Metrics.sc.segment = "corp";      /***always set as corp***/
    Dell.Metrics.sc.customerset = "";  /***dynamic value based on site or empty string***/
    Dell.Metrics.sc.cms = "mysales";
    Dell.Metrics.sc.pagename = "";  /***relevant unique page name or empty string***/
    Dell.Metrics.sc.applicationname = "mysales manager view"; /***relevant application name***/
}

function SetPageNameForOmniture(name) {
    Dell.Metrics.sc.pagename = "us|en|corp|mysales|" + name;
    console.log("sc object = " + Dell.Metrics.sc)
}
InitializeOmniture();
/*Metrics Script end*/