@extends('layouts.app')

@section('content')
    <div class="container" ng-controller="chatCtrl">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Chats</div>
                    <div class="panel-body">
                        <ul class="chat">
                            <li class="left clearfix" ng-repeat="message in messages">
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">@{{ message.user.name }}</strong>
                                    </div>
                                    <p>@{{ message.message }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-footer">
                        <form action="{{ route("chat.post.new-message") }}" ng-submit="sendMessage($event)">
                            <div class="input-group">
                                <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." ng-model="model_message">
                                <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm">Send</button>
                            </span>
                            </div>
                        </form>
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
            fetch_messages: "{{ route("chat.get.fetch-messages") }}",
            user: {name: "{{ auth()->user()->name }}"}
        }
    </script>
@endsection