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
echo $this->Html->link('Editorial Calendar', '/twitter/calendar/0');
?>
<br/>
<br/>
<div id='addTweetWrapper'>
<?php
//Add Tweet
echo $this->Form->create('Tweet', array('url' => array('controller' => 'twitter', 'action' => 'testing'), 'id' => 'submitTweet'));
		echo $this->Form->textarea('body', array('label' => false, 'type' => 'post', 'class' => 'ttt'));
		//URL Shortener
		echo $this->Form->button('Shorten URL', array('id' => 'shortIt', 'class' => 'urlSubmit', 'type' => 'button'));
echo $this->Form->end(array('id' => 'AddTweet', 'value' => 'AddTweet', 'class' => 'addTweet'));
?>
</div>
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
<? if ($this->Session->read('Auth.User.Team.id') != 0) {
	echo '<small>Your team\'s code: <br />' . $this->Session->read('Auth.User.Team.hash') . '</small><br />'; 
	if ($this->Session->read('Auth.User.group_id') == 1 || $this->Session->read('Auth.User.group_id') == 5) {
		echo '<small>' . $this->Html->link('Manage Team', '/teams/manageteam') . '</small> <br />';
		echo '<small>' . $this->Html->link('Manage Tweets', '/twitter/index') . '</small>';
	}
	}?>
</div>

<!--Table goes here -->
<table id='table'>
</table>
<table id='table1'>
</table>
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

        	
			<? if ($this->Session->read('Auth.User.calendar_activated') == 1) {
				if ($this->Session->read('access_token.account_id') !== null) {?>
				$('#table1').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
					$('#addTweetWrapper').load("/editorial_calendars/editorialrefresh");
				});
				<?}?>
			<?} else {?>
				$('#table').load('/twitter/tablerefresh', function() {
				});
			<?}?>
			
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

    		jQuery.urlShortener.settings.apiKey = 'AIzaSyC27e05Qg5Tyghi1dk5U7-nNDC0_wift08';
			$("#shortIt").click(function () {
				alert('test');
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