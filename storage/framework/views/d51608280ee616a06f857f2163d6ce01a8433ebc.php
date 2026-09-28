<style>
    /* Property Finder Search Panel */
    .pf-search-panel {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 350px;
        max-height: 500px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 9999;
        overflow: hidden;
        animation: slideInFromBottomRight 0.3s ease-out;
    }

    .pf-panel-header {
        background: #495057;
        color: white;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pf-panel-body {
        padding: 15px;
        max-height: 350px;
        overflow-y: auto;
    }

    .pf-panel-footer {
        padding: 10px 15px;
        border-top: 1px solid #dee2e6;
        background: #f8f9fa;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .pf-result-item {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .pf-result-item:hover {
        background-color: #f8f9fa;
    }

    @keyframes slideInFromBottomRight {
        from {
            transform: translateY(100%) translateX(50%);
            opacity: 0;
        }
        to {
            transform: translateY(0) translateX(0);
            opacity: 1;
        }
    }
    .pf-tree-path {
        display: none;
    }
</style>
<!-- Property Finder Search Panel -->
<div id="pfSearchPanel" class="pf-search-panel d-none">
    <div class="pf-panel-header">
        <h6 class="mb-0 text-white">Search At Property Finder</h6>
        <button type="button" class="btn-close btn-close-white" id="closePfPanel"></button>
    </div>
    <div class="pf-panel-body">
        <div class="mb-3">
            <label for="pfSearchInput" class="form-label">Search Community</label>
            <input type="text" class="form-control form-control-sm" id="pfSearchInput" placeholder="Enter community name...">
        </div>
        <div id="pfSearchResults" class="d-none">
            <h6 class="small">Search Results:</h6>
            <div id="pfResultsList"></div>
        </div>
        <div id="pfSearchLoading" class="text-center d-none">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="ms-2 small">Searching...</span>
        </div>
    </div>
    <div class="pf-panel-footer">
        <button type="button" class="btn btn-secondary btn-sm" id="closePfPanelBtn">Close</button>
        <button type="button" class="btn btn-primary btn-sm" id="pfSearchBtn">Search</button>
    </div>
</div>
<script>
    $(document).on("click", "#searchAtPf", function() {
        $("#pfSearchPanel").removeClass("d-none");

        // Auto-populate search input with current community name
        const communityName = $("#name").val();
        if (communityName) {
            $("#pfSearchInput").val(communityName);
        }
        $("#pfSearchBtn").trigger("click");
    });

    // Close panel handlers
    $(document).on("click", "#closePfPanel, #closePfPanelBtn", function() {
        $("#pfSearchPanel").addClass("d-none");
        $("#pfSearchResults").addClass("d-none");
        $("#pfSearchLoading").addClass("d-none");
        $("#pfSearchInput").val("");
    });

    $(document).on("click", "#pfSearchBtn", function() {
        const searchTerm = $("#pfSearchInput").val().trim();

        if (!searchTerm) {
            showToastify("Please enter a search term", "error");
            return;
        }

        $("#pfSearchLoading").removeClass("d-none");
        $("#pfSearchResults").addClass("d-none");

        $.ajax({
            url: "<?php echo e(route('admin.location.extra.pf.search')); ?>",
            type: 'GET',
            dataType: 'json',
            data: {
                search_term: searchTerm
            },
            success: function (result) {
                $("#pfSearchLoading").addClass("d-none");

                if (result.status == '1') {
                    $("#pfSearchResults").removeClass("d-none");

                    if (result.data && Array.isArray(result.data)) {
                        let resultsHtml = '';
                        result.data.forEach((item) => {
                            // Build tree path string
                            let treePath = '';
                            let full_path = '';
                            if (item.tree && Array.isArray(item.tree)) {
                                treePath = item.tree.map(node => `${node.name} (${node.type})`).join(' → ');
                                full_path = item.tree.slice().reverse().map(node => node.name).join(' ,');
                            }

                            // Get type badge color
                            let typeColor = '';
                            switch(item.type) {
                                case 'CITY':
                                    typeColor = 'bg-primary';
                                    break;
                                case 'COMMUNITY':
                                    typeColor = 'bg-success';
                                    break;
                                case 'SUBCOMMUNITY':
                                    typeColor = 'bg-warning';
                                    break;
                                default:
                                    typeColor = 'bg-secondary';
                            }

                            resultsHtml += `
<div class="pf-result-item">
   <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="pf-result-action btn btn-sm btn-outline-primary"
         data-name="${item.name}"
         data-full-path="${full_path}"
         data-lat="${item.coordinates.lat}"
         data-lng="${item.coordinates.lng}"
         data-type="${item.type}"
         data-pf-id="${item.id}">
        <i class="ri-arrow-left-line"></i> Copy
    </div>
       <span class="small text-muted">ID: ${item.id}</span>
    <span class="badge ${typeColor} small">${item.type}</span>
</div>
    <div class="pf-result-content">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <strong class="small">${item.name}</strong>
        </div>
        <div class="mb-1">
            <small class="text-muted">Lat: ${item.coordinates.lat}, Lng: ${item.coordinates.lng}</small>
        <div class="mb-1">
            <small class="text-info pf-full-path" style="cursor: pointer;">${full_path}</small>
</div>
        </div>
        ${treePath ? `
        <div class="pf-tree-path font-12">
            <small class="text-muted">📍 ${treePath}</small>
        </div>
        ` : ''}
    </div>
</div>
`;
                        });
                        $("#pfResultsList").html(resultsHtml);
                    } else {
                        $("#pfResultsList").html('<p class="text-muted small">No results found</p>');
                    }
                } else {
                    $("#pfSearchResults").removeClass("d-none");
                    $("#pfResultsList").html('<p class="text-danger small">Search failed: ' + (result.msg || 'Unknown error') + '</p>');
                }
            },
            error: function (xhr, status, error) {
                $("#pfSearchLoading").addClass("d-none");
                $("#pfSearchResults").removeClass("d-none");
                $("#pfResultsList").html('<p class="text-danger small">Search failed. Please try again.</p>');
                console.log('AJAX Error:', error);
            }
        });
    });



    $(document).on("click", ".pf-result-action", function() {
        let location_id = $(this).data("pf-id");
        let module = $("#module").val();
        let id = $("#id").val();
        $("#pf_id").val(location_id);
      //  $("#latitude").val($(this).data("lat"));
      //  $("#longitude").val($(this).data("lng"));
        $("#pf_path").val($(this).data("full-path"));

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        

        
        
        
    });

    // Close panel when clicking outside
    $(document).on("click", function(event) {
        if (!$(event.target).closest("#pfSearchPanel, #searchAtPf").length) {
            $("#pfSearchPanel").addClass("d-none");
        }
    });
    $(document).on("click", ".pf-full-path", function() {
        $(this).closest('.pf-result-item').find('.pf-tree-path').toggle();
    });
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/location/includes/pfSearch.blade.php ENDPATH**/ ?>