<?php
App::uses('AppController', 'Controller');

/**
 * Top Controller
 *
 * This controller is for top page.
 * 
 * @property Department $Department
 * @property Member $Member
 * @property Device $Device
 * @property ExpressionReport $ExpressionReport
 */
class TopController extends AppController {
	public function index(){
		$this->autoLayout = false;
	}



}
