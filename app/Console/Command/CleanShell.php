<?php 
//Cleans db queue every day
App::import('Vendor', 'OAuth/OAuthClient');

class CleanShell extends AppShell {
	public $uses = array('TwitterAccount', 'CronTweet');

    public function main() {
        $upperbound = time();
        $count = $this->CronTweet->find('count', array('conditions' => array('timestamp <' => $upperbound)));
        $toDelete = $this->CronTweet->find('list', array('fields' => array('id'), 'conditions' => array('timestamp <' => $upperbound)));

        if ($count != 0) {
            $count-=1;
            $this->CronTweet->deleteAll(array('id' => $toDelete));
            $this->out('Complete');
        } elseif ($count == 0) {
            $this->out('No Tweets');
        }
    }
}