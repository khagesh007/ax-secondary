importScripts('https://js.pusher.com/7.0/pusher.worker.min.js');

// An array of the clients/tabs using this worker
var clients = [];
var initialize = false;

// These two variable value will be received on post message from main thread
var pusherKey = null;
function initalizePusherFn(pusherKey) {

    // Connect to Pusher
    var pusher = new Pusher(pusherKey, {
        encrypted: true,
        cluster: 'ap2',
        useTLS : true
    });
    // Log pusher details to console
    Pusher.logToConsole = true
    // Subscribe to the channel we specified in our Laravel Event
    var channel = pusher.subscribe('web-notification');
    const eventList = ['App\\Events\\LeadCreated','App\\Events\\EventCreated',
        'App\\Events\\PublishRequested','App\\Events\\PocketRequest',
        'App\\Events\\ActionOnListingChanges','App\\Events\\UnpublishRequested',
        'App\\Events\\ChangesRequested','App\\Events\\ListingNoteCreated',
        'App\\Events\\ContractExpiring','App\\Events\\ExclusiveExpiring',
        'App\\Events\\ShootingRequestCreated',  'App\\Events\\ShootJobAction','App\\Events\\ShootJobStatusChange'
    ];
    let eventName;
    for( var i = 0; i < eventList.length; ++i ) {
        eventName = eventList[i];
        channel.bind(eventName, function(data) {
            // Relay the message on to each client
            clients.forEach(function(client){
                client.postMessage(data);
            });
        });
    }


    // Bind a function to a Event (the full Laravel class)
    // channel.bind('my-event', function(data) {
    //     // Relay the message on to each client
    //     clients.forEach(function(client){
    //         console.log(data)
    //         client.postMessage(data);
    //     });
    // });

}

self.addEventListener("connect", function(evt){
    // Add the port to the list of connected clients
    var port = evt.ports[0];
    clients.push(port);
    port.addEventListener('message', function(eventM){
        var data = eventM.data;
        pusherKey = data.key;
        // this should only be working once for multiple tabs, else channel and
        // message count will increase for pusher
        if(!initialize)
            initalizePusherFn(pusherKey);
        initialize = true;
    }, false);
    port.start();
});
