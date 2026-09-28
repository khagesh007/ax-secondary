function initColorPicker(parentElement, targetElement = "", defaultColor = "") {
    $(parentElement).empty();
    $("<div>", {
        id: "color-picker",
        class: "color-picker",
    }).appendTo("#color-picker-parent");
    Pickr.create({
        el: "#color-picker",
        theme: "classic", // or 'monolith', or 'nano'
        swatches: [
            "rgba(244, 67, 54, 1)",
            "rgba(233, 30, 99, 0.95)",
            "rgba(156, 39, 176, 0.9)",
            "rgba(103, 58, 183, 0.85)",
            "rgba(63, 81, 181, 0.8)",
            "rgba(33, 150, 243, 0.75)",
            "rgba(3, 169, 244, 0.7)",
            "rgba(0, 188, 212, 0.7)",
            "rgba(0, 150, 136, 0.75)",
            "rgba(76, 175, 80, 0.8)",
            "rgba(139, 195, 74, 0.85)",
            "rgba(205, 220, 57, 0.9)",
            "rgba(255, 235, 59, 0.95)",
            "rgba(255, 193, 7, 1)",
        ],
        components: {
            preview: true,
            opacity: true,
            hue: true,
            interaction: {
                hex: true,
                rgba: true,
                hsla: true,
                hsva: true,
                cmyk: true,
                input: true,
                clear: true,
                save: true,
            },
        },
        default: defaultColor,
    })
        .on("clear", function (instance) {
            if (targetElement) {
                $(targetElement).val("").trigger("change");
            }
        })
        .on("cancel", function (instance) {
            if (targetElement) {
                $(targetElement)
                    .val(instance.getSelectedColor().toHEXA().toString())
                    .trigger("change");
            }
        })
        .on("change", function (color, instance) {
            if (targetElement) {
                $(targetElement)
                    .val(color.toHEXA().toString())
                    .trigger("change");
            }
        });
}

function urldecode(str) {
    return decodeURIComponent((str + "").replace(/\+/g, "%20"));
}

function QueryStringToJSON(str) {
    str = decodeURI(str);
    var pairs = str.split("&");
    var result = {};
    pairs.forEach(function (pair) {
        pair = pair.split("=");
        var name = pair[0];
        var value = pair[1];
        if (name.length)
            if (result[name] !== undefined) {
                if (!result[name].push) {
                    result[name] = [result[name]];
                }
                result[name].push(value || "");
            } else {
                result[name] = value || "";
            }
    });
    return result;
}

function scrollTop(speed = "fast") {
    $("html, body").animate({ scrollTop: 0 }, speed);
}
function uuidv4() {
    return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    );
}
function isSelect2Initialized(element) {
    return $(element).data("select2") !== undefined;
}

!(function () {
    // form validation used from theme
    "use strict";
    window.addEventListener(
        "load",
        function () {
            var t = document.getElementsByClassName("needs-validation");
            t &&
                Array.prototype.filter.call(t, function (t) {
                    t.addEventListener(
                        "submit",
                        function (e) {
                            !1 === t.checkValidity() &&
                                (e.preventDefault(), e.stopPropagation()),
                                t.classList.add("was-validated");
                        },
                        !1
                    );
                });
        },
        !1
    );
})();

function initPhoneValidator(input = "mobile", hiddenInput = "full_mobile") {
    let inputMobile = document.querySelector(`#${input}`);

    var iMobile = window.intlTelInput(inputMobile, {
        initialCountry: "ae",
        preferredCountries: ["ae"],
        placeholderNumberType: "MOBILE",
        hiddenInput: hiddenInput,
        utilsScript: laravelAssetUrl + "assets/libs/intl-tel-input/js/utils.js",
    });

    return iMobile;
}

