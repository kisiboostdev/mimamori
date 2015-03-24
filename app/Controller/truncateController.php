<?php
App::uses('AppController', 'Controller');

/**
 * Init Controller
 * 
 * This controller is a tool for loading sample data to expressions table.
 * 
 * @property Department $Department
 * @property Member $Member
 * @property Device $Device
 * @property ExpressionReport $ExpressionReport
 */
class TruncateController extends AppController
{
    public function index()
    {
        $this->autoRender = false;

        //初期化
        $conditions = array("ExpressionReport.id >" => 0);
        $result = $this->ExpressionReport->deleteAll($conditions, false);
        if($result === true){
            print "ExpressionReports table initialized. <br>";
        }else{
            print "ExpressionReports table did not initialize.<br>";
        }
    }
}
