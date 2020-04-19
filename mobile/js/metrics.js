/*Metrics Script start*/
function InitializeOmniture()
{
	Dell = window.Dell || {};
	Dell.Metrics = Dell.Metrics || {};
	Dell.Metrics.sc = Dell.Metrics.sc || {};
	Dell.Metrics.sc.country = "us";         /***dynamic value based on site***/
	Dell.Metrics.sc.language = "en";      /***dynamic value based on site***/
	Dell.Metrics.sc.segment = "corp";      /***always set as corp***/
	Dell.Metrics.sc.customerset = "";  /***dynamic value based on site or empty string***/
	Dell.Metrics.sc.cms = "mysales";  
	Dell.Metrics.sc.pagename = "us|en|corp|mysales|dashboard";  /***relevant unique page name or empty string***/
	Dell.Metrics.sc.applicationname = "mysales manager view"; /***relevant application name***/
}

function SetPageNameForOmniture(name)
{
    if (typeof Dell === "undefined" || typeof Dell.Metris == "undefined" || typeof Dell.Metrics.sc == "undefined")
        InitializeOmniture();
    Dell.Metrics.sc.pagename = name;
    console.log("sc object = " + Dell.Metrics.sc)
}
/*Metrics Script end*/