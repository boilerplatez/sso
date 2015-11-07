<?php

namespace app\model {

    /**
     * Class User
     * @Model("sessionUser")
     */
    class User extends AbstractUser
    {

        public function auth($username, $passowrd)
        {
            if($username === "admin"){
                $this->uname = "admin";
                $this->uid = 1;
                $this->role = "ADMIN";
                $this->setValid();
                return TRUE;
            }
            return FALSE;
        }

        public function unauth()
        {
            $this->setInValid();
        }
    }
}
