<?php
namespace Exceptions;

use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
//use MongoDB\Driver\Exception\InvalidArgumentException;
use Phalcon\Di;
use Phalcon\Http\Response;
use Respect\Validation\Exceptions\NestedValidationException;
use Utils\ErrorCode;
use Utils\Reporter;
use Utils\Log;

class Handler
{
    protected $dontReport = [
        NestedValidationException::class
    ];//不需要报告的

    private $dontAlert = [

    ];//不需要警惕的

    public function shouldReport(\Throwable $e){
        foreach ($this->dontReport as $type){
            if ($e instanceof $type){
                return false;
            }
        }
        return true;
    }

    public function shouldAlert(\Throwable $e){
        foreach ($this->dontAlert as $type) {
            if ($e instanceof $type){
                return false;
            }
        }
        return true;
    }

    public function handle(\Throwable $e){
        $request = request();

        $message = $e . "\nRequest:\nAction: " . $request->getMethod() . " " . $request->getUri() . "\nQuery: " . $request->getUri()->getQuery() . "\nParameters: " . json_encode($request->getParsedBody());
        //登记需要登记的错误=>为验证错误异常
        if ($this->shouldReport($e)){
            Log::error($message);
            $this->report($e);
        }
        //非验证错误异常处理
        return $this->render($e);
    }

    public function report(\Throwable $e){
        $errArr = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ];

        if (!$this->shouldAlert($e)){
            return;
        }

        //取http头中的第一个Refer，解析其中的appId
        $request = request();
        $referer = $request->getHeader('Referer');
        $appId = '';

        if($referer){
            $referer = $referer[0];
            preg_match("/^https:\/\/servicewechat\.com\/([^\/]*)\/([^\/]*)\/(page-frame\.html)?/", $referer, $refer_args);
            if (count($refer_args)) {
                $appId = $refer_args[1];
            }
        }
        //环境控制：检测环境是否为正式服(做报警处理)
        $config = Di::getDefault()->getConfig();
        $env = $config->application->env;

        if ($env != 'prod'){
            return;
        }
        // 报警处理

    }

    public function responseJson($data){
        $response = new Response();
        $response->setHeader('Content-type','application/json');
        if (!is_string($data)){
            $data = json_encode($data , JSON_UNESCAPED_UNICODE);
        }
        $response->setContent($data);

        return $response;
    }

    public function packResult($errCode,$msg,$data = []){
        return $this->responseJson(compact('errCode','msg','data'));
    }

    public function render(\Throwable $e){
        if ($e instanceof NestedValidationException) { // 检测参数错误类型
            return $this->packResult(ErrorCode::$wrongParam, 'Validation Failed', $e->getMessages());
        }else if($e instanceof LoginException){
            return $this->packResult(ErrorCode::$loginError,$e->getMessage());
        }else if($e instanceof TokenServiceException){
            return $this->packResult(ErrorCode::$tokenServiceError,"tokenservice: ".$e->getMessage());
        }else if($e instanceof UserNotFoundException){
            return $this->packResult(ErrorCode::$userNotFound,"请重新login");
        }else if($e instanceof SetUserPlatformException){
            return $this->packResult(ErrorCode::$setUserInfoError,$e->getMessage());
        }else if($e instanceof CreateUidException){
            return $this->packResult(ErrorCode::$createUid,"snowflake: ".$e->getMessage());
        }else if($e instanceof CreateUserException){
            return $this->packResult(ErrorCode::$createUserError,$e->getMessage());
//        }else if($e instanceof InvalidArgumentException){
//            return $this->packResult(ErrorCode::$wrongFormat,"album_id format invalid");
        }else if($e instanceof SessionKeyOverException){
            return $this->packResult(ErrorCode::$sessionExpired,"sessionKey over");
        }

        $config = Di::getDefault()->getConfig();

        if ($config->application->debug) {
            echo $e->getFile().PHP_EOL;
            echo $e->getLine().PHP_EOL;
            echo $e->getMessage() . '<br>';
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            exit;
        }


        return $this->packResult(ErrorCode::$unknownError, "Internal Server Error");
    }
}