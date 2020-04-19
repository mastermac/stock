function InitSettings() {
    //loadSkeleton();
    createHeader("../common-ux/header.html");
    AssignEvents();

    SetBioAuthSettings();
    $(document).ready(function () {
        $('.mdb-select').materialSelect();
    });

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-full-width",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "500",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

}

function AssignEvents() {
    if (userState != null && userState.IsNotificationEnabled != null) {
        $("#divNotificationRow").show();
        $("#divNotificationRowSeparator").show();
        SetNotificationSettings();
    }

    document.getElementById("notificationSwitch").onclick = function () {
        $("#divProcessingTxt").show();
        $("#notificationSwitchBtn").addClass("disabled");
        if (document.getElementById("notificationSwitch").checked) {
            SetNotification(true);
            window.location.assign("http://notificationon");
        } else {
            SetNotification(false);
            window.location.assign("http://notificationoff");
        }
    };
    $("#selectedBioAuthFrequency").change(function () {
        window.location.assign("http://updatebioauthsettings#" + document.getElementById("bioAuthSwitch").checked + "-" + $("#selectedBioAuthFrequency").val());
    });
}

function SetNotificationSettings() {
    try {
        if (userState != null && userState.IsNotificationEnabled != null) {
            SetNotification(userState.IsNotificationEnabled);
        } else
            SetNotification(false);
    } catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
}
var IsBiometricSensorAvailable = IsBiometricEnrolled = IsBioAuthAvailable = false;
var BiometricLabelText = "";

function SetBioAuthSettings() {
    try {
        if (userState != null) {
            CheckBiometricStatus();
            if (!IsBiometricSensorAvailable) {
                BiometricLabelText = "Device does not support Biometrics";
                $('#bioSwitchDiv').css('display', 'none');
            } else {
                if (userState.IsAndroid != null) {
                    if (userState.IsAndroid)
                        BiometricLabelText = "Fingerprint based authentication";
                    else
                        BiometricLabelText = "Fingerprint/Face based authentication";
                } else
                    BiometricLabelText = "Enhance your security";
            }

            $("#biometricsMsg").text(BiometricLabelText);

            if (userState.IsBioAuthEnabled != null) {
                document.getElementById("bioAuthSwitch").checked = userState.IsBioAuthEnabled;
            } else {
                document.getElementById("bioAuthSwitch").checked = false;
            }
            $("#selectedBioAuthFrequency").val(userState.SelectedBioAuthSetting);
            ToggleAuthFrequencyRow(false);
        }
    } catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
}

function CheckBiometricStatus() {
    if (userState.BiometricStatus != null) {
        switch (userState.BiometricStatus) {
            case 0:
                IsBioAuthAvailable = IsBiometricSensorAvailable = IsBiometricEnrolled = true;
                break;
            case 1:
            case 2:
            case 3:
            case 6:
            case 7:
            case 4:
                IsBioAuthAvailable = IsBiometricSensorAvailable = IsBiometricEnrolled = false;
                break;
            case 5:
                IsBioAuthAvailable = false;
                IsBiometricSensorAvailable = true;
                IsBiometricEnrolled = false;
                break;
            default:
                IsBioAuthAvailable = IsBiometricSensorAvailable = IsBiometricEnrolled = false;
                break;
        }
    } else if (userState.IsBioAuthAvailable != null) {
        IsBioAuthAvailable = IsBiometricSensorAvailable = IsBiometricEnrolled = userState.IsBioAuthAvailable;
    }
}

function SetNotification(isEnabled) {
    try {
         document.getElementById("notificationSwitch").checked = isEnabled;
        if (userState != null && userState.NotificationCategory != null) {
            if (!$("#notityCatSave").length) {
                $("<button class='btn-save btn btn-primary btn-sm' id='notityCatSave' onclick='NotificationCategoryDone();'>Done</button>").insertAfter("#notificationCategorySelect");
            }
            $("#notificationCategorySelect").val(userState.NotificationCategory);
            if (isEnabled)
                $("#notificationCategoryRow").show();
            else
                $("#notificationCategoryRow").hide();
        }
    }
    catch (e) {
        console.log(e.message);
        logToKibana(e);
    }
}

function NotificationSettingUpdated(isOk, value) {
    $("#divProcessingTxt").hide();
    $("#notificationSwitchBtn").removeClass("disabled");
    if (isOk == "false" && value != null) {
        SetNotification(value);
    }
}
document.getElementById("bioAuthSwitch").onclick = ToggleAuthFrequencyRow;

function ToggleAuthFrequencyRow(updateValues = true) {
    if (document.getElementById("bioAuthSwitch").checked) {
        if (IsBioAuthAvailable) {
            $('#bioAuthSettingRow').css('padding-bottom', '0px');
            $('#authFrequencyRow').css('display', '');
        } else {
            updateValues = false;
            document.getElementById("bioAuthSwitch").checked = false;
            if (userState.IsAndroid != null) {
                if (!userState.IsAndroid)
                    toastr["info"]("To use this feature, Please enroll FingerPrint or Face Recognition from phone security settings and restart the app.");
                else {
                    if (userState.IsWorkProfileUser)
                        toastr["info"]("To use this feature, Please enroll fingerprint from phone security settings -> Work Profile and restart the app.");
                    else
                        toastr["info"]("To use this feature, Please enroll fingerprint from phone security settings and restart the app.");
                }
            } else
                toastr["info"]("To enable Biometrics Authentication, first add Finger Print or Face Recognition from Phone’s Security Settings and restart the app.");
        }
    } else {
        $('#authFrequencyRow').css('display', 'none');
        $('#bioAuthSettingRow').css('padding-bottom', '10px');
    }
    if (updateValues) {
        $("#selectedBioAuthFrequency").val(0);
        window.location.assign("http://updatebioauthsettings#" + document.getElementById("bioAuthSwitch").checked + "-" + $("#selectedBioAuthFrequency").val());
    }
}

function NotificationCategoryDone(selectedValues) {
    var select = $("#notificationCategorySelect");
    if (select != null && select.val().length > 0) {
        window.location.assign("http://notificationcategory#" + select.val());
    }
    else {
        window.location.assign("http://notificationcategory#" + "-1");
    }
}