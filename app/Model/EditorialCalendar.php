<?php
App::uses('AppModel', 'Model');
/**
 * EditorialCalendar Model
 *
 * @property Team $Team
 */
class EditorialCalendar extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'TwitterAccount' => array(
			'className' => 'TwitterAccount',
			'foreignKey' => 'twitter_account_id',
			'conditions' => '',
			'fields' => 'account_id',
			'order' => ''
		)
	);

	public $hasMany = array(
		'Tweet' => array(
			'className' => 'Tweet',
			'foreignKey' => 'calendar_id',
			'conditions' => '',
			//'fields' => 'account_id',
			'order' => 'Tweet.timestamp ASC'
		)
	);
}
