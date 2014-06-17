<?php
App::uses('AuthComponent', 'Controller/Component');
class User extends AppModel {
    public $actsAs = array('Acl' => array('type' => 'requester', 'enabled' => false));
    public $validate = array(
        'email' => array(
            'required' => array(
            'rule' => array('email',true),
                'message' => 'An email is required'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            ),
            'between' => array(
                'rule'    => array('between', 6, 20),
                'message' => 'Password must be between 6 and 20 characters',
                'last' => false
            ),
            'alphanumeric' => array(
                'rule' => 'alphanumeric',
                'message' => 'Password must contain only letters and numbers.'
            )
        ),
        'password2' => array(
            'equaltofield' => array(
            'rule' => array('equaltofield','password'),
            'message' => 'Passwords do not match.',
            'on' => 'create'
            )
        ),
        'first_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter your first name'
            )
        )
    );

    public $belongsTo = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Team' => array(
            'className' => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function bindNode() {
        $data = AuthComponent::user();
        return array('model' => 'Group', 'foreign_key' => $data['Group']['id']);
    }

    public function beforeSave($options = array()) {
    if (isset($this->data[$this->alias]['password'])) {
        $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
    }
    return true;
    }

    public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        } else {
            return array('Group' => array('id' => $groupId));
        }
    }

    function equaltofield($check,$otherfield) {
        //get name of field
        $fname = '';
        foreach ($check as $key => $value){
            $fname = $key;
            break;
        }
        return $this->data[$this->name][$otherfield] === $this->data[$this->name][$fname];
    } 

}
?>