<?php
App::uses('AppModel', 'Model');

class ExpressionReport extends AppModel{
    public $name = 'ExpressionReport';
    public $belongsTo = 'Member';

} 