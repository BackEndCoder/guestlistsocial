<?php
App::import('Vendor', 'OAuth/OAuthClient');

class TwitterController extends AppController {
    public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User');

    public function index() {
        if (isset($this->request->data['accountSubmit'])) {
            $screen_name = $this->request->data['accountSubmit'];
            $new_oauth_tokens = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $screen_name)));
            $this->Session->write('access_token.oauth_token', $new_oauth_tokens[0]['TwitterAccount']['oauth_token']);
            $this->Session->write('access_token.oauth_token_secret', $new_oauth_tokens[0]['TwitterAccount']['oauth_token_secret']);
            $this->Session->write('access_token.account_id', $new_oauth_tokens[0]['TwitterAccount']['account_id']);
            $this->Session->write('access_token.screen_name', $new_oauth_tokens[0]['TwitterAccount']['screen_name']);
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        } else {
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        }

        $tweets = $this->CronTweet->find('all', array('fields' => array('id', 'body', 'time'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
        $this->set('tweets', $tweets);
        
        if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name')));
        } else {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
        }
        
        $this->set('accounts', $accounts);
    }

    public function admin() {
        if (isset($this->request->data['accountSubmit'])) {
            $screen_name = $this->request->data['accountSubmit'];
            $new_oauth_tokens = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $screen_name)));
            $this->Session->write('access_token.oauth_token', $new_oauth_tokens[0]['TwitterAccount']['oauth_token']);
            $this->Session->write('access_token.oauth_token_secret', $new_oauth_tokens[0]['TwitterAccount']['oauth_token_secret']);
            $this->Session->write('access_token.account_id', $new_oauth_tokens[0]['TwitterAccount']['account_id']);
            $this->Session->write('access_token.screen_name', $new_oauth_tokens[0]['TwitterAccount']['screen_name']);
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        } else {
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        }

        if ($this->Session->read('Auth.User.Team.id') !== 0) {
            $conditions = array('team_id' => $this->Session->read('Auth.User.Team.id'));
        } else {
            $conditions = array('user_id' => $this->Session->read('Auth.User.id'));
        }
        $tweets = $this->Tweet->find('all', array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
        $this->set('tweets', $tweets);

        $info = $this->TwitterAccount->find('all', array('fields' => array('infolink'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
        $this->set('info', $info);

        $teamMembers = $this->User->find('all', array('fields' => array('first_name', 'group_id'), 'conditions' => array('team_id' => $this->Session->read('Auth.User.Team.id'))));
        $this->set('teamMembers', $teamMembers);
        
        if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name')));
        } else {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => $conditions));
        }
        
        $this->set('accounts', $accounts);
    }

    public function client() {
        if (isset($this->request->data['accountSubmit'])) {// temporary until ACLs are in
            $screen_name = $this->request->data['accountSubmit'];
            $new_oauth_tokens = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $screen_name)));
            $this->Session->write('access_token.oauth_token', $new_oauth_tokens[0]['TwitterAccount']['oauth_token']);
            $this->Session->write('access_token.oauth_token_secret', $new_oauth_tokens[0]['TwitterAccount']['oauth_token_secret']);
            $this->Session->write('access_token.account_id', $new_oauth_tokens[0]['TwitterAccount']['account_id']);
            $this->Session->write('access_token.screen_name', $new_oauth_tokens[0]['TwitterAccount']['screen_name']);
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        } else {
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        }

        $tweets = $this->Tweet->find('all', array('fields' => array('id', 'body', 'verified', 'client_verified'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'), 'verified' => 1)));
        $this->set('tweets', $tweets);
        
        if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name')));
        } else {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
        }
        
        $this->set('accounts', $accounts);
    }

    public function connect() {
        $client = $this->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/twitter/twitterredirect');

        if ($requestToken) {
            $this->Session->write('twitter_request_token', $requestToken);
            $this->redirect('https://api.twitter.com/oauth/authorize?force_login=true&oauth_token=' . $requestToken->key);
        } else {
            echo 'HELLO';
        }
    }

    public function twitterredirect() {
        $requestToken = $this->Session->read('twitter_request_token');
        $client = $this->createClient();
        $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);

        $this->Session->write('access_token.oauth_token', $accessToken['oauth_token']);
        $this->Session->write('access_token.oauth_token_secret', $accessToken['oauth_token_secret']);
        $this->Session->write('access_token.screen_name', $accessToken['screen_name']);

        $this->TwitterAccount->create();
        $this->TwitterAccount->save($accessToken);
        $this->TwitterAccount->saveField('user_id', $this->Session->read('Auth.User.id'));
        $this->TwitterAccount->saveField('team_id', $this->Session->read('Auth.User.Team.id'));
        
        $account = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $accessToken['screen_name'])));
        $this->Session->write('access_token.account_id', $account[0]['TwitterAccount']['account_id']);

        $this->redirect('/twitter/info');//REMOVE WHEN REPORTING IS COMPLETE

        //setting database table for reporting archives
        /*$modeldate = 'archive_' . strtolower(date('d-M-Y', time() - 60 * 60 * 24 * 1));
        $name = $accessToken['screen_name'];
        $oauth_token = $accessToken['oauth_token'];
        $oauth_token_secret = $accessToken['oauth_token_secret'];
        $account_id = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $name), 'fields' => 'account_id', 'limit' => 1));

        $this->loadModel($modeldate);

            if ($oauth_token&&$oauth_token_secret) {
                $details = json_decode($client->get($oauth_token, $oauth_token_secret, "https://api.twitter.com/1.1/users/show.json?screen_name=$name"), true);
            } else {
                $this->Session->setFlash('Please select an account to tweet from');
            }

            $tweet_rollover = $details['status']['id'] - 1;
            
            $save = array($modeldate => array(
                          'account_id' => $account_id[0]['TwitterAccount']['account_id'],
                          'screen_name' => $name,
                          'followers_count' => $details['followers_count'],
                          'favourites_count' => $details['favourites_count'],
                          'tweet_rollover' => $tweet_rollover));

            $this->$modeldate->save($save);

        $this->redirect('/twitter/');*/

    }

    public function posttweet() {
        $oauth_token = $this->Session->read('access_token.oauth_token');
        $oauth_token_secret = $this->Session->read('access_token.oauth_token_secret');
        $client = $this->createClient();

        if ($oauth_token&&$oauth_token_secret) {
            $client->post($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update.json', array('status' => $this->request->data['Tweet']));
        } else {
            $this->Session->setFlash('Please select an account to tweet from');
        }

        $this->Session->setflash('Tweet Sent');
        $this->redirect('/twitter/');
    }

    private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }

    public function testing() {//temporary non-verified tweet save
        if ($this->request->data) {
                    //$this->request->data['CronTweet']['time']['hour'] += $this->Session->read('Auth.User.GMT_offset');
                    $this->Tweet->save($this->request->data);
                    $this->Tweet->saveField('user_id', $this->Session->read('Auth.User.id'));
                    $this->Tweet->saveField('account_id', $this->Session->read('access_token.account_id'));
                    $this->Tweet->saveField('team_id', $this->Session->read('Auth.User.Team.id'));
                    $this->Tweet->saveField('first_name', $this->Session->read('Auth.User.first_name'));
                    $this->redirect(array('action' => 'admin'));
        }

        //$this->redirect('/twitter/');
    }

    public function test() {//temporary verified and timed tweet save
        if ($this->request->data) {
                    //$this->request->data['CronTweet']['time']['hour'] += $this->Session->read('Auth.User.GMT_offset');
                    $this->CronTweet->save($this->request->data);
                    $this->CronTweet->saveField('user_id', $this->Session->read('Auth.User.id'));
                    $this->CronTweet->saveField('account_id', $this->Session->read('access_token.account_id'));
        }

        //$this->redirect('/twitter/');
    }

    public function edit() {
        foreach ($this->request->data['Tweet'] as $key) {
            $id = $key['id'];
            $this->Tweet->id = $id;
            $this->CronTweet->id = $id;
            if ($key['timestamp']) {
            $key['time'] = $key['timestamp'];
            $key['timestamp'] = strtotime($key['timestamp']);
            } else {

            $key['timestamp'] = 0;
            }
            $key['user_id'] = $this->Session->read('Auth.User.id');
            $key['account_id'] = $this->Session->read('access_token.account_id');
            if ($this->Tweet->save($key)) {
                if ($key['verified'] = 1) {
                    $this->CronTweet->save($key);
                    $this->CronTweet->deleteAll(array('timestamp' => 0));
                }
            } else {
            $this->Session->setFlash('Unable to update your post.');
            }
        }

        $this->redirect(array('action' => 'admin'));
    }

    public function delete($id) {
        if($this->Tweet->delete($id)) {
            $this->CronTweet->delete($id);
            $this->Session->setFlash('Tweet has been deleted.');
            $this->redirect(array('action' => 'admin'));
        }
    }

    public function tablerefresh() {
        $tweets = $this->Tweet->find('all', array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
        $this->set('tweets', $tweets);
        $this->layout = '';
    }


    public function info() {
        if ($this->request->data) {
            $id = $this->Session->read('access_token.account_id');
            $this->TwitterAccount->id = $id;
            $info = $this->request->data['TwitterAccount']['infolink'];
            $this->TwitterAccount->saveField('infolink', $info);

            $this->redirect('/twitter/');
        }
    }
}