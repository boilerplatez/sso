<?php


namespace Parichya {

    class OAuthServer
    {
        public static $PHASE_USER_VERIFICATION = false;
        public static $PHASE_SERVICE_VERIFICATION = false;

        public static function setSessionToken($authToken)
        {
            $_SESSION["otp:authToken"] = $authToken;
        }

        /**
         *  This method is will generate unique authToken for one user valid for one session only.
         * @return array
         */
        private static function generateAuthData()
        {
            return array("otp:authToken" => $_SESSION["otp:authToken"]);
        }

        /**
         *  To return autho data to client, $_SESSION variable might not be avaiable for this method
         *
         * @param $publicKey
         * @param $privateKey
         * @param $authToken
         * @return array
         */
        private static function getAuthData($publicKey, $privateKey, $authToken)
        {
            return array(
                "success" => true,
                "otp:authToken" => $authToken,
                "otp:mobileNumber" => "9930104050"
            );
        }

        public static function returnToClient()
        {
            $returnObj = parse_url($_SESSION["otp:returnUrl"]);
            $REDIRECT_URL = $returnObj["scheme"] . "://" . $returnObj["host"] . $returnObj["path"] . "?"
                . (isset($returnObj["query"]) ? $returnObj["query"] : "") . "&" . http_build_query(self::generateAuthData());
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Location:' . $REDIRECT_URL, true, 307);
        }

        public static function verifyService($callable)
        {
            echo json_encode($callable(
                $_REQUEST["otp:publicKey"],
                $_REQUEST["otp:privateKey"],
                $_REQUEST["otp:authToken"]
            ));
        }

        public static function init()
        {
            if (isset($_REQUEST["otp:privateKey"]) && isset($_REQUEST["otp:authToken"])) {
                self::$PHASE_SERVICE_VERIFICATION = TRUE;
//                echo json_encode(self::getAuthData(
//                    $_REQUEST["otp:publicKey"],
//                    $_REQUEST["otp:privateKey"],
//                    $_REQUEST["otp:authToken"]
//                ));
            } else if (isset($_REQUEST["otp:publicKey"])) {
                self::$PHASE_USER_VERIFICATION = TRUE;
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                } else if(session_id() == '') {
                    session_start();
                }
                $_SESSION["otp:publicKey"] = $_REQUEST["otp:publicKey"];
                $_SESSION["otp:returnUrl"] = $_REQUEST["otp:returnUrl"];
            } else {
                echo json_encode(array(
                    "success" => false
                ));
            }
        }
    }
}