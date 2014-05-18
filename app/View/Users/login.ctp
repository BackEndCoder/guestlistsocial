<?php 
echo $this->Session->flash('auth');
echo $this->Form->create('User');
echo __('Please enter your email and password');
        echo $this->Form->input('email');
        echo $this->Form->input('password');
    	echo $this->Form->end(__('Login')); 

echo "Don't Have an account?" . $this->Html->link('Register', '/users/register');
?>