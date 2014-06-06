<?php
//Finds tweets from db with timestamp in last minute and sends them off
App::import('Vendor', 'OAuth/OAuthClient');

class ScheduleShell extends AppShell {
	public $uses = array('TwitterAccount', 'CronTweet', 'Tweet');

    public function main() {
        $lowerbound = time() + 3600 - 120; //adding 3600 due to GMT -> BST, remove if not // will come up with a solution soon
        $upperbound = time() + 3600;
        $toTweet = $this->CronTweet->find('all', array('conditions' => array('timestamp <' => $upperbound, 'timestamp >' => $lowerbound)));
        $count = $this->CronTweet->find('count', array('conditions' => array('timestamp <' => $upperbound, 'timestamp >' => $lowerbound)));
        $toDelete = $this->CronTweet->find('list', array('fields' => array('id'), 'conditions' => array('timestamp <' => $upperbound, 'timestamp >' => $lowerbound)));

        if ($count != 0) {
            $count-=1;
           
            $i = 0;
            for ($i < $count; $i <= $count; $i++) {
                $accountID = $toTweet[$i]['CronTweet']['account_id'];
                $accountDetails = $this->TwitterAccount->find('all', array('conditions' => array('account_id' => $accountID)));

                $oauth_token = $accountDetails[0]['TwitterAccount']['oauth_token'];
                $oauth_token_secret = $accountDetails[0]['TwitterAccount']['oauth_token_secret'];
                $client = $this->createClient();

                if ($oauth_token&&$oauth_token_secret) {
                    $client->post($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update.json', array('status' => $toTweet[$i]['CronTweet']['body']));
                    $this->Tweet->id = $toTweet[$i]['CronTweet']['id'];
                    $this->Tweet->saveField('published', 1);
                    $this->out('Complete');
                }
            $this->CronTweet->deleteAll(array('id' => $toDelete));

            }
        } elseif ($count == 0) {
            $this->out('No Tweets');
        }
    }

    private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }
}
