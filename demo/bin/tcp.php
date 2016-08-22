<?php
$serv = new \Swoole\Server("0.0.0.0", 9502);
$serv->set(array(
    'worker_num' => 4,   //工作进程数量
//    'daemonize' => true, //是否作为守护进程
));
$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "HTTP/1.1 200 OK\r\n\r\nHello swoole.");
    $serv->close($fd);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();
