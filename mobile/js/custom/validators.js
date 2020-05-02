function assignDefaultsIfEmpty() {
    //localforage.getItem('ManagerUserState').then(function (value) {
    //    if (value != null)
    //        userState = JSON.parse(value);
    //    else {
    //        userState = {
    //            FirstName: 'Shubham',
    //            LastName: 'Gupta',
    //            BadgeNumber: '1180711',
    //            Environment: 'prod',
    //            AppVersion: '3.1.3',
    //            SFDCInstance: 'LDELL',
    //            timestamp: new Date().getTime()
    //        };

    //        localforage.setItem('ManagerUserState', JSON.stringify(userState)); 
    //    }
    //    if (userState.SFDCInstance == "LEMC")
    //        SFDCInstance = "1";
    //    else
    //        SFDCInstance = "0";
    //});

    //localforage.getItem('ManagerTokens').then(function (value) {
    //    if (value != null)
    //        managerTokens = JSON.parse(value);
    //    else {
    //        managerTokens = {
    //            SFDCToken: '00D300000006urq!ARUAQAS.kv0SLPDOG9ZE6xtf5fQaiWIDVJzIbI3lQSW7061_QHkKNDToQDyjtCEZbEnqpjhgikZJvG4b25xwa6UPIKbhyxK.',
    //            SFDCInstanceUrl: 'https://dell.my.salesforce.com/',
    //            timestamp: new Date().getTime()
    //        };
    //        localforage.setItem('ManagerTokens', JSON.stringify(managerTokens));
    //    }
    //});
}
function NullCheckNA(data) {
    if (!data)
        return "NA";
    return data;
}
function NullCheck(data) {
    if (data == null || $.trim(data).length == 0)
        return "";
    return data;
} 

function IsUserInfoAvailable() {
    if (config == null || config == undefined || managerTokens == undefined || userState == undefined || SFDCInstance.length == 0)
        return false;
    return true;
}