if ($("#phone").length) {
    $("#phone").change(function () {
        if (phoneInput.isValidNumber()) {
            $("#phone").removeClass("is-invalid");
            $("#phone").addClass("is-valid");
            // $("#saveBtn").attr("disabled", false);
            $(".phoneValidation").html("");
            $("#phone").removeClass("is-invalid");
            $('input[name="full_phone"]').val(phoneInput.getNumber());
            $('input[name="phone"]').val(phoneInput.getNumber());
        } else {
            $("#phone").removeClass("is-valid");
            $("#phone").addClass("is-invalid");
            // $("#saveBtn").attr("disabled", true);
            $('input[name="full_phone"]').val(phoneInput.getNumber());
            $('input[name="phone"]').val(phoneInput.getNumber());

            $(".phoneValidation").html("Please enter a valid phone number with Country Code.");
        }
        if (phoneInput.isValidNumber() == false ) {
            $(".phoneValidation").html("Please enter a valid phone number with Country Code.");
        }
        if ($("#phone").val() == "" || $("#phone").val() == null) {
            $(".phoneValidation").html("");
            $("#phone").removeClass("is-invalid");
        }
    });
}
if ($("#mobile").length) {
    $("#mobile").change(function () {
        // console.log(mobileInput.getNumber());
        // console.log(mobileInput.isValidNumber());
        if (mobileInput.isValidNumber()) {
            $("#mobile").removeClass("is-invalid");
            $("#mobile").addClass("is-valid");
            // $("#saveBtn").attr("disabled", false);
            $('input[name="full_mobile"]').val(mobileInput.getNumber());
            $('input[name="mobile"]').val(mobileInput.getNumber());
            $(".mobileValidation").html("");
            $("#mobile").removeClass("is-invalid");
        } else {
            $("#mobile").removeClass("is-valid");
            $("#mobile").addClass("is-invalid");
            // $("#saveBtn").attr("disabled", true);
            $('input[name="full_mobile"]').val(mobileInput.getNumber());
            $('input[name="mobile"]').val(mobileInput.getNumber());
        }

        if (mobileInput.isValidNumber() == false) {
            mobileNumberValidatorFalied = 1;
            $(".mobileValidation").html("Please enter a valid Mobile number with Country Code.");
        }else{
            mobileNumberValidatorFalied=0;
        }

        if ($("#mobile").val() == "" || $("#mobile").val() == null) {
            $(".mobileValidation").html("");
            $("#mobile").removeClass("is-invalid");
        }

    });
}


function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    Toastify({
        text: "Copied the Ref # " + $(element).text(),
        close: true,
        duration: 3000,
    }).showToast();
    $temp.remove();
}

function showToastify(
    text = "",
    type = "success",
    duration = 3000,
    gravity = "bottom"
) {
    let style = {};
    if (type == "error") {
        style = {
            background: "linear-gradient(to right, #df2808, #df5c45)",
        };
    }
    Toastify({
        text,
        close: true,
        duration,
        gravity,
        style,
    }).showToast();
}

$(document).on("click", ".userDetails", function (e) {
    e.preventDefault();
    let agent_id = $(this).data("agent-id");
    console.log(agent_id);
    if (agent_id) {
        $.ajax({
            url: userDetailsRoute,
            type: "GET",
            dataType: "json",
            data: { agent_id: agent_id },
            cache: false,

            success: function (result) {
                if (result.status) {
                    $("#user_details_modal").html(result.userDetails);
                    $("#userProfileInformationModal").modal("show");
                } else {
                    console.log(result.status);
                }
            },
            error: function (jqXhr, json, errorThrown) {
                let data = jqXhr.responseJSON;
                if (data.errors) {
                    $.each(data.errors, function (index, item) {
                        showToastify(item[0], "error");
                    });
                }
                if (jqXhr.status == 500 || jqXhr.status == 400) {
                    showToastify(data.msg, "error");
                }
            },
        });
    }
});

