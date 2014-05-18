<?php
echo $this->Session->flash('auth');
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
    	)); 
echo $this->Form->end();

?>

<?php echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'edit'))));?>
<table>
	<th>Tweet</th>
	<th>Client Verified</th>
	<?php foreach ($tweets as $key) { ?>
	<?php if ($key['Tweet']['client_verified'] == 1) {$checked = 'checked';}else{$checked = '';} ?>
	<tr>
	  <td><?php echo $key['Tweet']['body']; ?></td>
	  <td><?php echo $this->Form->input('client_verified', array('type' => 'checkbox', 'value' => 1, 'label' => false, 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][client_verified]', $checked));?></td>
	  <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['Tweet']['id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][id]'));?>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Form->end('Update'); ?>

<?php echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php echo $this->Html->link('Logout', '/users/logout');?>