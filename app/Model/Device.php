<?php
App::uses('AppModel', 'Model');

class Device extends AppModel{
    public $name = 'Device';
    public $belongsTo = 'Member';

} 