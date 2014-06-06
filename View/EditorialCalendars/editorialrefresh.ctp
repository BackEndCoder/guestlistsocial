<?echo $this->Form->create('Calendar', array('url'=>$this->Html->url(array('controller'=>'editorial_calendars', 'action'=>'calendarsave')), 'id' => 'edit'));?>
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
        <td><b><? echo $key['EditorialCalendar']['time']; ?></b></td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'topic')) {
                    echo '<td>' . $value1 . '</td>';
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
                    echo '<td>' . $value1 . '</td>';
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
                    echo '<td>' . $value1 . '</td>';
                }
            }
        }?>
    </tr>
    <tr>
    <td>
    </td>
    </tr>
<?
}
?>
</table>
<div id='hide'>
<? echo $this->Form->button('Hide editoral calendar', array('type' => 'button', 'class' => 'hide')); ?>
<? echo $this->Form->button('Show editoral calendar', array('type' => 'button', 'class' => 'show')); ?>
</div>
<script>
    $(document).ready(function () {
        $('.show').hide();
        $('#hide').on('click', '.hide', function() {
            $('#table1').hide();
            $('.hide').hide();
            $('.show').show();
        });
        $('#hide').on('click', '.show', function() {
            $('#table1').show();
            $('.show').hide();
            $('.hide').show();
        });
     });
</script>
<style>
#table1 {
    font-size: 100%;
}
#addTweetWrapper {
    width: 70%;
    float: left;
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