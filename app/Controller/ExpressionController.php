<?php
App::uses('AppController', 'Controller');

/**
 * Expression Controller
 *
 * @property Department $Department
 * @property Member $Member
 * @property Device $Device
 * @property ExpressionReport $ExpressionReport
 */
class ExpressionController extends AppController
{
    public function index()
    {
        $datas = $this->ExpressionReport->find('all');
        $this->set('datas', $datas);
    }

    public function readExpressionReport()
    {
        $this->autoRender = false;

        $secondInterval = 5;
        //SQL example
        //select expression, sum(score) as score, avg(degree)::int as degree from expression_reports where regist_time >= now() - interval '1000 seconds' and regist_time <= now() group by expression order by expression;
        $params = array(
            'fields' => array('expression', 'sum(score) as "ExpressionReport__score"', 'avg(degree)::int as "ExpressionReport__degree"'),
            'conditions' => array("ExpressionReport.regist_time > now() - interval '" . $secondInterval . " seconds'", 'ExpressionReport.regist_time <= now()'),
            'group' => array('ExpressionReport.expression'),
            'order' => array('ExpressionReport.expression')
        );
        $result = $this->ExpressionReport->find('all', $params);

        /*
         * array's key is expression code
         * 	HVC_EX_NEUTRAL = 1;
         * 	HVC_EX_HAPPINESS = 2;
         *  HVC_EX_SURPRISE = 3;
         *  HVC_EX_ANGER = 4;
         * 	HVC_EX_SADNESS = 5;
         */
        $now = new DateTime();
        $now = $now->getTimestamp();
        $reports = array(
            '1' => array('now' => $now, 'score' => 0, 'degree' => 0),
            '2' => array('now' => $now, 'score' => 0, 'degree' => 0),
            '3' => array('now' => $now, 'score' => 0, 'degree' => 0),
            '4' => array('now' => $now, 'score' => 0, 'degree' => 0),
            '5' => array('now' => $now, 'score' => 0, 'degree' => 0)
        );

        foreach ($result as $row) {
            $exReport = $row['ExpressionReport'];
            $reports[$exReport['expression']] = array('now' => $now, 'score' => $exReport['score'], 'degree' => $exReport['degree']);
        }
        $json = json_encode($reports);
        return new CakeResponse(array('body' => $json));
    }

    public function readDegreeReport()
    {
        $this->autoRender = false;
        $secondInterval = 5;

        $date = new DateTime();
        $now = $date->getTimestamp();
        $toTime = $date->format('Y-m-d H:i:s');
        $interval = new DateInterval('PT' . $secondInterval . 'S');
        $fromTime = $date->sub($interval)->format('Y-m-d H:i:s');

        //TODO １SQLで取得したい
        $params = array(
            'fields' => array('sum(degree) as "ExpressionReport__degree"'),
            'conditions' => array("ExpressionReport.regist_time > '" . $fromTime . "'", "ExpressionReport.regist_time <= '" . $toTime . "'", 'ExpressionReport.degree > 0'),
        );
        $result = $this->ExpressionReport->find('first', $params);
        $positiveScore = $result['ExpressionReport']['degree'];

        $params = array(
            'fields' => array('abs(sum(degree)) as "ExpressionReport__degree"'),
            'conditions' => array("ExpressionReport.regist_time > '" . $fromTime . "'", "ExpressionReport.regist_time <= '" . $toTime . "'", 'ExpressionReport.degree < 0'),
        );
        $result = $this->ExpressionReport->find('first', $params);
        $negativeScore = $result['ExpressionReport']['degree'];

        $positiveParcent = 0;
        $negativeParcent = 0;
        if ($positiveScore > 0 && $negativeScore > 0) {
            $positiveParcent = (int)($positiveScore * 100 / ($positiveScore + $negativeScore));
            $negativeParcent = 100 - $positiveParcent;

        } elseif ($positiveScore > 0 && is_null($negativeScore)) {
            $positiveParcent = 100;

        } elseif (is_null($positiveScore) && $negativeScore > 0) {
            $negativeParcent = 100;
        }

        $report = array('now' => $now, 'positiveParcent' => $positiveParcent, 'negativeParcent' => $negativeParcent);
        $json = json_encode($report);
        return new CakeResponse(array('body' => $json));
    }

    public function add()
    {
        $this->autoRender = false;
        $responseJsonData = null;
        if ($this->request->is('post') || $this->request->is('get')) {
            $device = $this->Device->find('first', array('conditions' => array('Device.uuid' => $this->request->data('uuid'))));

            if (!empty($device)) {
                $memberId = $device['Member']['id'];
                $memberId = strval($memberId);
                $result = $this->ExpressionReport->save(
                    array(
                        'member_id' => $memberId,
                        'expression' => $this->request->data('expression'),
                        'score' => $this->request->data('score'),
                        'degree' => $this->request->data('degree')
                    ));
                $responseJsonData = parent::getJsonResponseData(200, 'Your report added.');
            } else {
                $responseJsonData = parent::getJsonResponseData(400, 'uuid did not found.');
            }
            $json = json_encode($responseJsonData);
            return new CakeResponse(array('body' => $json));
        }
    }
}
