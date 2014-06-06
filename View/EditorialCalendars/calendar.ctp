    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
    <script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
    <?php echo $this->Html->script('charCount');
    echo $this->Html->script('jquery-ui-1.10.3.custom'); 
    echo $this->Html->script('jquery-ui-timepicker-addon');
    echo $this->Html->script('jquery.urlshortener');
    echo $this->Html->script('mindmup-editabletable');
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
if ($this->Session->read('access_token.account_id') !== null) {
echo $this->Form->create('calendar_activated');
echo $this->Form->input('calendar_activated', array(
    'type' => 'select',
    'label' => 'Activate Editorial Calendars for your team?',
    'options' => array(
        0 => 'No',
        1 => 'Yes'),
    'onchange' => 'this.form.submit()',
    'selected' => $this->Session->read('Auth.User.calendar_activated')));
echo $this->Form->end();
//Table
echo $this->Form->create('Calendar', array('url'=>$this->Html->url(array('controller'=>'editorial_calendars', 'action'=>'calendarsave')), 'id' => 'edit'));?>
<table id="table1">
<th class="tableheader"></th>
<th class="tableheader">Monday</th>
<th class="tableheader">Tueday</th>
<th class="tableheader">Wednesday</th>
<th class="tableheader">Thursday</th>
<th class="tableheader">Friday</th>
<th class="tableheader">Saturday</th>
<th class="tableheader">Sunday</th>


<?
foreach ($calendar as $key) {?>
    <tr class="topic">
        <td><b><? echo $this->Form->input('Time', array('value' => $key['EditorialCalendar']['time'], 'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .'][time]')); ?></b></td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'topic')) {
                    echo '<td>' . $this->Form->input($value1, array(
                        'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']',
                        'value' => $value1,
                        'label' => false)) . '</td>';
                }
            }
        }?>
    </tr>
    <tr class="content-type">
        <td><b>Content type</b></td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'content')) {
                    echo '<td>' . $this->Form->input($value1, array(
                        'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']',
                        'value' => $value1,
                        'label' => false)) . '</td>';
                }
            }
        }?>
    </tr>
    <tr class="notes">
        <td><b>Notes</b></td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'notes')) {
                    echo '<td>' . $this->Form->input($value1, array(
                        'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']',
                        'value' => $value1,
                        'label' => false)) . '</td>';
                }
            }
        }?>
    </tr>
    <tr style="height:20px"><td><?php echo $this->Html->Link('delete', array('controller' => 'editorial_calendars', 'action' => 'deletecalendar', $key['EditorialCalendar']['id'])); ?></td></tr>
<?
echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['EditorialCalendar']['id'], 'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .'][id]'));
}
?>
</table>
<?php 
echo $this->Html->Link('Add', array('controller' => 'editorial_calendars', 'action' => 'addCalendar'));
echo $this->Form->end('Go'); ?>

<? 
$base = strtotime(date('Y-m',time()) . '-01 00:00:01');
if (!isset($months)) {
    $months = 0;
}
$daysinmonth = (int)date('t', strtotime('+' . $months . ' month', $base));
$days = array();
$month = date('m', strtotime('+' . $months . ' month', $base));
if ($months == 0) {
    $day = date('d');
} elseif ($months !== 0) {
    $day = 1;
} 
$year = date('Y');

$count = $daysinmonth - $day;
for ($i=$day; $i<=$daysinmonth; $i++) {
    $days[date('d-m-Y',mktime(0,0,0,$month,$i,$year))] = date('l',mktime(0,0,0,$month,$i,$year));
}

echo $this->Form->input('Select Month', array(
    'options' => array(
        0 => date('F Y', strtotime('+0 month', $base)),
        1 => date('F Y', strtotime('+1 month', $base)),
        2 => date('F Y', strtotime('+2 month', $base)),
        3 => date('F Y', strtotime('+3 month', $base)),
        4 => date('F Y', strtotime('+4 month', $base)),
        5 => date('F Y', strtotime('+5 month', $base))
        ),
    'selected' => $months,
    'id' => 'monthSelector',
    'onchange' => 'window.location.replace("/twitter/calendar/" + this.value)'
    ));

echo $this->Form->create('Tweet', array('url' => array('controller' => 'editorial_Calendars', 'action' => 'editcalendartweet'), 'id' => 'submitTweets'));
?>

<table>
<tr>
<th></th>
<th>Tweet</th>
<th>Written By</th>
<th>Verified</th>
<th>Scheduled</th>
</tr>
<?
$testid = 1;
foreach ($days as $key => $value) { ?>
<tr>
<th class='day'><b> <? echo $value; ?></b></th>
<th class='day'></th>
<th class='day'></th>
<th class='day'></th>
<th class='day'></th>
</tr>
<?php
foreach ($calendar as $key1) {
    $testid = $testid + 1;
    echo '<tr>';
    echo '<td class="topic"><b>' . $key1['EditorialCalendar'][strtolower($value) . '_topic'] . '</b></td>';

    foreach ($key1['Tweet'] as $key2) {
        if ($key2['time'] === date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time']))) {
            $value2 = $key2['body'];
            $value1 = $testid;
            $id = $key2['id'];
            echo '<td>' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]')) . '</td>';
            $body = '';
            $firstName = $key2['first_name'];
            $verified = $key2['verified'];
            break;
        } else {
            $value2 = '';
            $value1 = $testid;
            $id = '';
            $body = '<td>' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]')) . '</td>'; 
            $firstName = '';
            $verified = 0;
        }
    }

    if ($key1['Tweet'] == false) {
        $value2 = '';
        $value1 = $testid;
        $id = '';
        $body = '<td>' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]')) . '</td>'; 
        $firstName = '';
        $verified = 0;
    }


        if ($verified == 1) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

    echo $body;
    echo '<td>' . $firstName . '</td>';
    echo '<td>' . $this->Form->input('verified', array('type' => 'checkbox', 'label' => false, 'name' => 'data[Tweet]['.$value1.'][verified]', $checked)) . '</td>'; 
    echo '<td>' . date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])) . '</td>';
    echo $this->Form->input('timestamp', array('type' => 'hidden', 'value' => date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])), 'name' => 'data[Tweet]['.$value1.'][timestamp]'));
    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $id, 'name' => 'data[Tweet]['.$value1.'][id]'));
    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$value1.'][calendar_id]'));
    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['team_id'], 'name' => 'data[Tweet]['.$value1.'][team_id]'));
    echo '</tr>';
}
}
?>
</table>
<? echo $this->Form->end('Save');}?>

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
<style>
#table1 {
    font-size: 100%;
}
.tableheader {
    background: #c4e6f1;
}
.topic {
    background: #b4ddb4!important;
}
.content-type {
    background: #ebc582!important;
}
.notes {
    background: #c2afd4!important;
}
.input.text {
    font-size: 65%!important;
}
#TweetBody {
    font-size: 100%;
}
.day {
    background: #6787e1;
}
</style>