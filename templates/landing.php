<h2>Lid worden</h2>

<p>
Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
no sea takimata sanctus est Lorem ipsum dolor sit amet.
</p>
<?php if ($flash['error']): ?>
  <?php print $flash["error"] ?>
<?php endif; ?>
<form action="pirate" method="post" accept-charset="utf-8">
  <div class="form-wrapper">
    <label for="initials">Voorletters:<span class="required-marker">*</span></label><input type="text" name="initials" value="" id="initials">
  </div>
  <div class="form-wrapper">
    <label for="name">Achternaam:<span class="required-marker">*</span></label><input type="text" name="name" value="" id="name">
  </div>
  <div class="form-wrapper">
    <label for="email">E-mailadres:<span class="required-marker">*</span></label><input type="email" name="email" value="" id="email">
  </div>
  <div class="form-wrapper">
    <label for="address">Adres:<span class="required-marker">*</span></label><textarea name="address" rows="3" cols="40"></textarea>
  </div>
  <div class="form-wrapper">
    <label for="city">Woonplaats:<span class="required-marker">*</span></label><input type="text" name="city" value="" id="city">
  </div>

  <div class="row">
    <div class="two columns big">
      <label>Kosten:</label></br>
      € <?php printf("%0.2f", $default_amount) ?>
    </div>
    <div class="two columns">
      <label>Betalen met:</label></br>
      <img src="ideal.png" alt="iDeal logo"/>
      <span class="ideal">iDeal</span>
    </div>
  </div>
  <div class="form-wrapper">
    <input type="submit" value="Opslaan en betalen">
  </div>
</form>
