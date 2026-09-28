<style>
    .btn-outline-danger{
        border-color: #f3a696 !important;
        color: #f3a696;
    }
    .btn-outline-dark{
        border-color: #8d9092 !important;
        color: #8d9092;
    }
</style>
<div class="col">
    <div class="row gy-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            <i class=" ri-shield-user-line" style="color: #c48667; vertical-align: bottom;"></i>
                            <span id="attachments-contact-deal-type"></span> (<span id="attachments-contact-name"></span>)
                        </h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-danger">Please upload missing documents</span>
                            <button type="button" class="btn btn-sm btn-soft-success waves-effect waves-light float-end" onclick="toggleFileUploader('attachments-contact-document-uploader')"><i class="bx bx-cloud-upload"></i> Upload </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mt-2" id="attachments-contact-document-uploader">
                        <div class="hstack gap-1 flex-wrap" id="attachments-contact-document-uploader-required-checkbox-list"></div>
                        <div id="attachments-contact-document-uploader-required-list"></div>
                        <div class="card">
                            <div class="card-body">
                                <div id="attachments-contact-dropzone" class="dropzone custom-dropzone"></div>
                                <div id="attachments-contact-previewContainer" class="d-none"></div>
                            </div>
                            <div class="card-body">
                                <div id="attachments-contact-document-uploader-list"></div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-info">Please upload document and click save button.</span>
                                            <div>
                                                <button class="btn btn-primary float-end ml-2">Save</button>
                                                <button type="button" class="btn btn-light float-end wave-effect" onclick="toggleFileUploader('attachments-contact-document-uploader', false)">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="attachments-contact-document-uploaded-list"></div>
                </div>
            </div>
        </div>
        <div id="attachments-lead-document-attachments"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            <i class=" ri-folder-reduce-line" style="color: #c48667; vertical-align: bottom;"></i>
                            Other Documents
                        </h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-danger">Please upload missing documents</span>
                            <button type="button" class="ml-2 btn btn-sm btn-soft-success waves-effect waves-light float-end" onclick="toggleFileUploader('attachments-deal-document-uploader')"><i class="bx bx-cloud-upload"></i> Upload </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mt-2" id="attachments-deal-document-uploader">
                        <div class="hstack gap-1 flex-wrap" id="attachments-deal-document-uploader-required-checkbox-list"></div>
                        <div id="attachments-deal-document-uploader-required-list"></div>
                        <div class="card">
                            <div class="card-body">
                                <div id="attachments-deal-dropzone" class="dropzone custom-dropzone"></div>
                                <div id="attachments-deal-previewContainer" class="d-none"></div>
                            </div>
                            <div class="card-body">
                                <div id="attachments-deal-document-uploader-list"></div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-info">Please upload document and click save button.</span>
                                            <div>
                                                <button class="btn btn-primary float-end ml-2">Save</button>
                                                <button type="button" class="btn btn-light float-end wave-effect" onclick="toggleFileUploader('attachments-deal-document-uploader', false)">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="attachments-deal-document-uploaded-list"></div>
                </div>
            </div>
        </div>
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

    let rentalRequiredDocumentList = {!! json_encode(getDealRequiredDocumentList('Rental')) !!};
    let primarySaleRequiredDocumentList = {!! json_encode(getDealRequiredDocumentList('Primary Sale')) !!};
    let secondarySaleRequiredDocumentList = {!! json_encode(getDealRequiredDocumentList('Secondary Sale')) !!};

    let openedDocumentUploaders = [];

    let dropzoneDefaultMessage = `<div style="display: flex; justify-content: center;">
                                    <i class="ri ri-upload-cloud-line" style="font-size: 5rem;color: #adadad;"></i>

                                    </div>
                                    <p style="text-align: center; margin: 0;color: #6f6f6f;">
                                        <strong>Drag and drop files to upload</strong>
                                    </p>`;

    function generateSlug(text) {
        return text
        .toLowerCase()
        .replace(/\s+/g, '-')         // Replace spaces with -
        .replace(/[^\w\-]+/g, '')     // Remove non-word characters
        .replace(/\-\-+/g, '-')       // Replace multiple - with single -
        .replace(/^-+/, '')           // Trim - from start of text
        .replace(/-+$/, '');          // Trim - from end of text
    }

    function initContactDropzone() {
        let dropzoneElement = $("#attachments-contact-dropzone")[0].dropzone;
        if(dropzoneElement){
            dropzoneElement.destroy();
        }
        let contactDropzone = new Dropzone("#attachments-contact-dropzone", {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('add.deal.doc') }}",
            method: "post",
            paramName: "file",
            previewsContainer: "#attachments-contact-previewContainer",
            maxFilesize: 3, // MB,
            acceptedFiles: ".jpg,.png,.jpeg,.webp,.pdf,.doc,.docx,.zip",
            dictDefaultMessage: dropzoneDefaultMessage,
        });

        contactDropzone.on("sending", function(file, xhr, formData) {
            formData.append("deal_id", deal_id); // DEAl ID
            formData.append("model", "contact");
            formData.append("id", contact_id);//CONTACT ID
        });

        contactDropzone.on("success", function(file, response) {
            if (response.status == '1') {
                renderUploaderDocumentItem("attachments-contact-document-uploader-list", response.data)
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
        let dropzoneElement = $("#attachments-deal-dropzone")[0].dropzone;
        if(dropzoneElement){
            dropzoneElement.destroy();
        }
        let dealDropzone = new Dropzone("#attachments-deal-dropzone", {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('add.deal.doc') }}",
            method: "post",
            paramName: "file",
            previewsContainer: "#attachments-deal-previewContainer",
            maxFilesize: 3, // MB,
            acceptedFiles: ".jpg,.png,.jpeg,.webp,.pdf,.doc,.docx,.zip",
            dictDefaultMessage: dropzoneDefaultMessage,
        });

        dealDropzone.on("sending", function(file, xhr, formData) {
            formData.append("deal_id", deal_id);//DEAL ID
            formData.append("model", "deal");
            formData.append("id", deal_id);//DEAL ID
        });

        dealDropzone.on("success", function(file, response) {
            if (response.status == '1') {
                renderUploaderDocumentItem("attachments-deal-document-uploader-list", response.data)
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

    function initLeadDropzone(dropzoneId, dropzonePreviewId, lead, documentUploaderListElementId) {
        let dropzoneElement = $(`#${dropzoneId}`)[0].dropzone;
        if(dropzoneElement){
            dropzoneElement.destroy();
        }
        let dropzone = new Dropzone(`#${dropzoneId}`, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('add.deal.doc') }}",
            method: "post",
            paramName: "file",
            previewsContainer: `#${dropzonePreviewId}`,
            maxFilesize: 3, // MB,
            acceptedFiles: ".jpg,.png,.jpeg,.webp,.pdf,.doc,.docx,.zip",
            dictDefaultMessage: dropzoneDefaultMessage,
        });

        dropzone.on("sending", function(file, xhr, formData) {
            formData.append("deal_id", deal_id);//DEAL ID
            formData.append("model", "lead");
            formData.append("id", lead.id);//LEAD ID
        });

        dropzone.on("success", function(file, response) {
            if (response.status == '1') {
                renderUploaderDocumentItem(documentUploaderListElementId, response.data)
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
                text: response.msg,
                style: {
                    background: "linear-gradient(to right, #df2808, #df5c45)",
                },
                close: true,
                duration: 3000

            }).showToast();
        });
    }

    function renderUploaderDocumentItem(elementId, doc) {
        const model = elementId.split('-')[1];

        const matches = elementId.match(/-(\d+)(?![\d\D]*-)/);
        let uniqueId = matches ? parseInt(matches[1], 10) : "";

        const select2ElementId = `attachments-${model}-uploader-document-${doc.id}`;

        let documentItem = `<div class="row mt-2 uploader-document-list-item">
                        <div class="col-2">
                            <input type="hidden" name="documents[${doc.id}][id]" value="${doc.id}"/>
                            <input type="hidden" name="documents[${doc.id}][model]" value="${model}"/>
                            <img src="/assets/icons/documents.png" class="avatar-sm rounded" alt=".">
                        </div>
                        <div class="col-4">${doc.description}</div>
                        <div class="col-5">
                            <select name="documents[${doc.id}][title]" id="${select2ElementId}" class="form-control select2 select-document-type-select2" required>
                                <option value="">Select Document</option>
                            </select>
                        </div>
                        <div class="col-1">
                            <a href="javascript:void(0)" class="delete-deal-media-button" data-id="${doc.id}" data-model="${model}">
                                <i class="ri ri-delete-bin-2-fill" style="font-size: 1.5rem; color: #c48667;"></i>
                            </a>
                        </div>
                    </div>`;

        $(`#${elementId}`).append(documentItem);
        setDocumentTitleOptions(select2ElementId, uniqueId);
        // $(`#${select2ElementId}`).select2();
    }


    function renderUploadedDocumentItem(elementId, doc) {
        const model = elementId.split('-')[1];

        let documentItem = `<div class="col-12">
                            <div class="row mb-1 border-bottom">
                                <div class="col-8">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                            <img src="${doc.path}" alt="" class="img-fluid d-block">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 my-1">
                                                <a href="javascript:void(0)" data-title="${doc.title}" data-url="${doc.path}" data-extension="${doc.extension}" class="text-reset view-document-button">
                                                    ${doc.title}
                                                </a>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="fs-14 my-1">
                                                <a :href="javascript:void(0)" class="text-reset">
                                                    ${doc.created_by?.full_name}
                                                </a>
                                            </span>
                                            <br>
                                            <span class="text-muted fs-10">${moment(doc.created_at).format("DD-MM-YYYY hh:mm A")}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-soft-info waves-effect waves-light btn-sm float-end edit-media-button" data-id="${doc.id}" data-model="${model}" data-element="${elementId}" data-title="${doc.title}">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm float-end delete-media-button" data-id="${doc.id}" data-model="${model}" data-element="${elementId}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;

        $(`#${elementId}`).append(documentItem);
    }

    function setDocumentTitleOptions(elementId, uniqueId){
        const model = elementId.split("-")[1];
        let options = "<option value=''>Select Document</option>";

        let documentList = [];
        if (deal.value.type == "Rental") {
            documentList = rentalRequiredDocumentList;
            rentalRequiredDocumentList.map(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            })
        }
        if(deal.value.type == "Primary Sale"){
            documentList = primarySaleRequiredDocumentList;
            primarySaleRequiredDocumentList.map(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            })
        }
        if(deal.value.type == "Secondary Sale"){
            documentList = secondarySaleRequiredDocumentList;
        }
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

        //CHECKBOX VALIDATIONS
        if (elementId !== null && elementId !== undefined) {
            // Store the previously selected item ID
            var previousSelectedId;

            $(`#${elementId}`).html(options);

            $(`#${elementId}`).select2({
                data: opt
            })
            .on('change', function() {
                // Get the currently selected item's slug
                const currentSlug = $(this).select2('data')[0]?.slug;

                // Uncheck the previously selected item
                if (previousSelectedId !== undefined && previousSelectedId !== null) {
                    if($(`#${previousSelectedId}`).hasClass("document-not-uploaded")){
                        $(`#${previousSelectedId}`).prop("checked", false).trigger("change");
                        $(`#${previousSelectedId}`).next("label").removeClass("btn-outline-success");
                        if($(`#${previousSelectedId}`).prop("required")){
                            $(`#${previousSelectedId}`).next("label").addClass("btn-outline-danger");
                        }else{
                            $(`#${previousSelectedId}`).next("label").addClass("btn-outline-dark");
                        }
                    }
                }

                // Check the newly selected item
                if (currentSlug !== null && currentSlug !== undefined) {
                    if($(`#${currentSlug}`).hasClass("document-not-uploaded")){
                        $(`#${currentSlug}`).prop("checked", true).trigger("change");
                        $(`#${currentSlug}`).next("label").removeClass("btn-outline-danger").removeClass("btn-outline-dark").addClass("btn-outline-success");
                    }
                }

                // Update the previously selected item ID
                previousSelectedId = currentSlug;

                return false;
            });
        }

    }

    function setRequiredDocumentListButtons(elementId, uploadedDocumentList = []){
        let buttons = "";
        let documentList = [];

        if (deal.value.type == "Rental") {
            documentList = rentalRequiredDocumentList;
        }
        if (deal.value.type == "Primary Sale") {
            documentList = primarySaleRequiredDocumentList;
        }

        if (deal.value.type == "Secondary Sale") {
            documentList = secondarySaleRequiredDocumentList;
        }

        documentList.map(item => {
                const uploaded = checkIfDocumentUploaded(item.name, uploadedDocumentList);

                if(uploaded){
                    buttons += `<button type="button" class="btn btn-sm btn-white border-success text-success btn-label right rounded-pill">
                                <i class="ri-check-double-line label-icon align-middle rounded-pill bg-succes text-success fs-16 ms-2"></i>
                                ${item.name}
                            </button>`;
                }else{
                    buttons += `<button type="button" class="mx-1 my-1 btn btn-sm btn-white btn-label right rounded-pill ${item.required ? 'border-danger text-danger' : 'border-gray-new text-dark'}">
                                    <i class="ri-question-line label-icon align-middle rounded-pill fs-16 ms-2 ${item.required ? 'text-danger' : 'text-dark'} data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="<ul>
                                        <li>Allowed file type: ${item.extensions}</li>
                                        <li>Allowed file size: ${item.size}</li>
                                        <li>Required: ${item.required ? 'Yes' : 'No'}</li>
                                        </ul>"></i>
                                    ${item.name}
                                </button>`;
                }
            })
        if(elementId !== null && elementId !== undefined){
            $(`#${elementId}`).html(buttons);
        }
    }


    function setRequiredDocumentListCheckboxes(elementId, uploadedDocumentList = [], uniqueId = ""){
        let checkboxes = "";
        let documentList = [];

        if (deal.value.type == "Rental") {
            documentList = rentalRequiredDocumentList;
        }
        if (deal.value.type == "Primary Sale") {
            documentList = primarySaleRequiredDocumentList;
        }

        if (deal.value.type == "Secondary Sale") {
            documentList = secondarySaleRequiredDocumentList;
        }

        const model = elementId.split("-")[1];

        documentList.map((item, index) => {
                const uploaded = checkIfDocumentUploaded(item.name, uploadedDocumentList);

                const checkboxElementId = `${elementId}-${index}`;

                const slug = `attachments-${model}${uniqueId ? '-' + uniqueId : ''}-${generateSlug(item.id)}`;

                if(uploaded){

                    checkboxes += `<div data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="<ul>
                                            <li>Allowed file type: ${item.extensions}</li>
                                            <li>Allowed file size: ${item.size}</li>
                                            <li>Required: ${item.required ? 'Yes' : 'No'}</li>
                                            </ul>">
                                        <input type="checkbox" class="btn-check uploader-checkbox document-already-uploaded" id="${slug}" checked>
                                        <label class="btn btn-outline-success btn-sm rounded-pill" for="${slug}">${item.name} <i class="ri-check-double-line"></i></label>
                                    </div>`
                }else{
                    checkboxes += `<div data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="<ul>
                                            <li>Allowed file type: ${item.extensions}</li>
                                            <li>Allowed file size: ${item.size}</li>
                                            <li>Required: ${item.required ? 'Yes' : 'No'}</li>
                                            </ul>">
                                        <input type="checkbox" class="btn-check uploader-checkbox document-not-uploaded select-document-checkbox" data-slug="${slug}" id="${slug}" ${item.required ? 'required' : ''}>
                                        <label class="btn btn-outline-${item.required ? 'danger' : 'dark'} btn-sm rounded-pill" for="${slug}">${item.name} <i class="ri-question-line"></i></label>
                                    </div>`;
                }
        })

        if(elementId !== null && elementId !== undefined){
            $(`#${elementId}`).html(checkboxes);
            $(".uploader-checkbox").click(function() { return false; });
        }
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
        const documentUploaderElementId = `attachments-lead-document-uploader-${lead.id}`;
        const documentUploaderListElementId = `attachments-lead-document-uploader-list-${lead.id}`;
        const documentUploadedListElementId = `attachments-lead-document-uploaded-list-${lead.id}`;

        const isOpened = openedDocumentUploaders.includes(documentUploaderElementId);

        let card = `<div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5>
                                <i class=" ri-user-3-line" style="color: #c48667; vertical-align: bottom;"></i>
                                ${deal.value?.type == 'Rental' ? 'Tenant' : 'Buyer'} (${lead.first_name || ''} ${lead.last_name || ''})
                            </h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-danger">Please upload missing documents</span>
                                <button type="button" class="btn btn-sm btn-soft-success waves-effect waves-light float-end" onclick="toggleFileUploader('attachments-lead-document-uploader-${lead.id}')"><i class="bx bx-cloud-upload"></i> Upload </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mt-2 ${isOpened ? '' : ''}" id="attachments-lead-document-uploader-${lead.id}">
                            <div class="hstack gap-1 flex-wrap" id="attachments-lead-document-uploader-${lead.id}-required-checkbox-list"></div>
                            <div id="attchments-lead-document-uploader-${lead.id}-required-list"></div>
                            <div class="card">
                                <div class="card-body">
                                    <div id="attachments-lead-dropzone-${lead.id}" class="dropzone custom-dropzone"></div>
                                    <div id="attachments-lead-previewContainer-${lead.id}" class="d-none"></div>
                                </div>
                                <div class="card-body">
                                    <div id="${documentUploaderListElementId}"></div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-info">Please upload document and click save button.</span>
                                                <div>
                                                    <button class="btn btn-primary float-end ml-2">Save</button>
                                                    <button type="button" class="btn btn-light float-end wave-effect" onclick="toggleFileUploader('attachments-lead-document-uploader-${lead.id}', false)">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2" id="${documentUploadedListElementId}"></div>
                    </div>
                </div>
            </div>`;

            $("#attachments-lead-document-attachments").append(card);
            lead.documents?.map(doc => {
                renderUploadedDocumentItem(documentUploadedListElementId, doc)
            })
            initLeadDropzone(`attachments-lead-dropzone-${lead.id}`, `attachments-lead-previewContainer-${lead.id}`, lead, documentUploaderListElementId);
            setRequiredDocumentListCheckboxes(`attachments-lead-document-uploader-${lead.id}-required-checkbox-list`, lead.documents, lead.id)
            $('[data-bs-toggle="popover"]').popover();
    }

    function getOwner(ownerId) {
        $.ajax({
            url: "{{ route('get.contact') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                id: ownerId
            },
            success: function(response) {
                if(response.status == 1){
                    const {data} = response;
                    initContactDropzone();
                    $("#attachments-contact-name").text(`${data?.first_name} ${data?.last_name}`);
                    $("#attachments-contact-deal-type").text(`${deal.value?.type == 'Rental' ? 'Owner' : 'Seller'}`);
                    $("#attachments-contact-document-uploader-list").html("");
                    $("#attachments-contact-document-uploaded-list").html("");
                    data.documents?.map(doc => {
                        renderUploadedDocumentItem("attachments-contact-document-uploaded-list", doc)
                    })
                    setRequiredDocumentListCheckboxes("attachments-contact-document-uploader-required-checkbox-list", data.documents);
                }
            },
            error: function(response) {
                console.error(response);
            }
        });
    }

    function getDealMedia(dealId) {
        $.ajax({
            url: "{{ route('getDealMedia') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                deal_id: dealId
            },
            success: function(response) {
                if(response.status == 1){
                    const {data} = response;
                    initDealDropzone();
                    $("#attachments-deal-document-uploader-list").html("");
                    $("#attachments-deal-document-uploaded-list").html("");
                    data?.map(doc => {
                        renderUploadedDocumentItem("attachments-deal-document-uploaded-list", doc)
                    })
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
            url: "{{ route('getDealLeads') }}",
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


    function deleteDealMedia(mediaId, model, elementToBeRemove = null){
        $.ajax({
             url: "{{ route('deal.delete.doc') }}",
             type: 'POST',
             dataType: 'json',
             data: {
                id: mediaId,
                model: model,
                deal_id: 1
             },
             cache: false,
             success: function(response) {
                if(response.status == 1){
                    if(elementToBeRemove !== null){
                        $(elementToBeRemove).remove();
                    }
                    showToastify(response.msg, "success");
                }
             },
             error: function(jqXhr, json, errorThrown) {
                 showToastify("Something Went Wrong!", "error");
             },
         });
    }

    function deleteMedia(mediaId, model, uploadedElementId){
        let formData = new FormData();
        formData.append("id", mediaId);
        formData.append("model", model);

        $.ajax({
             url: "{{ route('deleteMedia') }}",
             type: 'POST',
             dataType: 'json',
             data: {
                id: mediaId,
                model: model
             },
             cache: false,
             success: function(response) {
                if(response.status == 1){
                    showToastify(response.msg, "success");
                    if(uploadedElementId != null && uploadedElementId != ""){
                        $(`#${uploadedElementId}`).html("");
                        const model = uploadedElementId.split('-')[1];
                        if(model == "contact"){
                            getOwner(contact_id);
                        }
                        if(model == "deal"){
                            getDealMedia(deal_id);
                        }
                        if(model == "lead"){
                            getDealLeads(deal_id);
                        }
                    }
                }
             },
             error: function(jqXhr, json, errorThrown) {
                 showToastify("Something Went Wrong!", "error");
                 // $('#screen_loader').addClass('d-none');

             },
         });
    }

    function toggleFileUploader(elementId, open = true) {
        if (open) {
            if(!openedDocumentUploaders.includes(elementId)){
                openedDocumentUploaders.push(elementId);
            }
            $(`#${elementId}`).removeClass("d-none");
        } else {
            const index = openedDocumentUploaders.indexOf(elementId);
            if(index >= 0){
                openedDocumentUploaders.splice(index, 1)
            }
            $(`#${elementId}`).addClass("d-none")
        }
    }

    $(document).on("click", ".delete-deal-media-button", function(){
        const parentElement = $(this).closest(".row");
        const id = $(this).data("id");
        const model = $(this).data("model");
        deleteDealMedia(id, model, parentElement);
    });

    $(document).on("click", ".delete-media-button", function(){
        const parentElement = $(this).closest(".row");
        const id = $(this).data("id");
        const model = $(this).data("model");
        const element = $(this).data("element");

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
                deleteMedia(id, model, element);
            }
        });
    });

    $(document).on("submit", "#form-4", function(event){
        event.preventDefault();
        let formData = $(this).serializeArray();

        if(formData.length <= 0){
            return;
        }
        $.ajax({
             url: "{{ route('update.deal.doc') }}",
             type: 'POST',
             dataType: 'json',
             data: formData,
             cache: false,
             success: function(response) {
                if(response.status == 1){
                    showToastify(response.msg, "success");
                    getOwner(contact_id);
                    getDealLeads(deal_id);
                    getDealMedia(deal_id);
                }
             },
             error: function(jqXhr, json, errorThrown) {
                 showToastify("Something Went Wrong!", "error");
                 // $('#screen_loader').addClass('d-none');
             },
         });
    })

    $(document).on("submit", "#attachmentsEditDocumentForm", function(event){
        event.preventDefault();
        let formData = $(this).serializeArray();

        if(formData.length <= 0){
            return;
        }
        const uploderElementId = $("#attachmentsEditDocumentUploderElementId").val();
        $.ajax({
             url: "{{ route('update.deal.document') }}",
             type: 'POST',
             dataType: 'json',
             data: formData,
             cache: false,
             success: function(response) {
                if(response.status == 1){
                    showToastify(response.msg, "success");
                    $("#attachmentsEditDocumentModal").modal("hide");
                    if(uploderElementId != null && uploderElementId != ""){
                        $(`#${uploderElementId}`).html("");
                        const model = uploderElementId.split('-')[1];
                        if(model == "contact"){
                            getOwner(contact_id);
                        }
                        if(model == "deal"){
                            getDealMedia(deal_id);
                        }
                        if(model == "lead"){
                            getDealLeads(deal_id);
                        }
                    }
                }
             },
             error: function(jqXhr, json, errorThrown) {
                 showToastify("Something Went Wrong!", "error");
                 // $('#screen_loader').addClass('d-none');

             },
         });
    })

    $(document).on("click", ".view-document-button", function(){
        $("#attachmentsViewDocumentModalContent").html("");
        let title = $(this).data("title");
        let url = $(this).data("url");
        // let url = "https://images.unsplash.com/photo-1532012197267-da84d127e765?w=800&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8MXx8fGVufDB8fHx8fA%3D%3D";
        // let url = "https://www.africau.edu/images/default/sample.pdf";
        let extension = $(this).data("extension");
        let content = "";
        if(getFileTypeByExtension(extension) == "document"){
            content = `<iframe src="${url}"></iframe>`;
        }else if(getFileTypeByExtension(extension) == "image"){
            content = `<img src="${url}" />`;
        }
        $("#attachmentsViewDocumentModalLabel").text(title);
        $("#attachmentsViewDocumentModalContent").html(content);
        $("#attachmentsViewDocumentModal").modal("show");
    })

    $(document).on("click", ".edit-media-button", function(){
        $("#attachmentsEditDocumentDocumentTitle").html("");
        let id = $(this).data("id");
        let model = $(this).data("model");
        let title = $(this).data("title");
        let element = $(this).data("element");
        $("#attachmentsEditDocumentId").val(id);
        $("#attachmentsEditDocumentModel").val(model);
        $("#attachmentsEditDocumentUploderElementId").val(element);
        setDocumentTitleOptions("attachmentsEditDocumentDocumentTitle");
        $("#attachmentsEditDocumentDocumentTitle").val(title).trigger("change")
        $("#attachmentsEditDocumentModal").modal("show");
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

</script>
@endpush
