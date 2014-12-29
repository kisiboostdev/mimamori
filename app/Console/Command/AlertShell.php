<?php

/**
 * Ussage: path/to/app/Console/cake Alert
 *         ex) /var/www/html/mimamori/app/Console/cake Alert
 *
 * @property Department $Department
 * @property Member $Member
 * @property Device $Device
 * @property ExpressionReport $ExpressionReport
 */
class AlertShell extends AppShell
{
    public $uses = array('Department', 'Member', 'Device', 'ExpressionReport');

    public function main()
    {
        //ネガティブ系の感情(4,5)の直近平均数値が以下の閾値を超えれば通知
        $alertScore = 50;
        /*
         *  HVC_EX_NEUTRAL = 1;
         *  HVC_EX_HAPPINESS = 2;
         *  HVC_EX_SURPRISE = 3;
         *  HVC_EX_ANGER = 4;
         *  HVC_EX_SADNESS = 5;
         */
        $negativeExpressionArray = array(4, 5);
        $secondInterval = 600; //10分
        $params = array(
            'fields' => array('avg(score)::int as "ExpressionReport__score"', 'Member.name', 'Member.department_id', 'Member.id'),
            'conditions' => array(
                "ExpressionReport.expression in (" . implode(',', $negativeExpressionArray) . ")",
                "ExpressionReport.regist_time >= now() - interval '" . $secondInterval . " seconds'",
                'ExpressionReport.regist_time <= now()',
                "ExpressionReport.score > " . $alertScore),
            'group' => array('ExpressionReport.member_id', 'Member.name', 'Member.department_id', 'Member.id'),
            'order' => array('ExpressionReport.member_id')
        );
        //TODO Cakeな書き方でscore > 50 を一回で取る
        $result = $this->ExpressionReport->find('all', $params);

        $alertDepartments = array();
        foreach ($result as $row) {
            $score = $row['ExpressionReport']['score'];
            $member = $row['Member'];
            $memId = $member['id'];
            $memName = $member['name'];
            $departmentId = $member['department_id'];

            if ($score > $alertScore) {
                $alertDepartments[$departmentId][$memId] = array('departmentId' => $departmentId, 'memName' => $memName, 'score' => $score);
            }
        }

        if (count($alertDepartments)) {
            App::uses('HttpSocket', 'Network/Http');
            $httpSocket = new HttpSocket();
            $url = 'https://clojureybot.herokuapp.com/tdm2';
        }

        //部署毎に通知
        //TODO Modelに
        foreach ($alertDepartments as $alertDepartment) {
            $departmentId = null;
            $msg = '';
            foreach ($alertDepartment as $alertMember) {
                $departmentId = $alertMember['departmentId'];
                $memberName = $alertMember['memName'];
                $score = $alertMember['score'];
                $msg .= $memberName . 'さんのネガティブスコアが' . $score . 'です。';
            }
            $msg .= "至急、様子を確認しましょう♪";

            $params = array(
                'conditions' => array(
                    'NOT' => array('Member.twitter_uname' => null),
                    'Member.department_id' => $departmentId
                ),
                'order' => array('Member.id')
            );
            $alertToMembers = $this->Member->find('all', $params);

            foreach ($alertToMembers as $alertToMember) {
                $twitterUName = $alertToMember['Member']['twitter_uname'];
                $params = array(
                    'uname' => $twitterUName,
                    'msg' => $msg,
                    'infouname' => $twitterUName
                );
                $res = $httpSocket->get($url, array($params));
                // var_dump($res);
            }
        }
        exit;
    }
} 