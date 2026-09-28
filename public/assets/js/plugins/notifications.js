
// browser notification setting start
if (window.Notification) {

    if (Push.Permission.has()) {

    } else {
        $("#notification-request-tab").removeClass('d-none');
    }
} else {
    console.log('Notifications aren\'t supported on your browser! :(');
}
///notification start
const requestButton = document.querySelector("#permission-request-button");
const showButton = document.querySelector("#notification-show-button");

function onGranted() {
    showToastify(`Congratulations you have enabled browser notification!!.`, "success")
    requestButton.style.background = "green";
}

function onDenied() {
    showToastify(`You have decided to block browser notification. You will need to enable it in future if you need it.`, "error")
    requestButton.style.background = "red";
}

if(requestButton) {
    requestButton.onclick = function () {
        Push.Permission.request(onGranted, onDenied);
    }
}
if(showButton) {
    showButton.onclick = function () {
        Push.create("Hello!!" + globalUser['name'] + " !!", {
            body: "This is a web notification!",
            icon: iconPath,
            timeout: 5000,
            link: 'http://google.com',
            onClick: function () {
                // console.log('do action');
            }
        });
    };
}
// browser notification setting end
// top bar notification toggle list refresh
function getNotifications() {
    $.ajax({
        url: refreshNotificationUrl,
        type: "get",
        success: function (result) {
            if (result.status == '1') {
                let html_data = '';
                $.each(result.data, (index, item) => {
                    let timeInt = moment(item.notification.created_at).fromNow()
                    html_data += ` <div  onclick="notificationView('${item.id}','${item.notification.url}')" id="noti${item.id}"  class="text-reset notification-item d-block dropdown-item position-relative noti-item active">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title bg-soft-info text-info rounded-circle fs-16">
                                                  <i class="mdi mdi-bell-ring-outline"></i>
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <a data-id="${item.id}" data-url="${item.notification.url}" href="javascript:void(0)"  class="noti-item stretched-link">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">${item.notification.title}</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">${item.notification.message}.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> ${timeInt}</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                   <a href="${item.notification.url}" target="_blank"> <span class="ri-external-link-line" style="font-size: 20px;color: #0874d6;"></span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                });
                $("#notificationTopBarList").html(html_data);
                $("#topNotiBadge").html(result.count);

            }
        }
    });
}
function notificationView(id,url){
    $.ajax({
        url: refreshNotificationReadUrl,
        type: "POST",
        data: {
            id: id,
        },
        success: function (result) {
            $("#noti"+id).remove();
            window.location.href = url;
        },

    })
}

// pusher notification start
// Enable pusher logging - don't include this in production
// Pusher.logToConsole = false;
//
// var pusher = new Pusher(pusherKey, {
//     encrypted: true,
//     cluster: 'ap2',
//     authEndpoint: '/broadcasting/auth',
//     auth: {
//         headers: {
//             'X-CSRF-TOKEN': '{{ csrf_token() }}',
//         }
//     }
// });
const playNotification = () => {
    let src = notificationSoundFile;
    let audio = new Audio(src);
    audio.play();
}

// let channel = pusher.subscribe('web-notification');
let pusherCallback = function(data) {

    let subscriber = data.subscriber;
    if(Array.from(subscriber).indexOf(parseInt(globalUser['id'])) > -1){

        Toastify({
            text: data?.notification?.message,
            duration: 5000,
            destination: data?.notification?.url,
            newWindow: true,
            close: true,
            gravity: "top", // `top` or `bottom`
            position: "right", // `left`, `center` or `right`
            stopOnFocus: true, // Prevents dismissing of toast on hover
            className:"info-toastify",
            onClick: function(){} // Callback after click
        }).showToast();
        getNotifications();
        playNotification();
        if(Push.Permission.has()) {
            Push.create("Hello "+globalUser['name']+" !!", {
                body: data?.notification?.title,
                timeout: 5000,
                icon: iconPath
            });
        }
    }
};
// const eventList = ['App\\Events\\LeadCreated','App\\Events\\EventCreated',
//     'App\\Events\\PublishRequested','App\\Events\\PocketRequest',
//     'App\\Events\\ActionOnListingChanges','App\\Events\\UnpublishRequested',
//     'App\\Events\\ChangesRequested','App\\Events\\ListingNoteCreated',
//     'App\\Events\\ContractExpiring','App\\Events\\ExclusiveExpiring',
//     'App\\Events\\ShootingRequestCreated',  'App\\Events\\ShootJobAction','App\\Events\\ShootJobStatusChange'
// ];
// let eventName;
// for( var i = 0; i < eventList.length; ++i ) {
//     eventName = eventList[i];
//     channel.bind( eventName, callback );
// }

if (typeof(window.SharedWorker) === 'undefined') {
    throw("Your browser does not support SharedWorkers")
} else {
    console.log("Your browser supports SharedWorkers")
    // Start our new worker
    var worker = new SharedWorker(pusherWorker);
    worker.port.onmessage = function(evt){
        pusherCallback(evt.data);
    };
    // If we get an error, log it out and close the worker.
    worker.onerror = function(err){
        console.log(err.message);
        worker.port.close();
    }

    // Start the worker.
    worker.port.start();
    worker.port.postMessage({key : pusherKey});
}
// pusher notification end
