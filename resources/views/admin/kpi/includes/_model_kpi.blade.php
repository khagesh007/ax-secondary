@if ($model == 'Lead')
<option value="New Lead">New Leads</option>
<option value="Successful Lead">Successful Leads</option>
@elseif($model == 'Property')
<option value="New Listing">New Listings</option>
<option value="Tru Check">Tru Check</option>
<option value="Total Listings">Total Listings</option>
@elseif($model == 'ListingLead')
<option value="New Listing Leads">New Listing Leads</option>
<option value="Successful Listing Leads">Successful Listing Leads</option>
@elseif($model == 'Event')
<option value="Total Event">Total Events</option>
@endif
