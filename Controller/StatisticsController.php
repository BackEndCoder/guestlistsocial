<?php
App::import('Vendor', 'OAuth/OAuthClient');


class StatisticsController extends AppController {
	public $components = array('Session');
    public $helpers =  array('Html' , 'Form');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet');

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

		if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name')));
        } else {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
        }
        
        $this->set('accounts', $accounts);


        //load dates for graphs
    	$modeldate = 'archive_' . strtolower(date('d-M-Y'));
    	$yesterday = 'archive_' . strtolower(date('d-M-Y',  time() - 60 * 60 * 24));
    	$yesterday2 = 'archive_' . strtolower(date('d-M-Y',  time() - 60 * 60 * 24 * 2));
    	$yesterday3 = 'archive_' . strtolower(date('d-M-Y',  time() - 60 * 60 * 24 * 3));
    	$yesterday4 = 'archive_' . strtolower(date('d-M-Y',  time() - 60 * 60 * 24 * 4));
    	$yesterday5 = 'archive_' . strtolower(date('d-M-Y',  time() - 60 * 60 * 24 * 5));
    	$yesterday6 = 'archive_' . strtolower(date('d-M-Y',  time() - 60 * 60 * 24 * 6));
    	$from = 'archive_' . strtolower(date('d-M-Y',  $this->request->data['Date']['from']));

		$this->loadModel($modeldate);
		$this->loadModel($yesterday);
		$this->loadModel($yesterday2);
		$this->loadModel($yesterday3);
		$this->loadModel($yesterday4);
		$this->loadModel($yesterday5);
		$this->loadModel($yesterday6);
		$name = $this->Session->read('access_token.screen_name');

		//setting stats for view
		$currentretweets = $this->$yesterday->field('daily_retweets', array('screen_name' => $name));
		$currentmentions = $this->$yesterday->field('daily_mentions', array('screen_name' => $name));
		$currentfavourites = $this->$yesterday->field('favourites_count', array('screen_name' => $name)) - $this->$yesterday2->field('favourites_count', array('screen_name' => $name));
		$currentfollowers = $this->$yesterday->field('followers_count', array('screen_name' => $name)) - $this->$yesterday2->field('followers_count', array('screen_name' => $name));

		$this->set('retweets', $currentretweets);
		$this->set('mentions', $currentmentions);
		$this->set('favourites', $currentfavourites);
		$this->set('followers', $currentfollowers);

		$dates = '\''.date('d M',  time() - 60 * 60 * 24 * 4) .'\', \''.date('d M',  time() - 60 * 60 * 24 * 3).'\', \''.date('d M',  time() - 60 * 60 * 24 * 2).'\', \''.date('d M',  time() - 60 * 60 * 24 * 1).'\', \''.date('d M',  time()).'\'';
		
		$this->set('dates', $dates);

		$currentfollowers1 = $this->$yesterday->field('followers_count', array('screen_name' => $name));
		$currentfollowers2 = $this->$yesterday2->field('followers_count', array('screen_name' => $name));
		$currentfollowers3 = $this->$yesterday3->field('followers_count', array('screen_name' => $name));
		$currentfollowers4 = $this->$yesterday4->field('followers_count', array('screen_name' => $name));
		$currentfollowers5 = $this->$yesterday5->field('followers_count', array('screen_name' => $name));

		$followerdata = $currentfollowers5.','.$currentfollowers4.','.$currentfollowers3.','.$currentfollowers2.','.$currentfollowers1.'';
		$this->set('followerdata', $followerdata);


		$dbdfollowers1 = $this->$yesterday->field('followers_count', array('screen_name' => $name)) - $this->$yesterday2->field('followers_count', array('screen_name' => $name));
		$dbdfollowers2 = $this->$yesterday2->field('followers_count', array('screen_name' => $name)) - $this->$yesterday3->field('followers_count', array('screen_name' => $name));
		$dbdfollowers3 = $this->$yesterday3->field('followers_count', array('screen_name' => $name)) - $this->$yesterday4->field('followers_count', array('screen_name' => $name));
		$dbdfollowers4 = $this->$yesterday4->field('followers_count', array('screen_name' => $name)) - $this->$yesterday5->field('followers_count', array('screen_name' => $name));
		$dbdfollowers5 = $this->$yesterday5->field('followers_count', array('screen_name' => $name)) - $this->$yesterday6->field('followers_count', array('screen_name' => $name));

		$dbdfollower = $dbdfollowers5.','.$dbdfollowers4.','.$dbdfollowers3.','.$dbdfollowers2.','.$dbdfollowers1.'';
		$this->set('dbdfollower', $dbdfollower);
	}

	private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }

    public function sheel() {
    	$tabledate = 'archive_' . strtolower(date('d-M-Y')) . 's';
    	$modeldate = 'archive_' . strtolower(date('d-M-Y'));
    	$yesterday = 'archive_' . strtolower(date('d-M-Y',  time() - 60 * 60 * 24));

    	$this->Tweet->query("CREATE TABLE IF NOT EXISTS `$tabledate` (
		  `account_id` int(11) DEFAULT NULL,
		  `screen_name` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `followers_count` int(10) NOT NULL,
		  `daily_retweets` int(10) NOT NULL,
		  `daily_mentions` int(10) NOT NULL,
		  `favourites_count` int(10) NOT NULL,
		  `tweet_rollover` bigint(20) NOT NULL,
  		  PRIMARY KEY (`account_id`)
		);");

		$this->loadModel($modeldate);
		$this->loadModel($yesterday);

    	//gather data
    	$data = $this->TwitterAccount->find('all');

    	foreach ($data as $key) {
			$name = $key['TwitterAccount']['screen_name'];
			$oauth_token = $this->TwitterAccount->field('oauth_token', array('screen_name' => $name));
	        $oauth_token_secret = $this->TwitterAccount->field('oauth_token_secret', array('screen_name' => $name));
			$client = $this->createClient();
    		$account_id = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $name), 'fields' => 'account_id', 'limit' => 1));

	        if ($oauth_token&&$oauth_token_secret) {
	            $details = json_decode($client->get($oauth_token, $oauth_token_secret, "https://api.twitter.com/1.1/users/show.json?screen_name=$name"), true);
	        } else {
	            $this->Session->setFlash('Please select an account to tweet from');
	        }

	        $tweet_rollover = $this->$yesterday->field('tweet_rollover', array('screen_name' => $name));

    		$retweets = json_decode($client->get($oauth_token, $oauth_token_secret, "https://api.twitter.com/1.1/statuses/retweets_of_me.json?trim_user=true&count=100&since_id=$tweet_rollover"),true);

    		$mentions = json_decode($client->get($oauth_token, $oauth_token_secret, "https://api.twitter.com/1.1/statuses/mentions_timeline.json?trim_user=true&count=200&since_id=$tweet_rollover&include_rts=1&since_id=$tweet_rollover"),true);

    		$retweets = count($retweets);

    		$mentions = count($mentions);

    		echo $mentions;
	        
	        $save = array($yesterday => array(
	        			  'account_id' => $account_id[0]['TwitterAccount']['account_id'],
	        			  'screen_name' => $name,
	        			  'followers_count' => $details['followers_count'],
	        			  'favourites_count' => $details['favourites_count'],
	        			  'daily_retweets' => $retweets,
	        			  'daily_mentions' => $mentions,
	        			  'tweet_rollover' => $details['status']['id']));

	        $this->$yesterday->save($save);
    	}
    }
}