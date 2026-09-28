<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.mailbox'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
    <style>
        .read {
            background: whitesmoke;
        }

        .link-icon {
            font-size: 15px;
            float: right;
            color: #015ae9;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="email-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">


        <div class="email-content">

            <div class="p-4 pb-0">
                <div class="border-bottom border-bottom-dashed">
                    <div class="row mt-n2 mb-3 mb-sm-0">
                        <div class="col col-sm-auto order-1 d-block d-lg-none">
                            <button type="button" class="btn btn-soft-success btn-icon btn-sm fs-16 email-menu-btn">
                                <i class="ri-menu-2-fill align-bottom"></i>
                            </button>
                        </div>
                        <div class="col-sm order-3 order-sm-2">
                            <div class="hstack gap-sm-1 align-items-center flex-wrap email-topbar-link">
                                <div class="form-check fs-14 m-0">
                                    <input class="form-check-input" type="checkbox" value="" id="checkall">
                                    <label class="form-check-label" for="checkall"></label>
                                </div>
                                <div id="email-topbar-actions">
                                    <div class="hstack gap-sm-1 align-items-center flex-wrap d-none">
                                        <button type="button"
                                                class="btn btn-ghost-secondary btn-icon btn-sm shadow-none fs-16"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                                title="Archive">
                                            <i class="ri-inbox-archive-fill align-bottom"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-ghost-secondary btn-icon btn-sm shadow-none fs-16"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                                title="Report Spam">
                                            <i class="ri-error-warning-fill align-bottom"></i>
                                        </button>
                                        <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                             title="Trash">
                                            <button type="button"
                                                    class="btn btn-ghost-secondary btn-icon btn-sm shadow-none fs-16"
                                                    data-bs-toggle="modal" data-bs-target="#removeItemModal">
                                                <i class="ri-delete-bin-5-fill align-bottom"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="vr align-self-center mx-2"></div>
                                <div class="dropdown d-none">
                                    <button class="btn btn-ghost-secondary btn-icon shadow-none btn-sm fs-16"
                                            type="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-price-tag-3-fill align-bottom"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">Support</a>
                                        <a class="dropdown-item" href="#">Freelance</a>
                                        <a class="dropdown-item" href="#">Social</a>
                                        <a class="dropdown-item" href="#">Friends</a>
                                        <a class="dropdown-item" href="#">Family</a>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-ghost-secondary shadow-none btn-icon btn-sm fs-16"
                                            type="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-more-2-fill align-bottom"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#" id="mark-selected-read">Mark selected as Read</a>
                                        <a class="dropdown-item" href="#" id="mark-selected-unread">Mark selected as
                                            UnRead</a>
                                        <a class="dropdown-item" href="#" id="mark-all-read">Mark All as
                                            Read</a>
                                    </div>
                                </div>
                                <div
                                    class="alert alert-warning alert-dismissible unreadConversations-alert px-4 fade show "
                                    id="unreadConversations" role="alert">
                                    No Unread Conversations
                                </div>
                            </div>
                        </div>
                        <div class="col-auto order-2 order-sm-3 d-none">
                            <div class="d-flex gap-sm-1 email-topbar-link">
                                <button type="button" class="btn btn-ghost-secondary btn-icon shadow-none btn-sm fs-16">
                                    <i class="ri-refresh-line align-bottom"></i>
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-ghost-secondary btn-icon shadow-none btn-sm fs-16"
                                            type="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-more-2-fill align-bottom"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">Mark as Unread</a>
                                        <a class="dropdown-item" href="#">Mark as Important</a>
                                        <a class="dropdown-item" href="#">Add to Tasks</a>
                                        <a class="dropdown-item" href="#">Add Star</a>
                                        <a class="dropdown-item" href="#">Mute</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-end mt-3">
                        <div class="col">
                            <div id="mail-filter-navlist">
                                <ul class="nav nav-tabs nav-tabs-custom gap-1 text-center border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link fw-semibold active" id="pills-primary-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-primary" type="button"
                                                role="tab"
                                                aria-controls="pills-primary" aria-selected="true">
                                            <i class="ri-inbox-fill align-bottom d-inline-block"></i>
                                            <span class="ms-1 d-none d-sm-inline-block">Primary</span>
                                        </button>
                                    </li>
                                    <li class="nav-item d-none">
                                        <button class="nav-link fw-semibold" id="pills-social-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-social" type="button" role="tab"
                                                aria-controls="pills-social" aria-selected="false">
                                            <i class="ri-group-fill align-bottom d-inline-block"></i>
                                            <span class="ms-1 d-none d-sm-inline-block">Social</span>
                                        </button>
                                    </li>
                                    <li class="nav-item d-none">
                                        <button class="nav-link fw-semibold" id="pills-promotions-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-promotions" type="button"
                                                role="tab" aria-controls="pills-promotions" aria-selected="false">
                                            <i class="ri-price-tag-3-fill align-bottom d-inline-block"></i>
                                            <span class="ms-1 d-none d-sm-inline-block">Promotions</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                        </div>
                        <div class="col-auto">
                            <div class="text-muted mb-2 d-none">1-50 of 154</div>
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-primary" role="tabpanel"
                         aria-labelledby="pills-primary-tab">
                        <div class="message-list-content mx-n4 px-4 message-list-scroll" data-simplebar>
                            <div id="mailLoader">
                                <div class="spinner-border text-primary avatar-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <ul class="message-list" id="notification-list">
                                <?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <li data-id="<?php echo e($list->id); ?>" id="notili<?php echo e($list->id); ?>"
                                        <?php if($list->read == 0): ?>  class="unread" <?php else: ?> class="read" <?php endif; ?>>
                                        <div class="col-mail col-mail-1">
                                            <div class="form-check checkbox-wrapper-mail fs-14">
                                                <input class="form-check-input notificationList" type="checkbox"
                                                       value="<?php echo e($list->id); ?>" id="checkbox-<?php echo e($list->id); ?>">
                                                <label class="form-check-label" for="checkbox-<?php echo e($list->id); ?>"></label>
                                            </div>
                                            <input type="hidden" value="" class="mail-userimg">
                                            <button type="button" class="btn avatar-xs p-0 favourite-btn fs-15 d-none ">
                                                <i class="ri-star-fill"></i></button>
                                            <a href="javascript: void(0);" class="title"><span
                                                    class="title-name"><?php echo e($list->notification->title); ?></span></a></div>
                                        <div class="col-mail col-mail-2">
                                            <a href="javascript: void(0);" class="subject"><span class="subject-title"><!-- subject here --></span>
                                                <span class="teaser"><?php echo e($list->notification->message); ?></span> </a>
                                            <div class="date"><?php echo e(date('F, d',strtotime($list->created_at))); ?></div>
                                        </div>
                                    </li>



                                    <li class="d-none">
                                        <div class="col-mail col-mail-1">
                                            <div class="form-check checkbox-wrapper-mail fs-14">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                       id="checkbox-1"> <label class="form-check-label"
                                                                               for="checkbox-1"></label></div>
                                            <input type="hidden" value="assets/images/users/avatar-2.jpg"
                                                   class="mail-userimg">
                                            <button type="button" class="btn avatar-xs p-0 favourite-btn fs-15"><i
                                                    class="ri-star-fill"></i></button>
                                            <a href="javascript: void(0);" class="title"><span class="title-name">Peter, me</span>
                                            </a></div>
                                        <div class="col-mail col-mail-2"><a href="javascript: void(0);" class="subject"><span
                                                    class="subject-title">Hello</span> – <span class="teaser">Trip home from Colombo has been arranged, then Jenna will come get me from Stockholm. :)</span>
                                            </a>
                                            <div class="date">Mar 7</div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <ul class="message-list d-none" id="mail-list">
                            </ul>
                        </div>
                        <div class="d-flex">

                            <button class="btn btn-primary btn-block flex-grow-1 waves-effect waves-light" data-next="2"
                                    id="loadMoreNotifications">Load More
                            </button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-social" role="tabpanel" aria-labelledby="pills-social-tab">
                        <div class="message-list-content mx-n4 px-4 message-list-scroll" data-simplebar>
                            <ul class="message-list" id="social-mail-list"></ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-promotions" role="tabpanel"
                         aria-labelledby="pills-promotions-tab">
                        <div class="message-list-content mx-n4 px-4 message-list-scroll" data-simplebar>
                            <ul class="message-list" id="promotions-mail-list"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end email-content -->

        <div class="email-detail-content">
            <div class="p-4 d-flex flex-column h-100">
                <div class="pb-4 border-bottom border-bottom-dashed">
                    <div class="row">
                        <div class="col">
                            <div class="">
                                <button type="button"
                                        class="btn btn-soft-danger btn-icon btn-sm fs-16 close-btn-email shadow-none">
                                    <i class="ri-close-fill align-bottom"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="hstack gap-sm-1 align-items-center flex-wrap email-topbar-link d-none">
                                <button type="button"
                                        class="btn btn-ghost-secondary btn-icon btn-sm shadow-none fs-16 favourite-btn active">
                                    <i class="ri-star-fill align-bottom"></i>
                                </button>
                                <button class="btn btn-ghost-secondary btn-icon btn-sm shadow-none fs-16">
                                    <i class="ri-printer-fill align-bottom"></i>
                                </button>
                                <button class="btn btn-ghost-secondary btn-icon btn-sm shadow-none fs-16">
                                    <i class="ri-delete-bin-5-fill align-bottom"></i>
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-ghost-secondary btn-icon btn-sm shadow-none fs-16"
                                            type="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-more-2-fill align-bottom"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">Mark as Unread</a>
                                        <a class="dropdown-item" href="#">Mark as Important</a>
                                        <a class="dropdown-item" href="#">Add to Tasks</a>
                                        <a class="dropdown-item" href="#">Add Star</a>
                                        <a class="dropdown-item" href="#">Mute</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mx-n4 px-4 email-detail-content-scroll" data-simplebar>
                    <div class="mt-4 mb-3">
                        <h5 class="fw-bold email-subject-title">New updates for Skote Theme</h5>
                    </div>

                    <div class="accordion accordion-flush">


                        <div class="accordion-item border-dashed left">
                            <div class="accordion-header">
                                <a role="button" class="btn w-100 text-start px-0 bg-transparent shadow-none"
                                   data-bs-toggle="collapse" href="#email-collapseThree" aria-expanded="true"
                                   aria-controls="email-collapseThree">
                                    <div class="d-flex align-items-center text-muted">
                                        <div class="flex-shrink-0 avatar-xs me-3 d-none">
                                            <img src="<?php echo e(URL::asset('assets/images/users/avatar-3.jpg')); ?>" alt=""
                                                 class="img-fluid rounded-circle">

                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="fs-14 text-truncate email-user-name mb-0">Jack Davis</h5>
                                            <div class="text-truncate fs-12">to: me</div>
                                        </div>
                                        <div class="flex-shrink-0 align-self-start">

                                            <div class="text-muted fs-12">
                                                <span id="rightBarMsgTime"></span>
                                                <br>

                                                <span class="link-icon">

                                                </span>
                                            </div>


                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div id="email-collapseThree" class="accordion-collapse collapse show">
                                <div class="accordion-body text-body px-0">
                                    <div>

                                        <p id="rightBarMessage"></p>
                                        <p>
                                            <a id="takeActionBtn" target="_blank" href="#" class="btn btn-primary btn-block flex-grow-1 waves-effect waves-light">Take action</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end accordion-item -->
                    </div>
                    <!-- end accordion -->
                </div>

            </div>
        </div>
        <!-- end email-detail-content -->
    </div>
    <!-- end email wrapper -->


    <!-- end email chat detail -->





