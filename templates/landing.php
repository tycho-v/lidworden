<h1>Lid worden</h1>

<p>
  Mooi, je wilt lid worden! Vul onderstaand formulier in, en klik <em>Opslaan en Betalen</em>. 
  Je komt dan bij een bevestigingspagina, waarmee je de iDeal betaling kunt afronden bij je eigen bank.<br/>
  Wij krijgen een mail, kijken alles met de hand na en sturen je per mail een update van je lidmaatschap, dat kan dus eventjes duren.
</p>

<?php if ($flash['error']): ?>
  <div class="messages error"><?php print $flash["error"] ?></div>
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
    <div class="two columns">
      <label>Kosten:</label><span class="big">€ <?php printf("%0.2f", $default_amount) ?></span>
    </div>
    <div class="two columns">
      <label>Betalen met:</label></br>
      <img src="/css/ideal.png" alt="iDeal logo"/>
    </div>
  </div>
  <div class="submit">
    <input type="submit" class="big" value="Opslaan en betalen">
  </div>
</form>
