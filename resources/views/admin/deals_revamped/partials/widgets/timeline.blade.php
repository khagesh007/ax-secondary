<style scoped>
    .offcanvas .offcanvas-header {
        background-color: #f4f4f4 !important;
    }
    .acitivity-item{
        cursor: pointer !important;
    }
</style>
<div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Deal Timeline</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" onclick="closeTimeline()"></button>
    </div>
    <div class="offcanvas-body">

        <!-- canvas body start -->
        <div data-simplebar="init" style="height: calc(100vh - 112px);" class="simplebar-scrollable-y">
            <div class="simplebar-wrapper" style="margin: 0px;">
                <div class="simplebar-height-auto-observer-wrapper">
                    <div class="simplebar-height-auto-observer"></div>
                </div>
                <div class="simplebar-mask">
                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                        <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                            <div class="simplebar-content" style="padding: 0px;">
                                <div class="acitivity-timeline p-4" id="acitivity-timeline"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="simplebar-placeholder" style="width: 399px; height: 985px;"></div>
            </div>
            <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
            </div>
            <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                <div class="simplebar-scrollbar" style="height: 184px; transform: translate3d(0px, 242px, 0px); display: block;"></div>
            </div>
        </div>
        <!-- canvas body end -->
    </div>
</div>
@push("page-js")
<script>
    function iconSelector(event){
        if(event == "created"){
            return "ri-add-line";
        }else if(event == "updated"){
            return "ri-edit-line";
        }
        return  'ri-message-3-line';
    }
    function renderActivityLogItem(item) {

        let attributes = old = "";
    let icon = iconSelector(item.event);

        if(item.properties.attributes.length != 0){
            attributes = "<br><strong>New Data:</strong><br>";
            Object.keys(item.properties.attributes).map(key => {
                if(Array.isArray(item.properties.attributes[key])){
                    let subList = "";
                    Object.keys(item.properties.attributes[key]).map(subKey => {
                        subList += `<li>${subKey}: ${item.properties.attributes[key][subKey]}</li>`
                    })
                    attributes += `<strong>${key}:<strong><ul>${subList}</ul>`;
                }else{
                    attributes += `<li>${key}: ${item.properties.attributes[key]}</li>`
                }
            })
        }

        if(item.properties.old.length != 0){
            old = "<br><strong>Old Data:</strong><br>";
            Object.keys(item.properties?.old).map(key => {
                if(Array.isArray(item.properties.old[key])){
                    let subList = "";
                    Object.keys(item.properties.old[key]).map(subKey => {
                        subList += `<li>${subKey}: ${item.properties.old[key][subKey]}</li>`
                    })
                    old += `<strong>${key}:<strong><ul>${subList}</ul>`;
                }else{
                    old += `<li>${key}: ${item.properties.old[key]}</li>`
                }
            })
        }

        let content = `<div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                            <div class="avatar-xs acitivity-avatar">
                                <div
                                    class="avatar-title rounded-circle bg-danger-subtle text-success">
                                    <i class="${icon}"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">${item.description}</h6>
                            <small class="text-muted mb-2">${moment(item.created_at).format('DD-MM-YYYY h:mm A')}</small>
                            <div class="d-none acitivity-item-more-values">
                                <div>${attributes}</div>
                                <div>${old}</div>
                            </div>
                        </div>
                    </div>`;
        $("#acitivity-timeline").append(content);
    }

    const getDealActivity = () => {
        $("#acitivity-timeline").html("");
        if(!deal_id){
            return;
        }
        $.ajax({
            url: "{{ route('getDealActivityLogs') }}",
            type: "get",
            data: {
                id: deal_id,
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(response) {
                if (response.status == '1') {
                    let data = response.data;
                    data?.map(item => {
                        renderActivityLogItem(item)
                    })
                } else {
                }
            },
            error: function() {
                console.log("Activity log AJAX request was a failure");
            }
        });
    }

    $(document).on("click", ".acitivity-item", function(){
        $(this).find(".acitivity-item-more-values").toggleClass("d-none");
    })
    const closeTimeline = () => {
        $("#offcanvasRight").removeClass("show");
    }
</script>
@endpush
