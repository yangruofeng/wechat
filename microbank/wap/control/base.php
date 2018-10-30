<?php

class baseControl extends control{
    public $user_id;
    public $user_name;
    public $user_info;
    public $auth_list;

    function __construct(){
        parent::__construct();
        Language::read('auth');
        $user = userBase::Current();
        $user_info = $user->property->toArray();
        $this->user_info = $user_info;
        $this->user_id = $user_info['uid'];
        $this->user_name = $user_info['user_code'];

        $this->auth_list = $user->getAuthList();
    }

}
