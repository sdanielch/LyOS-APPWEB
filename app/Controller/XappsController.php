<?php

App::uses('AppController', 'Controller');

class XappsController extends AppController
{
	public $layout = "ajax";
	public function index() {
		$this->set('title_for_layout', "Inicio");

	}

	public function controlpanel() {

	}
}
