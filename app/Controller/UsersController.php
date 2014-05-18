<?php 
class UsersController extends AppController {
    public $helpers = array('Html','Form');

    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow(array('action' => 'logout'));
		$this->Auth->allow('initDB');
    }

	public function register() {
	        if ($this->request->is('post')) {
	            if ($this->User->save($this->request->data)) {
	            	$this->User->saveField('session_id', $this->Session->id());
	            	$this->User->saveField('group_id', 2);
	                $this->Session->setFlash(__('The user has been saved'));
	                $this->redirect(array('controller' => 'users', 'action' => 'login'));
	            } else {
	                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
	            }
	        }
	}

	public function login() {
    	if ($this->request->is('post')) {
        	if ($this->Auth->login()) {
        	 $this->User->id = $this->Session->read('Auth.User.id');
	         $this->User->saveField('session_id', $this->Session->id());
           	 $this->redirect($this->Auth->redirect());
      	  } else {
           	 $this->Session->setFlash(__('Invalid username or password, try again'));
        	}
    	}
	}

	public function logout() {
	 $this->Session->destroy();
   	 $this->redirect($this->Auth->logout());
	}


    public function initDB() {
    $group = $this->User->Group;

    // Allow admins to everything
    //$group->id = 1;
    //$this->Acl->allow($group, 'controllers/teams/manage');

    // allow managers to posts and widgets
    $group->id = 2;
    $this->Acl->allow($group, 'controllers/teams/manage');

    //$group->id = 5;
    //$this->Acl->allow($group, 'controllers');
    //$this->Acl->allow($group, 'controllers/teams/manage');

    // allow basic users to log out
    //$this->Acl->allow($group, 'controllers/users/logout');

    // we add an exit to avoid an ugly "missing views" error message
    echo "all done";
    exit;
}
}