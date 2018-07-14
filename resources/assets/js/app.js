/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// init angular module
let app = angular.module("laravel_chat", []);

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
        if (! _isArgsValid(title, options) || _isPageVisible(options.force) || currentPermission() !== PERMISSIONS.GRANTED) return;

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
     * @param callback
     */
    function listenChatEvents(callback) {

        // Request notification permission
        if(isPageChat()) $notification.requestPermission();

        //  Events Chat
        window.Echo.private('chat').listen('MessageSent', (e) => {

            // For multiple tabs open
            if(window.user.uid !== e.user.uid) {

                // Browser notification
                $notification.show(`New message from ${e.user.name}`, {
                    body: e.message.message,
                    icon: "https://i.imgur.com/2PKFLc5.png",
                    force: (! isPageChat()),
                    tag: e.message.id,
                    onClick: () => {
                        window.open(e.url, '_blank');
                    }
                });
            }

            // Callback when receive event
            if(angular.isFunction(callback)) callback(e);
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
    $scope.model_message = "";

    // Init page
    angular.element(document).ready(() => {

        // fetch messages user
        $scope.fetchMessages();

        // Listen events chat
        $chat.listenChatEvents((e) => {

            // Push message
            $scope.messages.push({
                message: e.message.message,
                user: e.user
            });

            $scope.$apply();
        });
    });

    /**
     * Fetch user messages
     */
    $scope.fetchMessages = () => {
        $http.get(window.params["fetch_messages"]).then((response) => {
            $scope.messages = response.data;
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
                message: message,
                user: window.user
            });
            // Sends user message
            $http.post($this.attr("action"), {
                message: message
            });
        }
    };

}]);