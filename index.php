<?php
require 'Slim/Slim.php';
require 'config.php';
require 'ideal.php';

$app = new Slim(array(
  'templates.path' => './templates'
));

$app->get('/', 'get_landing');
$app->post('/lid', 'add_lid');
$app->get('/success', 'get_success');
$app->get('/error', 'get_error');

$app->run();


function get_landing() {
}

/**
 * Database:
    mysql> show columns from members;
    +-----------+--------------+------+-----+---------+----------------+
    | Field     | Type         | Null | Key | Default | Extra          |
    +-----------+--------------+------+-----+---------+----------------+
    | member_id | int(11)      | NO   | PRI | NULL    | auto_increment |
    | status    | varchar(32)  | NO   |     |         |                |
    | email     | varchar(255) | NO   |     |         |                |
    | initials  | varchar(255) | NO   |     |         |                |
    | name      | varchar(255) | NO   |     |         |                |
    | address   | text         | NO   |     | NULL    |                |
    | city      | varchar(255) | NO   |     |         |                |
    +-----------+--------------+------+-----+---------+----------------+
 */
function add_lid() {
  //sanitize
  //format of fields.
  //requirements.

  //Write to database.
  //Prepare mail.
  //Send or Queue mail.

  //Prepare form for payment
  // render form.
}

function get_success() {
}

function get_error() {
}
