<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Chat</title>

        <!-- Bootstrap core CSS -->
        <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="http://getbootstrap.com/examples/signin/signin.css" rel="stylesheet">
        <style>
            .red {
                font-weight: bold;
                color: red;
            }
        </style>
        <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
        <!--[if lt IE 9]><script src="http://getbootstrap.com/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
        <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>
        <script src="//cdn.bootcss.com/jquery/1.8.1/jquery.min.js"></script>
        <script src="/reconnecting-websocket.min.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            span {
                font-size: 16px;
                color: red;
                font-weight: bold;
                padding: 5px 10px !important;
            }
        </style>
    </head>

    <body>

        <div class="container">

            <form>
                <input type="hidden" id="at" value="">
                <h2 class="form-signin-heading">闲聊瞎扯技术群</h2>
                <div id="chat" class="page-header" style="overflow: auto; height: 400px; width: 100%;">

                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon3">昵称</span>
                        <input type="text" class="form-control" id="username" aria-describedby="basic-addon3">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="messageAt">发言</span>
                        <input type="text" id="message" class="form-control" placeholder="说点什么吧！" autocomplete="off" required>
                    </div>
                </div>
                <div id="show"></div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Send</button>
            </form>

        </div> <!-- /container -->
        <script type="text/javascript">
            function at(userId, userName) {
                $('#at').val(userId);
                $('#messageAt').html('@' + userName + '<a href="javascript:cldAt();">[撤销]</a>');
            }
            function cldAt() {
                $('#at').val(null);
                $('#messageAt').text('发言');
            }
            $(document).ready(function () {
                var My = 0;
                // The WebSocket-Object (with resource + fallback)
                var ws = new ReconnectingWebSocket('ws://43.251.159.15:89', null, {debug: true, reconnectInterval: 3000});
                ws.onopen = function (evt) {
                    console.log(evt);
                };
                ws.onclose = function (evt) {
                    console.log('< Uoke - Chat error : ' + evt.data);
                };
                ws.onmessage = function (evt) {
                    var userMessage = evt.data.split("<r>");
                    console.log(My + '--' + userMessage[0] + '++' + userMessage[1]);
                    if (userMessage[0] == 'system' && undefined == userMessage[2]) {
                        $('#chat').append('<div class="alert alert-success" role="alert">' + userMessage[1] + ' 已经@你了！</div>');
                        return;
                    }
                    var urlRegExp = /^((https|http|ftp|rtsp|mms)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
                    var url = userMessage[2].split(" ");
                    if (urlRegExp.test(url[0])) {
                        $.getJSON('http://cdn-jp.acghx.net/HttpDo/', {http: url[0]}, function (e) {
                            if (e.title == '') {
                                e.title = '这个网站抬头被吃掉了！';
                            }
                            $('#chat').append('<div class="panel panel-default"><div class="panel-body">' + userMessage[0] + ' 分享网站：' + e.title + '</div><div class="panel-footer"><a target="_blank" href="' + e.url + '">点此访问网站（存在风险）</a></div></div>');
                            $('#chat').scrollTop($('#chat')[0].scrollHeight);
                        }, 'json');
                    } else {
                        if (userMessage[0] == 'system') {
                            My = userMessage[1];
                            $('#chat').append('<div class="well well-sm">' + userMessage[0] + ': ' + userMessage[2] + '</div>');
                        } else {
                            var m = '';
                            if (undefined !== userMessage[3]) {
                                m = '@' + userMessage[3] + '';
                            }
                            $('#chat').append('<div class="well well-sm"><a href="javascript:at(' + "'" + userMessage[1] + "'" + ', ' + "'" + userMessage[0] + "'" + ');"><strong>@' + userMessage[0] + '</strong></a> :   ' + m + '  ' + userMessage[2] + '</div>');
                        }
                        $('#chat').scrollTop($('#chat')[0].scrollHeight);
                    }

                    console.log('< Uoke - Chat : ' + evt.data);
                };
                ws.onerror = function (evt) {
                    console.log(evt);
                };
                ws.onconnecting = function (evt) {
                    My = 0;
                    $('#chat').append('<p>系统正在努力恢复记录！</p>');
                    $('#chat').scrollTop($('#chat')[0].scrollHeight);
                }
                $('form').submit(function (e) {
                    e.preventDefault();
                    var Chat = {
                        'user': $('#username').val(),
                        'message': $('#message').val(),
                        'at': $('#at').val()
                    };
                    ws.send(JSON.stringify(Chat));
                    $('#message').val(null);
                });
            });
        </script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>

