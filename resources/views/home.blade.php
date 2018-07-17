@extends('layouts.app')

@section('content')

    <div class="container" ng-controller="chatCtrl" data-page="chat">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <strong>Chat room </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox chat-view">
                        <div class="ibox-title">Chat room panel</div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="chat-users">
                                        <div class="users-list">

                                            <div class="chat-user" ng-repeat="chat in chats">
                                                <span class="label badge badge-secondary">@{{ chat["room_last_time"] }}</span>
                                                <img class="chat-avatar" ng-src="@{{ chat['room_image'] }}" alt="@{{ chat['room_title'] }}">
                                                <div class="chat-user-name">
                                                    <a href="#" ng-click="joinRoom(chat, $event)">@{{ chat["room_title"] }}</a>
                                                    <span>@{{ chat["room_last_message"] }}</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9 ">
                                    <div class="chat-discussion">
                                        <div class="chat-message left" ng-repeat="message in messages">
                                            <img class="message-avatar" ng-src="@{{ message.message_user_avatar }}" alt="@{{ message.message_user }}">
                                            <div class="message">
                                                <a class="message-author" href="#">@{{ message.message_user }}</a>
                                                <span class="message-date">@{{ message.message_date }}</span>
                                                <span class="message-content">@{{ message.message_content }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chat-message-form">
                                        <div class="form-group">
                                            <textarea class="form-control message-input" name="message" placeholder="Enter message text and press enter"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("scripts")
    @parent
    <script>
        window.params = {
            fetch_chats: "{{ route('app.user.list-chat') }}"
        }
    </script>
@endsection
