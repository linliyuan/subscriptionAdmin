<?php

namespace Utils;

class ErrorCode {
    public static $wrongParam        = 40001;
    public static $wrongFormat       = 40002;
    public static $accessDenied      = 40003;
    public static $sessionExpired    = 40004;
    public static $wrongsProtoCode   = 40005;
    public static $unknownError      = 40006;
    public static $thirdApiError     = 40007;
    public static $invalidRequest    = 40008;
    public static $tokenServiceError = 40009;
    public static $userNotFound      = 40010;
    public static $loginError        = 40011;
    public static $setUserInfoError  = 40012;
    public static $createUid         = 40013;
    public static $createUserError   = 40014;


    //public static $someBizCode   = 40101;

    public static $IllegalAesKey     = 41001;
    public static $IllegalIv         = 41002;
    public static $IllegalBuffer     = 41003;
    public static $DecodeBase64Error = 41004;

    private function __construct() {
    }
}