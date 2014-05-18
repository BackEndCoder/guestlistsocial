<?php 
class TeamsController extends AppController {
    var $uses = array('User', 'Team', 'TwitterAccount', 'TwitterPermission');
    public $helpers =  array('Html' , 'Form');
    
    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow();
    }


	public function manage() {
		$data = $this->request->data;
		if (isset($data['name'])) {
			$data['hash'] = substr(md5(rand()), 0, 20);;
			$this->Team->save($data);
	
			$this->addtoTeam($data['hash']);
			$this->User->id = $this->Session->read('Auth.User.id');
			$this->User->saveField('group_id', 1);

			$accounts = $this->TwitterAccount->find('all', array('fields' => array('account_id', 'team_id'), 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
			foreach ($accounts as $key) {
			if ($key['TwitterAccount']['team_id'] == 0) {
				$this->TwitterAccount->id = $key['TwitterAccount']['account_id'];
				$this->TwitterAccount->saveField('team_id', $this->Session->read('Auth.User.Team.id'));
				}
			}
		$this->redirect('/twitter/admin');
		} elseif (isset($data['hash'])) {
			$this->addtoTeam($data['hash']);
			$this->User->saveField('group_id', 2);
		$this->redirect('/twitter/admin');
		}
	}

	public function manageteam() {
		if ($this->request->data) {
			debug($this->request->data);
		}
		$conditions = array('team_id' => $this->Session->read('Auth.User.Team.id'));
		$accounts = $this->TwitterAccount->find('all', array('fields' => array('screen_name', 'account_id'), 'conditions' => $conditions));
		$this->set('accounts', $accounts);
		$users = $this->User->find('list', array('fields' => array('first_name'), 'conditions' => $conditions));

		//use a foreach loop
		foreach ($users as $key => $value) {
			$permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('user_id' => $key)));
			$users[$key] = array('name' => $value , 'user_id' => $key, 'permissions' => $permissions);
		}

		$this->set('users', $users);
		//$permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_user_id', 'conditions' => array('user_id' => $userID)));
	}

	public function permissionSave() {
		$data = $this->request->data;
		$dbComparisons = $this->TwitterPermission->find('all', array('conditions' => array('team_id' => $this->Session->read('Auth.User.Team.id'))));
		foreach ($data['Teams'] as $key) {
			foreach ($key['permissions'] as $key1 => $value1) {
				if ($value1 != 0) {
					$this->TwitterPermission->create();
					$this->TwitterPermission->saveField('user_id', $value1);
					$this->TwitterPermission->saveField('twitter_account_id', $key1);
					$this->TwitterPermission->saveField('team_id', $key['team_id']);
					//check if already exists in db then go back to admin.ctp...
				}
			}
		}
	}

	private function addtoTeam($teamHash) {
		$team = $this->Team->find('all', array('fields' => array('id', 'name', 'hash'), 'conditions' => array('hash' => $teamHash)));
		$this->User->id = $this->Session->read('Auth.User.id');
		$this->User->saveField('team_id', $team[0]['Team']['id']);
		$this->Session->write('Auth.User.Team.id', $team[0]['Team']['id']);
		$this->Session->write('Auth.User.Team.name', $team[0]['Team']['name']);
		$this->Session->write('Auth.User.Team.hash', $team[0]['Team']['hash']);
		$this->Session->setFlash('You have been added to team ' . $this->Session->read('Auth.User.Team.name'));
	}
}