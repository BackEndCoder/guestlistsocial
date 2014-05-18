<?php
class CronTweet extends AppModel {

function beforeSave($options = array()) {
	App::uses('CakeSession', 'Model/Datasource');

    if (!empty($this->data[$this->alias]['time'])) {

        $this->data[$this->alias]['timestamp'] = strtotime($this->data[$this->alias]['time']);
    }

    return true;
}

}