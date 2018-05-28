<?php
$memcache_obj = memcache_connect('192.168.0.122', 9901);
memcache_flush($memcache_obj);
$memcache_obj = new Memcache;
$memcache_obj->connect('192.168.0.122', 9901);
echo $memcache_obj->flush();

echo "清除 memcache 完成";
?>