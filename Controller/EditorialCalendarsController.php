<?php
class EditorialCalendarsController extends AppController {
    public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar');

    public function calendarsave() {
        $data = $this->request->data;
        if ($data) {
            foreach ($data['EditorialCalendar'] as $key) {
                $id = $key['id'];
                $this->EditorialCalendar->id = $id;
                $this->EditorialCalendar->save($key);    
            }
        }
        $this->redirect(Controller::referer());
    }

    public function editcalendartweet() {
        foreach ($this->request->data['Tweet'] as $key) {
            if ($key['id']) {
            $id = $key['id'];
            $this->Tweet->id = $id;
            $this->CronTweet->id = $id;
            } else {
                $key['first_name'] = $this->Session->read('Auth.User.first_name');
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
            if ($key['body']) {
                if ($this->Tweet->save($key)) {
                    if ($key['verified'] == 1) {
                        $this->CronTweet->save($key);
                        $this->CronTweet->deleteAll(array('timestamp' => 0));
                    }
                } else {
                $this->Session->setFlash('Unable to update your post.');
                }
            } elseif ($key['id'] && !$key['body']) {
                $this->Tweet->delete($id);
            }
        }

        $this->redirect(Controller::referer());
    }

    public function addCalendar() {
        $this->EditorialCalendar->create();
        $this->EditorialCalendar->saveField('twitter_account_id', $this->Session->read('access_token.account_id'));
        $this->EditorialCalendar->saveField('team_id', $this->Session->read('Auth.User.Team.id'));
        $this->redirect(Controller::referer());
    }

    public function deleteCalendar($id) {
        $this->EditorialCalendar->delete($id);
        $this->redirect(Controller::referer());
    }

    public function calendarRefresh ($months) {
        $calendar = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC')));
        
        $this->set('calendar', $calendar);
        
        if (isset($months)) {
            $this->set('months', $months);
        }
        $this->layout = '';
    }
    
    public function editorialRefresh() {
        $calendar = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC')));
        $this->set('calendar', $calendar);
        $this->layout = '';
    }
}
?>