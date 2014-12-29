<?php
App::uses('AppModel', 'Model');

class Member extends AppModel{
    public $name = 'Member';
    public $belongsTo = 'Department';
    public $hasMany = array('Device', 'ExpressionReport');
} 