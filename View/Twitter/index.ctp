<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<?php
echo $this->Html->script('jquery-ui-1.10.3.custom'); 
echo $this->Html->css('calendar');
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

<script>
  $(function() {
    $( ".datepicker" ).datepicker(
    	{
    		dateFormat: 'dd/mm/yy',
    		altFormat: '@',
    	});
  });
</script>

<?php
echo $this->Form->create('Date', array('url' => array('controller' => 'statistics', 'action' => 'index')));
        echo $this->Form->input('from', array('class' => 'datepicker', 'width' => '20px'));
        echo $this->Form->input('to', array('class' => 'datepicker'));
echo $this->Form->end('Go!');
?>

<?php
echo $this->Form->create('Tweet', array('url' => array('controller' => 'twitter', 'action' => 'posttweet')));
		echo $this->Form->input('tweet', array('label' => false, 'type' => 'post'));
		echo $this->Form->input('time', array(
    		'type' => 'datetime',
    		'timeFormat'=>'24'));
echo $this->Form->end('Tweet!');
?>

<?php echo $this->Form->create('CronTweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'edit'))));?>
<table>
	<th>Tweet</th>
	<th>Time</th>
	<th>Verified</th>
	<th>Client Verified</th>
	<?php foreach ($tweets as $key) { ?>
	<tr>
	  <td><?php echo $key['CronTweet']['body']; ?></td>
	  <td><?php echo $key['CronTweet']['time']; ?></td>
	  <td><?php echo $this->Form->input('verified', array('type' => 'checkbox', 'value' => 1, 'label' => false, 'name' => 'data[CronTweet]['.$key['CronTweet']['id'].'][verified]'));?></td>
	  <td><?php echo $this->Form->input('client_verified', array('type' => 'checkbox', 'value' => 1, 'label' => false, 'name' => 'data[CronTweet]['.$key['CronTweet']['id'].'][client_verified]'));?></td>
	  <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['CronTweet']['id'], 'name' => 'data[CronTweet]['.$key['CronTweet']['id'].'][id]'));?>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Form->end('Update'); ?>

<?php echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php echo $this->Html->link('Logout', '/users/logout');?>