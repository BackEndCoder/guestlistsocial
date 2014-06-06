<?php
App::uses('AuthComponent', 'Controller/Component');

class TwitterAccount extends AppModel {
    public $validate = array(
        'oauth_token' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Oauth token not supplied - Please try again'
            )
        ),
        'oauth_token_secret' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Oauth token secret not supplied - Please try again'
            )
        ),
    );

    public $primaryKey = 'account_id';

}
?>