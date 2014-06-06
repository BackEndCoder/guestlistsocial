<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<?php echo $this->Html->script('jquery-ui-1.10.3.custom'); 
echo $this->Html->css('calendar');?>
<script src="http://code.highcharts.com/highcharts.js"></script>
<script>
$(function () { 
    $('#graph').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Followers'
        },
        xAxis: {
            categories: [<?php echo $dates; ?>]
        },
        yAxis: {
            title: {
                text: 'Number of followers'
            }
        },
        series: [{
            name: <?php echo '\''.$selected.'\''; ?>,
            data: [<?php echo $followerdata; ?>]
        }]
    });

    $('#dbdfollower').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Day-By-Day Follower Change'
        },
        xAxis: {
            categories: [<?php echo $dates; ?>]
        },
        yAxis: {
            title: {
                text: 'Number of followers'
            }
        },
        series: [{
            name: <?php echo '\''.$selected.'\''; ?>,
            data: [<?php echo $dbdfollower; ?>]
        }]
    });
});
</script>
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
        echo $this->Form->input('from', array('class' => 'datepicker'));
        echo $this->Form->input('to', array('class' => 'datepicker'));
echo $this->Form->end('Go!');
?>
<?php
echo $dates . '<br>';
echo $followerdata . '<br>';
echo $dbdfollower . '<br>';
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
    	)); 
echo $this->Form->end();

?>
<p>
previous days statistics: <br /><br />

favourites: <?php echo $favourites; ?><br />
retweets: <?php echo $retweets; ?><br />
mentions: <?php echo $mentions; ?><br />
followers: <?php echo $followers; ?><br />
</p>

<div id="graph" style="width:50%; height:300px; float: left"></div>

<div id="dbdfollower" style="width:50%; height:300px; float: right"></div>