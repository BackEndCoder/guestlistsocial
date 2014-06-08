<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<?php
echo $this->Html->script('jquery.tablesorter');
echo $this->Session->flash('auth');
?>
<b>To be verified:</b>
<table>
<tr><td><?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'emptySave')), 'id' => 'edit'));?>
<table id="table">
<thead>
    <th><? echo $this->Paginator->sort('timestamp', 'Schedule');?></th>
    <th><? echo $this->Paginator->sort('screen_name', 'Account');?></th>
    <th><? echo $this->Paginator->sort('first_name', 'Written By');?></th>
    <th><? echo $this->Paginator->sort('body', 'Tweet');?></th>
    <th>Verified</th>
    <th>Status</th>
    <th></th>
</thead>
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
                echo '';
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
            }?>
      </td>
      <td>
        <? echo $key['Tweet']['screen_name']; ?>
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
            'id' => $key['Tweet']['id'] . '_' . $this->Session->read('Auth.User.first_name')));
            echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $key['Tweet']['user_id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][user_id]'));
            echo $this->Form->input('account_id', array('type' => 'hidden', 'value' => $key['Tweet']['account_id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][account_id]'));?>
    </tr>
    <?php } ?>
</table>

<?php echo $this->Form->end('Go'); ?></td></tr>
<tr><td>
<?echo $this->Paginator->numbers();?>
</td></tr>
</table>
<?php echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php echo $this->Html->link('Logout', '/users/logout');?>

<script>
$(document).ready(function() { 
        /*$("#table").tablesorter({
            dateFormat: "uk",
            headers: { 
            4: { 
                sorter: false 
            }, 
            5: { 
                sorter: false 
            } 
            }
        }); */

        $("#table").on("click", ".delete", function() {
            id = $(this).attr('id');
            $.ajax({url: "/twitter/delete/" + id, success: function() {
            window.location.reload(true);}});
        });
});
</script>