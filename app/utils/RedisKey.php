<?php


namespace Utils;


class RedisKey
{
    //create user lock
    const S_CREATE_USER_LOCK = "s:sb:lock:user:creating:"; // 用户创建锁
    const S_USER_SESSION_KEY = 's:sb:session:key:'; // 用户sessionKey

    const Z_MSG_CONFIRM = "z:sb:msg:confirm"; // 确认消息
    const H_MSG_DETAIL = "h:sb:msg:detail"; // 消息详情信息

}