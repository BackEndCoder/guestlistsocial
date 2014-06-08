<?php
App::import('Vendor', 'OAuth/OAuthClient');

class TwitterController extends AppController {
    public $components = array('Session', 'Auth', 'Paginator');
    public $helpers =  array('Html' , 'Form');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar');
    public $paginate = array(
        'limit' => 25
    );

    public function index() {
        $this->Paginator->settings = array(
        'conditions' => array('team_id' => $this->Session->read('Auth.User.Team.id'), 'verified' => 0, 'published' => 0, 'timestamp >' => time()),
        'limit' => 10
        );

        //$toCheck = $this->Tweet->find('all', array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name', 'account_id'), 'conditions' => array('team_id' => $this->Session->read('Auth.User.Team.id'), 'verified' => 0, 'published' => 0, 'timestamp >' => time()), 'order' => array('Tweet.timestamp' => 'ASC')));
        $toCheck = $this->Paginator->paginate('Tweet');

            $i = 0;
        foreach ($toCheck as $key) {
            $array = $this->TwitterAccount->find('first', array('fields' => 'screen_name', 'conditions' => array('account_id' => $key['Tweet']['account_id'])));
            $toCheck[$i]['Tweet']['screen_name'] = $array['TwitterAccount']['screen_name'];
            $i++;
        }
        $this->set('tweets', $toCheck);
    }

    public function admin() {
        if (isset($this->request->data['currentmonth'])) {
            $this->Session->write('Auth.User.monthSelector', $this->request->data['currentmonth']['Select Month']);
        } elseif ($this->Session->read('Auth.User.monthSelector') == false) {
            $this->Session->write('Auth.User.monthSelector', 0);
        }
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

        if ($this->Session->read('Auth.User.Team.id')) {
            $permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
            $conditions = array('account_id' => $permissions);
        } else {
            $conditions = array('user_id' => $this->Session->read('Auth.User.id'));
        }
        $tweets = $this->Tweet->find('all', array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id')), 'order' => array('Tweet.timestamp' => 'ASC')));
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

    public function calendar($months) {
        if (isset($this->request->data['calendar_activated']['calendar_activated'])) {
            $this->activateCalendar($this->request->data['calendar_activated']['calendar_activated']);
        }
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
            $permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
            $conditions = array('team_id' => $this->Session->read('Auth.User.Team.id'));
        } else {
            $conditions = array('user_id' => $this->Session->read('Auth.User.id'));
        }
        $calendar = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC')));
        $this->set('calendar', $calendar);

        $info = $this->TwitterAccount->find('all', array('fields' => array('infolink'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
        $this->set('info', $info);

        $teamMembers = $this->User->find('all', array('fields' => array('first_name', 'group_id'), 'conditions' => array('team_id' => $this->Session->read('Auth.User.Team.id'))));
        $this->set('teamMembers', $teamMembers);
        
        if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name')));
        } else {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => array('account_id' => $permissions)));
        }
        
        $this->set('accounts', $accounts);

        if ($months) {
            $this->set('months', $months);
        }
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

        $count = $this->TwitterAccount->find('count', array('conditions' => array('screen_name' => $accessToken['screen_name'])));

        if ($count == 0) {
            $this->TwitterAccount->create();
            $this->TwitterAccount->save($accessToken);
            $this->TwitterAccount->saveField('user_id', $this->Session->read('Auth.User.id'));
            $this->TwitterAccount->saveField('team_id', $this->Session->read('Auth.User.Team.id'));
            
            $account = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $accessToken['screen_name'])));
            $this->Session->write('access_token.account_id', $account[0]['TwitterAccount']['account_id']);
        } else {
            $this->Session->setFlash('This account is already linked to a user. Know the user? Ask for their team code and join their team!');
            $this->redirect('/twitter/');
        }
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
            $client->post($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update.json', array('status' => 'HELLO'));
            $this->Session->setflash('Tweet Sent');
        } else {
            $this->Session->setFlash('Please select an account to tweet from');
        }

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

    public function edit() {
        foreach ($this->request->data['Tweet'] as $key) {
            if ($key['id']) {
            $id = $key['id'];
            $this->Tweet->id = $id;
            $this->CronTweet->id = $id;
            }

            if ($key['timestamp']) {
            $key['time'] = $key['timestamp'];
            $key['timestamp'] = strtotime($key['timestamp']);
            } else {
            $key['timestamp'] = 0;
            }

            //$key['first_name'] = $this->Session->read('Auth.User.first_name');

            $key['user_id'] = $this->Session->read('Auth.User.id');
            $key['account_id'] = $this->Session->read('access_token.account_id');
            if ($this->Tweet->save($key)) {
                if ($key['verified'] == 1) {
                    $this->CronTweet->save($key);
                    $this->CronTweet->deleteAll(array('timestamp' => 0));
                }
            } else {
            $this->Session->setFlash('Unable to update your post.');
            }
        }

        $this->redirect(Controller::referer());
    }

    public function emptySave() {
        debug($this->request->data);
        foreach ($this->request->data['Tweet'] as $key) {
            if ($key['id']) {
            $id = $key['id'];
            $this->Tweet->id = $id;
            $this->CronTweet->id = $id;
            $tweet = $this->Tweet->find('first', array('conditions' => array('id' => $id)));
            }

            if ($key['timestamp']) {
            $key['time'] = $key['timestamp'];
            $key['timestamp'] = strtotime($key['timestamp']);
            } else {
            $key['timestamp'] = 0;
            }

            if ($this->Tweet->saveField('verified', $key['verified'])) {
                if ($key['verified'] == 1) {
                    $this->CronTweet->save($key);
                    $this->CronTweet->deleteAll(array('timestamp' => 0));
                }
            } else {
            $this->Session->setFlash('Unable to update your post.');
            }
        }

        $this->redirect(Controller::referer());
        
    }

    public function delete($id) {
        if($this->Tweet->delete($id)) {
            $this->CronTweet->delete($id);
            $this->Session->setFlash('Tweet has been deleted.');
            $this->redirect(array('action' => 'admin'));
        }
    }

    public function tablerefresh() {
        $tweets = $this->Tweet->find('all', array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id')), 'order' => array('Tweet.timestamp' => 'ASC')));
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

    private function activateCalendar($data) {
        if ($this->Session->read('Auth.User.Team.id') == 0) {
            $conditions = array('id' => $this->Session->read('Auth.User.id'));
        } else {
            $conditions = array('team_id' => $this->Session->read('Auth.User.Team.id'));
        }
        $users = $this->User->find('list', array('conditions' => $conditions, 'fields' => 'id'));

        foreach ($users as $key) {
            $this->User->id = $key;
            $this->User->savefield('calendar_activated', $data);
        }
        
        $this->Session->write('Auth.User.calendar_activated', $data);
        if ($data == 1) {
            $this->Session->setFlash('Editorial Calendars have been activated for you team. Your team will now see them on the main page.');
        } elseif ($data == 0) {
            $this->Session->setFlash('Editorial Calendars deactivated.');
        }
    }


    public function adminView() {

    }
}