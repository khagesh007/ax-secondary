<style>
    .pointerCursor {
        cursor: pointer;
    }

    .loader {
        position: absolute;
        /* Position the loader */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .highlighted {
        background-color: #ffff00;
        font-weight: bold;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<!-- staticBackdrop Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">

        <div class="modal-content" style="max-height:100%">
            <div class="modal-header">
                <h5>Universal Search</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="globalClose"></button>
            </div>
            <div class="modal-body text-center p-1 bg-light">
                <div class="container">
                    <form action="javascript:void(0);" id="globalSearchForm" class="app-search">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div>
                                        <div class="form-icon">
                                            <input type="text" class="form-control form-control-icon bg-white"
                                                id="search_keywords" name="search_keywords" placeholder="Search..."
                                                autocomplete="off">
                                            <i class=" bx bx-search "></i>
                                            <span
                                                class="mdi mdi-close-circle search-widget-icon search-widget-icon-close"
                                                id="clearInput"></span>
                                        </div>
                                        <input type="hidden" name="search_modal">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="" class="fs-13 text-muted mb-2">What are you looking
                                        for</label>
                                    <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-0"
                                        role="tablist">
                                        <li class="nav-item active rmActive" id="getAllTab">
                                            <a class="nav-link active selectModal selectModal" data-bs-toggle="tab"
                                                id="addActive" href="#getAll" role="tab" aria-selected="true"
                                                data-modal="All">
                                                All</a>
                                        </li>
                                        <li class="nav-item rmActive">
                                            <a class="nav-link selectModal rmActive" data-bs-toggle="tab"
                                                href="#getListingAll" role="tab" aria-selected="false"
                                                data-modal="Property">
                                                Properties</a>
                                        </li>
                                        <li class="nav-item rmActive">
                                            <a class="nav-link selectModal rmActive" data-bs-toggle="tab"
                                                href="#getLeadsAll" role="tab" aria-selected="false"
                                                data-modal="Lead">
                                                Leads</a>
                                        </li>

                                        <li class="nav-item rmActive">
                                            <a class="nav-link selectModal rmActive" data-bs-toggle="tab"
                                                href="#getContactsAll" role="tab" aria-selected="false"
                                                data-modal="Contact">
                                                Contacts</a>
                                        </li>
                                        <li class="nav-item rmActive">
                                            <a class="nav-link selectModal rmActive" data-bs-toggle="tab"
                                                href="#getListingLeads" role="tab" aria-selected="false"
                                                data-modal="Listing Leads">
                                                Listing Leads</a>
                                        </li>
                                        <?php if(!auth()->user()->hasRole(['Sales Director', 'Line Manager', 'Team Leader', 'Agent'])): ?>
                                            <li class="nav-item ">
                                                <a class="nav-link  selectModal" data-bs-toggle="tab"
                                                    href="#getUsersAll" role="tab" aria-selected="true"
                                                    data-modal="User">
                                                    Users</a>
                                            </li>
                                        <?php endif; ?>

                                    </ul>
                                    <div class="tab-content text-muted">


                                        <div class="tab-pane" id="getListingAll" role="tabpanel">
                                        </div>
                                        <div class="tab-pane" id="getLeadsAll" role="tabpanel">
                                        </div>
                                        <div class="tab-pane" id="getContactsAll" role="tabpanel">
                                        </div>
                                        <div class="tab-pane" id="getListingLeads" role="tabpanel">
                                        </div>
                                        <?php if(!auth()->user()->hasRole(['Sales Director', 'Line Manager', 'Team Leader', 'Agent'])): ?>
                                            <div class="tab-pane" id="getUsersAll" role="tabpanel">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-none">
                                        <div class="form-icon text-start">

                                            <div>
                                                <span class="badge bg-success fs-12 selectModal pointerCursor"
                                                    data-modal="Null">All</span>
                                                <span
                                                    class="badge bg-primary  fs-12 selectModal pointerCursor bg-primary"
                                                    data-modal="Property">Listing</span>
                                                <span
                                                    class="badge bg-primary fs-12 selectModal pointerCursor bg-primary"
                                                    data-modal="Lead">Leads</span>
                                                <span
                                                    class="badge bg-primary fs-12 selectModal pointerCursor bg-primary"
                                                    data-modal="Contact">Contact</span>
                                                    <span
                                                    class="badge bg-primary fs-12 selectModal pointerCursor bg-primary"
                                                    data-modal="Listing Leads">Listing Leads</span>
                                                <?php if(!auth()->user()->hasRole(['Sales Director', 'Line Manager', 'Team Leader', 'Agent'])): ?>
                                                    <span
                                                        class="badge bg-primary fs-12 selectModal pointerCursor bg-primary"
                                                        data-modal="User">User</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="text-end">

                                    
                                    
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <div class="row mt-5 d-none" style="min-height:500px" id="loadingRow">
                            <div class="loader"></div>
                        </div>


                        <div class="row" id="showFilteredResults">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $__env->startSection('global_scripts'); ?>
    <script>
        var currentRequest = null;


        $('#clearInput').hide();
        $(document).ready(function() {
            $('.selectModal').click(function() {
                var modal = $(this).data('modal');
                // $(this).siblings().addClass('bg-primary');
                // $(this).siblings().removeClass('bg-success');
                // $(this).addClass('bg-success');
                $('input[name="search_modal"]').val(modal);
            });
        });

        $('#clearInput').click(function() {
            $('#search_keywords').val('');
            $('#clearInput').hide();
            $('#showFilteredResults').html('');
        });

        // on change of search_keywords after 3 characters show #clearInput else hide #clearInput
        $('#search_keywords').on('input', function() {
            if ($(this).val().length > 0) {
                $('#clearInput').show();
            } else {
                $('#clearInput').hide();
            }
        });

        $('#globalClose').click(function() {
            $('#search_keywords').val('');
            $('#showFilteredResults').html('');

        });

        // $(document).on('submit', '#globalSearchForm', function(event) {
        //     event.preventDefault();
        //     $('#showFilteredResults').html('');
        //     $('#loadingRow').removeClass('d-none');

        //     var search_modal = $('input[name="search_modal"]').val();
        //     var search_keywords = $('input[name="search_keywords"]').val();
        //     var formData = new FormData($(this)[0]);

        //     $.ajax({
        //         url: "<?php echo e(route('ajax.globalSearch')); ?>",
        //         type: 'POST',
        //         dataType: 'html',
        //         data: formData,
        //         cache: false,
        //         processData: false,
        //         contentType: false,
        //         success: function(result) {
        //             $('#showFilteredResults').html(result);
        //             var modal = $("#staticBackdrop");
        //                             var searchKeywords = $("#search_keywords").val();
        //                             if (searchKeywords !== "") {
        //                                 var regex = new RegExp(searchKeywords, "gi");
        //                                 modal.find("p").each(function() {
        //                                     var text = $(this).text();
        //                                     $(this).html(text.replace(regex,
        //                                         "<span class='highlighted'>$&</span>"));
        //                                 });
        //                                 modal.find("span").each(function() {
        //                                     var text = $(this).text();
        //                                     $(this).html(text.replace(regex,
        //                                         "<span class='highlighted'>$&</span>"));
        //                                 });
        //                                 modal.find("h6").each(function() {
        //                                     var text = $(this).text();
        //                                     $(this).html(text.replace(regex,
        //                                         "<span class='highlighted'>$&</span>"));
        //                                 });
        //                                 modal.find("small").each(function() {
        //                                     var text = $(this).text();
        //                                     $(this).html(text.replace(regex,
        //                                         "<span class='highlighted'>$&</span>"));
        //                                 });

        //                                 $('#loadingRow').addClass('d-none');
        //                             }
        //             $('#loadingRow').addClass('d-none');

        //         },
        //         error: function(jqXhr, json, errorThrown) {

        //         },
        //     });
        // });



$('#search_keywords').on('input', function() {
  var searchKeywords = $(this).val();

  if (searchKeywords.length > 2) {
    event.preventDefault();
    $('#showFilteredResults').html('');
    $('#loadingRow').removeClass('d-none');

    // Abort the previous request, if it exists
    if (currentRequest) {
      currentRequest.abort();
    }

    var formData = new FormData(document.querySelector('#globalSearchForm'));

    // Send the new AJAX request
    currentRequest = $.ajax({
      url: "<?php echo e(route('ajax.globalSearch')); ?>",
      type: 'POST',
      dataType: 'html',
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function(result) {
        $('#showFilteredResults').html('');

        // Make the function wait for 1 second
        // setTimeout(function() {
          $('#showFilteredResults').html(result);
          var modal = $("#staticBackdrop");

          if (searchKeywords !== "") {
            var regex = new RegExp(escapeRegExp(searchKeywords), "gi");
            modal.find(".searchable").each(function() {
              var text = $(this).text();
            //   $(this).html(text.replace(regex, "<span class='highlighted'>$&</span>"));
            $(this).html(text.replace(regex, "<span class='highlighted'>$&</span>"));

            });
          }

          $('#loadingRow').addClass('d-none');
        // }, 1000);
      },
      error: function(jqXhr, json, errorThrown) {

      },
      complete: function() {
        // Reset the currentRequest variable when the request is completed
        currentRequest = null;
      }
    });
  } else if (searchKeywords.length > 0 && searchKeywords.length < 3) {
    $('#showFilteredResults').html('');
    $('#loadingRow').addClass('d-none');
  }
});

function escapeRegExp(string) {
  return string.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&');
}


        $('#globalSearchOpenBtn').click(function() {
            $('input[name="search_modal"]').val('All');
            $('#getAllTab').addClass('active');
            $('#getAll').addClass('active');
            $('.selectModal').removeClass('active');
            $('#addActive').addClass('active');

            $('#search_keywords').val('');
            $('#showFilteredResults').html('');



        });
      // Declare a variable to hold the reference to the current AJAX request

$('.selectModal').on('click', function(event) {
  if ($('#search_keywords').val().length > 2) {
    event.preventDefault();
    $('#loadingRow').removeClass('d-none');

    $('input[name="search_modal"]').val($(this).data('modal'));

    $('#showFilteredResults').html('');

    var search_modal = $('input[name="search_modal"]').val();
    var search_keywords = $('input[name="search_keywords"]').val();
    var formData = new FormData(document.querySelector('#globalSearchForm'));

    // Abort the previous request, if it exists
    if (currentRequest) {
      currentRequest.abort();
    }

    // Send the new AJAX request
    currentRequest = $.ajax({
      url: "<?php echo e(route('ajax.globalSearch')); ?>",
      type: 'POST',
      dataType: 'html',
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function(result) {
        $('#showFilteredResults').html('');

        // setTimeout(function() {
          $('#showFilteredResults').html(result);

          var modal = $("#staticBackdrop");
          var searchKeywords = $("#search_keywords").val();
          if (searchKeywords !== "") {
            var regex = new RegExp(escapeRegExp(searchKeywords), "gi");
            modal.find(".searchable").each(function() {
              var text = $(this).text();
            //   $(this).html(text.replace(regex, "<span class='highlighted'>$&</span>"));
              $(this).html(text.replace(regex, "<span class='highlighted'>$&</span>"));

            });
          }

          $('#loadingRow').addClass('d-none');
        // }, 1000);
      },
      error: function(jqXhr, json, errorThrown) {
        $('#showFilteredResults').html(result);
        $('#loadingRow').addClass('d-none');
        showToastify('Something Went Wrong. Please Try Again.', "error");
      },
      complete: function() {
        // Reset the currentRequest variable when the request is completed
        currentRequest = null;
      }
    });
  }
});

    </script>
<?php $__env->stopSection(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/layouts/global-search.blade.php ENDPATH**/ ?>