<!DOCTYPE html>
<html>
<head>
    <title>User Tree</title>
    {{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">--}}
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <script src="https://balkangraph.com/js/latest/OrgChart.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.js"></script>--}}
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        html, body {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        #tree {
            width: 100%;
            height: 100%;
        }

        .field_0 {
            font-family: Impact;
        }

        .node.Admin rect {
            fill: #F57C00;
        }

        .node.Sales rect {
            fill: #039BE5;
        }

        .node.Director rect {
            fill: #FFCA28;
        }
    </style>
</head>
<body>

<input type="hidden" id="user_id">
<div style="height: 100%">
    <div class="d-flex justify-content-end align-items-center">

    <a href="{{ route("admin.user.users.index") }}" class="btn btn-secondary m-2">Back</a>
    </div>
    <div id="tree"></div>
    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showModalLongTitle">View Details</h5>
                    <button type="button" class="close btn btn-sm btn-danger" id="close-button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img src="" id="user-img" alt="User Photo" style="width: 100px; height: 100px; border-radius: 50%">
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div>First Name: <span id="first_name"></span></div>
                        </div>
                        <div class="col-lg-6">
                            <div>Last Name: <span id="last_name"></span></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div>Email: <span id="email"></span></div>
                        </div>
                        <div class="col-lg-6">
                            <div>Mobile: <span id="mobile"></span></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div>Roles: <span id="roles"></span></div>
                        </div>
                        <div class="col-lg-6">
                            <div>Parent: <span id="parent"></span></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div>Portals: <button class="btn btn-sm btn-light" id="edit-portal">Edit</button></div>
                        </div>
                        <div class="col-lg-12">
                            <div id="allocated-portals"></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div>Sub Communities: <button class="btn btn-sm btn-light" id="edit-sub-communities">Edit</button></div>
                        </div>
                        <div class="col-lg-12">
                            <div id="allocated-sub-communities"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subCommunitiesModal" tabindex="-1" role="dialog" aria-labelledby="subCommunitiesModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <form id="locationForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subCommunitiesModalLongTitle">Sub Communities</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-4">
                                    <label for="community_id">Community</label>
                                </div>
                                <div class="col-8">
                                    <select name='community_id' id="community_id" class="form-control" style="width: 100%" required>
                                        <option value="">Select</option>
                                        @foreach($communities ?? [] as $community)
                                            <option value="{{ $community->id }}">{{ $community->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">
                                    <label for="sub_communities">Sub Communities</label>
                                </div>
                                <div class="col-8">
                                    <select name='sub_communities[]' id="sub_communities"
                                            class="form-control select2" multiple required style="width: 100%"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="portalsModal" tabindex="-1" role="dialog" aria-labelledby="portalsModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <form id="portalForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="portalsModalLongTitle">Portals</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="row">
                            @foreach($portals ?? [] as $portal)
                                <div class="col-4 mt-2">
                                    <label for="portal-{{ $portal->id }}"
                                           class="form-label">{{ $portal->name }}</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <div class="input-group">
                                        <label class="input-group-text"
                                               for="portal-{{ $portal->id }}">Quota: <span
                                                class="portal-quota-label"
                                                id="portal-{{ $portal->id }}-label">{{ $portal->quota }}</span></label>
                                        <input type="number" class="form-control portal-allocation"
                                               id="portal-{{ $portal->id }}"
                                               name="portals[{{$portal->id}}][allocation]"
                                               max="{{ $portal->quota }}"
                                               placeholder="{{ $portal->name }} allocation">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>

    let selectedNodeId = "";

    $('#sub_communities').select2({
        placeholder: 'Select Sub Communities',
        allowClear: true,
        closeOnSelect: false,
        dropdownParent: $('#subCommunitiesModal')
    });

    $("#community_id").select2({
        placeholder: 'Select Community',
        allowClear: true,
        dropdownParent: $('#subCommunitiesModal')
    })

    $('#portalsModal, #subCommunitiesModal').on('hidden.bs.modal', function (e) {
        getDetails(selectedNodeId);
        getPortalQuotas(selectedNodeId);
        $("#showModal").modal("show")
    })

    function appendSubCommunity(data, selected = false){
        if (!$('#sub_communities').find("option[value='" + data.id + "']").length) {
            let newOption = new Option(data.name, data.id, selected, selected);
            $('#sub_communities').append(newOption);
        }else{
            if (selected){
                let selected = [];
                $("#sub_communities option:selected").map((ind, el) => {
                    selected.push($(el).val())
                });
                const index = selected.indexOf(data.id.toString());
                if (index == -1) {
                    selected.push(data.id);
                } else {
                    selected.splice(index, 1);
                }
                $("#sub_communities").val(selected).trigger("change")
            }
        }
    }

    $(document).on("change", "#community_id", function () {
        let community_id = $(this).val();
        if (community_id) {
            $.ajax({
                url: "{{route('ajax.sub-communities')}}",
                type: 'GET',
                dataType: 'json',
                data: {community_id},
                success: function (result) {
                    result?.data.map(item => {
                        appendSubCommunity(item)
                    })
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }
    })

    $(document).on('submit', '#locationForm', function (event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        formData.append("id", $("#user_id").val())
        $.ajax({
            url: "{{route('admin.user.users.allocate-sub-communities')}}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.status == '1') {
                    $("#subCommunitiesModal").modal("hide")
                } else if (result.status == '2') {
                    let error_message = "";
                    $.each(result.errors, function (key, value) {
                        error_message += '' + key + ': ' + value + ' | ';
                    });
                    showToastify(error_message, "error")
                } else {
                    showToastify(result.msg, "error")
                }
            },
            error: function (jqXhr, json, errorThrown) {
                let data = jqXhr.responseJSON;
                if (data.errors) {

                }
                if (jqXhr.status == 500 || jqXhr.status == 400) {
                    showToastify(data.message, "error")
                }
            },
        });
    });

    function getPortalQuotas(user_id) {
        $(".portal-quota-label").html("")
        $.ajax({
            url: "{{route('ajax.portal-quotas')}}",
            type: 'GET',
            dataType: 'json',
            data: {user_id},
            success: function (result) {
                result?.data.map(item => {
                    $(`#portal-${item.id}-label`).html(`<span class="text-danger" title="Used Quota: ${item.used}">${item.used}</span>/<span class="text-primary" title="Total Quota: ${item.quota}">${item.quota}</span>(<span class="text-success" title="Available Quota: ${item.available}">${item.available}</span>)`)
                    $(`#portal-${item.id}`).attr("max", item.available)
                })
            },
            error: function (result) {
                console.log(result);
            }
        });
    }

    $(document).on('submit', '#portalForm', function (event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        formData.append("id", $("#user_id").val())
        $.ajax({
            url: "{{route('admin.user.users.allocate-portals')}}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.status == '1') {
                    $("#portalsModal").modal("hide")
                } else if (result.status == '2') {
                    let error_message = "";
                    $.each(result.errors, function (key, value) {
                        error_message += '' + key + ': ' + value + ' | ';
                    });
                    showToastify(error_message, "error")
                } else {
                    showToastify(result.msg, "error")
                }
            },
            error: function (jqXhr, json, errorThrown) {
                let data = jqXhr.responseJSON;
                if (data.errors) {
                    $.each(data.errors, function (index, item) {
                        showToastify(item[0], "error")
                    })
                }
                if (jqXhr.status == 500 || jqXhr.status == 400) {
                    showToastify(data.message, "error")
                }
            },
        });
    });

    var showDetails = function () {
        this.nodeId = null;
    };

    showDetails.prototype.init = function (obj) {
        var that = this;
        this.obj = obj;
        this.showDetails = document.getElementById("showModal");
        this.nameInput = document.getElementById("name");
        this.titleInput = document.getElementById("title");
        this.cancelButton = document.getElementById("close-button");
        this.editPortalsButton = document.getElementById("edit-portal");
        this.editSubCommunitiesButton = document.getElementById("edit-sub-communities");

        this.cancelButton.addEventListener("click", function () {
            that.hide();
        });

        this.editPortalsButton.addEventListener("click", function () {
            $("#showModal").modal("hide")
            $("#portalsModal").modal("show")
        });
        this.editSubCommunitiesButton.addEventListener("click", function () {
            $("#showModal").modal("hide")
            $("#subCommunitiesModal").modal("show")
        });
    };

    showDetails.prototype.show = function (nodeId) {
        this.hide();
        this.nodeId = nodeId;
        selectedNodeId = nodeId;
        getDetails(nodeId)
        getPortalQuotas(nodeId);
        $("#showModal").modal("show")
    };

    showDetails.prototype.hide = function (showldUpdateTheNode) {
        $("#showModal").modal("hide")
    };

    var chart = new OrgChart(document.getElementById("tree"), {
        enableSearch: true,
        enableDragDrop: true,
        layout: OrgChart.mixed,
        mouseScrool: OrgChart.none,
        collapse: {
            level: 1,
            allChildren: true
        },
        editUI: new showDetails(),
        nodeBinding: {
            field_0: "name",
            field_1: "roles",
            img_0: "img"
        }
    });

    chart.on('drop', function (sender, draggedNodeId, droppedNodeId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        let formData = new FormData();
        formData.append("id", draggedNodeId)
        formData.append("parent_id", droppedNodeId)
        $.ajax({
            url: "{{route('admin.user.users.change-parent-id')}}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.status == '1') {
                    return true
                }
                return false
            },
            error: function (jqXhr, json, errorThrown) {
                let data = jqXhr.responseJSON;
                return false;
            },
        });
    });

    loadNodes()

    function loadNodes() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('ajax.load-users-tree')}}",
            type: 'GET',
            dataType: 'json',
            data: {},
            success: function (result) {
                if (result.data) {
                    chart.load(result.data);
                    chart.draw(OrgChart.action.init);
                }
            },
            error: function (result) {
                console.log(result);
            }
        });
    }

    function getDetails(id){
        $("#community_id").val("").trigger("change")
        $("#sub_communities").val("").trigger("change")
        $("#user_id").val(id)
        $("#user-img").attr("src", "")
        $("#first_name, #last_name, #email, #mobile, #roles, #parent").text("")
        $("#allocated-portals, #allocated-sub-communities").text("")
        $(".portal-quota-label").html("")
        $(".portal-allocation").val("")
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{route('admin.user.users.get')}}",
            type: 'GET',
            dataType: 'json',
            data: {id},
            success: function (result) {
                if (result.status == '1') {
                    let data = result.data;
                    if (data) {
                        $("#user-img").attr("src", data.thumb_url);
                        let roles = "";
                        data?.roles.map(item => {
                            roles += `${item.name}, `;
                        })
                        roles = roles.substring(0, roles.length - 2);
                        $("#roles").text(roles)

                        let portals = "";
                        data?.allocatedPortals.map(item => {
                            portals += `<div class="badge bg-secondary"><img style="width: 15px; height: 15px" src="${item.thumbnail}" alt=""> ${item.name}: ${item.used}/${item.allocation}</div> `;
                        });
                        $("#allocated-portals").html(portals)

                        data?.portals.map(item => {
                            $(`#portal-${item.portal_id}`).val(item.allocation);
                        })

                        let sub_communities = "";
                        data?.sub_communities.map(item => {
                            sub_communities += `<div class="badge bg-secondary">${item.sub_community?.name}</div> `;
                            appendSubCommunity(item.sub_community, true)
                        })
                        $("#allocated-sub-communities").html(sub_communities)
                        $("#first_name").text(data.first_name)
                        $("#last_name").text(data.last_name)
                        $("#email").text(data.email)
                        $("#mobile").text(data.mobile)
                        $("#community_id").val(data.community_id).trigger("change")
                        $("#parent").text(data.parent ? data.parent.name : "")

                        chart.update({
                            id: data.id,
                            name: data.name,
                            img: data.thumb_url,
                            roles: roles,
                            pid: data.parent_id,
                            "Allocated Portals": portals,
                            "Allocated Sub Communities": sub_communities,
                        });
                        chart.draw();
                    }
                }
            },
            error: function (result) {
                console.log(result);
            }
        });
    }
</script>
</body>
</html>
