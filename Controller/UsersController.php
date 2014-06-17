<?php 
class UsersController extends AppController {
    public $helpers = array('Html','Form');

    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow(array('action' => 'logout'));
		$this->Auth->allow('initDB', 'verify');
    }

	public function register() {
	        if ($this->request->is('post')) {
                $hash=sha1($this->request->data['User']['first_name'].rand(0,100));
                $this->User->data['User']['registration_hash'] = $hash;
	            if ($this->User->save($this->request->data)) {
	            	$this->User->saveField('session_id', $this->Session->id());
	            	$this->User->saveField('group_id', 6);
                    $id = $this->User->getLastInsertId();
                    $msg = "Please click on the link below to activate you account with Guestlist Social:

                    " . Router::url(array('action' => 'verify', 'id' => $id, 'h' => $hash), true);
                    $Email = new CakeEmail();
                    $Email->from(array('registration@social.guestlist.net' => 'Guestlist Social'));
                    $Email->to($this->request->data['User']['email']);
                    $Email->subject('Confirm Registration for Guestlist Social');
                    $Email->send($msg);
	                $this->Session->setFlash(__('Please check your email to complete registration.'));
	                $this->redirect(array('controller' => 'users', 'action' => 'login'));
	            } else {
	                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
	            }
	        }
	}

    public function verify() {
        if (!empty($this->passedArgs['id']) && !empty($this->passedArgs['h'])){
            $id = $this->passedArgs['id'];
            $hash = $this->passedArgs['h'];
            $results = $this->User->find('all', array('fields' => array('group_id', 'registration_hash'), 'conditions' => array('User.id' => $id)));
            //check if the user is already activated
            if ($results[0]['User']['group_id'] == 6) {
            //check the token
                if($results[0]['User']['registration_hash'] == $hash) {
                    debug($id);
                    $this->User->id = $id;
                    $this->User->saveField('group_id', 2);
                    $this->Session->setFlash('Your registration is complete. Please log in.');
                    $this->redirect('/users/login');
                    exit;
                } else { //hashes don't match
                    $this->Session->setFlash('Your registration failed please try again');
                    $this->redirect('/users/register');
                }
            } else { // activated = 1
                $this->Session->setFlash('Token has alredy been used');
                $this->redirect('/users/register');
            }
        } else { //empty arguments
            $this->Session->setFlash('Token corrupted. Please re-register');
            $this->redirect('/users/register');
        }
            
    } 

	public function login() {
    	if ($this->request->is('post')) {
        	if ($this->Auth->login()) {
        	 $this->User->id = $this->Session->read('Auth.User.id');
	         $this->User->saveField('session_id', $this->Session->id());
           	 $this->redirect('/');
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
    //$this->Acl->allow($group, 'controllers/teams/manageteam');

    // allow managers to posts and widgets
    //$group->id = 2;
    //$this->Acl->allow($group, 'controllers/teams/manage');

    //$group->id = 5;
    //$this->Acl->allow($group, 'controllers');
    //$this->Acl->allow($group, 'controllers/teams/manageteam');

    // allow basic users to log out
    //$this->Acl->allow($group, 'controllers/users/logout');

    // we add an exit to avoid an ugly "missing views" error message
    echo "all done";
    exit;
}
}