<div></div>
<table>
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
?><tr><td><?
echo $this->Form->create('currentmonth', array('url' => array('controller' => 'twitter', 'action' => 'admin'), 'id' => 'monthForm'));
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
    'onchange' => 'this.form.submit()'
    ));
echo $this->Form->end();

?></td></tr>
<tr>
<td> 
<? echo $this->Form->button('Shorten URLs', array('id' => 'shortIt1', 'class' => 'urlSubmit1', 'type' => 'button'));
echo $this->Form->create('Tweet', array('url' => '/editorial_calendars/editcalendartweet', 'id' => 'submitTweets'));
?>

<?php if (!empty($calendar)) { ?>
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
            echo '<td class="nopadding">' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')) . '</td>';
            $body = '';
            $firstName = $key2['first_name'];
            $verified = $key2['verified'];
            break;
        } else {
            $value2 = '';
            $value1 = $testid;
            $id = '';
            $body = '<td class="nopadding">' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')) . '</td>'; 
            $firstName = '';
            $verified = 0;
        }
    }

    if ($key1['Tweet'] == false) {
        $value2 = '';
        $value1 = $testid;
        $id = '';
        $body = '<td class="nopadding">' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')) . '</td>'; 
        $firstName = '';
        $verified = 0;
    }


        if ($verified == 1) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

    echo $body;
    echo '<td class="writtenBy">' . $firstName . '</td>';
    echo '<td class="verified">' . $this->Form->input('verified', array('type' => 'checkbox', 'label' => false, 'name' => 'data[Tweet]['.$value1.'][verified]', $checked)) . '</td>'; 
    echo '<td class="scheduled">' . date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])) . '</td>';
    echo $this->Form->input('timestamp', array('type' => 'hidden', 'value' => date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])), 'name' => 'data[Tweet]['.$value1.'][timestamp]'));
    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $id, 'name' => 'data[Tweet]['.$value1.'][id]'));
    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$value1.'][calendar_id]'));
    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['team_id'], 'name' => 'data[Tweet]['.$value1.'][team_id]'));
    echo '</tr>';
}
}
?>
</table>
<? echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'Save', 'value' => 'Save')); }?>
</td></tr>
</table>


<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () {
            $('#table1').on('click', '#tweetsubmit', function() {
                $('#submitTweets').submit();
            });
            $('#table1').on('change', '#monthSelector', function() {
                $('#monthForm').submit(); 
            });
            $("#shortIt1").click(function () {
                //$("#shortUrlInfo").html("<img src='images/loading.gif'/>");
                regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
                $(".editing").each(function() {
                    var longUrlLink = $(this).val().match(regex);
                    //split = longUrlLink.split(",");
                    //alert(split[1]);
                    var $this = $(this);
                    jQuery.urlShortener({
                        longUrl: longUrlLink,
                        success: function (shortUrl) {
                            $this.val($this.val().replace(longUrlLink, shortUrl));
                        },
                        error: function(err) {
                            $("#shortUrlInfo").html(JSON.stringify(err));
                        }
                    });

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
    width: 10%;
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
#table tr {
    height: 10px;
}
.writtenBy {
    width: 10%;
}
.scheduled {
    width: 15%;
}
.verified {
    width: 10%;
}
</style>