<?php
/**
 * @author yuyi
 * @created 2016/7/30 20:00
 * @email 297341015@qq.com
 */

/*function com_find_process( $keyword ){
    $obj       = new COM ( 'winmgmts://localhost/root/CIMV2' );
    $processes = $obj->ExecQuery("Select * from Win32_Process Where Name=\"php.exe\" ");

    foreach ($processes as $process) {
        if ( strpos($process->CommandLine, $keyword) !== false ) {
             return true;
        }
    }
    return false;
}*/



wing_set_env("data","这句话将被传到子进程");
$command = WING_PHP." ".__DIR__."/wing_create_process_runer.php";
//$process_id = wing_create_process( $command, "这句话也会被传到子进程");
$process_id = wing_create_process_ex(__DIR__."/wing_create_process_runer.php","这句话将被传到子进程");

echo "进程id:",$process_id,"\r\n";


//wing_process_kill( $process_id ); //可以是用这个api杀死正在运行的进程



/*if( com_find_process( $command ) ) { //为了测试这段代码 可以去掉 wing_process_kill的注释
    //建议使用这种方式判断进程是否正在运行 可靠
    echo $process_id,"正在运行\r\n";
}else{
    echo $process_id,"未运行\r\n";
}*/


if( WING_ERROR_PROCESS_IS_RUNNING == wing_process_isalive( $process_id ) ) {
    //wing_process_isalive 不要太依赖此api，因为进程id的复用性，进程退出后，有可能相同的进程id立马被创建
    echo $process_id,"正在运行\r\n";
}



$wait_code = wing_process_wait( $process_id, WING_INFINITE ); //永不超时等待子进程退出 第二个参数为可选参数 默认为WING_INFINITE
switch( $wait_code ) {
    case WING_ERROR_FAILED :
            echo "等待失败\r\n";
        break;

    case WING_WAIT_ABANDONED :
            echo '没有释放mutex（当hHandle为mutex时，如果拥有mutex的线程在结束时没有释放核心对象会引发此返回值。）\r\n';
        break;

    case WING_WAIT_TIMEOUT://仅当wing_process_wait第二个参数不为 WING_INFINITE有效
        echo "等待超时\r\n";
        break;
    
    default:
        echo "进程退出码：",$wait_code,"\r\n"; //在子进程调用exit时传入的参数
}

