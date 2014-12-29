<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $uses = array('Department', 'Member', 'Device', 'ExpressionReport');

    public $jsonResponseData = array('status' => 'success', 'code' => '200', 'msg' => '');

    /**
     * @param $code
     * @param $msg
     * @return array
     */
    public function getJsonResponseData($code, $msg)
    {
        switch ($code) {
            case 200:
                $data = array('status' => 'success', 'code' => $code, $msg);
                break;
            case 400:
                $data = array('status' => 'failure', 'code' => $code, $msg);
                break;
            default:
                $data = array();
        }
        return $data;
    }
 }
