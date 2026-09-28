<style>
    .btn-outline-danger{
        border-color: #f3a696 !important;
        color: #f3a696;
    }
    .btn-outline-dark{
        border-color: #8d9092 !important;
        color: #8d9092;
    }
    /* .dropzone{
        height: 70px !important;
        min-height: 50px !important;
        padding: 0px !important;
    } */
    /* .dropzone .dz-message {
        text-align: center !important;
        margin: 12px 0 !important;
    } */

    .should-update-document-type-item{
        background-color: #fff2ef;
    }

    .btn-outline-success,.btn-outline-danger,.btn-outline-dark{
        font-size: 9px !important;
        padding: 1px 5px !important;
    }

    .view-document-button:hover{
        color: #c48667 !important;
    }

    #attachmentsViewDocumentModalContent{
        height: 100% !important;
        width: 100% !important;
    }

</style>
<div class="text-end">
    <div id="btn-edit-attachments-mode" class=" btn  btn-outline-secondary  w-20 edit-action">
        <i class="mdi mdi-eye"></i> View
    </div>
</div>
<div class="col">
    <div class="row gy-3">
        <div class="col-12">

            @if ($isAlreadyInvited == true && $isDealInviteLeadDocumentUploaded == true)
                <div class="alert alert-success alert-border-left alert-dismissible fade show" role="alert">
                    <i class="ri-notification-line me-1 align-middle fs-16"></i><strong>Your colleague has uploaded lead
                        documents you can move to next step <span id="alert_agent_name"> </span> !</strong>
                    - Thank you for your patience
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                </div>
            @elseif($isAlreadyInvited == true)
                <div class="alert alert-warning alert-border-left alert-dismissible fade show" role="alert">
                    <i class="ri-notification-line me-1 align-middle fs-16"></i><strong>You have invited <span
                        id="alert_agent_name"> </span>
                    to upload the lead documents !</strong>
                    - Please wait for your colleague to upload the lead documents
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                </div>
            @endif
        </div>
        @if($isAllowedToUploadDealDocuments == true)
            <div class="col-12" id="contactDocumentSec">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5>
                                <i class=" ri-shield-user-line" style="color: #c48667; vertical-align: bottom;"></i>
                                <span id="attachments-contact-deal-type"></span> (<span id="attachments-contact-name"></span>)
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="mt-2" id="attachments-contact-document-uploader">
                                    <div class="accordion" id="attachments-contact-document-uploader-accordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="attachments-contact-document-uploader-accorion-required-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#attachments-contact-document-uploader-accorion-required-collabpse" aria-expanded="false" aria-controls="attachments-contact-document-uploader-accorion-required-collabpse">
                                                    List of Documents
                                                </button>
                                            </h2>
                                            <div id="attachments-contact-document-uploader-accorion-required-collabpse" class="accordion-collapse collapse" aria-labelledby="attachments-contact-document-uploader-accorion-required-header" data-bs-parent="#attachments-contact-document-uploader-accordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group" id="attachments-contact-document-uploader-required-checkbox-list-required" style="list-style: none"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card attachments-hide-on-view-mode">
                                        <div class="card-body">
                                            <div id="attachments-contact-dropzone" class="dropzone custom-dropzone"></div>
                                            <div id="attachments-contact-previewContainer" class="d-none"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <textarea  id="contact_docuemnt_note" class="form-control attachments-disable-on-view-mode" placeholder="Password for documents"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="row mt-2" id="attachments-contact-document-uploaded-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($isAllowedToUploadLeadDocuments == true)
            <div id="attachments-lead-document-attachments"></div>
        @endif

        @if($isAllowedToUploadDealDocuments == true)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5>
                                <i class=" ri-folder-reduce-line" style="color: #c48667; vertical-align: bottom;"></i>
                                Other Documents
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="mt-2" id="attachments-deal-document-uploader">
                                    <div class="accordion" id="attachments-deal-document-uploader-accordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="attachments-deal-document-uploader-accorion-required-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#attachments-deal-document-uploader-accorion-required-collabpse" aria-expanded="false" aria-controls="attachments-deal-document-uploader-accorion-required-collabpse">
                                                    List of Documents
                                                </button>
                                            </h2>
                                            <div id="attachments-deal-document-uploader-accorion-required-collabpse" class="accordion-collapse collapse" aria-labelledby="attachments-deal-document-uploader-accorion-required-header" data-bs-parent="#attachments-deal-document-uploader-accordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group" id="attachments-deal-document-uploader-required-checkbox-list-required" style="list-style: none"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card attachments-hide-on-view-mode d-none">
                                        <div class="card-body">
                                            <div id="attachments-deal-dropzone" class="dropzone custom-dropzone"></div>
                                            <div id="attachments-deal-previewContainer" class="d-none"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <textarea  id="other_docuemnt_note" class="form-control attachments-disable-on-view-mode" placeholder="Password for documents"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="row mt-2" id="attachments-deal-document-uploaded-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@push("modals")
    @include('admin.deals_revamped.partials.attachments.viewDocumentModal')
    @include('admin.deals_revamped.partials.attachments.editDocumentModal')
@endpush

