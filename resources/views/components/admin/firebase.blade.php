<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-analytics.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-messaging.js"></script>
<script>
    var firebaseConfig = {
        apiKey: "AIzaSyBlp0MEm_5BRHXleR8owSqJmvw_uZmIMzI",
        authDomain: "ewaiter-a25bf.firebaseapp.com",
        databaseURL: "https://ewaiter-a25bf.firebaseio.com",
        projectId: "ewaiter-a25bf",
        storageBucket: "ewaiter-a25bf.appspot.com",
        messagingSenderId: "556392585583",
        appId: "1:556392585583:web:c70660576801ed2b58709a",
        measurementId: "G-PN6H8KW4MP"
    };
    firebase.initializeApp(firebaseConfig);

    const permissionDivId = 'permission_div';
    const messaging = firebase.messaging();
    messaging.usePublicVapidKey("BJUYQmqGakB1iCVkjZhLjKb5Z2hS9z_ThTpSF-ySEKsu_hDt-d3FOtmCj3q99WfhgK-9WrPZb2ykZrN48WGo-5M");

    messaging.onTokenRefresh(() => {
        messaging.getToken().then((refreshedToken) => {
            console.log('Token refreshed.');
            setTokenSentToServer(false);
            sendTokenToServer(refreshedToken);
            resetUI();
        }).catch((err) => {
            console.log('Unable to retrieve refreshed token ', err);
        });
    });


    messaging.onMessage((payload) => {
        console.log('Message received. ', payload);
        appendMessage(payload);
    });

    function resetUI() {
        messaging.getToken().then((currentToken) => {
            if (currentToken) {
                sendTokenToServer(currentToken);
                updateUIForPushEnabled(currentToken);
            } else {
                console.log('{{__('firebase.Notification blocked')}}');
                addAlertFirebase('warning','{{__('firebase.Notification blocked')}}');
                updateUIForPushPermissionRequired();
                setTokenSentToServer(false);
            }
        }).catch((err) => {
            updateUIForPushPermissionRequired();
            console.log('An error occurred while retrieving token. ', err);
            setTokenSentToServer(false);
        });
    }

    function sendTokenToServer(token) {
        $.ajax({
            type: "POST",
            url: "{{ route('firebase.store') }}",
            data: {
                _token: '{{ csrf_token() }}',
                token: token
            },
            dataType: 'JSON',
            success: function(response) {
                if(response.status != 'success') {
                    addAlertFirebase('warning',response.komunikat);
                }
            },
            error: function (data) {
                addAlertFirebase('warning','{{__('firebase.Notification blocked')}}');
            }
        }).always(function() {
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },0);
        });

        if (!isTokenSentToServer()) {
            console.log('Sending token to server...');
            setTokenSentToServer(true);
        } else {
            console.log('Token already sent to server so won\'t send it again ' +
                'unless it changes');
        }

    }

    function isTokenSentToServer() {
        return window.localStorage.getItem('sentToServer') === '1';
    }

    function setTokenSentToServer(sent) {
        window.localStorage.setItem('sentToServer', sent ? '1' : '0');
    }

    function showHideDiv(divId, show) {
        const div = document.querySelector('#' + divId);
        if(div) {
            div.style = show ? 'display: visible' : 'display: none'
        };
    }

    function requestPermission() {
        console.log('Requesting permission...');
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                console.log('{{__('firebase.Permission granted')}}');
                addAlertFirebase('success','{{__('firebase.Permission granted')}}');
                resetUI();
            } else {
                addAlertFirebase('warning','{{__('firebase.Notification blocked')}}');
                console.log('{{__('firebase.Notification blocked')}}');
            }
        });
    }

    function appendMessage(payload) {
//        var notify = new Notification(payload.notification.title, {
//            body: payload.notification.body,
//            icon: 'https://panel.wirtualnykelner.pl/images/logo_w.png',
//            data: payload.data.url
//        });
//
//        notify.onclick = function(e) {
//            window.location.href = e.target.data;
//        }
        var type = payload.data.type;
        if(payload.data.url)
            addAlertFirebase('success','<h3>'+payload.notification.title+'</h3><p>'+payload.notification.body+'</p><p><a href="'+payload.data.url+'">{{__('firebase.Go to')}}</a></p>',type);
        else
            addAlertFirebase('success','<h3>'+payload.notification.title+'</h3><p>'+payload.notification.body+'</p>',type);

    }

    function updateUIForPushEnabled(currentToken) {
        showHideDiv(permissionDivId, false);
    }

    function updateUIForPushPermissionRequired() {
        showHideDiv(permissionDivId, true);
    }

    function addAlertFirebase(type,message,notification_type){
        var alert_firebase = '<div class="alert alert-'+type+'" onclick="this.classList.add(\'hidden\');"> \
            <button type="button" class="close" data-dismiss="alert" aria-label="{{__('admin.Close')}}"> \
                <span aria-hidden="true">&times;</span> \
            </button> \
            <p>'+message+'</p> \
        </div>';
        $('.flash-message .col-md-12').append(alert_firebase);

        if(notification_type && $.inArray( notification_type, [ "reservation" ] )  !== -1 ) {
            $('#notificationModal').modal('show');
            $('#notificationModal audio').each(function(){
                this.play();
            });
            $('#notificationModal').on('hidden.bs.modal', function () {
                $('#notificationModal audio').each(function(){
                    this.pause();
                    this.currentTime = 0;
                });
            });
        }

        setTimeout(
            function()
            {
                reloadNotifications();
            }, 5000);
    }

    resetUI();

</script>
