<?php
/**
 * Created by IntelliJ IDEA.
 * User: lalittanwar
 * Date: 20/09/15
 * Time: 11:54 AM
 */
error_reporting(E_ALL ^ E_NOTICE);
include_once 'bootstrap.php';

\Parichya\Service::init();

if (\Parichya\Service::$PHASE_USER_VERIFICATION) {
	header('Location:'.$config['baseUrl'].'signin.php');
	// Authenticate user, OTP or GMAAIL and if user is valid, generate SessionToken, and set it.
	//     \Parichya\Service::setSessionToken("USER-SESSION-TOKEN-JHGSJGHDGS");

	//TO Send Client Back to Service via broswer;
	//     \Parichya\Service::returnToClient();

} else if (\Parichya\Service::$PHASE_SERVICE_VERIFICATION) {
	\Parichya\Service::verifyService(function ($publicKey, $privateKey, $authToken) {
		//Verify $publicKey, $privateKey, $authToken and if all valid send requested data
// 		R::debug( TRUE );
		$authdata = R::findOne('authtoken',' authtoken = ? AND publickey = ?',array($authToken, $publicKey));
		if(is_null($authdata)){
			return array(
					"success" => false
			);
		} else {
			$privateKeyData = R::findOne('subscriber',' subscriber_privatekey = ? AND subscriber_publickey = ?',
					array($privateKey, $publicKey));
			if(is_null($privateKeyData)){
				return array(
						"success" => false
				);
			} else {
				$user = R::findOne('users','id = ? ',array($authdata->user_id));
				if(is_null($user)){
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

// 		return array(
// 				"success" => true,
// 				"otp:authToken" => $authToken,
// 				"otp:mobileNumber" => "9735866250",
// 				"otp:name" => "avi",	
// 				"otp:email" => "email",
// 		);

	});
}
?>






