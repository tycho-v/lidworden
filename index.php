<?php
require 'Slim/Slim.php';
require 'config.php';
require 'ideal.php';

$app = new Slim(array(
  'templates.path' => './templates'
));

$app->get('/', 'get_landing');
$app->post('/pirate', 'add_pirate');
$app->get('/success', 'get_success');
$app->get('/error', 'get_error');

$app->run();

/***************************************************************************
 *                                Callbacks                                *
 ***************************************************************************/
function get_landing() {
}

/**
 */
function add_pirate() {
  // @TODO: lookup: sanitize?
  $pirate = Slim::getInstance()->request()->params(); //@TODO: lookup proper reference!!

  if (not(
    validate('initials', FALSE) &&
    validate('name', FALSE) &&
    validate('email', FALSE, '') && //@TODO simple email regexp
    validate('address', FALSE) &&
    validate('city', FALSE) )) {

    flash(); //@TODO lookup slim flash messages.
    redirect_to(); //@TODO lookup slim redirection.
  }

  $pirate = write_pirate($pirate);
  write_mail();
  //Prepare form for payment
  $ideal = new Ideal(MERCHANT_ID, SUB_ID, HASH_KEY, AQUIRER_NAME, AQUIRER_URL);

  $ideal->amount = DEFAULT_AMOUNT;
  $ideal->order_id = $pirate->id;
  $ideal->amount = (float) AMOUNT;
  $ideal->order_description = "
:w
:q
:wq
:wq
:wq


    // render form.
  render( //@TODO lookup format of render
    'pirate.php',
    array($ideal->hidden_form(),
  );
}

function get_success() {
}

function get_error() {
}

/***************************************************************************
 *                                Utilities                                *
 ***************************************************************************/
/**
 * validate a field.
 * @param $name, the name of the field as found in request.
 * @param $allow_blank, set to false if a field with only whitespace is allowed.
 * @param $format, provide an optional regular expression for string-format validation.
 */
function validate($name, $allow_blank = FALSE, $format = '') {
  $value = $request->fields(); //@TODO lookup.
  return FALSE
}

/*
 * Database:
 *   mysql> show columns from pirates;
 *   +-----------+--------------+------+-----+---------+----------------+
 *   | Field     | Type         | Null | Key | Default | Extra          |
 *   +-----------+--------------+------+-----+---------+----------------+
 *   | pirate_id | int(11)      | NO   | PRI | NULL    | auto_increment |
 *   | status    | varchar(32)  | NO   |     |         |                |
 *   | email     | varchar(255) | NO   |     |         |                |
 *   | initials  | varchar(255) | NO   |     |         |                |
 *   | name      | varchar(255) | NO   |     |         |                |
 *   | address   | text         | NO   |     | NULL    |                |
 *   | city      | varchar(255) | NO   |     |         |                |
 *   +-----------+--------------+------+-----+---------+----------------+
 */
function write_pirate($pirate) {
  $sql = "INSERT INTO pirates (status, email, initials, name, address, city) VALUES (:name, :email, :initials, :name, :address, :city)";
  try {
    $db = get_connection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("name", $pirate->name);
    $stmt->bindParam("email", $pirate->email);
    $stmt->bindParam("country", $pirate->initials);
    $stmt->bindParam("region", $pirate->name);
    $stmt->bindParam("year", $pirate->address);
    $stmt->bindParam("description", $pirate->city);
    $stmt->execute();
    $pirate->id = $db->lastInsertId();
    $db = NULL;
    return $pirate;
  } catch(PDOException $e) {
    return $e->getMessage();
  }
  return NULL;
}

// @TODO fetch from config instead.
// @TODO enforce singleton.
function get_connection() {
  $dbhost="127.0.0.1";
  $dbuser="root";
  $dbpass="";
  $dbname="ship";
  $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbh;
}
