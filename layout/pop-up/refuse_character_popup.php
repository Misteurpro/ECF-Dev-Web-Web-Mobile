<form class="refuse_pop-up" method="POST" action="/employee/approuver-personnage?submit-refusal">
	<div>
		<div>
		<label for="reason-textarea">Raison du refus :</label>
		</div>
		<textarea name="reason-textarea" id="reason-textarea" required></textarea>
		<input type="hidden" name="character_id" id="character_id" value="<?php echo $character_id ?>">
	</div>
	<div><button><a href="/employee/approuver-personnage">annuler</a></button> <button type="submit">soumettre</button></div>
</form>