@push("page-js")
<link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet">
<script src="{{ URL::asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
<script>

    let rentalRequiredDocumentListForContact = {!! json_encode(getDealRequiredDocumentList('Rental', 'Contact')) !!};
    let rentalRequiredDocumentListForContactAndCompany = {!! json_encode(getDealRequiredDocumentList('Rental', 'Contact', true)) !!};
    let rentalRequiredDocumentListForLead = {!! json_encode(getDealRequiredDocumentList('Rental', 'Lead')) !!};
    let rentalRequiredDocumentListForLeadAndCompany = {!! json_encode(getDealRequiredDocumentList('Rental', 'Lead', true)) !!};
    let rentalRequiredDocumentListForOther = {!! json_encode(getDealRequiredDocumentList('Rental', 'Other')) !!};

    let secondarySaleRequiredDocumentListForContact = {!! json_encode(getDealRequiredDocumentList('Secondary Sale', 'Contact')) !!};
    let secondarySaleRequiredDocumentListForContactAndCompany = {!! json_encode(getDealRequiredDocumentList('Secondary Sale', 'Contact', true)) !!};
    let secondarySaleRequiredDocumentListForLead = {!! json_encode(getDealRequiredDocumentList('Secondary Sale', 'Lead')) !!};
    let secondarySaleRequiredDocumentListForLeadAndCompany = {!! json_encode(getDealRequiredDocumentList('Secondary Sale', 'Lead', true)) !!};
    let secondarySaleRequiredDocumentListForOther = {!! json_encode(getDealRequiredDocumentList('Secondary Sale', 'Other')) !!};

    let primaryResaleRequiredDocumentListForContact = {!! json_encode(getDealRequiredDocumentList('Primary Resale', 'Contact')) !!};
    let primaryResaleRequiredDocumentListForLead = {!! json_encode(getDealRequiredDocumentList('Primary Resale', 'Lead')) !!};
    let primaryResaleRequiredDocumentListForOther = {!! json_encode(getDealRequiredDocumentList('Primary Resale', 'Other')) !!};

    let totalDocumentsCount = 0;

    let openedDocumentUploaders = [];

    let dropzoneDefaultMessage = `<p style="text-align: center; margin: 0;color: #6f6f6f; font-size:15px">
                                        <span>Drag and Drop your files or <u>Browse</u></span>
                                    </p>`;

    var attachmentsMode = "edit";

    var attachmentDocumentsUploaded = {
        "contact": true,
        "deal" : true
    }

    $('#btn-edit-attachments-mode').click(function() {
        if (attachmentsMode == "edit") {
            attachmentsMode = "view";
            attachmentsFieldsDisabled(true);
        } else {
            attachmentsMode = "edit";
            attachmentsFieldsDisabled(false);
        }
    })



    attachmentsFieldsDisabled(true);

    function generateSlug(text) {
        return text
        .toLowerCase()
        .replace(/\s+/g, '-')         // Replace spaces with -
        .replace(/[^\w\-]+/g, '')     // Remove non-word characters
        .replace(/\-\-+/g, '-')       // Replace multiple - with single -
        .replace(/^-+/, '')           // Trim - from start of text
        .replace(/-+$/, '');          // Trim - from end of text
    }

    function initContactDropzone(isCompany = false) {
        if($("#attachments-contact-dropzone").length <= 0){
            return;
        }
        let dropzoneElement = $("#attachments-contact-dropzone")[0].dropzone;
        if(dropzoneElement){
            dropzoneElement.destroy();
        }
        let contactDropzone = new Dropzone("#attachments-contact-dropzone", {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('admin.work.deals.add.deal.doc') }}",
            method: "post",
            paramName: "file",
            previewsContainer: "#attachments-contact-previewContainer",
            maxFilesize: 3, // MB,
            acceptedFiles: ".jpg,.png,.jpeg,.webp,.gif,.pdf,.doc,.docx",
            dictDefaultMessage: dropzoneDefaultMessage,
        });

        contactDropzone.on("sending", function(file, xhr, formData) {
            formData.append("deal_id", deal_id); // DEAl ID
            formData.append("model", "contact");
            formData.append("id", contact_id);//CONTACT ID
            renderUploaderDocumentLoaderItem("attachments-contact-document-uploaded-list");
        });

        contactDropzone.on("success", function(file, response) {
            if (response.status == '1') {
                renderUploaderDocumentItem("attachments-contact-document-uploaded-list", response.data, isCompany);
            }
            let previewElement = file.previewElement;

            file.upload.image_id = response.data.id;

            if (response.status == '1') {
                // $('#image_list').append('<input type="hidden" id="image'+response.data.id+'" name="images[]" value="'+response.data.id+'">');
            } else {
                Toastify({
                    text: 'Error in uploading image',
                    style: {
                        background: "linear-gradient(to right, #df2808, #df5c45)",
                    },
                    close: true,
                    duration: 3000

                }).showToast();
            }
        });

        contactDropzone.on("error", function(file, response) {
            Toastify({
                text: response.msg,
                style: {
                    background: "linear-gradient(to right, #df2808, #df5c45)",
                },
                close: true,
                duration: 3000

            }).showToast();
        });
    }

    function initDealDropzone() {
        if($("#attachments-deal-dropzone").length <= 0){
            return;
        }
        let dropzoneElement = $("#attachments-deal-dropzone")[0].dropzone;
        if(dropzoneElement){
            dropzoneElement.destroy();
        }
        let dealDropzone = new Dropzone("#attachments-deal-dropzone", {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('admin.work.deals.add.deal.doc') }}",
            method: "post",
            paramName: "file",
            previewsContainer: "#attachments-deal-previewContainer",
            maxFilesize: 3, // MB,
            acceptedFiles: ".jpg,.png,.jpeg,.webp,.gif,.pdf,.doc,.docx",
            dictDefaultMessage: dropzoneDefaultMessage,
        });

        dealDropzone.on("sending", function(file, xhr, formData) {
            formData.append("deal_id", deal_id);//DEAL ID
            formData.append("model", "deal");
            formData.append("id", deal_id);//DEAL ID
            renderUploaderDocumentLoaderItem("attachments-deal-document-uploaded-list");
        });

        dealDropzone.on("success", function(file, response) {
            if (response.status == '1') {
                renderUploaderDocumentItem("attachments-deal-document-uploaded-list", response.data)
            }
            let previewElement = file.previewElement;

            file.upload.image_id = response.data.id;

            if (response.status == '1') {
                // $('#image_list').append('<input type="hidden" id="image'+response.data.id+'" name="images[]" value="'+response.data.id+'">');
            } else {
                Toastify({
                    text: 'Error in uploading image',
                    style: {
                        background: "linear-gradient(to right, #df2808, #df5c45)",
                    },
                    close: true,
                    duration: 3000

                }).showToast();
            }
        });

        dealDropzone.on("error", function(file, response) {
            Toastify({
                text: response.msg,
                style: {
                    background: "linear-gradient(to right, #df2808, #df5c45)",
                },
                close: true,
                duration: 3000

            }).showToast();
        });
    }

    function initLeadDropzone(dropzoneId, dropzonePreviewId, lead, documentUploadedListElementId, isCompany = false) {
        if($(`#${dropzoneId}`).length <= 0){
            return;
        }
        let dropzoneElement = $(`#${dropzoneId}`)[0].dropzone;
        if(dropzoneElement){
            dropzoneElement.destroy();
        }
        let dropzone = new Dropzone(`#${dropzoneId}`, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('admin.work.deals.add.deal.doc') }}",
            method: "post",
            paramName: "file",
            previewsContainer: `#${dropzonePreviewId}`,
            maxFilesize: 3, // MB,
            acceptedFiles: ".jpg,.png,.jpeg,.webp,.gif,.pdf,.doc,.docx",
            dictDefaultMessage: dropzoneDefaultMessage,
        });

        dropzone.on("sending", function(file, xhr, formData) {
            formData.append("deal_id", deal_id);//DEAL ID
            formData.append("model", "lead");
            formData.append("id", lead.id);//LEAD ID
            renderUploaderDocumentLoaderItem(documentUploadedListElementId);
        });

        dropzone.on("success", function(file, response) {
            if (response.status == '1') {
                renderUploaderDocumentItem(documentUploadedListElementId, response.data, isCompany)
            }
            let previewElement = file.previewElement;

            file.upload.image_id = response.data.id;

            if (response.status == '1') {
                // $('#image_list').append('<input type="hidden" id="image'+response.data.id+'" name="images[]" value="'+response.data.id+'">');
            } else {
                Toastify({
                    text: 'Error in uploading image',
                    style: {
                        background: "linear-gradient(to right, #df2808, #df5c45)",
                    },
                    close: true,
                    duration: 3000

                }).showToast();
            }
        });

        dropzone.on("error", function(file, response) {
            Toastify({
                text: 'Something went wrong and your file may be not uploaded. Please try again.',
                style: {
                    background: "linear-gradient(to right, #df2808, #df5c45)",
                },
                close: true,
                duration: 3000

            }).showToast();
        });
    }

    function renderUploaderDocumentItem(elementId, doc, isCompany = false) {
        let noDocumentsCardElement = $(`#${elementId}`).find(".card-body");
        if(noDocumentsCardElement.length){
            $(noDocumentsCardElement).remove();
        }
        const model = elementId.split('-')[1];
        const matches = elementId.match(/-(\d+)(?![\d\D]*-)/);
        let uniqueId = matches ? parseInt(matches[1], 10) : "";

        const select2ElementId = `attachments-${model}-uploader-document-${doc.id}`;

        let thumbnailUrl = "{{asset('assets/icons/pdf-file.png')}}";
        if(doc.thumbnail){
            thumbnailUrl = doc.thumbnail;
        }

        $(`#${elementId} .uploader-document-list-item-loader:last`).remove();

        let documentItem = `<div class="col-12 uploaded-document-list-item">
                                <div class="row mb-1 border-bottom">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded p-1 me-2">
                                                <img src="${thumbnailUrl}" alt="" class="img-fluid d-block">
                                                <input type="hidden" name="documents[${totalDocumentsCount}][id]" value="${doc.id}" class="attachments-disable-on-view-mode"/>
                                                <input type="hidden" name="documents[${totalDocumentsCount}][model]" value="${model}" class="attachments-disable-on-view-mode"/>
                                            </div>
                                            <div>
                                                <h5 class="fs-14 my-1">
                                                    <a href="javascript:void(0)" data-title="${doc.title}" data-url="${doc.path}" data-extension="${doc.extension}" class="text-reset view-document-button">
                                                        <span class="document-title">${doc.description}</span><br><small class="document-description d-none">${doc.description}</small>
                                                    </a>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <select name="documents[${totalDocumentsCount}][title]" id="${select2ElementId}" class="form-control select2 select-document-type-select2 ${model}-documents attachments-disable-on-view-mode" data-model="${model}" required>
                                            <option value="">Select Document</option>
                                        </select>
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm float-end delete-document-button attachments-disable-on-view-mode" data-select="${select2ElementId}" data-id="${doc.id}" data-model="${model}" data-element="${elementId}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;

        totalDocumentsCount++;
        $(`#${elementId}`).append(documentItem);
        setDocumentTitleOptions(select2ElementId, uniqueId, "", isCompany);
        // $(`#${select2ElementId}`).select2();
    }


    function renderUploadedDocumentItem(elementId, doc, isCompany = false) {
        const model = elementId.split('-')[1];
        const matches = elementId.match(/-(\d+)(?![\d\D]*-)/);
        let uniqueId = matches ? parseInt(matches[1], 10) : "";

        const select2ElementId = `attachments-${model}-uploaded-document-${doc.id}`;

        let shouldUpdate = false;

        if(doc.title == "Other"){
            shouldUpdate = true;
        }

        let thumbnailUrl = "{{asset('assets/icons/pdf-file.png')}}";
        if(doc.thumbnail){
            thumbnailUrl = doc.thumbnail;
        }

        let documentItem = `<div class="col-12 uploaded-document-list-item ${shouldUpdate ? 'should-update-document-type-item' : ''}">
                            <div class="row mb-1 border-bottom">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                            <img src="${thumbnailUrl}" alt="" class="img-fluid d-block">
                                            <input type="hidden" name="documents[${totalDocumentsCount}][id]" value="${doc.id}" class="attachments-disable-on-view-mode"/>
                                            <input type="hidden" name="documents[${totalDocumentsCount}][model]" value="${model}" class="attachments-disable-on-view-mode"/>
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1">
                                                <a href="javascript:void(0)" data-title="${doc.title}" data-url="${doc.path}" data-extension="${doc.extension}" class="text-reset view-document-button">
                                                    ${doc.title} <br><small style="color: #f3a696"><i>${shouldUpdate ? "(*Please update document type)" : ""}</i></small>
                                                </a>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="uploaded-document-list-item-select d-none">
                                        <select name="documents[${totalDocumentsCount}][title]" id="${select2ElementId}" class="form-control select2 select-document-type-select2 ${model}-documents attachments-disable-on-view-mode" data-model="${model}" required>
                                            <option value="">Select Document</option>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center uploaded-document-list-item-created-by">
                                        <div>
                                            <span class="fs-14 my-1">
                                                <a :href="javascript:void(0)" class="text-reset">
                                                    ${doc?.created_by?.full_name || ''}
                                                </a>
                                            </span>
                                            <br>
                                            <span class="text-muted fs-10">${moment(doc.created_at).format("DD-MM-YYYY hh:mm A")}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 d-flex justify-content-end">
                                    <button type="button" class="btn btn-soft-info waves-effect waves-light btn-sm float-end edit-media-button attachments-disable-on-view-mode" data-id="${doc.id}" data-model="${model}" data-select="${select2ElementId}" data-title="${doc.title}">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm float-end delete-document-button attachments-disable-on-view-mode" data-select="${select2ElementId}" data-confirm="true" data-id="${doc.id}" data-model="${model}" data-element="${elementId}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
        totalDocumentsCount++;
        $(`#${elementId}`).append(documentItem);
        setDocumentTitleOptions(select2ElementId, uniqueId, doc.title, isCompany);
        $(`#${select2ElementId}`).val(doc.title).trigger("change");
    }

    function renderUploaderDocumentLoaderItem(elementId) {
        let documentItem = `<div class="col-12 document uploader-document-list-item-loader">
                            <div class="row mb-1 border-bottom">
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-muted">Please wait while document is getting uploading...</strong>
                                        <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
        $(`#${elementId}`).append(documentItem);
    }

    function getDocumentList(type, model, isCompany = false){
        if(type == "Rental"){
            if(model == "contact"){
                if(isCompany){
                    return rentalRequiredDocumentListForContactAndCompany;
                }
                return rentalRequiredDocumentListForContact;
            }
            if(model == "lead"){
                if(isCompany){
                    return rentalRequiredDocumentListForLeadAndCompany;
                }
                return rentalRequiredDocumentListForLead;
            }
            return rentalRequiredDocumentListForOther;
        }

        if(type == "Secondary Sale"){
            if(model == "contact"){
                if(isCompany){
                    return secondarySaleRequiredDocumentListForContactAndCompany;
                }
                return secondarySaleRequiredDocumentListForContact;
            }
            if(model == "lead"){
                if(isCompany){
                    return secondarySaleRequiredDocumentListForLeadAndCompany;
                }
                return secondarySaleRequiredDocumentListForLead;
            }
            return secondarySaleRequiredDocumentListForOther;
        }

        if(type == "Primary Resale"){
            if(model == "contact"){
                return primaryResaleRequiredDocumentListForContact;
            }
            if(model == "lead"){
                return primaryResaleRequiredDocumentListForLead;
            }
            return primaryResaleRequiredDocumentListForOther;
        }
        return [];
    }

    function setDocumentTitleOptions(elementId, uniqueId, selectedText = "", isComapny){
        const model = elementId.split("-")[1];
        let options = "<option value='' disabled selected>Select Document</option>";

        const documentList = getDocumentList(deal.value.type, model, isComapny);

        let opt = [];
        documentList.map(item => {
            const slug = `attachments-${model}${uniqueId ? '-' + uniqueId : ''}-${generateSlug(item.id)}`;
            options += `<option data-slug="${slug}" value="${item.id}">${item.name}</option>`;
            opt.push({
                id: item.name,
                text: item.name,
                slug: slug
            })
        })
        let optionExists = documentList.findIndex(item => item.name == selectedText) > -1 || selectedText == "Other";
        if(!optionExists && selectedText != ""){
            const slug = `attachments-${model}${uniqueId ? '-' + uniqueId : ''}-${generateSlug(selectedText)}`;
            options += `<option data-slug="${slug}" value="${selectedText}">${selectedText}</option>`;
            opt.push({
                id: selectedText,
                text: selectedText,
                slug: slug
            })
        }

        //CHECKBOX VALIDATIONS
        if (elementId !== null && elementId !== undefined) {
            $(`#${elementId}`).html(options);

            $(`#${elementId}`).select2({
                data: opt,
                tags: true,
                tokenSeparators: [','],
            })
            .on('change', function() {
                $(`#${elementId}`).closest(".uploaded-document-list-item").find(".document-description").removeClass("d-none");
                $(`#${elementId}`).closest(".uploaded-document-list-item").find(".document-title").text($(this).val());
                let model = $(this).data("model");
                const slug = $(this).select2('data')[0]?.slug;
                $(`.${model}-document-not-uploaded`).each(function(){
                    $(this).prop("checked", false);
                    $(this).next("label").removeClass("btn-outline-success");
                    if($(this).prop("required")){
                        $(this).next("label").addClass("btn-outline-danger");
                    }else{
                        $(this).next("label").addClass("btn-outline-dark");
                    }
                });
                // if (slug != undefined) {
                    // Get the model from the current select


                    let modelClass = model + "-documents";
                    // Loop through all selects with the same model
                    $("."+modelClass).each(function(){
                        // Do something with each related select
                        const relatedSlug = $(this).select2('data')[0]?.slug;
                        if (relatedSlug != null && relatedSlug != undefined) {
                            $("#" + relatedSlug).prop("checked", true).trigger("change");
                            $(`#${relatedSlug}`).next("label").addClass("btn-outline-success");
                            if($(`#${relatedSlug}`).prop("required")){
                                $(`#${relatedSlug}`).next("label").removeClass("btn-outline-danger");
                            }else{
                                $(`#${relatedSlug}`).next("label").removeClass("btn-outline-dark");
                            }
                        }
                    });
                // }
                return false;
            });
        }

    }


    function setRequiredDocumentListCheckboxes(elementId, uploadedDocumentList = [], uniqueId = "", isCompany = false){
        let checkboxes = "";

        const model = elementId.split("-")[1];

        const documentList = getDocumentList(deal.value.type, model, isCompany);

        let isAllRequiredDocumentsUploaded = true;

        documentList.map((item, index) => {
                const uploaded = checkIfDocumentUploaded(item.name, uploadedDocumentList);

                const checkboxElementId = `${elementId}-${index}`;

                const slug = `attachments-${model}${uniqueId ? '-' + uniqueId : ''}-${generateSlug(item.id)}`;

                const isRequired = item.required ? true : false;

                let checkbox = "";

                if(uploaded){
                    checkbox += `<li data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="<ul>
                                            <li>Allowed file type: ${item.extensions}</li>
                                            <li>Allowed file size: ${item.size}</li>
                                            <li>Required: ${item.required ? 'Yes' : 'No'}</li>
                                            </ul>">
                                        <input type="checkbox" class="btn-check uploader-checkbox document-already-uploaded attachments-disable-on-view-mode" id="${slug}" checked ${item.required ? 'required' : ''}>
                                        <label class="text-left btn btn-outline-success btn-sm for="${slug}">${item.name} <i class="ri-check-double-line"></i></label>
                                    </li>`;
                }else{
                    checkbox += `<li data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="<ul>
                                            <li>Allowed file type: ${item.extensions}</li>
                                            <li>Allowed file size: ${item.size}</li>
                                            <li>Required: ${item.required ? 'Yes' : 'No'}</li>
                                            </ul>">
                                        <input type="checkbox" class="btn-check uploader-checkbox ${model}-document-not-uploaded select-document-checkbox attachments-disable-on-view-mode" data-slug="${slug}" id="${slug}" ${item.required ? 'required' : ''}>
                                        <label class="text-left btn btn-outline-${item.required ? 'danger' : 'dark'} btn-sm" for="${slug}">${item.name} <i class="ri-question-line"></i></label>
                                    </li>`;

                    if(isRequired){
                        isAllRequiredDocumentsUploaded = false;
                    }
                }
                if(elementId !== null && elementId !== undefined){
                    $(`#${elementId}-required`).append(checkbox);
                    $(".uploader-checkbox").click(function() { return false; });
                }

        })

        attachmentDocumentsUploaded[`${model}${uniqueId ? uniqueId : ''}`] = isAllRequiredDocumentsUploaded;
    }

    function checkIfDocumentUploaded(name, uploadedDocumentList){
        if(uploadedDocumentList != null && uploadedDocumentList != undefined){
            const index = uploadedDocumentList.findIndex(item => item.title == name);
            if(index >= 0){
                return true;
            }
        }
        return false;
    }

    function renderLeadDocumentCard(lead){
        const isCompany = checkIfTypeIsCompany(lead?.lead_meta)
        const documentUploaderElementId = `attachments-lead-document-uploader-${lead.id}`;
        const documentUploadedListElementId = `attachments-lead-document-uploaded-list-${lead.id}`;

        const isOpened = openedDocumentUploaders.includes(documentUploaderElementId);

        let card = `<div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5>
                                <i class=" ri-user-3-line" style="color: #c48667; vertical-align: bottom;"></i>
                                ${lead?.pivot?.lead_type || ''} ${deal.value?.type == 'Rental' ? 'Tenant' : 'Buyer'} (${lead.first_name || ''} ${lead.last_name || ''})
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="mt-2 ${isOpened ? '' : ''}" id="attachments-lead-document-uploader-${lead.id}">
                                    <div class="accordion" id="attachments-lead-document-uploader-${lead.id}-accordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="attachments-lead-document-uploader-${lead.id}-accorion-required-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#attachments-lead-document-uploader-${lead.id}-accorion-required-collabpse" aria-expanded="false" aria-controls="attachments-lead-document-uploader-${lead.id}-accorion-required-collabpse">
                                                    List of Documents
                                                </button>
                                            </h2>
                                            <div id="attachments-lead-document-uploader-${lead.id}-accorion-required-collabpse" class="accordion-collapse collapse" aria-labelledby="attachments-lead-document-uploader-${lead.id}-accorion-required-header" data-bs-parent="#attachments-lead-document-uploader-${lead.id}-accordion">
                                                <div class="accordion-body">
                                                    <ul class="list-group" id="attachments-lead-document-uploader-${lead.id}-required-checkbox-list-required" style="list-style: none"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card attachments-hide-on-view-mode">
                                        <div class="card-body">
                                            <div id="attachments-lead-dropzone-${lead.id}" class="dropzone custom-dropzone"></div>
                                            <div id="attachments-lead-previewContainer-${lead.id}" class="d-none"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <textarea   data-lead_id="${lead.id}"  class="lead_docuemnt_note form-control attachments-disable-on-view-mode" placeholder="Password for documents">`+getDocumentNoteForLead(lead.id)+`</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="row mt-2" id="${documentUploadedListElementId}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

            $("#attachments-lead-document-attachments").append(card);
            if(lead.deal_media.length){
                lead.deal_media?.map(doc => {
                    renderUploadedDocumentItem(documentUploadedListElementId, doc, isCompany)
                })
            }else{
                renderNoDocumentsCard(documentUploadedListElementId);
            }

            initLeadDropzone(`attachments-lead-dropzone-${lead.id}`, `attachments-lead-previewContainer-${lead.id}`, lead, documentUploadedListElementId, isCompany);
            $(`#attachments-lead-document-uploader-${lead.id}-required-checkbox-list-required`).html("");
            setRequiredDocumentListCheckboxes(`attachments-lead-document-uploader-${lead.id}-required-checkbox-list`, lead.deal_media, lead.id, isCompany)
            $('[data-bs-toggle="popover"]').popover();
            attachmentsFieldsDisabled(attachmentsMode == "view");
    }

    function getOwner(ownerId) {
        $.ajax({
            url: "{{ route('admin.work.deals.get.contact') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                id: ownerId,
                deal_id: deal_id
            },
            success: function(response) {
                if(response.status == 1){
                    const {data} = response;
                    const isCompany = checkIfTypeIsCompany(response.data?.contact_meta);
                    initContactDropzone(isCompany);
                    $("#attachments-contact-name").text(`${data?.first_name} ${data?.last_name}`);
                    $("#attachments-contact-deal-type").text(`${deal.value?.type == 'Rental' ? 'Owner' : 'Seller'}`);
                    $("#attachments-contact-document-uploaded-list").html("");
                    $("#attachments-contact-document-uploaded-list").html("");
                    if(data.deal_media.length){
                        data.deal_media?.map(doc => {
                            renderUploadedDocumentItem("attachments-contact-document-uploaded-list", doc, isCompany)
                        })
                    }else{
                        renderNoDocumentsCard("attachments-contact-document-uploaded-list");
                    }
                    $("#attachments-contact-document-uploader-required-checkbox-list-required").html("");
                    $("#attachments-contact-document-uploader-required-checkbox-list-other").html("");
                    setRequiredDocumentListCheckboxes("attachments-contact-document-uploader-required-checkbox-list", data.deal_media, "", isCompany);
                }
            },
            error: function(response) {
                console.error(response);
            }
        });
    }

    function getDocumentNoteForLead(lead_id){

        if(lead_document_notes_fetched.length){
            const index = lead_document_notes_fetched.findIndex(item => item.lead_id == lead_id);

            if(index >= 0){
                return lead_document_notes_fetched[index].note;
            }
        }

        return "";
    }

    function getDealMedia(dealId) {
               $.ajax({
            url: "{{ route('admin.work.deals.getDealMedia') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                deal_id: dealId
            },
            success: function(response) {
                if(response.status == 1){
                    const {data} = response;
                    initDealDropzone();
                    $("#attachments-deal-document-uploaded-list").html("");
                    $("#attachments-deal-document-uploaded-list").html("");
                    if(data.length){
                        data?.map(doc => {
                            renderUploadedDocumentItem("attachments-deal-document-uploaded-list", doc)
                        })
                    }else{
                        renderNoDocumentsCard("attachments-deal-document-uploaded-list")
                    }

                    $("#attachments-deal-document-uploader-required-checkbox-list-required").html("");
                    $("#attachments-deal-document-uploader-required-checkbox-list-other").html("");
                    setRequiredDocumentListCheckboxes("attachments-deal-document-uploader-required-checkbox-list", data);
                }
            },
            error: function(response) {
                console.error(response);
            }
        });
    }

    function getDealLeads(dealId){
        $.ajax({
            url: "{{ route('admin.work.deals.getDealLeads') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                deal_id: dealId
            },
            success: function(response) {
                if(response.status == 1){
                    const {data} = response;
                    $("#attachments-lead-document-attachments").html("")
                    data?.map(lead => {
                        renderLeadDocumentCard(lead)
                    })
                }
            },
            error: function(response) {
                console.error(response);
            }
        });
    }


    function deleteDealDocument(mediaId, model, selectedSlug, elementToBeRemove = null){
        $.ajax({
             url: "{{ route('admin.work.deals.deal.delete.doc') }}",
             type: 'POST',
             dataType: 'json',
             data: {
                id: mediaId,
                model: model,
                deal_id: deal_id
             },
             cache: false,
             success: function(response) {
                if(response.status == 1){
                    if(elementToBeRemove !== null){
                        $(`#${selectedSlug}`).removeClass("document-already-uploaded");
                        $(`#${selectedSlug}`).addClass(model+"-document-not-uploaded");
                        if($(elementToBeRemove).siblings().length <= 0){
                            renderNoDocumentsCard($(elementToBeRemove).closest(".row").attr("id"))
                        }
                        $(elementToBeRemove).remove();
                        toogleDocumentCheckboxes(model, selectedSlug);
                    }
                    showToastify(response.msg, "success");
                }
             },
             error: function(jqXhr, json, errorThrown) {
                 showToastify("Something Went Wrong!", "error");
             },
         });
    }

    $(document).on("click", ".delete-document-button", function(){
        const id = $(this).data("id");
        const model = $(this).data("model");
        const shouldConfirm = $(this).data("confirm");
        let selectElementId = $(this).data("select");
        const selectedSlug = $(`#${selectElementId}`).select2('data')[0]?.slug;

        let parentElement = $(this).closest(".uploaded-document-list-item");
        if(shouldConfirm){
            Swal.fire({
            html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to delete this documents ?</p></div></div>',
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            cancelButtonClass: 'btn btn-warning action-close w-xs',
            showCloseButton: true,
            confirmButtonText: 'Delete',
            confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',
            buttonsStyling: false,
            }).then(function (result) {
                if (result.isConfirmed) {
                    deleteDealDocument(id, model, selectedSlug, parentElement);
                }
            });
        }else{
            deleteDealDocument(id, model, selectedSlug, parentElement);
        }
    });

    $(document).on("submit", "#form-4", function(event){


        isValidSubmission = false;
        event.preventDefault();
        let formData = $(this).serializeArray();

        // if(formData.length <= 0){
        //     // isValidSubmission = true;
        //     // if(nextStep){
        //     //     $('#smartwizard').smartWizard("goToStep", nextStep);
        //     // }
        //     // isValidSubmission = false;
        //     return false;
        // };
        if(attachmentsMode == "edit"){

            let contact_docuemnt_note = $('#contact_docuemnt_note').val();
            let other_docuemnt_note = $('#other_docuemnt_note').val();
            let lead_document_notes = [];
            $('.lead_docuemnt_note').each(function() {
                lead_document_notes.push({
                    lead_id: $(this).data('lead_id'),
                    note: $(this).val()
                });
            });

            let document_notes = {
                contact_docuemnt_note: contact_docuemnt_note,
                other_docuemnt_note: other_docuemnt_note,
                lead_document_notes: lead_document_notes
            };

            let documnet_notes = JSON.stringify(document_notes);


            formData.push({
                name: "deal_id",
                value: deal_id
            });
            formData.push({
                name: "attachments_mode",
                value: "view"
            });

            formData.push({
                name: "document_notes",
                value: documnet_notes
            });

            $.ajax({
                url: "{{ route('admin.work.deals.update.deal.doc') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                success: function(response) {
                    if(response.status == 1){
                        if(contact_id != null && contact_id != undefined && contact_id != ""){
                            getOwner(contact_id);
                        }
                        if (deal_id != null && deal_id != undefined && deal_id != "") {
                            getDealLeads(deal_id);
                            getDealMedia(deal_id);
                        }
                        showToastify(response.msg, "success");
                        <?php echo 'var fromInvitePage = '.json_encode($fromInvitePage).';'; ?>
                        if(fromInvitePage){
                            $('a[href="#step-5"]').addClass('disabled');
                        }
                        getOwner(contact_id);
                        getDealLeads(deal_id);
                        getDealMedia(deal_id);
                        isValidSubmission = true;


                        if(!fromInvitePage){

                            <?php echo 'var isAlreadyInvited = '.json_encode($isAlreadyInvited).';'; ?>
                            <?php echo 'var isDealInviteLeadDocumentUploaded = '.json_encode($isDealInviteLeadDocumentUploaded).';'; ?>

                            if(isAlreadyInvited){
                                if(!isDealInviteLeadDocumentUploaded){
                                    showToastify("Please wait for invited agent upload documents", "error");
                                    return false;
                                }
                            }

                            if(nextStep){
                                //redirect to next step by reloading the page and append #step-5 to the url
                                let currentUrl = window.location.href;
                                let newUrl = currentUrl.split("#")[0];
                                newUrl += "#step-5";
                                //window.location.href = newUrl;
                                window.location.replace(newUrl);
                                location.reload();
                                $('#smartwizard').smartWizard("goToStep", nextStep);
                            }
                            isValidSubmission = false;
                        }

                    }else{
                        isValidSubmission = false;
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    isValidSubmission = false;
                    showToastify("Something Went Wrong!", "error");
                    // $('#screen_loader').addClass('d-none');
                },
            });
        }else{


            <?php echo 'var fromInvitePage = '.json_encode($fromInvitePage).';'; ?>

            if(fromInvitePage){
                isValidSubmission = false;
                showToastify("Please click on edit first and than submit documents", "error");
                return false;
            }else{

                <?php echo 'var isAlreadyInvited = '.json_encode($isAlreadyInvited).';'; ?>
                <?php echo 'var isDealInviteLeadDocumentUploaded = '.json_encode($isDealInviteLeadDocumentUploaded).';'; ?>


                if(isAlreadyInvited){
                    if(!isDealInviteLeadDocumentUploaded){
                        showToastify("Please wait for invited agent upload documents", "error");
                        return false;
                    }else{
                        isValidSubmission = true;
                        if(nextStep){
                            $('#smartwizard').smartWizard("goToStep", nextStep);
                        }
                        isValidSubmission = false;
                    }
                }else{
                    isValidSubmission = true;
                    if(nextStep){
                        $('#smartwizard').smartWizard("goToStep", nextStep);
                    }
                    isValidSubmission = false;
                }
            }
        }

    })

    // $(document).on("submit", "#attachmentsEditDocumentForm", function(event){
    //     event.preventDefault();
    //     let formData = $(this).serializeArray();

    //     if(formData.length <= 0){
    //         return;
    //     }
    //     const uploderElementId = $("#attachmentsEditDocumentUploderElementId").val();
    //     $.ajax({
    //          url: "{{ route('update.deal.document') }}",
    //          type: 'POST',
    //          dataType: 'json',
    //          data: formData,
    //          cache: false,
    //          success: function(response) {
    //             if(response.status == 1){
    //                 showToastify(response.msg, "success");
    //                 $("#attachmentsEditDocumentModal").modal("hide");
    //                 if(uploderElementId != null && uploderElementId != ""){
    //                     $(`#${uploderElementId}`).html("");
    //                     const model = uploderElementId.split('-')[1];
    //                     if(model == "contact"){
    //                         getOwner(contact_id);
    //                     }
    //                     if(model == "deal"){
    //                         getDealMedia(deal_id);
    //                     }
    //                     if(model == "lead"){
    //                         getDealLeads(deal_id);
    //                     }
    //                 }
    //             }
    //          },
    //          error: function(jqXhr, json, errorThrown) {
    //              showToastify("Something Went Wrong!", "error");
    //              // $('#screen_loader').addClass('d-none');

    //          },
    //      });
    // })

    $(document).on("click", ".view-document-button", function(){
        $("#attachmentsViewDocumentModalContent").html("");
        let title = $(this).data("title");
        let url = $(this).data("url");
        let extension = $(this).data("extension");
        let content = "";
        if(getFileTypeByExtension(extension) == "document"){
            content = `<iframe src="${url}" height="100%" width="100%"></iframe>`;
        }else if(getFileTypeByExtension(extension) == "image"){
            content = `<img src="${url}" height="100%" width="100%"/>`;
        }
        $("#attachmentsViewDocumentModalLabel").text(title);
        $("#attachmentsViewDocumentModalContent").html(content);
        $("#attachmentsViewDocumentModal").modal("show");
    })

    $(document).on("click", ".edit-media-button", function(){
        let selectContainer = $(this).closest(".uploaded-document-list-item").find(".uploaded-document-list-item-select");
        let createdByContainer = $(this).closest(".uploaded-document-list-item").find(".uploaded-document-list-item-created-by");
        if(selectContainer.hasClass("d-none")){
            selectContainer.removeClass("d-none");
            createdByContainer.addClass("d-none");
            $(this).find("i").removeClass("bx-pencil").addClass("bx-x");
        }else{
            selectContainer.addClass("d-none");
            createdByContainer.removeClass("d-none");
            $(this).find("i").removeClass("bx-x").addClass("bx-pencil")
        }
        let id = $(this).data("id");
        let model = $(this).data("model");
        let title = $(this).data("title");
        let select = $(this).data("select");
        $(`#${select}`).val(title).trigger("change");
        let alreadyUploadedSlug = $(`#${select}`).select2('data')[0]?.slug;
        let selectModel = $(`#${select}`).data('model');
        $(`#${alreadyUploadedSlug}`).removeClass("document-already-uploaded");
        $(`#${alreadyUploadedSlug}`).addClass(selectModel+"-document-not-uploaded");

    })

    function getFileTypeByExtension(extension){
        if(["jpg", "jpeg", "png", "gif"].includes(extension)){
            return "image";
        }
        if(extension == "pdf"){
            return "document";
        }
        return "document";
    }

    function toogleDocumentCheckboxes(model, slug){
        $(`.${model}-document-not-uploaded`).each(function(){
            $(this).prop("checked", false);
            $(this).next("label").removeClass("btn-outline-success");
            if($(this).prop("required")){
                $(this).next("label").addClass("btn-outline-danger");
            }else{
                $(this).next("label").addClass("btn-outline-dark");
            }
        });
        let modelClass = model + "-documents";
        // Loop through all selects with the same model
        $("."+modelClass).each(function(){
            // Do something with each related select
            const relatedSlug = $(this).select2('data')[0]?.slug;
            if (relatedSlug != null && relatedSlug != undefined) {
                $("#" + relatedSlug).prop("checked", true).trigger("change");
                $(`#${relatedSlug}`).next("label").addClass("btn-outline-success");
                if($(`#${relatedSlug}`).prop("required")){
                    $(`#${relatedSlug}`).next("label").removeClass("btn-outline-danger");
                }else{
                    $(`#${relatedSlug}`).next("label").removeClass("btn-outline-dark");
                }
            }
        });
        return false;
    }

    function renderNoDocumentsCard(elementId){
        let content = `<div class="card-body">
                            <div class="d-flex mb-2 text-center">
                                <div class="flex-grow-1">
                                    <lord-icon src="{{ asset('assets/json/icons/common-event.json') }}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:125px;height:125px"> </lord-icon>
                                </div>
                            </div>
                            <h3 class="mb-2 text-center"><span class="text-muted text-center">No Documents</span><small class="text-muted fs-13"></small></h3>
                            <h6 class="text-muted mb-0"></h6>
                        </div>`;
        $(`#${elementId}`).html(content);
    }

    function checkIfAllDocumentsUploaded() {
        return Object.values(attachmentDocumentsUploaded).every(value => value === true);
    }

    function checkIfTypeIsCompany(metaArr) {
        isCompany = false;
        if(metaArr != undefined && metaArr != null && metaArr?.length > 0){
            const index = metaArr.findIndex(item => item.meta_key == "type");

            if(index >= 0){
                const type = metaArr[index];
                isCompany = type.meta_value == "COMPANY";
            }
        }
        return isCompany;
    }

</script>
@endpush
