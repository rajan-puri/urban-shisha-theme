<?php
defined( 'ABSPATH' ) || exit;
?>
<!-- Age Confirmation Gate -->
<dialog class="age-dialog" id="age-dialog" aria-labelledby="age-title">
  <div class="age-panel">
    <span class="wordmark">URBAN<span>SHISHA</span></span>
    <p class="eyebrow">A CONSIDERED COLLECTION. FOR ADULTS ONLY.</p>
    <h2 id="age-title">GOOD TO<br>SEE YOU.</h2>
    <p>You must be 18 or older to explore Urban Shisha.</p>
    <div class="age-actions">
      <button type="button" class="button close-dialog" id="age-accept">I’m 18 or older <svg><use href="#i-arrow"/></svg></button>
      <button type="button" class="age-decline" id="age-decline">I’m under 18</button>
    </div>
    <p class="age-declined" id="age-declined" hidden>This store is for adults aged 18 and over.</p>
    <small>Smoking is injurious to health.</small>
  </div>
</dialog>

<!-- Toast notification element -->
<div class="toast" id="toast" role="status" aria-live="polite" aria-atomic="true"></div>

<!-- Site Footer -->

