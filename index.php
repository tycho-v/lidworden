<?php
require 'Slim/Slim.php';
require 'config.inc';
require 'ideal.inc';
if (SECRETS_FILE) require SECRETS_FILE;

$app = new Slim(array(
  'templates.path' => './templates'
));
$app->add(new Slim_Middleware_SessionCookie(array(
  'expires' => '20 minutes',
  'path' => '/',
  'domain' => null,
  'secure' => false,
  'httponly' => false,
  'name' => 'slim_session',
  'secret' => 'CHANGE_ME',
  'cipher' => MCRYPT_RIJNDAEL_256,
  'cipher_mode' => MCRYPT_MODE_CBC
)));

$app->get('/', function () use ($app) {
  $app->render('_head.inc');
  $app->render(
    'landing.php',
    array(
      'default_amount' => DEFAULT_AMOUNT,
    )
  );
  $app->render('_footer.inc');
});

$app->post('/pirate', function () use ($app) {
  $pirate = $app->request()->params();
  $pirate["status"] = "pending";

  // @TODO: append messages somehow. Now only first error is returned.
  if (!valid($app, 'initials', FALSE, '', 'Voorletters zijn vereist') ||
    !valid($app,'name', FALSE, '', 'Naam is vereist') ||
    !valid($app,'email', FALSE, '/.+@.+\..+/', 'E-mailadres is niet correct') || # haha. @TODO mail regexp is way too simple. But, well.
    !valid($app, 'address', FALSE, '', 'Adres is vereist') ||
    !valid($app,'city', FALSE, '', 'Stad is vereist')) {
      $app->redirect("/");
  }

  $pirate = write_pirate($pirate);
  write_mail($pirate);
  // @TODO: if pirate['id'] is not set, writing to database failed. Redirect to error in that case.
  //Prepare form for payment
  $ideal = new Ideal(MERCHANT_ID, SUB_ID, HASH_KEY, AQUIRER_NAME, AQUIRER_URL);

  $ideal->order_id = $pirate["id"];
  $ideal->amount = (float) DEFAULT_AMOUNT;
  $ideal->order_description = "Piratenpartij lidmaatschap";
  $base = $app->request()->getUrl();
  $ideal->url_cancel  = "{$base}/error";
  $ideal->url_success = "{$base}/success";
  // render form.

  $app->render('_head.inc');
  $app->render(
    'pirate.php',
    array(
      'hidden_form' => $ideal->hidden_form(),
      'url'         => IDEAL_URL,
    )
  );
  $app->render('_footer.inc');
});

$app->get('/success', function () use ($app) {
  $app->render('_head.inc');
  $app->render(
    'success.php'
  );
  $app->render('_footer.inc');
});

$app->get('/error', function () use ($app) {
 $app->render('_head.inc');
 $app->render(
    'error.php'
  );
  $app->render('_footer.inc');
});

$app->run();

/***************************************************************************
 *                                Utilities                                *
 ***************************************************************************/
/**
 * valid a field.
 * @param $app, pass along the request for poking around and testing against.
 * @param $name, the name of the field as found in request.
 * @param $allow_blank, set to false if a field with only whitespace is allowed.
 * @param $format, provide an optional regular expression for string-format validation.
 * @param $message, optional message to be added to flash.
 */
function valid($app, $name, $allow_blank = FALSE, $format = '', $message = '') {
  $valid = true;
  $value = $app->request()->params($name);

  if ($allow_blank === FALSE && empty($value)) {
    $valid = false;
  }
  elseif (!empty($format) && !preg_match($format, $value)) {
    $valid = false;
  }

  if (!empty($message) && !$valid) {
    $app->flash("error", $message);
  }

  return $valid;
}

/**
 * Write a mail. Message is ugly and hardcoded.
 *  @TODO: unhardcode.
 * @param $pirate: associative arry containing a pirate record.
 * @returns status from php mail();
 */
function write_mail($pirate) {
  $from = "Lid Worden <noreply@piratenpartij.nl>";
  $to   = EMAIL_TO;
  $subject = "[Lid] Nieuw lid";

  $body=  $pirate["initials"] . "," . 
          $pirate["name"] . "," . 
	        $pirate["address"] . "," .
	        $pirate["city"] . "," . 
	        $pirate["email"];
  $body = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
  $body = wordwrap($body);
  $headers =
    'From: '. $from . "\r\n" .
    'Reply-To: '. $from . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

  //Possibly extend with smtp using PEAR mail. But PEAR Mail sucks just as hard as php mail().
  return mail($to, $subject, $body, $headers);
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
  $sql = "INSERT INTO pirates (status, email, initials, name, address, city) VALUES (:status, :email, :initials, :name, :address, :city)";
  try {
    $db = get_connection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("status", $pirate["status"]);
    $stmt->bindParam("name", $pirate["name"]);
    $stmt->bindParam("email", $pirate["email"]);
    $stmt->bindParam("initials", $pirate["initials"]);
    $stmt->bindParam("name", $pirate["name"]);
    $stmt->bindParam("address", $pirate["address"]);
    $stmt->bindParam("city", $pirate["city"]);
    $stmt->execute();
    $pirate["id"] = $db->lastInsertId();
    $db = NULL;
    return $pirate;
  } catch(PDOException $e) {
    print $e->getMessage();
  }
  return NULL;
}

// @TODO fetch from config instead.
// @TODO enforce singleton.
function get_connection() {
  $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbh;
}
