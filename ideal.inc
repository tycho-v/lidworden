<?php
/**
 * Ideal Utility class
 * 
 * **NOTE**: Class output has no security in XSS whatsoever. It merely escapes 
 * HTML to avoid broken HTML. But security is left to user of the class.
 */
class Ideal {
  // Default settings
  public $currency = 'EUR'; // Ideal only support payments in EURO.
  public $language_code = 'nl';
  public $payment_type = 'ideal';

  // Account settings
  public $merchant_id = '';
  public $sub_id = 0;
  public $hash_key = '';
  public $aquirer_name = '';
  public $aquirer_url = '';

  // Order settings
  public $amount = 0.00;
  public $order_id = '';
  public $order_description = '';
  public $url_cancel = '';
  public $url_error = '';
  public $url_success = '';

  public function __construct($merchant_id, $sub_id, $hash_key, $aquirer_name, $aquirer_url) {
    $this->merchant_id    = $merchant_id;
    $this->sub_id         = $sub_id;
    $this->hash_key       = $hash_key;
    $this->aquirer_name   = $aquirer_name;
    $this->aquirer_url    = $aquirer_url;
  }

  public function amount_in_cents() {
    return (int) $this->amount * 100;
  }

  public function valid_untill() {
    # @TODO: return a valid datetime object instead.
    return date('Y-m-d\TG:i:s\Z', strtotime('+1 hour'));
  }

  public function hash() {
    $hash_string = $this->hash_key . $this->merchant_id . $this->sub_id
      . $this->amount_in_cents() . $this->order_id . $this->payment_type . $this->valid_untill()
      . '1' . $this->order_description . '1' . $this->amount_in_cents();
    $hash_string = html_entity_decode($hash_string);
    $hash_string = str_replace(array("\t", "\n", "\r", " "), '', $hash_string);

    return sha1($hash_string);
  }

  //@TODO remove and replace with utility functions. We should not build
  // HTML in a class, that is part of a view of an implementor.
  public function hidden_form() {
    $html =
        '<input type="hidden" name="merchantID" value="' . $this->merchant_id . '">'
      . '<input type="hidden" name="subID" value="' . $this->sub_id . '">'
      . '<input type="hidden" name="amount" value="' . $this->amount_in_cents() . '" >'
      . '<input type="hidden" name="purchaseID" value="' . htmlspecialchars($this->order_id) . '">'
      . '<input type="hidden" name="language" value="' . htmlspecialchars($this->language_code) . '">' // NL
      . '<input type="hidden" name="currency" value="' . htmlspecialchars($this->currency) . '">'
      . '<input type="hidden" name="description" value="' . htmlspecialchars($this->order_description) . '">'
      . '<input type="hidden" name="hash" value="' . $this->hash() . '">'
      . '<input type="hidden" name="paymentType" value="' . htmlspecialchars($this->payment_type) . '">'
      . '<input type="hidden" name="validUntil" value="' . $this->valid_untill() . '">'
      . '<input type="hidden" name="itemNumber1" value="1">'
      . '<input type="hidden" name="itemDescription1" value="' . htmlspecialchars($this->order_description) . '">'
      . '<input type="hidden" name="itemQuantity1" value="1">'
      . '<input type="hidden" name="itemPrice1" value="' . $this->amount_in_cents() . '">'
      . ($this->url_cancel ? '<input type="hidden" name="urlCancel" value="' . htmlspecialchars($this->url_cancel) . '">' : '')
      . ($this->url_success ? '<input type="hidden" name="urlSuccess" value="' . htmlspecialchars($this->url_success) . '">' : '')
      . ($this->url_error ? '<input type="hidden" name="urlError" value="' . htmlspecialchars($this->url_error) . '">' : '');
    return $html;
  }
}
