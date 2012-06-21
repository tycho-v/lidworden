<?php
/**
 * Ideal Utility class
 * 
 * **NOTE**: Class output has no security in XSS whatsoever. It merely escapes 
 * HTML to avoid broken HTML. But security is left to user of the class.
 */
class ClassName {
  // Default settings
  public $currency = 'EUR'; // Ideal only support payments in EURO.
  public $language_code = 'nl';
  public $payment_type = 'ideal';

  // Account settings
  public $merchant_id = '';
  public $subId = 0;
  public $hashKey = '';
  public $aquirer_name = '';
  public $aquirer_url = '';

  // Order settings
  public $amount = 0.00;
  public $order_id = '';
  public $order_description = '';
  public $url_cancel = '';
  public $url_error = '';
  public $url_success = '';

  public function amount_in_cents() {
    return (int) $this->amount * 100;
  }

  public function hidden_form() {
    $html =
        '<input type="hidden" name="merchantID" value="' . $this->merchant_id . '">'
      . '<input type="hidden" name="subID" value="' . $this->sub_id . '">'
      . '<input type="hidden" name="amount" value="' . $this->amount_in_cents() . '" >'
      . '<input type="hidden" name="purchaseID" value="' . htmlspecialchars($this->order_id) . '">'
      . '<input type="hidden" name="language" value="' . htmlspecialchars($this->language_code) . '">' // NL
      . '<input type="hidden" name="currency" value="' . htmlspecialchars($this->currency) . '">'
      . '<input type="hidden" name="description" value="' . htmlspecialchars($this->order_description) . '">'
      . '<input type="hidden" name="hash" value="' . $sHash . '">'
      . '<input type="hidden" name="paymentType" value="' . htmlspecialchars($this->payment_type) . '">'
      . '<input type="hidden" name="validUntil" value="' . $sValidUntil . '">'
      . '<input type="hidden" name="itemNumber1" value="1">'
      . '<input type="hidden" name="itemDescription1" value="' . htmlspecialchars($this->order_description) . '">'
      . '<input type="hidden" name="itemQuantity1" value="1">'
      . '<input type="hidden" name="itemPrice1" value="' . $iAmount . '">'
      . ($this->url_cancel ? '<input type="hidden" name="urlCancel" value="' . htmlspecialchars($this->url_cancel) . '">' : '')
      . ($this->url_success ? '<input type="hidden" name="urlSuccess" value="' . htmlspecialchars($this->url_success) . '">' : '')
      . ($this->url_error ? '<input type="hidden" name="urlError" value="' . htmlspecialchars($this->url_error) . '">' : '');
    return $html;
  }
}
