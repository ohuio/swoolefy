<?php
/**
+----------------------------------------------------------------------
| swoolefy framework bases on swoole extension development, we can use it easily!
+----------------------------------------------------------------------
| Licensed ( https://opensource.org/licenses/MIT )
+----------------------------------------------------------------------
| Author: bingcool <bingcoolhuang@gmail.com || 2437667702@qq.com>
+----------------------------------------------------------------------
*/

// udpserver协议层配置
return [
    'app_conf' => [], // 应用层配置，需要根据实际项目导入
	'application_service' => 'Service\\Application',
	'start_init' => \Swoolefy\Core\StartInit::class,
	'master_process_name' => 'php-udp-master',
	'manager_process_name' => 'php-udp-manager',
	'worker_process_name' => 'php-udp-worker',
	'www_user' => 'www',
	'host' => '0.0.0.0',
	'port' => '9505',
	'time_zone' => 'PRC',
    'runtime_enable_coroutine' => true,
	'setting' => [
		'reactor_num' => 1,
		'worker_num' => 3,
		'max_request' => 1000,
		'task_worker_num' => 2,
		'task_tmpdir' => '/dev/shm',
		'daemonize' => 0,
		'dispatch_mode' => 3,

        'enable_coroutine' => 1,
        'task_enable_coroutine' => 1,

		'log_file' => __DIR__.'/log/log.txt',
		'pid_file' => __DIR__.'/log/server.pid',

	],
	'enable_table_tick_task' => true,
];