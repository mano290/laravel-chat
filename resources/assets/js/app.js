/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// init angular module
let app = angular.module("laravel_chat", []);

/**
 * Functions Helpers
 * @type {{dateNow: function()}}
 */
let Helpers = {
    dateNow: () => {
        let date = new Date();
        return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
    }
};

/**
 * Local Storage
 * @type {{db: Storage, select: Function, insert: Function}}
 */
let Storage = {
    db: window.localStorage,
    select: function (chave) {
        let valor = Storage.db.getItem(chave);
        return JSON.parse(valor);
    },
    insert: function (chave, valor) {
        let jsonString = JSON.stringify(valor);
        Storage.db.setItem(chave, jsonString);
    }
};

// Notification factory
app.factory("$notification", ["$q", "$timeout", ($q, $timeout) => {

    // API browser notification
    let Notification = window.Notification || window.mozNotification || window.webkitNotification;

    // List permission
    const PERMISSIONS = { DEFAULT: 'default', GRANTED: 'granted', DENIED: 'denied' };

    // Settings notification
    const SETTINGS = { autoClose: true, duration: 15, force: false };

    /**
     * Returns if notification is supported
     * @returns {boolean}
     */
    function isSupported() {
        if(typeof Notification === "undefined") {
            console.error("Notification API not supported");
            return false
        }
        return true;
    }

    /**
     * Returns if authorized
     * @returns {*}
     */
    function currentPermission() {
        if (! isSupported()) return PERMISSIONS.DENIED;
        return Notification.permission;
    }

    /**
     * Request permission to user
     * @returns {jQuery.promise|promise|*|Promise|PromiseLike<any>}
     */
    function requestPermission() {

        // If not support
        if (! isSupported()) $q.reject('Notification API not supported');

        // Promisse
        let deferred = $q.defer();

        // Request notification permission
        Notification.requestPermission().then((permission) => {
            if (permission === PERMISSIONS.GRANTED) {
                deferred.resolve(permission);
            } else {
                deferred.reject(permission);
            }
        });

        return deferred.promise;
    }

    /**
     * Show notification
     * @param title
     * @param options
     * @returns {*}
     */
    function show(title, options) {

        // Ensures that options is always an object
        options = options || {};

        // Merge options
        angular.extend(SETTINGS, options);

        // Check first if supported, validate arguments, then check if
        // notification is disabled by the client
        if (! _isArgsValid(title, options)|| _isPageVisible(options.force) || currentPermission() !== PERMISSIONS.GRANTED) return;

        let notification = new Notification(title, options);
        let autoClose = (options.autoClose === undefined) ? SETTINGS.autoClose : options.autoClose;
        let duration = options.duration || SETTINGS.duration;

        // Event click on notification
        notification.onclick = options.onClick;

        // If autoClose is set to true, close the notification using the duration
        if (autoClose) _autoCloseAfter(notification, duration);

        return notification;
    }

    /**
     * Valid function show arguments
     * @param title
     * @param options
     * @returns {boolean}
     * @private
     */
    function _isArgsValid(title, options) {

        // title notification
        if(! angular.isString(title)) {
            console.error("notification title is required");
            return false;
        }

        // function onclick notification
        if(typeof options.onClick === "undefined") {
            console.error("option.onClick is required");
            return false;
        }

        // valid function onclick
        if(! angular.isFunction(options.onClick)) {
            console.error("option.onClick is not a function");
            return false;
        }

        // body notification
        if(typeof options.body === "undefined") {
            console.error("option.body is required");
            return false;
        }

        // icon notification
        if(typeof options.icon === "undefined") {
            console.error("option.icon is required");
            return false;
        }

        return true;
    }

    /**
     * Check if page is visible
     * @param force
     * @returns {boolean}
     * @private
     */
    function _isPageVisible(force) {
        return ! (
            window.document.hidden ||
            window.document.mozHidden ||
            window.document.webkitHidden ||
            force
        );
    }

    /**
     * Auto close notification
     * @param notification
     * @param duration
     * @private
     */
    function _autoCloseAfter(notification, duration) {
        let durationInMs = duration * 1000;
        $timeout(notification.close.bind(notification), durationInMs, false);
    }

    // Public Methods
    return {
        isSupported: isSupported,
        currentPermission: currentPermission,
        requestPermission: requestPermission,
        show: show
    };
}]);

