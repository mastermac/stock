const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];

function toTitleCase(str) {
    if (str == null)
        return "";
    return str.toLowerCase().replace(/(?:^|\s)\w/g, function (match) {
        return match.toUpperCase();
    });
}
function ConvertDate(date) {
    try {
        if (date != null) {
            var d = date.split("-");
            if (d.length == 3) {
                var dt = new Date(date);
                return months[dt.getMonth()] + " " + dt.getFullYear();
            }
        }
        return "NA";
    } catch (e) {
        return "NA";
    }
}
/* Amount abbreviations Converter*/
function CurrencyConverter(number) {
    try {

        var decPlaces = 0;
        if (number > 999999999) {
            decPlaces = 3;
        }
        else if (number > 999999) {
            decPlaces = 2;
        }
        else if (number > 999) {
            decPlaces = 1;
        }

        decPlaces = Math.pow(10, decPlaces);
        // Enumerate number abbreviations
        var abbrev = ["K", "M", "B", "T"];
        // Go through the array backwards, so we do the largest first
        for (var i = abbrev.length - 1; i >= 0; i--) {
            // Convert array index to "1000", "1000000", etc
            var size = Math.pow(10, (i + 1) * 3);
            // If the number is bigger or equal do the abbreviation
            if (size <= number) {
                // Here, we multiply by decPlaces, round, and then divide by decPlaces.
                // This gives us nice rounding to a particular decimal place.
                number = Math.round(number * decPlaces / size) / decPlaces;

                // Handle special case where we round up to the next abbreviation
                if ((number == 1000) && (i < abbrev.length - 1)) {
                    number = 1;
                    i++;
                }
                // Add the letter for the abbreviation
                number += '' + abbrev[i];
                break;
            }
        }
    } catch (e) {
        console.log(e.message);
    }
    if(number==''||number==null)
        number=0;
    return number;
};
function getStageColorClassName(stageName) {
    if (stageName.split(" ")[0] == "Plan") {
        return "planStageColor";
    } else if (stageName.split(" ")[0] == "Discover") {
        return "discoverStageColor";
    } else if (stageName.split(" ")[0] == "Qualify") {
        return "qualifyStageColor";
    } else if (stageName.split(" ")[0] == "Commit") {
        return "commitStageColor";
    } else if (stageName.split(" ")[0] == "Propose") {
        return "proposeStageColor";
    } else if (stageName.split(" ")[0] == "Win") {
        return "winStageColor";
    } else if (stageName.split(" ")[0] == "Lost") {
        return "lostStageColor";
    }
} 
