	<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
	<script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
	<?php echo $this->Html->script('charCount');
	echo $this->Html->script('jquery-ui-1.10.3.custom'); 
	echo $this->Html->script('jquery-ui-timepicker-addon');
	echo $this->Html->script('jquery.urlshortener');
	echo $this->Html->css('calendar'); ?>

<style>
#AddTweet:disabled {opacity: .4}
</style>

<?php
echo $this->Session->flash('auth');
//Select Twitter Account
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
    	)); 
echo $this->Form->end();
if (isset($info[0]['TwitterAccount']['infolink'])) {
echo $this->Html->link('Info', $info[0]['TwitterAccount']['infolink'], array('target' => '_blank'));
} ?>

<?
echo $this->Html->link('Edit Info', '/twitter/info');
?>
<br/>
<br/>
<?php
//Add Tweet
echo $this->Form->create('Tweet', array('url' => array('controller' => 'twitter', 'action' => 'testing'), 'id' => 'submitTweet'));
		echo $this->Form->textarea('body', array('label' => false, 'type' => 'post', 'class' => 'ttt'));
		//URL Shortener
		echo $this->Form->button('Shorten URL', array('id' => 'shortIt', 'class' => 'urlSubmit', 'type' => 'button'));
echo $this->Form->end(array('id' => 'AddTweet', 'value' => 'AddTweet', 'class' => 'addTweet'));
?>
<div id='team'>
<table>
<th>My Team</th>
	<? foreach ($teamMembers as $key) { ?>
	<tr>
		<td>
			<? if ($key['User']['group_id'] == 1 || $key['User']['group_id'] == 5) {
				$admin = '<i> - admin </i>';
			} else {
				$admin = '';
			}
			echo $key['User']['first_name'] . $admin;?>
		</td>
	</tr>
	<? } ?>
	<?php if ($this->Session->read('Auth.User.Team.id') == 0) {echo '<tr><td>' . $this->Html->link('Part of a marketing team?', '/teams/manage') . '</td></tr>';}?>
</table>
<? if ($this->Session->read('Auth.User.Team.id') != 0) {echo '<small>Your team\'s code: ' . $this->Session->read('Auth.User.Team.hash') . '</small>'; }?>
</div>
<?php 
//Table
echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'edit')), 'id' => 'edit'));?>
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
			$color = 'Red';} ?>
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
	  		'label' => '', 
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
	  		'label' => '', 
	  		'style' => 'display: none')); ?> 
	  </td>
	  <td>
	  	<?php echo $this->Form->input('verified', array(
	  	'type' => 'checkbox', 
	  	'value' => 1, 
	  	'label' => false, 
	  	'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified]', 
	  	$checked, 
	  	'class' => 'TwitterVerified'));?>
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
	  <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['Tweet']['id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][id]'));?>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Form->end('Go'); ?>

<?php
//Select Twitter Account
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
    	)); 
echo $this->Form->end();?>

<?php echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php echo $this->Html->link('Logout', '/users/logout');?>

<!-- SCRIPTS -->
<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () { 
        	var options = { 
        		clearForm: true
			}; 
            // bind 'myForm' and provide a simple callback function 
            $('#submitTweet').ajaxForm(function(options) { 
 				$("#TweetBody").val("");
 				$("#table").load("/twitter/tablerefresh", function() {
 					$('.schedule').each(function(){
						$(this).datetimepicker({
    						dateFormat: 'dd-mm-yy',
    						altFormat: '@',
						});
    				});

 				});
			});

        	$("#table").on("change", ".TwitterVerified" , function() {
        		$("#table").css('opacity', '.4');
        		$('#edit').ajaxSubmit();
        		setTimeout(refresh, 100);//delaying the table refresh so that the form can successfully submit into the databases
        		function refresh() {
        			$('#table').load('/twitter/tablerefresh', function() {
  					$("#table").css('opacity', '1');
  					$('.schedule').each(function(){
						$(this).datetimepicker({
    						dateFormat: 'dd-mm-yy',
    						altFormat: '@',
						});
    				});
				});
        		};
        		
 			});
        	//Submit table form on delete button click
        	$("#table").on("click", ".delete", function() {
        		$("#table").css('opacity', '.4');
        		id = $(this).attr('id');
        		$.ajax({url: "/twitter/delete/" + id});
        		setTimeout(refresh1, 100);
        		function refresh1() {
	     			$('#table').load('/twitter/tablerefresh', function() {
	  					$("#table").css('opacity', '1');
	  					$('.schedule').each(function(){
							$(this).datetimepicker({
	    						dateFormat: 'dd-mm-yy',
	    						altFormat: '@',
							});
	    				});
					});
     			};
			});

			$("#TweetBody").charCount();
			//Hiding and showing tweet body input on click
			$("#table").on("click", ".tweetbody", function() {
				id = $(this).attr('id');
				$("#" + id + " .notediting").hide();
				$("#" + id + " .editing").show();
			});

			$("#table").on("click", ".time", function() {
				id = $(this).attr('id');
				if ($("#" + id + " .schedule").length) {
						$("#" + id + " .notediting").hide();
				}
				$("#" + id + " .schedule").show();
				$("#" + id + " .schedule").css("margin-bottom", "1em");
			});


			$('#AddTweet').attr('disabled','disabled');
			//disabing addtweet button if tweet is empty
			$('#TweetBody').bind('keyup', function() { 
				var nameLength = $("#TweetBody").val().length;

				if (0 < nameLength) {
				   $('#AddTweet').removeAttr('disabled');
				   if (nameLength > 140) {
				   	$('#AddTweet').attr('disabled','disabled');
				   }
				}
			});

			$('.schedule').each(function(){
				$(this).datetimepicker({
    				dateFormat: 'dd-mm-yy',
    				altFormat: '@',
				});
    		});

    		jQuery.urlShortener.settings.apiKey = 'AIzaSyC27e05Qg5Tyghi1dk5U7-nNDC0_wift08';
			$("#shortIt").click(function () {
    			//$("#shortUrlInfo").html("<img src='images/loading.gif'/>");
    			regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
    			var longUrlLink = $("#TweetBody").val().match(regex);
    			//split = longUrlLink.split(",");
    			//alert(split[1]);
    			jQuery.urlShortener({
        			longUrl: longUrlLink,
        			success: function (shortUrl) {
            			$("#TweetBody").val($("#TweetBody").val().replace(longUrlLink, shortUrl));
        			},
        			error: function(err) {
        				$("#shortUrlInfo").html(JSON.stringify(err));
        			}
    			});

			});
        });

</script>