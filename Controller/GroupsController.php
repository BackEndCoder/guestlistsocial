<?php
class GroupsController extends AppController {
	public function add() {
		if($this->request->data) {
			$data = $this->request->data;
			$this->Group->save($data);
		}
	}
}
?>