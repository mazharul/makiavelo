<?php

 class UserController extends ApplicationController {

 	public function newAction() {
		$entity = new User();
		$this->render(array("entity" => $entity));
	}

	public function deleteAction() {
		delete_user($this->request->getParam("id"));
		$this->flash->success("Delete successfull!");
		$this->redirect_to(user_list_path());
	}

	public function editAction() {
		$tb = load_user($this->request->getParam("id"));

		$this->render(array("entity" => $tb));
	}

	public function showAction() {
		$id = $this->request->getParam("id");
		$ent = load_user($id);
		$this->render(array("entity" => $ent));
	}

	public function createAction() {
		$entity = new User();
		$entity->load_from_array($this->request->getParam("user"));
		$entity->role = "user";
		if(save_user($entity)) {
			login_user($entity->email, $entity->password);
			$this->redirect_to(home_root_path_path());
		} else {
			$this->render(array("entity" => $entity), "new");
		}
	}

	public function indexAction() {
		$entity_list = list_user();
		$this->render(array("entity_list" => $entity_list));
	}


 }


?>