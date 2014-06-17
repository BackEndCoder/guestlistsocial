<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>

<?
echo $this->Form->create('filterAccount');
echo $this->Form->input('account', array(
	'label' => 'Select by Twitter Account:',
	'onchange' => 'this.form.submit()',
	'options' => array('empty' => 'Select Account...', array_combine($dropdownaccounts,$dropdownaccounts))));
echo $this->Form->end();

echo $this->Form->create('filterUser');
echo $this->Form->input('user', array(
	'label' => 'Select by User:',
	'onchange' => 'this.form.submit()',
	'options' => array('empty' => 'Select User...', $dropdownusers)));
echo $this->Form->end();
?>

<? echo $this->Form->create('Teams', array('action' => 'permissionsave'));?>
<div id='selectall'>
<? echo $this->Form->input('Select All', array('type' => 'checkbox', 'class' => 'selectAll', 'label' => 'Select All')); ?>
</div>

<?if (isset($accountTable)) { //If they are filtering by account show this table?>
<table>
<th></th><?
	echo '<th>' . $currentAccount .'</th>';

foreach ($users as $key) {?>
	<tr>
		<td> <? echo $key['name']; ?> </td>
	<? 
		if (in_array($twitter_account_id, $key['permissions'])) {
			$checked = 'checked';
			$value = $twitter_account_id;
		} else {
			$checked = '';
			$value = $twitter_account_id;
			//$value = 0;
		}

		echo '<td>' . $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => '', 'name' => 'data[Teams]['.$key['user_id'].'][permissions]['.$value.']', 'value' => $key['user_id'])) . '</td>';
		echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.User.Team.id'), 'name' => 'data[Teams]['.$key['user_id'].'][team_id]'));
		echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $key['user_id'], 'name' => 'data[Teams]['.$key['user_id'].'][user_id]'));
	
}
?>
</table>
<?} elseif (isset($userTable)) {//If they are filtering by account show this table?>
<table>
<th></th><?
	echo '<th>' . $currentUser .'</th>';

foreach ($accounts as $key) {?>
	<tr>
		<td> <? echo $key['TwitterAccount']['screen_name']; ?> </td>
	<? 
		if (in_array($key['TwitterAccount']['account_id'], $users['permissions'])) {
			$checked = 'checked';
			$value = $key['TwitterAccount']['account_id'];
		} else {
			$checked = '';
			$value = $key['TwitterAccount']['account_id'];
			//$value = 0;
		}

		echo '<td>' . $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => '', 'name' => 'data[Teams]['.$users['user_id'].'][permissions]['.$value.']', 'value' => $users['user_id'])) . '</td>';
		echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.User.Team.id'), 'name' => 'data[Teams]['.$users['user_id'].'][team_id]'));
		echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $users['user_id'], 'name' => 'data[Teams]['.$users['user_id'].'][user_id]'));
	
}
?>
</table>
	<?}?>
<? echo $this->Form->end('Submit changes'); ?>
<script>
$(document).ready(function () { 
	$('#selectall').on('change', '.selectAll', function(e) {
	  if(this.checked) {
	      // Iterate each checkbox
	      $(".aCheckbox").prop('checked', this.checked);
	  }
	  else {
	    $(".aCheckbox").prop('checked', this.checked);
	  }
	});
});
</script>
