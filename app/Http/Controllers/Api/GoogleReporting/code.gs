Code 1

function fetchDataAndStoreInSheet() {
  // Set the API endpoint URL and token
  var url = "http://secondary.axcrm.ae/reporting/scripts/google/google-listings-report";  // Replace with the actual API endpoint
  var token = "8889998jjjjhh";

  // Set the POST request options
  var options = {
    "method": "get",
    "headers": {
      "Content-Type": "application/json",
      "Authorization": "Bearer " + token
    }
  };

  // Send the POST request
  var response = UrlFetchApp.fetch(url, options);
  var responseData = JSON.parse(response.getContentText());

  // Check if the response was successful
  if (responseData.status && responseData.code === 200) {
    // Get the data array
    var data = responseData.data;

    // Access the active spreadsheet and sheet
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();

    // Clear the existing content
    sheet.clear();

    // Set the headers in the first row
    var headers = ["ID", "Agent Name", "Manager", "Property Count", "PF Rank 100", "PF Verified", "Avg PF Rank",
                   "Bayut Rank 100", "Bayut Verified","Bayut Trucheck", "Bayut Avg Rank", "Dubbizle Rank 100", "Dubbizle Verified",
                   "Dubbizle Avg Rank", "WhatsApp Avg Response"];
    sheet.appendRow(headers);

    // Loop through the data and add rows to the sheet
    data.forEach(function(item) {
      var row = [
        item.id,
        item.agent_name,
        item.manager,
        item.property_count,
        item.property_finder_data.pf_rank['100'],
        item.property_finder_data.pf_verified,
        item.property_finder_data.avg_pf_rank,
        item.bayut_data.original.data.ranks['100'],
        item.bayut_data.original.data.verified,
        item.bayut_data.original.data.trucheck,
        item.bayut_data.original.data.avg_rank,
        item.dubbizle_data.original.data.ranks['100'],
        item.dubbizle_data.original.data.verified,
        item.dubbizle_data.original.data.avg_rank,
        item.property_finder_data.whatsapp_avg_res
      ];
      sheet.appendRow(row);
    });
  } else {
    Logger.log("Failed to fetch data. Status: " + responseData.status + ", Code: " + responseData.code);
  }
}


code 2 (Working)

function fetchDataAndStoreInSheet() {
  // Set the API endpoint URL and token
  var url = "http://secondary.axcrm.ae/reporting/scripts/google/google-listings-report";  // Replace with the actual API endpoint
  var token = "8889998jjjjhh";

  // Set the GET request options
  var options = {
    "method": "get",
    "headers": {
      "Content-Type": "application/json",
      "Authorization": "Bearer " + token
    }
  };

  // Send the GET request
  var response = UrlFetchApp.fetch(url, options);
  var responseData = JSON.parse(response.getContentText());

  // Check if the response was successful
  if (responseData.status && responseData.code === 200) {
    // Get the data array
    var data = responseData.data;

    // Access the active spreadsheet and sheet
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();

    // Clear the existing content
    sheet.clear();

    // Set the headers in the first row
    var headers = ["ID", "Agent Name", "Manager", "Property Count","offPlanCount","readyCount", "PF Rank 100", "PF Verified", "Avg PF   Rank", "Online","Offline","Standard", "Featured","Premium",
                   "Bayut Rank 100", "Bayut Verified","Bayut Trucheck","Bayut Checked","Verified", "Bayut Avg Rank","Bayut Standard","Bayut Signature","Bayut Hot", "Dubbizle Rank 100", "Dubbizle TruCheck",
                   "Dubbizle Avg Rank","Premium","Featured","Standard", "WhatsApp Avg Response"];
    sheet.appendRow(headers);

    // Loop through the data and add rows to the sheet
    data.forEach(function(item) {
      var row = [
        item.id,
        item.agent_name,
        item.manager,
        item.property_count,
        item.property_finder_data.offPlanCount,
        item.property_finder_data.readyCount,
        item.property_finder_data.pf_rank['100'],
        item.property_finder_data.pf_verified,
        item.property_finder_data.avg_pf_rank,
        item.property_finder_data.online,
        item.property_finder_data.offline,
        item.property_finder_data.level['Standard'],
        item.property_finder_data.level['Featured'],
        item.property_finder_data.level['Premium'],
        item.bayut_data.original.data.ranks['100'],
        item.bayut_data.original.data.verified,
        item.bayut_data.original.data.trucheck,
        item.bayut_data.original.data.checked,
        item.bayut_data.original.data.verified,
        item.bayut_data.original.data.avg_rank,
        item.bayut_data.original.data.level['Standard'],
        item.bayut_data.original.data.level['Signature'],
        item.bayut_data.original.data.level['Hot'],
        item.dubbizle_data.original.data.ranks['100'],
        item.dubbizle_data.original.data.trucheck,
        item.dubbizle_data.original.data.avg_rank,
        item.dubbizle_data.original.data.level['Premium'],
        item.dubbizle_data.original.data.level['Featured'],
        item.dubbizle_data.original.data.level['Standard'],
        item.property_finder_data.whatsapp_avg_res
      ];
      sheet.appendRow(row);
    });

    // Sort the data by Manager (column C, index 3)
    var range = sheet.getRange(2, 1, sheet.getLastRow() - 1, sheet.getLastColumn());
    range.sort({column: 3, ascending: true});

    // Apply conditional formatting based on ranks
    applyConditionalFormatting(sheet);

  } else {
    Logger.log("Failed to fetch data. Status: " + responseData.status + ", Code: " + responseData.code);
  }
}

function applyConditionalFormatting(sheet) {
  var rules = sheet.getConditionalFormatRules();

  // Define the colors
  var green = SpreadsheetApp.newConditionalFormatRule()
      .whenNumberGreaterThanOrEqualTo(90)
      .setBackground("#00FF00")  // Green color
      .setRanges([sheet.getRange("E2:E" + sheet.getLastRow()),
                  sheet.getRange("H2:H" + sheet.getLastRow()),
                  sheet.getRange("K2:K" + sheet.getLastRow())])
      .build();

  var orange = SpreadsheetApp.newConditionalFormatRule()
      .whenNumberBetween(70, 89)
      .setBackground("#FFA500")  // Orange color
      .setRanges([sheet.getRange("E2:E" + sheet.getLastRow()),
                  sheet.getRange("H2:H" + sheet.getLastRow()),
                  sheet.getRange("K2:K" + sheet.getLastRow())])
      .build();

  var red = SpreadsheetApp.newConditionalFormatRule()
      .whenNumberLessThan(70)
      .setBackground("#FF0000")  // Red color
      .setRanges([sheet.getRange("E2:E" + sheet.getLastRow()),
                  sheet.getRange("H2:H" + sheet.getLastRow()),
                  sheet.getRange("K2:K" + sheet.getLastRow())])
      .build();

  // Clear existing rules and add new ones
  rules = rules.concat([green, orange, red]);
  sheet.setConditionalFormatRules(rules);
}

