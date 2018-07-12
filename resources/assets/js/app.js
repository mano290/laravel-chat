
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// init angular module
let app = angular.module("laravel_chat", []);

// Controller chat
app.controller("chatCtrl", ["$scope", "$http", ($scope, $http) => {

    $scope.messages = [];
    $scope.model_message = "";

    // Init page
    angular.element(document).ready(() => {

        // fetch messages user
        $scope.fetchMessages();

        //  Events Chat
        window.Echo.private('chat').listen('MessageSent', (e) => {
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
                user: window.params.user
            });
            // Sends user message
            $http.post($this.attr("action"), {
                message: message
            });
        }
    };

}]);