function fetchAndSaveData() {
   var token = "ax-cap-pro-2024";
  var url = "http://secondary.axcrm.ae/reporting/scripts/google/leads-by-status?secret=" + token;  // Token as a URL param

  // Set the GET request options (without token in the headers)
  var options = {
    "method": "get",
    "headers": {
      "Content-Type": "application/json"
    }
  };

  // Send the GET request
  var response = UrlFetchApp.fetch(url, options);
  var jsonData = JSON.parse(response.getContentText());

  // Open the Google Sheet where you want to save the data
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  sheet.clear(); // Clear any existing content

  // Define the heading row with all lead fields
  var leadFields = [
    'New', 'Unspecified', 'Contacted', 'Viewing Arranged', 'Qualified', 'Successful',
    'Not Answered', 'Unsuccessful', 'Agent', 'Agent External', 'Agent Internal', 'Bayut',
    'Business Card', 'Client referral', 'Cold Call', 'Company Email', 'Database', 'Developer',
    'Direct Call', 'Direct Client', 'Dubizzle', 'Email campaign', 'Event', 'Existing client',
    'Facebook', 'Friend or Relative', 'Google', 'Gulf News', 'Instagram', 'JamesEdition',
    'Landing Page', 'Magazine', 'Newsletter', 'Newspaper advert', 'Online Campaign',
    'Open House', 'Personal Referral', 'Property Finder', 'Referral', 'Roadshow',
    'Search Engine', 'Signboard', 'SMS campaign', 'Social media Campaign', 'Twitter', 'Walk-in',
    'Website', 'Whatsapp', 'Word of Mouth', 'Youtube', 'Other', 'Online', 'Zoom Property',
    'CRM', 'Bitrix-unspecified', 'AX CAPITAL', 'Chat2Desk', 'Telegram', 'Live chat',
    'Listing Portal', 'Emirates.Estate', 'Stand', 'AX Corporate', 'LinkedIn', 'Emaar Stand',
    'Denis', 'Victoria', 'PRIAN', 'HOMESOVERSEAS', 'TRANIO', 'Houza', 'Telesales', 'VK',
    'Whitewill', 'Dialmydata', 'Front Desk Admin', 'AX CONTACTS', 'Yandex', 'AX CHAT'
  ];

  // Define the main heading row (standard columns plus lead fields)
  var headings = [
    'ID', 'Group', 'Agent Name', 'Manager', 'Mobile', 'Total Leads (Current Month)',
    'Average Leads per Month', 'Average Leads per Week', 'Average Leads per Day'
  ].concat(leadFields);

  // Append the heading row to the sheet
  sheet.appendRow(headings);

  // Apply formatting to heading row (first row)
  var headerRange = sheet.getRange(1, 1, 1, headings.length);
  headerRange.setFontWeight('bold');  // Set bold text
  headerRange.setBackground('#D3D3D3');  // Set background to grey

  // Iterate through the data and add it to the sheet
  jsonData.data.forEach(function(entry) {
    // Prepare the row data
    var row = [
      entry.id,
      entry.group,
      entry.name,
      entry.manager,
      entry.mobile,
      entry.total_leads_current_month,
      entry.average_leads_per_month,
      entry.average_leads_per_week,
      entry.average_leads_per_day
    ];

    // Extract all the lead fields and append them to the row
    leadFields.forEach(function(field) {
      row.push(entry.leads[field] || 0);  // Default to 0 if field is missing
    });

    // Append the row to the sheet
    sheet.appendRow(row);

    // Set the agent's name in bold
    var lastRow = sheet.getLastRow();
    sheet.getRange(lastRow, 3).setFontWeight('bold');
  });

  // Auto-resize columns to fit content
  sheet.autoResizeColumns(1, headings.length);
}