function setRadioCheckedByValue(radioName, value) {
    $("input[name=" + radioName + "][value=" + value + "]").attr(
        "checked",
        true
    );
}
const route = function (routeName, replacements = {}) {
    let uri = routeList.hasOwnProperty(routeName)
        ? routeList[routeName]
        : undefined;
    if (uri === undefined) {
        console.error("Cannot find route:", routeName);
        return "";
    } else {
        Object.keys(replacements).forEach((key) => {
            if (replacements[key] !== undefined) {
                uri = uri.replace(
                    new RegExp("{" + key + "\\??}"),
                    replacements[key]
                );
            }
        });
        return uri.replace(/\/{[a-zA-Z]+\?}/, "");
    }
    //this is how this can be used
    //route( 'user.note.store', { user: 123 } );
    //route('admin.dashboard');
};

// $(document).on("click", ".pagination", function() {
//     $(".chk_child").prop("checked", false);
//     $('.multi_options').remove();
//     $('#checkAll').prop("checked", false);
// });
function formatOwnerSelect2(e) {
    let own_mobile = e.mobile ?? $(e.element).data("mobile");
    own_mobile =
        own_mobile != "undefined" && own_mobile != null ? own_mobile : "";
    return e.id
        ? $(
              '<span><img src="https://ui-avatars.com/api/?name=' +
                  e.text +
                  '&color=7F9CF5&background=random" class="img-flag rounded-circle" height="18" /> ' +
                  e.text +
                  "</span> <span class='small ml-5 d-inline-block'> " +
                  (own_mobile != ""
                      ? `<i class='mdi mdi-phone'></i> ` + own_mobile
                      : "") +
                  "</span>"
          )
        : e.text;
}
function formatLeadSelect2(e) {
    let own_mobile = e.mobile ?? $(e.element).data("mobile");
    own_mobile =
        own_mobile != "undefined" && own_mobile != null ? own_mobile : "";
    return e.id
        ? $(
              '<span><img src="https://ui-avatars.com/api/?name=' +
                  e.text +
                  '&color=7F9CF5&background=random" class="img-flag rounded-circle" height="18" /> ' +
                  e.text +
                  "</span> <span class='small ml-5 d-inline-block'> " +
                  (own_mobile != ""
                      ? `<i class='mdi mdi-phone'></i> ` + own_mobile
                      : "") +
                  "</span>"
          )
        : e.text;
}
function formatAgentSelect2(e) {
    let img = e.image ?? $(e.element).data("image");
    img = img != "undefined" && img != null ? img : "";
    return e.id
        ? $(
              '<span><img src="' +
                  img +
                  '" class="img-flag rounded-circle" height="18" /> ' +
                  e.text +
                  "</span>"
          )
        : e.text;
}
function formatPropertySelect2(e) {
    let f_unit_no = e.unit_no ?? $(e.element).data("unit_no");
    let f_ref_no = e.ref_no ?? $(e.element).data("ref_no");
    let f_publish_status = e.publish_status ?? $(e.element).data("publish_status");
    let f_location = e.location ?? $(e.element).data("location");
    let f_type = e.type ?? $(e.element).data("type");
    let f_beds = e.beds ?? $(e.element).data("beds");
    let f_for = e.for ?? $(e.element).data("for");
    let f_first_image =
        e.first_image ?? $(e.element).data("first_image")
            ? $(e.element).data("first_image")
            : "https://ui-avatars.com/api/?name=" +
              f_unit_no +
              "&color=7F9CF5&background=random";
    let imgg =
        f_first_image != "undefined" &&
        f_first_image != null &&
        f_first_image != ""
            ? f_first_image
            : "https://ui-avatars.com/api/?name=" +
              f_unit_no +
              "&color=7F9CF5&background=random";
    return e.id
        ? $(
              '<span><img src="' +
                  imgg +
                  '" class="img-flag rounded-5" width="18" /> ' +
                  f_publish_status +" - "+ f_ref_no +
                  " - " +
                  f_type +
                  " - " +
                  f_unit_no +
                  "</span> <br> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-bed-double'></i>" +
                  f_beds +
                  "</span> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-google-maps'></i>" +
                  f_location +
                " - " + f_for +
                  "</span> "
          )
        : e.text;
}
(function ($) {
    $.fn.getAgentSelect2 = function (
        dropdownParent = "",
        allowClear = true,
        closeOnSelect = true,
        placeholder = "Select Agent"
    ) {
        this.select2({
            placeholder: placeholder,
            allowClear: allowClear,
            closeOnSelect: closeOnSelect,
            dropdownParent: $(dropdownParent),
            templateResult: formatAgentSelect2,
            templateSelection: formatAgentSelect2,
            ajax: {
                url: route('ajax.getUsersSelect2'),
                dataType: 'json',
                type: 'GET',
                processResults({data}) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item.text}`,
                                id: item.id,
                                mobile: item.mobile,
                                email: item.email,
                                image: item.image,
                            };
                        }),
                    };
                },
            },
        });
    };
    $.fn.getUserSelect2 = function (
        dropdownParent = "",
        allowClear = true,
        closeOnSelect = true,
        placeholder = "Select User"
    ) {
        this.select2({
            debug: true,
            placeholder: placeholder,
            allowClear: allowClear,
            closeOnSelect: closeOnSelect,
            dropdownParent: $(dropdownParent),
            templateResult: formatAgentSelect2,
            templateSelection: formatAgentSelect2,
            query: function (query) {
                $.ajax({
                    url: route('ajax.getUsersSelect2'),
                    dataType: 'json',
                    type: 'GET',
                    success: function (data) {
                        var results = $.map(data, function (item) {
                            return {
                                text: `${item.text}`,
                                id: item.id,
                                mobile: item.mobile,
                                email: item.email,
                                image: item.image,
                            };
                        });
                        query.callback({ results: results });
                    },
                });
            },
        });
    };
    $.fn.getPropertySelect2 = function (
        dropdownParent = "",
        allowClear = true,
        closeOnSelect = true,
        agent_id = null,
        placeholder = "Select Property"
    ) {
        this.select2({
            placeholder: placeholder,
            allowClear: allowClear,
            closeOnSelect: closeOnSelect,
            dropdownParent: dropdownParent,
            templateResult: formatPropertySelect2,
            templateSelection: formatAgentSelect2,
            query: function (query) {
                $.ajax({
                    url:
                        route("ajax.properties.select2") +
                        `?agent_id=${agent_id}`,
                    dataType: "json",
                    type: "GET",
                    processResults({ data }) {
                        results: $.map(data, function (item) {
                            return {
                                text: ` ${item?.category} ${item?.unit_no} ${item?.location} ${item.ref_no} ${item.for}`,
                                id: item.id,
                                title: item.title,
                                ref_no: item.ref_no,
                                unit_no: item.unit_no,
                                location: item.location,
                                first_image: item?.first_image?.thumbnail,
                                type: item?.property_category?.title,
                                beds: item?.bed?.name,
                                publish_status: item?.publish_status,
                                for: item?.for,
                            };
                        });
                        query.callback({ results: results });
                    },
                });
            },
        });
    };
    $.fn.propertySelect2 = function (
        dropdownParent = "",
        allowClear = true,
        closeOnSelect = true,
        agent_id = null,
        placeholder = "Select Property"
    ) {
        this.select2({
            placeholder: placeholder,
            allowClear: allowClear,
            closeOnSelect: closeOnSelect,
            dropdownParent: $(dropdownParent),
            templateResult: formatPropertySelect2,
            templateSelection: formatAgentSelect2,
            ajax: {
                url: route("ajax.properties.select2") + `?agent_id=${agent_id}`,
                dataType: "json",
                type: "GET",
                processResults({ data }) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item?.category} ${item?.unit_no} - ${item?.location} - ${item.ref_no} - ${item.for}`,
                                id: item.id,
                                title: item.title,
                                publish_status: item.publish_status,
                                ref_no: item.ref_no,
                                unit_no: item.unit_no,
                                location: item.location,
                                first_image: item.first_image?.thumbnail,
                                type: item?.property_category?.title,
                                beds: item?.bed?.name,
                                for: item?.for,
                            };
                        }),
                    };
                },
            },
        });
    };
    $.fn.contactSelect2 = function (
        dropdownParent = "",
        allowClear = true,
        closeOnSelect = true,
        agent_id = null,
        placeholder = "Select Contact"
    ) {

        this.select2({
            placeholder: placeholder,
            allowClear: allowClear,
            closeOnSelect: closeOnSelect,
            dropdownParent: $(dropdownParent),
            ajax: {
                url: contactSelect2url,
                dataType: "json",
                type: "POST",
                data: function (params) {
                    return {
                        search: params.term,
                        agent_id: agent_id,
                    };
                },
                processResults({ data }) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item.first_name || ""} ${
                                    item.last_name || ""
                                }`,
                                id: item.id,
                                first_name: item.first_name,
                                last_name: item.last_name,
                                full_name:
                                    item.first_name + " " + item.last_name,
                                mobile: item.mobile,
                                email: item.email,
                            };
                        }),
                    };
                },
            },
        });
    };
    $(document).on("select2:open", (e) => {
        const selectId = e.target.id;

        $(
            ".select2-search__field[aria-controls='select2-" +
                selectId +
                "-results']"
        ).each(function (key, value) {
            value.focus();
        })
    })
})( jQuery );

$.fn.getUserLeadSelect2 = function (
    dropdownParent = "",
    allowClear = true,
    closeOnSelect = true,
    placeholder = "Select Lead",
    agent_id = null

) {
    this.select2({
        debug: true,
        placeholder: placeholder,
        allowClear: allowClear,
        closeOnSelect: closeOnSelect,
        dropdownParent: $(dropdownParent),
        templateResult: formatLeadSelect2,
        templateSelection: formatLeadSelect2,
        query: function (query) {

            $.ajax({
                url: route('ajax.leads.select2'),
                dataType: 'json',
                type: 'GET',
                data: function (params) {
                    return {
                        search: params.term,
                        agent_id: agent_id,
                    }
                },
                success: function (data) {
                    var results = $.map(data, function (item) {
                        return {
                            text: `${item.text}`,
                            id: item.id,
                            mobile: item.mobile,
                            email: item.email,
                            image: item.image,
                        };
                    });
                    query.callback({ results: results });
                },
            });
        },
    });
};

function objectToQueryString(obj) {
    const keyValuePairs = [];

    for (const key in obj) {
        if (obj.hasOwnProperty(key) && obj[key] !== undefined) {
            if(typeof obj[key] =='object'){
                const valuesArray = Array.isArray(obj[key]) ? obj[key] : Object.values(obj[key]);
                valuesArray.forEach(value => {
                    keyValuePairs.push(`${encodeURIComponent(key)}[]=${encodeURIComponent(value)}`);
                });

            }else{

                keyValuePairs.push(`${encodeURIComponent(key)}=${encodeURIComponent(obj[key])}`);
            }
        }
    }
    console.log(keyValuePairs);

    return keyValuePairs.join('&');
}

function convertSecondsToDHMS(seconds) {
    //this function is to convert seconds to days, hours, minutes and seconds
    if(seconds == 0) return '';
    var days = Math.floor(seconds / 86400);
    var hours = Math.floor((seconds % 86400) / 3600);
    var minutes = Math.floor((seconds % 3600) / 60);
    var remainingSeconds = parseInt(seconds % 60);

    var result = '';

    if (days > 0) {
        result += days + (days === 1 ? ' day, ' : ' days, ');
    }

    if (hours > 0) {
        result += hours + (hours === 1 ? ' hour, ' : ' hours, ');
    }

    if (minutes > 0) {
        result += minutes + (minutes === 1 ? ' minute, ' : ' minutes, ');
    }

    result += remainingSeconds + (remainingSeconds === 1 ? ' second' : ' seconds');

    return result;
}
function humanReadableString(string) {
    // This will replace _ and make first letter Uppercase
    const firstLetter = string[0].toUpperCase();
    return `${firstLetter} ${string.slice(1).replace("_", " ")}`;
}

//// global date time functions

function globalDateTimeFormat() {
    return "DD-MM-YYYY hh:mm";
}
function globalDateFormat() {
    return "DD-MM-YYYY";
}
function datePickerFormat() {
    return "d-m-Y";
}
function dateTimePickerFormat() {
    return "d-m-Y H:i";
}
function globalDateTime(date) {
    if(date == null) return '';
    return moment(date).format("DD-MM-YYYY hh:mm A");
}
function globalDate(date) {
    if(date == null) return '';
    return moment(date).format("DD-MM-YYYY");
}
function dateFormatHandle(inputDate) {
    // this function will handle the date format from date picker where you can pass the date in any format and it will return the date in the format of DD-MM-YYYY
    //which can be used to set min and max date in date picker
    const parsedDate = new Date(
        inputDate.replace(/(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})/, '$3-$2-$1T$4:$5')
    );
    const formattedDate = `${parsedDate.getFullYear()}-${(parsedDate.getMonth() + 1).toString().padStart(2, '0')}-${parsedDate.getDate().toString().padStart(2, '0')} ${parsedDate.getHours().toString().padStart(2, '0')}:${parsedDate.getMinutes().toString().padStart(2, '0')}`;
    return formattedDate;
}
// check select2

function isSelect2Initiated(element) {
    return $(element).data("select2") !== undefined;
}


//dashboard common function

function recentLeadsDraw(data,elementId,loaderId) {
    $("#"+elementId).html('');
    let totalListing = data.totalLeads;
    let listingData = data.counts;
    for (let key in listingData) {
        if (listingData.hasOwnProperty(key)) {
            let percentage = (parseInt(listingData[key]) / totalListing) * 100;
            $("#"+loaderId).addClass("d-none");
            $("#"+elementId).append(`<tr>
                                                      <td class="fw-medium">${key}</td>
                                                      <td>${listingData[key]}</td>
                                                      <td>
                                                          <div class="d-flex align-items-center">
                                                              <div class="flex-shrink-0 me-1 text-muted fs-13">${percentage.toFixed(2)}%</div>
                                                              <div class="progress progress-sm bg-soft-primary flex-grow-1"
                                                                   style="width: 68%;">
                                                                  <div class="progress-bar bg-primary rounded"
                                                                       role="progressbar" style="width: ${percentage}%"
                                                                       aria-valuenow="${percentage}" aria-valuemin="0"
                                                                       aria-valuemax="100"></div>
                                                              </div>
                                                          </div>
                                                      </td>
                                                  </tr>`);
        }
    }


}

//number format

const numberFormatAX = (value) => {
    if(value == null || value.length == 0) return '';
    let numberFormats = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'AED',
        minimumFractionDigits: 0,
    });
    return  numberFormats.format(value); // AED 100,000
}
const numberFormat = (value, showZero =  false) => {
    if(value == null || value.length == 0 || value == 0 || value == '0' || value == '') {
        if (showZero) return 0;
        return '';
    }
    let numberFormats = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'AED',
        minimumFractionDigits: 0,
    });
    return  numberFormats.format(value).replace(/AED/g, ''); // 100,000
}

const forceNumericInput =(event)  => {
    const input = event.target;
   // input.value = input.value.replace(/\D/g, '');
    input.value = input.value.replace(/[^0-9,]/g, '');
}


const ucwords = (str) => {
    str = str.replace("_", " ")
    return str.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

const calculatePercentage = (total, achieved) => {
    if (total == 0) {
        return "0";
    }
    return ((achieved / total) * 100).toFixed(2);
}

const formatNumberMetricPrefixes =(number) => {
    if (number >= 1000000) {
        return (number / 1000000).toFixed(2) + 'M';
    } else if (number >= 1000) {
        return (number / 1000).toFixed(2) + 'K';
    } else {
        return number.toString();
    }
}
function removeDecimal(value) {
    return Math.trunc(value);
}