<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

    <script src="<?php echo e(URL::asset('assets/js/pages/inbox.js')); ?>"></script>
        

    <script>


        function randomPost() {
            return `<li class="">        <div class="col-mail col-mail-1">            <div class="form-check checkbox-wrapper-mail fs-14">                <input class="form-check-input" type="checkbox" value="9" id="checkbox-9">                <label class="form-check-label" for="checkbox-9"></label>            </div>            <input type="hidden" value="assets/images/users/avatar-10.jpg" class="mail-userimg">            <button type="button" class="btn avatar-xs p-0 favourite-btn fs-15 active">            <i class="ri-star-fill"></i>            </button>            <a href="javascript: void(0);" class="title"><span class="title-name">Infinity HR</span> </a>        </div>        <div class="col-mail col-mail-2">            <a href="javascript: void(0);" class="subject"><span class="subject-title">Sveriges Hetaste sommarjobb</span> – <span class="teaser">Hej Nicklas Sandell! Vi vill bjuda in dig till "First tour 2014", ett rekryteringsevent som erbjuder jobb på 16 semesterorter iSverige.</span>            </a>            <div class="date">Feb 27</div>        </div>    </li>`;
        }

        $(document).ready(function () {
            var win = $(window);

            // Each time the user scrolls
            win.scroll(function () {
                // End of the document reached?
                if ($(document).height() - win.height() == win.scrollTop()) {
                    console.log('add more');
                    //  $('#loading').show();

                    // Uncomment this AJAX call to test it

                    // $.ajax({
                    //     url: 'get-post.php',
                    //     dataType: 'html',
                    //     success: function(html) {
                    //         $('#posts').append(html);
                    //         $('#loading').hide();
                    //     }
                    // });


                    $('#mail-list').append(randomPost());
                    //$('#loading').hide();
                }
            });

            function viewMessage(id) {
                $.ajax({
                    url: "<?php echo e(route("admin.message.get.related.notification")); ?>",
                    type: "post",
                    data: {id: id},
                    dataType: "json",
                    success: function (result) {
                        if (result.status == '1') {
                            $("#rightBarMessage").html(result.data.notification.message);
                            let created_at = new Date(result.data?.notification?.created_at);
                            created_at = moment(created_at).format('YYYY-MM-DD hh:mm A')
                            $("#takeActionBtn").attr('href', result.data.notification.url)
                            $("#rightBarMsgTime").html(created_at);
                            $(".link-icon").html(` <a target="_blank" href="` + result.data.notification.url + `"><i class="ri-external-link-line"></i></a>`)

                        }
                    }
                });
            }

            function nextPage(page) {
                $.ajax({
                    url: "<?php echo e(route("admin.message.notification.paginate")); ?>?page=" + page,
                    type: "get",
                    dataType: "json",
                    success: function (result) {
                        if (result.status == '1') {
                            console.log(result.data.current_page);
                            $("#loadMoreNotifications").data('next', result.data.current_page + 1);
                            let html = '';
                            result.data.data.map(function (item) {

                                html+=` <li data-id="${item.id}" id="notili${item.id}" class="${item.read ==0 ? 'unread' : 'read'}">
                                        <div class="col-mail col-mail-1">
                                            <div class="form-check checkbox-wrapper-mail fs-14">
                                                <input class="form-check-input notificationList" type="checkbox"
                                                       value="${item.id}" id="checkbox-${item.id}">
                                                <label class="form-check-label" for="checkbox-${item.id}"></label>
                                            </div>
                                            <input type="hidden" value="" class="mail-userimg">
                                            <button type="button" class="btn avatar-xs p-0 favourite-btn fs-15 d-none ">
                                                <i class="ri-star-fill"></i></button>
                                            <a href="javascript: void(0);" class="title"><span
                                                    class="title-name">${item?.notification?.title}</span></a></div>
                                        <div class="col-mail col-mail-2">
                                            <a href="javascript: void(0);" class="subject"><span class="subject-title"><!-- subject here --></span>
                                                <span class="teaser">${item?.notification?.message}</span> </a>
                                            <div class="date"> ${moment(item?.created_at).format("MMM D")}</div>
                                        </div>
                                    </li>`;
                            });
                            $("#notification-list").append(html);
                        }
                    }
                });
            }

            $("#loadMoreNotifications").click(function () {
                let page = $(this).data('next');
                nextPage(page);
            });
            $(document).on("click",".read",function () {
                // $(this).removeClass('read');
                // $(this).addClass('unread');
                let mid = $(this).data('id');
                viewMessage(mid);
            });
            $(document).on("click",".unread",function () {
                $(this).removeClass('unread');
                $(this).addClass('read');
                let mid = $(this).data('id');
                viewMessage(mid);
            });
            $("#mark-selected-read").click(function () {
                let notiList = [];
                $('.notificationList:checkbox:checked').each(function () {
                    notiList.push($(this).val());
                });

                $.ajax({
                    url: "<?php echo e(route("admin.message.mark.read")); ?>",
                    type: "post",
                    data: {notification: notiList},
                    dataType: "json",
                    success: function (result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success");
                        } else {
                            showToastify(result.msg, "error");
                        }
                    }
                });
            });
            $("#mark-selected-unread").click(function () {
                let notiList = [];
                $('.notificationList:checkbox:checked').each(function () {
                    notiList.push($(this).val());
                    $("#notili" + $(this).val()).removeClass('active');
                    $("#notili" + $(this).val()).removeClass('read');
                    $("#notili" + $(this).val()).addClass('unread');
                });

                $.ajax({
                    url: "<?php echo e(route("admin.message.mark.unread")); ?>",
                    type: "post",
                    data: {notification: notiList},
                    dataType: "json",
                    success: function (result) {
                        if (result.status == '1') {
                            $('.notificationList:checkbox:checked').each(function () {
                                notiList.push($(this).val());

                            });
                            showToastify(result.msg, "success");
                        } else {
                            showToastify(result.msg, "error");
                        }
                    }
                });
            });
            $("#mark-all-read").click(function () {


                $.ajax({
                    url: "<?php echo e(route("admin.message.mark.all.read")); ?>",
                    type: "post",
                    dataType: "json",
                    success: function (result) {
                        if (result.status == '1') {

                            showToastify(result.msg, "success");
                        } else {
                            showToastify(result.msg, "error");
                        }
                    }
                });
            });

        });

        $("#sidebarNotifications").addClass('collapse show');
        $("#sidebarNotificationInbox").addClass('active');
        $("#sidebarNotiActive").addClass('active');

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/message/inbox.blade.php ENDPATH**/ ?>