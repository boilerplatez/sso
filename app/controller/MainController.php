<?php

namespace app\controller {

    use \RedBeanPHP\R;

    class MainController extends AbstractController
    {

        public function setupDB()
        {
            $config = \Config::getSection("DB1");
            R::setup("mysql:host={$config['host']};dbname={$config['dbname']}", "{$config['username']}", "{$config['password']}");
        }

        /**
         * @RequestMapping(url="sso",type="json")
         * @RequestParams(true)
         */
        public function auth($command = null)
        {
            //header('Content-type: application/json; charset=UTF-8');
            $ssoServer = new \app\service\MySSOServer();
            if (!$command || !method_exists($ssoServer, $command)) {
                header("HTTP/1.1 404 Not Found");
                header('Content-type: application/json; charset=UTF-8');

                echo json_encode(['error' => 'Unknown command']);
                exit();
            }
            return $ssoServer->$command();
        }

        /**
         * @RequestMapping(url="auth/{command}",type="json")
         * @RequestParams(true)
         */
        public function auth2($command = null)
        {
            return $this->auth($command);
        }

        /**
         * @RequestMapping(url="oauth/login",method="GET",type="template")
         * @RequestParams(true)
         */
        public function oauth($model)
        {

            $this::setupDB();
            \Parichya\OAuthServer::init();
            if (\Parichya\OAuthServer::$PHASE_USER_VERIFICATION) {
                return "login";
            }
            return "401";
        }

        /**
         * @RequestMapping(url="oauth/login",method="POST",type="template")
         * @RequestParams(true)
         */
        public function oauthSubmit($model,$user,$username,$password,$remember)
        {

            if($user->auth($username,$password)){
                return \Parichya\OAuthServer::returnToClient();
            };
            return "login";
        }


        /**
         * @RequestMapping(url="oauth/getdata",method="POST",type="json")
         * @RequestParams(true)
         */
        public function oauthGetData($model)
        {
            $this::setupDB();

            \Parichya\OAuthServer::init();

            if (\Parichya\OAuthServer::$PHASE_SERVICE_VERIFICATION) {
                \Parichya\Service::verifyService(function ($publicKey, $privateKey, $authToken) {
                    //Verify $publicKey, $privateKey, $authToken and if all valid send requested data
                    //R::debug( TRUE );
                    $authdata = R::findOne('authtoken', ' authtoken = ? AND publickey = ?', array($authToken, $publicKey));
                    if (is_null($authdata)) {
                        return array(
                            "success" => false
                        );
                    } else {
                        $privateKeyData = R::findOne('subscriber', ' subscriber_privatekey = ? AND subscriber_publickey = ?',
                            array($privateKey, $publicKey));
                        if (is_null($privateKeyData)) {
                            return array(
                                "success" => false
                            );
                        } else {
                            $user = R::findOne('users', 'id = ? ', array($authdata->user_id));
                            if (is_null($user)) {
                                return false;
                            } else {
                                return array(
                                    "success" => true,
                                    "otp:authToken" => $authToken,
                                    "otp:mobileNumber" => $user->phone,
                                    "otp:name" => $user->name,
                                    "otp:email" => $user->email,
                                );
                            }
                        }
                    }
                });
            }
            return "login";
        }

        /**
         * @RequestMapping(url="",method="GET",type="template",auth=true)
         * @RequestParams(true)
         * @Role(USER)
         */
        public function index()
        {

        }

    }

}

