<div class="password_checker<?php if(defined('EMPLOYEE_CREATOR'))echo' password_checker_color_white' ?>">
	<H3>Votre Mot de pass contient : </H3>
	<div><p><span id="12-character">🟥</span> 12 character minimum</p></div>
	<div><p><span id="1-number">🟥</span> 1 Chiffre</p></div>
	<div><p><span id="1-uppercase">🟥</span> 1 Majuscule</p></div>
	<div><p><span id="1-lowercase">🟥</span> 1 Minuscule</p></div>
	<div><p><span id="1-specialcase">🟥</span> 1 caractère spécial</p></div>

	<?php if(defined('FORGOT_PASSWORD')): ?>
	<input type='submit' name='submit' value='Modifier le mot de passe'>
	<?php endif; ?>
</div>