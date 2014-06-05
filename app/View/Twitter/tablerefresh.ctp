<table>
<tr><td><?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'edit')), 'id' => 'edit'));?>
<table id="table">
	<th>Schedule</th>
	<th>Written by</th>
	<th>Tweet</th>
	<th>Verified</th>
	<th>Status</th>
	<th></th>
	<?php foreach ($tweets as $key) { ?>
	<?php if ($key['Tweet']['verified'] == 1) {
			$checked = 'checked';
			$value = $key['Tweet']['time'];
			$color = '#ffb400';
		} elseif ($key['Tweet']['verified'] == 1 && $key['Tweet']['client_verified'] == 1) {
			$color = 'Green';
		} else {
			$checked = '';
			$value = '';
			$color = 'Red';} 

			if ($this->Session->read('Auth.User.group_id') == 2) {
				$disabled = 'disabled';
			} else {
				$disabled = '';
			}?>
	<tr>
	  <td class= 'time' id='time<?php echo $key['Tweet']['id']?>'> 
	  	<div class='notediting'><?php if($key['Tweet']['time'] && $key['Tweet']['published'] == 1) {
	  		echo $key['Tweet']['time'] . '<small>[Published]</small>';
	  		} elseif ($key['Tweet']['time']) {
	  			echo $key['Tweet']['time'];
	  		} else {
	  			echo 'Click to schedule';
	  			} ?>
	  	</div>
	  	<?php if($key['Tweet']['published'] == 0) {
	  		echo $this->Form->input('timestamp', array(
	  		'type' => 'text', 
	  		'label' => false, 
	  		'class' => 'schedule',
	  		'value' => $key['Tweet']['time'], 
	  		'id' => 'schedule'.$key['Tweet']['id'], 
	  		'name' => 'data[Tweet]['.$key['Tweet']['id'].'][timestamp]',
	  		'style' => 'display: none'
	  		));
	  		}
	  		if($key['Tweet']['verified'] == 0 && strtotime($key['Tweet']['time']) > time()) {
	  				echo "<span style='color: red'>*Tweet will not be sent until verified</span>";
	  			}?>
	  </td>
	  <td>
	  	<?php echo $key['Tweet']['first_name']; ?>
	  </td>
	  <td class='tweetbody' id=<?php echo $key['Tweet']['id']?>>
	  	<div class='notediting'><?php echo $key['Tweet']['body']; ?></div>
	  	<?php echo $this->Form->textarea('body', array(
	  		'class' => 'editing', 
	  		'value' => $key['Tweet']['body'], 
	  		'name' => 'data[Tweet]['.$key['Tweet']['id'].'][body]', 
	  		'label' => false, 
	  		'style' => 'display: none',
			'maxlength' => '140')); ?> 
	  </td>
	  <td>
	  	<?php echo $this->Form->input('verified', array(
	  	'type' => 'checkbox', 
	  	'value' => 1, 
	  	'label' => false, 
	  	'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified]', 
	  	$checked, 
	  	$disabled,
	  	'class' => 'TwitterVerified',
	  	'id' => $key['Tweet']['id']));?>
	  </td>
	  <td class="color" id=<?php echo $key['Tweet']['id']?>>
		  <div class='color verified' style='color: <?echo $color;?>'><?php if ($key['Tweet']['client_verified'] == 0 && $key['Tweet']['verified'] == 0) { ?>
		  Red <?php } elseif ($key['Tweet']['client_verified'] == 0 && $key['Tweet']['verified'] == 1) {?>
		  Amber <?php } elseif ($key['Tweet']['client_verified'] == 1 && $key['Tweet']['verified'] == 1) {?>
		  Green <?php } ?></div>
	  </td>
	  <td>
	  	<?php echo $this->Form->button('Delete', array('type' => 'button', 'class' => 'delete', 'id' => $key['Tweet']['id'])); ?>
	  </td>
	  <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['Tweet']['id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][id]'));
	  		echo $this->Form->input('verfied_by', array(
	  		'type' => 'hidden', 
	  		'value' => '', 
	  		'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified_by]', 
	  		'class' => 'verifiedby', 
	  		'id' => $key['Tweet']['id'] . '_' . $this->Session->read('Auth.User.first_name')));?>
	</tr>
	<?php } ?>
</table>

<?php echo $this->Form->end('Go'); ?></td></tr>
</table>

<script>
//Hiding and showing tweet body input on click
			$("#table").on("click", ".tweetbody", function() {
				id = $(this).attr('id');
				$("#" + id + " .notediting").hide();
				$("#" + id + " .editing").show();
				$("#" + id + " .editing").focus();
			});

			$("#table").on("click", ".time", function() {
				id = $(this).attr('id');
				if ($("#" + id + " .schedule").length) {
						$("#" + id + " .notediting").hide();
				}
				$("#" + id + " .schedule").show();
				$("#" + id + " .schedule").focus();
				$("#" + id + " .schedule").css("margin-bottom", "1em");
			});

			$(".editing").blur(function(){
				id = $(this).parent().attr('id');
				$("#" + id + " .editing").hide();
				value = $("#" + id + " .editing").val();
				$("#" + id + " .notediting").text(value)
				$("#" + id + " .notediting").show();
			});

			$(".schedule").blur(function(){
				id = $(this).parent().parent().attr('id');
				$("#" + id + " .schedule").hide();
				value = $("#" + id + " .schedule").val();
				$("#" + id + " .notediting").text(value)
				$("#" + id + " .notediting").show();
			});
</script>