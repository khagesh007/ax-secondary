<?php $__env->startSection('title'); ?>
    Criteria Matching
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }

        /* .selectionTabs .active {
                            background: linear-gradient( 90deg, rgb(211, 161, 136) 25%, rgb(255, 255, 255) 35%, rgb(211, 161, 136) 40%, rgb(211, 161, 136) 60%, rgb(255, 255, 255) 75%, rgb(211, 161, 136) 93% ) !important;
                            color: #5e5e5e !important;
                        } */
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Criteria Selection</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Criteria</a></li>
                            <li class="breadcrumb-item active">Matching</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">

                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-center mb-3">
                            <h5>
                                What are you looking for?
                            </h5>
                        </div>
                    </div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-3 center-tabs text-center" role="tablist">
                        <li class="nav-item modelSelection" data-val="Property">
                            <a class="nav-link  fs-16 border3s" data-bs-toggle="tab" href="#fetchListings" role="tab"
                                aria-selected="false">
                                Find Buyer/Tenant
                            </a>
                        </li>
                        <li class="nav-item modelSelection ms-1" data-val="Lead">
                            <a class="nav-link  fs-16 border3s" data-bs-toggle="tab" href="#fetchLeads" role="tab"
                                aria-selected="false">
                                Find Properties
                            </a>
                        </li>
                    </ul>

                    
                    <!-- Tab panes -->

                    <div class="row d-none" id="getModel">
                        <div class="col-md-6 text-center mb-3 offset-3">
                            <h6>
                                Please enter the ID/Ref No of the <span id="modelType"></span> you are looking for
                            </h6>
                            <div class="input-group  d-none">
                                <span class="input-group-text" id="defineModel">@</span>
                                <input type="text" class="form-control" id="defineModel_id" placeholder="Username"
                                    aria-label="Username" aria-describedby="basic-addon1">


                            </div>


                            <div id="property_ref_div">
                                <select name='property_ref' id="property_ref"
                                    class="form-control select2 search-inputs searchInput checkInput d-none">
                                    <option value=""></option>

                                    <?php $__currentLoopData = $properties ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($property->id); ?>"><?php echo e($property->ref_no); ?>

                                            <?php echo e($property->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div id="lead_id_div">
                                <select name='lead_id' id="lead_id"
                                    class="form-control select2 search-inputs searchInput checkInput d-none">
                                    <option value=""></option>

                                    <?php $__currentLoopData = $leads ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($lead->id); ?>"><?php echo e($lead->first_name); ?> <?php echo e($lead->last_name); ?> <
                                                <?php echo e($lead->mobile); ?>>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>


                        </div>
                    </div>



                    <!-- Custom Radio Color -->

                    <form action="javascript:void(0);" class="d-none mt-3" id="mainForm">
                        <input type="hidden" name="model_type" id="model_type">
                        <input type="hidden" name="model_id" id="model_id">

                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select name="property_category_id" id="property_category_id"
                                        class="form-control select2 property lead">
                                        <option value=""></option>

                                        <?php $__currentLoopData = $property_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                    </select>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="purpose_id" class="form-label">Purpose</label>
                                    <select name='for[]' id="for" class="form-control select2  property lead"
                                        multiple>

                                        <?php $__currentLoopData = \App\Models\Lead::FOR_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(
                                                $key == 'Unspecified' &&
                                                    auth()->user()->hasRole('Agent')): ?>
                                            <?php else: ?>
                                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?> </option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="bed_id" class="form-label">Bed</label>
                                    <select name="bed_id" id="bed_id" class="form-control select2  property lead">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $beds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bed->id); ?>"><?php echo e($bed->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                    </select>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="bath_id" class="form-label">Bath</label>
                                    <select name="bath_id" id="bath_id" class="form-control select2 property lead">
                                        <option value=""></option>

                                        <?php $__currentLoopData = $baths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bath->id); ?>"><?php echo e($bath->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="min_price" class="form-label">Communities</label>
                                    <select class="select2 inputsCriteria inputs" multiple id="communities"
                                        name="communities[]"></select>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="min_price" class="form-label">Location</label>
                                    <input type="hidden" name="locations" id="locations">
                                    <select class="select2 inputsCriteria inputs" multiple id="sub_communities"
                                        name="sub_communities[]"></select>
                                </div>
                            </div>



                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="min_price" class="form-label">Min Price</label>
                                    <input type="text" class="form-control property lead" placeholder="price"
                                        id="min_price" name="min_price">
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="max_price" class="form-label">Max Price</label>
                                    <input type="text" class="form-control property lead" placeholder="price"
                                        id="max_price" name="max_price">
                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>






                </div><!-- end card-body -->
            </div><!-- end card -->

        </div>
    </div>

    <div id="appendData">

    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script>
        $('#bed_id').select2({
            placeholder: 'Select No of Beds',
            allowClear: true
        });

        $('#bath_id').select2({
            placeholder: 'Select No of Bath',
            allowClear: true
        });

        $('#for').select2({
            placeholder: 'Select Purpose',
            allowClear: true
        });

        $('#property_category_id').select2({
            placeholder: 'Select Category',
            allowClear: true
        });


        let model_type = '';
        $(document).ready(function() {
            $('.modelSelection').click(function() {

                model_type = $(this).data('val');
                if (model_type == 'Property') {
                    $('#lead_id_div').addClass('d-none');
                    $('#property_ref_div').removeClass('d-none');

                    $('#modelType').text('Listing');
                    $('#defineModel').text('Listing');
                    $('#defineModel_id').attr('placeholder', 'Listing ID');
                    $('#mainForm').removeClass('d-none');
                } else {

                    $('#lead_id_div').removeClass('d-none');
                    $('#property_ref_div').addClass('d-none');
                    $('#modelType').text('Leads');
                    $('#defineModel').text('Lead');
                    $('#defineModel_id').attr('placeholder', 'Lead ID');
                    $('#mainForm').removeClass('d-none');
                }

                $('#getModel').removeClass('d-none');
                $('#model_type').val(model_type);


                // Create a new URL with the desired parameters
                var newUrl = window.location.pathname + '?model_type=' + model_type;
                history.pushState({}, '', newUrl);

            });


            $('.checkInput').change(function() {
                let vl = $(this).val();
                $('#defineModel_id').val(vl).trigger('change');
                var newUrl = window.location.pathname + '?model_type=' + model_type + '&defineID=' + vl;
                history.pushState({}, '', newUrl);
            })


            $('#defineModel_id').change(function() {
                let modelId = $(this).val();
                if (modelId == '') {} else {
                    let model_type = $('#model_type').val();
                    let modelId = $('#defineModel_id').val();
                    $('#model_id').val(modelId);
                    $.ajax({
                        url: "<?php echo e(route('admin.criteria.matching.fetchDetails')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            model_type: model_type,
                            model_id: modelId
                        },
                        success: function(result) {
                            console.table(result)
                            if (result.status == 1) {
                                let item = result.data;
                                console.table(item)
                                if (result.model_type == 'Property') {
                                    console.log('Property')
                                    $('#communities').val('').trigger('change');
                                    $('#sub_communities').val('').trigger('change');

                                    $('#for').val(item.for).trigger('change');
                                    $('#location_id').val(item.location);
                                    $('#bed_id').val(item.bed_id).trigger('change');
                                    $('#property_category_id').val(item.property_category_id)
                                        .trigger('change');
                                    if (item.property_category_id) {
                                        $('#property_category_id').val(item
                                            .property_category_id).trigger('change');
                                    }
                                    $('#bath_id').val(item.bath_id).trigger('change');
                                    $('#min_price').val(item.price.price);
                                    $('#communities').val(item.community_id).trigger('change');

                                    if ($('#communities').val().length == 0) {
                                        if (!$('#communities').find("option[value='" + item
                                                .community_id + "']").length) {
                                            $('#communities').val(null).trigger('change');
                                            var newOption = new Option(item.community.name, item
                                                .community_id, true, true);
                                            $('#communities').append(newOption).trigger(
                                                'change');
                                        } else {
                                            $('#communities').append(item.community_id).trigger(
                                                'change');
                                        }

                                    }


                                    if (!$('#sub_communities').find("option[value='" + item
                                            .sub_community_id + "']").length) {
                                        $('#sub_communities').val(null).trigger('change');
                                        var newOption = new Option(item.sub_community.name, item
                                            .sub_community_id + ':sub_community', true, true
                                            );
                                        $('#sub_communities').append(newOption).trigger(
                                            'change');
                                    } else {
                                        $('#sub_communities').append(item.sub_community_id)
                                            .trigger('change');
                                    }




                                } else if (result.model_type == 'Lead') {
                                    console.log('Lead')
                                    $('#communities').val('').trigger('change');
                                    $('#sub_communities').val('').trigger('change');
                                    $('#for').val(item.for).trigger('change');
                                    $('#location_id').val(item.location);
                                    if (item.criteria) {
                                        $('#bath_id').val(item.criteria.min_bath_id).trigger(
                                            'change');
                                        $('#bed_id').val(item.criteria.min_bed_id).trigger(
                                            'change');
                                        $('#min_price').val(item.criteria.min_price).trigger(
                                            'change');
                                        $('#max_price').val(item.criteria.max_price).trigger(
                                            'change');
                                    }
                                    if (item.property?.property_category_id) {
                                        $('#property_category_id').val(item.property
                                            .property_category_id).trigger('change');
                                    } else {
                                        $('#property_category_id').val('').trigger('change');
                                    }



                                    if (item?.criteria.locations) {
                                        let locations = item?.criteria.locations;
                                        let subCom = [];
                                        let locationTypes = [];
                                        let cmb = '';
                                        $.each(locations, function(key, value) {
                                            console.log('locations')

                                            if (value.parentable_type ==
                                                'App\\Models\\Community') {

                                                $('#communities').find(
                                                    "option[value='" + value
                                                    .parentable_id + "']").remove();
                                                var newOption = new Option(value
                                                    ?.parentable
                                                    ?.path_name, value
                                                    .parentable_id, true, true
                                                );
                                                $('#communities').append(newOption)
                                                    .trigger(
                                                        'change');
                                            }
                                            if (value.parentable_type ==
                                                'App\\Models\\SubCommunity') {
                                                let cmbid = value.parentable_id +
                                                    ':sub_community';
                                                if ($('#sub_communities').find(
                                                        "option[value='" + cmbid + "']")
                                                    .length) {
                                                    $('#sub_communities').val(value
                                                        .parentable_id).trigger(
                                                        'change');
                                                } else {
                                                    var newOption = new Option(value
                                                        ?.parentable?.path_name,
                                                        cmbid, true, true);
                                                    $('#sub_communities').append(
                                                        newOption).trigger('change');
                                                }
                                                subCom.push(cmbid);
                                                //  $("#sub_communities").append('<option data-type="sub_community" value="'+value.parentable_id+'" selected="true">'+value?.parentable?.path_name+'</option>');

                                            }
                                            if (value.parentable_type ==
                                                'App\\Models\\CommunityProperty') {
                                                let cmbid = value.parentable_id +
                                                    ':community_property';
                                                if ($('#sub_communities').find(
                                                        "option[value='" + cmbid + "']")
                                                    .length) {
                                                    $('#sub_communities').val(cmbid)
                                                        .trigger('change');
                                                } else {
                                                    var newOption = new Option(value
                                                        ?.parentable?.path_name,
                                                        cmbid, true, true);
                                                    $('#sub_communities').append(
                                                        newOption).trigger('change');
                                                }
                                                subCom.push(cmbid);
                                            }
                                        });


                                    } else {
                                        $('#communities').val('').trigger('change');
                                    }





                                    console.log(item)

                                }
                            }


                        },
                        error: function(result) {
                            console.log(result);
                        }
                    });


                }
            })



            $('#property_ref').select2({
                placeholder: 'Search',
                minimumInputLength: 0,
                ajax: {
                    url: "<?php echo e(route('ajax.propertyAutocomplete')); ?>",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: '[' + item.ref_no + '] ' + (item.title ?? "No Name"),
                                    id: item?.id ?? '-',
                                    unit_no: item?.unit_no ?? '-',
                                    ref_no: item?.ref_no ?? '-',
                                    location: item?.community_property?.name ?? '-',
                                    type: item?.property_category?.title ?? '-',
                                    beds: item?.bed?.name ?? '-',
                                    first_image: item?.first_image?.thumbnail ?? '-',
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: formatProperty2,

            });


            $('#lead_id').select2({
                placeholder: 'Search Leads',
                minimumInputLength: 0,
                ajax: {
                    url: "<?php echo e(route('ajax.leadsAutocomplete')); ?>",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: '[' + item.id + '] ' + (item.first_name ??
                                        "No Name") + ' ' + (item.last_name ?? "") + ' <' + (item
                                            .mobile ?? "No Mobile") + '> <' + (item.lead_status
                                            .name ?? "Unspecified") + '>',
                                    id: item?.id ?? '-',
                                    mobile: item?.mobile ?? '-',

                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: leadsFormat,

            });

        })

        function leadsFormat(e) {
            return e.text;
        }

        function formatProperty2(e) {
            let f_unit_no = e.unit_no;
            let f_ref_no = e.ref_no;
            let f_location = e.location;
            let f_type = e.type;
            let f_beds = e.beds;
            let f_first_image = e.first_image ? e.first_image :
                'https://ui-avatars.com/api/?name=' + f_unit_no + '&color=7F9CF5&background=random';

            return e.id ? $('<span><img src="' + f_first_image + '" class="img-flag rounded-5" width="18" /> ' + f_ref_no +
                ' - ' + f_type + ' - ' + f_unit_no +
                "</span><span class=' small ml-5 d-inline-block'> </span> <span class=' small ml-5 d-inline-block'> <i class='mdi mdi-google-maps '></i>" +
                f_location +
                "</span> ") : e.text;
        }


        $(document).on('submit', '#mainForm', function(event) {
            $('#appendData').html('');
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "<?php echo e(route('admin.criteria.matching.returnModel')); ?>",
                type: 'POST',
                dataType: 'html',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#appendData').html(result);
                    $("#submitBtn").prop('disabled', false);
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log(jqXhr);
                },
            });
        });




        locationSelect2();

        function locationSelect2() {

            $('#communities').select2({
                placeholder: 'Community',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '<?php echo e(route('ajax.community.select2')); ?>',
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.path_name}`,
                                    id: item.id

                                }
                            })
                        }
                    }
                }
            });

            $('#sub_communities').select2({
                placeholder: 'Tower, community, locality',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '<?php echo e(route('ajax.sub-community.property.select2')); ?>',
                    dataType: 'json',
                    data: function(term, page) {
                        return {
                            q: term, // search term
                            communities: $("#communities").val(),
                            page_limit: 10
                        };
                    },
                    processResults({
                        data
                    }) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    id: item.id + ':' + item.type,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'type': item.type
                                }
                            })
                        }
                    }
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/criteria_matching/index.blade.php ENDPATH**/ ?>