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
class InitController extends AppController
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
        /*
         *  HVC_EX_NEUTRAL = 1;
         *  HVC_EX_HAPPINESS = 2;
         *  HVC_EX_SURPRISE = 3;
         *  HVC_EX_ANGER = 4;
         *  HVC_EX_SADNESS = 5;
         */
        $expressionArray = array(1, 2, 3, 4, 5);
        for ($i = 0; $i <= 100; $i++) {
            $scoreArray[] = $i;
        }
        for ($i = -100; $i <= 100; $i++) {
            $degreeArray[] = $i;
        }

        $now = new DateTime();
        //$now->setTimeZone(new DateTimeZone('Asia/Tokyo'));
        $interval = new DateInterval('PT1S');

        for ($i = 0; $i < 3600; $i++) {
            $expression = $expressionArray[array_rand($expressionArray, 1)];
            $score = $scoreArray[array_rand($scoreArray, 1)];
            $degree = $degreeArray[array_rand($degreeArray, 1)];

            $result = $this->ExpressionReport->save(
                array(
                    'member_id' => 1,
                    'expression' => $expression,
                    'score' => $score,
                    'degree' => $degree,
                    'regist_time' => $now->add($interval)->format('Y-m-d H:i:s')

                ));
            $this->ExpressionReport->create();
        }
        print "Init Data has loaded.<br>";
        $now = new DateTime();
        $now->setTimeZone(new DateTimeZone('Asia/Tokyo'));
        print "Time:". $now->add($interval)->format('Y-m-d H:i:s');
    }
}
