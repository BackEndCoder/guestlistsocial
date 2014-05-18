<? echo $this->Form->create('Teams', array('action' => 'permissionsave'));?>
<table>
<th></th>
<?
foreach ($accounts as $key) {
	echo '<th>' . $key['TwitterAccount']['screen_name'] .'</th>';
}

foreach ($users as $key) { ?>
	<tr>
		<td> <? echo $key['name']; ?> </td>
	<? foreach ($accounts as $key1) {
		if (in_array($key1['TwitterAccount']['account_id'], $key['permissions'])) {
			$checked = 'checked';
			$value = $key1['TwitterAccount']['account_id'];
		} else {
			$checked = '';
			$value = $key1['TwitterAccount']['account_id'];
			//$value = 0;
		}

		echo '<td>' . $this->Form->input('twitter_permissions', array('type' => 'checkbox', $checked, 'label' => '', 'name' => 'data[Teams]['.$key['user_id'].'][permissions]['.$value.']', 'value' => $key['user_id'])) . '</td>';
		echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $this->Session->read('Auth.User.Team.id'), 'name' => 'data[Teams]['.$key['user_id'].'][team_id]'));
		//echo '<td>' . $this->Form->input('twitter_permissions', array('type' => 'checkbox', $checked, 'label' => '', 'name' => $key['user_id'] . '_' . $key1['TwitterAccount']['account_id'], 'value' => $value)) . '</td>';
	}
}
?>
</table>
<? echo $this->Form->end('Submit changes'); ?>