// Events chat
app.factory("$chat", ["$notification", ($notification) => {

    // Current page when chat page
    let $chat_page = document.querySelector("[data-page='chat']");

    /**
     * Listen events chat
     * @param eventName
     * @param callback
     */
    function listenChatEvents(eventName, callback) {

        // Request notification permission
        if(isPageChat()) $notification.requestPermission();

        //  Events Chat
        window.Echo.private(eventName).listen('NewMessageChat', (e) => {

            // For multiple tabs open
            if(window.user.uid !== e.data["user_uid"]) {

                // Browser notification
                $notification.show(`New message from ${e.data["message_user"]}`, {
                    body: e.data["message_content"],
                    icon: e.data["message_user_avatar"],
                    force: (! isPageChat()),
                    tag: e.data["message_uid"],
                    onClick: (event) => {
                        event.currentTarget.close();
                        window.focus();
                    }
                });
            }

            // Callback when receive event
            if(angular.isFunction(callback)) callback(e.data);
        });
    }

    /**
     * Returns if is page char
     * @returns {boolean}
     */
    function isPageChat() {
        return ($chat_page != null);
    }

    return {
        listenChatEvents: listenChatEvents,
        isPageChat: isPageChat
    }
}]);

// Set Socket ID in request and chat events
app.run(($http, $chat, $timeout) => {

    // Document ready
    angular.element(document).ready(() => {

        // Set header Socket ID
        $timeout(() => $http.defaults.headers.common["X-Socket-ID"] = Echo.socketId(), 500);

        // Notifications chat when not page chat
        if(! $chat.isPageChat()) $chat.listenChatEvents();
    });
});

// Controller chat
app.controller("chatCtrl", ["$scope", "$http", "$chat", ($scope, $http, $chat) => {

    $scope.messages = [];
    $scope.current_chat = [];
    $scope.model_message = "";
    $scope.chats = [];
    $scope.subscribe_events = false;

    // Init page
    angular.element(document).ready(() => {

        // fetch messages user
        $scope.fetchChats();
    });

    /**
     * Fetch user messages
     */
    $scope.fetchChats = () => {
        $http.get(window.params["fetch_chats"]).then((response) => {
            $scope.chats = response.data;

            // event subscribe
            if($scope.subscribe_events === false) {
                let chats_count = $scope.chats.length;
                for(let i = 0; i < chats_count; i++) {
                    // Listen events chat
                    $chat.listenChatEvents($scope.chats[i]["room_event"], (e) => {

                        // If chat is open in event room
                        if($scope.current_chat.room_uid === e["message_room_uid"]) {

                            // push message
                            $scope.messages.push({
                                message_user_avatar: e.message_user_avatar,
                                message_user: e.message_user,
                                message_date: e.message_date,
                                message_content: e.message_content,
                            });

                            $scope.scrollRoomChat(100);
                        }

                        $scope.fetchChats();
                    });
                }

                $scope.subscribe_events = true;
            }
        });
    };

    /**
     *
     * @param chat
     * @param event
     */
    $scope.joinRoom = (chat, event) => {
        event.preventDefault();
        $http.get(chat["room_messages"]).then((response) => {
            $scope.messages = response.data;
            $scope.scrollRoomChat();
            $scope.current_chat = chat;
        });
    };

    /**
     * Send message user
     * @param event
     */
    $scope.sendMessage = (event) => {
        event.preventDefault();
        let $this = angular.element(event.currentTarget);
        if($scope.model_message.length > 0) {
            let message = $scope.model_message;
            $scope.model_message = "";
            // Push message
            $scope.messages.push({
                message_user_avatar: window.user.avatar,
                message_user: window.user.name,
                message_date: Helpers.dateNow(),
                message_content: message,
            });
            // Scroll chat
            $scope.scrollRoomChat();
            // Sends user message
            $http.post($this.attr("action"), {
                room_uid: $scope.current_chat.room_uid,
                message: message
            }).then(() => {
                // fetch messages user
                $scope.fetchChats();
            });
        }
    };

    /**
     * Scroll bottom chat box
     */
    $scope.scrollRoomChat = (time = 0) => {
        setTimeout(() => {
            let scroller = document.querySelector(".chat-discussion");
            scroller.scrollTop = scroller.scrollHeight;
        }, time, false);
    }

}]);