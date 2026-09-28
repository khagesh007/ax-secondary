<style>
    #chat-circle {
        position: fixed;
        bottom: 150px;
        right: 23px;
        background: #c48667;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        color: white;
        padding: 15px;
        cursor: pointer;
        box-shadow: 0px 3px 16px 0px rgb(0 0 0 / 17%), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
    }


    #chat-overlay {
        background: rgba(255, 255, 255, 0.1);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: none;
    }

    .chat-box {
        display: none;
        background: #efefef;
        position: fixed;
        right: 30px;
        bottom: 50px;
        width: 350px;
        max-width: 85vw;
        max-height: 100vh;
        border-radius: 5px;
        box-shadow: 0px 5px 35px 9px #ccc;
        z-index: 10000;
    }

    .chat-box-toggle {
        float: right;
        margin-right: 15px;
        cursor: pointer;
    }

    .chat-box-header {
        background: #d3a188;
        height: 36px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        color: white;
        text-align: center;
        font-size: 19px;
        padding-top: 5px;
    }


    .chat-input>form {
        margin-bottom: 0;
    }


    @media only screen and (max-width: 500px) {
        .chat-logs {
            height: 40vh;
        }
    }

    .chat-msg.user>.msg-avatar img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        float: left;
        width: 15%;
    }

    .chat-msg.self>.msg-avatar img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        float: right;
        width: 15%;
    }

    .cm-msg-text {
        background: white;
        padding: 10px 15px 10px 15px;
        color: #666;
        max-width: 75%;
        float: left;
        margin-left: 10px;
        position: relative;
        margin-bottom: 20px;
        border-radius: 30px;
    }

    .chat-msg {
        clear: both;
    }

    .chat-msg.self>.cm-msg-text {
        float: right;
        margin-right: 10px;
        background: #5a5eb9;
        color: white;
    }

    .cm-msg-button>ul>li {
        list-style: none;
        float: left;
        width: 50%;
    }

    .cm-msg-button {
        clear: both;
        margin-bottom: 70px;
    }

    .media-body {
        margin-top: 8px !important;
    }
</style>
<div>
    <div id="chat-circle" class="btn btn-raised">
        <div id="chat-overlay"></div>
        <i class="mdi mdi-message"></i>
    </div>
    <div class="chat-box">
        <div class="chat-box-header">
            <small id="deal-notes-header-title"></small>
            <span class="chat-box-toggle"><i class="mdi mdi-close"></i></span>
        </div>
        <div class="row">
            <div class="col-12">
                <div data-simplebar class="px-3 mb-2 scrollable-notes" id="notes">
                    <div id="note-placeholder"></div>
                    <div id="notes-list"></div>
                </div>
                <div class="row align-items-center">
                    <form id="note-form">
                        <div class="input-group">
                            <textarea class="form-control" id="note_input" rows="2" placeholder="Enter a note" required></textarea>
                            <button class="btn btn-primary btn-icon waves-effect waves-light" type="submit" title="Save Note" id="addNoteButton" style="height:auto;">
                                <i class="mdi mdi-send-outline"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@push('page-js')
<script>
    $("#chat-circle").click(function() {
        $("#chat-circle").toggle('scale');
        $(".chat-box").toggle('scale');
    })

    $(".chat-box-toggle").click(function() {
        $("#chat-circle").toggle('scale');
        $(".chat-box").toggle('scale');
    })

    function showPlaceholder(){
        let placeholders = "";
        for(i = 0; i <= 2; i++){
            placeholders += `<div class="d-flex mb-0">
                                <div class="flex-shrink-0"><span class="avatar-xs placeholder rounded-circle"></span></div>
                                <div class="flex-grow-1 ms-3 placeholder-glow"><h5 class="fs-13">
                                    <a href="javascript:void(0)">
                                        <span class="placeholder col-7"></span></a>
                                    <br>
                                    <small class="text-muted"><span class="placeholder col-4"></span></small></h5>
                                    <p class="text-muted"> <span class="placeholder col-6"></span></p></div>
                            </div>`;
        }
        $("#note-placeholder").html(placeholders)
    }

    function renderDealNotes(notes){
        $("#notes-list").html("");
        let dealNotes = "";
        notes?.map(note => {
            dealNotes += `<div class="d-flex mb-0">
                    <div class="flex-shrink-0">
                        <img src="${note?.created_by?.thumb_url}" alt="${note.created_by ? note.created_by.name : 'System'}"
                                class="avatar-xs rounded-circle"/>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fs-13"><a href="javascript:void(0)">${note.created_by ? note.created_by.name : "System" }</a> <small
                            class="text-muted">${moment(note.created_at).format("DD/MM/YYYY hh:mm A")}</small></h5>
                        <p class="text-muted">${ note.note }</p>
                    </div>
                </div>`;
        })
        $("#notes-list").html(dealNotes);
    }

    function getDealNotes(deal_id, step) {
        $.ajax({
            url: "{{ route('admin.work.deals.getDealNotes') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                deal_id,
                step: step
            },
            success: function(response) {
                if(response.status == 1){
                    const {data} = response;
                    renderDealNotes(data);
                }
            },
            error: function(response) {
                console.error(response);
            }
        });
    }

    $(document).on("submit", "#note-form", function(event) {
        event.preventDefault();
        if(deal_id == undefined || deal_id == "" || deal_id == null){
            return
        }
        let note = $("#note_input").val();
        let formData = {
            deal_id: deal_id,
            step: currentStep,
            note: note
        }
        $.ajax({
            url: "{{ route('admin.work.deals.add-note') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            success: function(result) {
                if (result.status == 1) {
                    showToastify(result.msg, "success");
                    $("#note-form").trigger("reset");
                    getDealNotes(deal_id, currentStep);
                } else {
                    showToastify(result.msg, "error");
                }
            },
            error: function(jqXhr, json, errorThrown) {
                showToastify("Something Went Wrong!", "error");
                // $('#screen_loader').addClass('d-none');

            },
        });
    });
</script>
@endpush
