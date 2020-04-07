<?php


namespace Utils;


class RedisKey
{
    //create user lock
    const S_CREATE_USER_LOCK = "s:pxx:lock:user:creating:"; // 用户创建锁
    const S_USER_SESSION_KEY = 's:pxx:session:key:'; // 用户sessionKey

}