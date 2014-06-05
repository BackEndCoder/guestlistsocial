<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<? echo $this->Form->create('Teams', array('action' => 'permissionsave'));?>
<table>
<th></th>
<th>SELECT ALL</th>
<?
foreach ($accounts as $key) {
	echo '<th>' . $key['TwitterAccount']['screen_name'] .'</th>';
}

foreach ($users as $key) { ?>
	<tr>
		<td> <? echo $key['name']; ?> </td>
		<td> <? echo $this->Form->input('Select All', array('type' => 'checkbox', 'class' => 'selectAll', 'label' => false)); ?></td>
	<? foreach ($accounts as $key1) {
		if (in_array($key1['TwitterAccount']['account_id'], $key['permissions'])) {
			$checked = 'checked';
			$value = $key1['TwitterAccount']['account_id'];
		} else {
			$checked = '';
			$value = $key1['TwitterAccount']['account_id'];
			//$value = 0;
		}

		echo '<td>' . $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => '', 'name' => 'data[Teams]['.$key['user_id'].'][permissions]['.$value.']', 'value' => $key['user_id'])) . '</td>';
		echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.User.Team.id'), 'name' => 'data[Teams]['.$key['user_id'].'][team_id]'));
		echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $key['user_id'], 'name' => 'data[Teams]['.$key['user_id'].'][user_id]'));
	}
}
?>
</table>
<? echo $this->Form->end('Submit changes'); ?>
<script>
$(document).ready(function () { 
	$('table').on('change', '.selectAll', function(e) {
	  if(this.checked) {
	      // Iterate each checkbox
	      $(this).closest('tr').find(".aCheckbox").prop('checked', this.checked);
	  }
	  else {
	    $(this).closest('tr').find(".aCheckbox").prop('checked', this.checked);
	  }
	});
});
</script>
