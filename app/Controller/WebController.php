<?php

App::uses('AppController', 'Controller');

class WebController extends AppController
{
	public function index() {
		$this->set('title_for_layout', "Inicio");

	}
}
