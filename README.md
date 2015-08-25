# apc-stats-json

PHP script to return APCu stats in JSON.

Original version: https://github.com/krakjoe/apcu/blob/simplify/apc.php

## Sample output

```
curl http://localhost/apc_stats_json.php
{
    "general_cache_info": {
        "apcu_version": "4.0.7",
        "php_version": "5.5.28"
    },
    "apcu_host": "localhost",
    "server_software": "Apache/2.4.16 (Amazon) OpenSSL/1.0.1k-fips",
    "shared_memory_stats": {
        "num_seg": 1,
        "set_size": " 32.0 MBytes",
        "memory_type": "mmap"
    },
    "cache_stats": {
        "number_vars": 2,
        "size_vars": " 12.4 MBytes",
        "hits": 6,
        "misses": 1,
        "request_rate": "0.06",
        "hit_rate": "0.05",
        "miss_rate": "0.01",
        "insert_rate": "0.07",
        "num_expunges": 1
    },
    "apcu_runtime_settings": {
        "apc.coredump_unmap": "0",
        "apc.enable_cli": "0",
        "apc.enabled": "1",
        "apc.entries_hint": "4096",
        "apc.gc_ttl": "3600",
        "apc.mmap_file_mask": "/tmp/apc.9INHXh",
        "apc.preload_path": null,
        "apc.rfc1867": "0",
        "apc.rfc1867_freq": "0",
        "apc.rfc1867_name": "APC_UPLOAD_PROGRESS",
        "apc.rfc1867_prefix": "upload_",
        "apc.rfc1867_ttl": "3600",
        "apc.serializer": "php",
        "apc.shm_segments": "1",
        "apc.shm_size": "32M",
        "apc.slam_defense": "1",
        "apc.smart": "0",
        "apc.ttl": "0",
        "apc.use_request_time": "1",
        "apc.writable": "/tmp"
    },
    "memory_usage_stats": {
        "free": " 19.6 MBytes",
        "free_percent": "61.1%",
        "hits": 6,
        "used": " 12.4 MBytes",
        "used_percent": "38.9%",
        "misses": 1
    }
}

```
