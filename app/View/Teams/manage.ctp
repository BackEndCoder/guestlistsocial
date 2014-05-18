<?  if ($this->Session->read('Auth.User.Team.id') == 0) { ?>
Create team:
<?php
	echo $this->Form->create('createTeam');
	echo $this->Form->input('Team name', array('name' => 'name'));
	echo $this->Form->end('Submit');
?>

Join team:
<?php echo $this->Form->create('joinTeam');
	echo $this->Form->input('Team code', array('name' => 'hash'));
	echo $this->Form->end('Submit');
}
?>