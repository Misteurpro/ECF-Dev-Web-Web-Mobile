console.log("ATTENTION! Veuillez ne jamais mettre de code que vous ne comprenez pas et ne partagez jamais votre cookie de session!");
console.log("Ces cookies pourraient être utilisés pour accéder à votre compte");

function loadDoc(character_id) {
	var xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {

	}
	};
	shared = document.getElementById("tag_"+character_id).checked;

	let url = ("/menu/mon-espace?share_status="+shared+"&character_id="+character_id);
	xhttp.open("POST", url, true);
	xhttp.send();
}

function show_comment_bar(islogin){
	if(islogin){
		let user_comment_div = document.getElementById("user_comment_div");
		user_comment_div.style.display = 'block';
	}
	else{
		window.location = "/login"
	}
}

function deleteCharacter(){
	var xhttp = new XMLHttpRequest();

	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		window.location = this.responseText;
	}
	};
	const urls = window.location;
	const params = new URLSearchParams(urls.search);
	const character_id = params.get('character')

	let url = ("/menu/page-personnage?character="+character_id+"&delete");
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send("confirm");
}
function deleteUser(admin = false){
	var xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		window.location = this.responseText;
	}
	};
	const urls = window.location;
	const params = new URLSearchParams(urls.search);
	const user_id = params.get('user')

	if(admin){
		status = "admin";
	}
	else{
		status = "employee"
	}

	let url = ("/"+status+"/liste-utilisateur?user="+user_id+"&delete");
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send("confirm");
}

function deleteSelfAccount(){
	var xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		console.log(this.responseText);
		if(this.responseText == "deleted")
		window.location = "/";
	}
	};

	let url = ("/menu/mon-espace/supprimer-le-compte");
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send("confirm");
}

function manageItem(item, deactivate = false, itself){
	var xhttp = new XMLHttpRequest();

	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			let state = this.responseText;
			console.log(itself)

			if(itself.dataset.isactive == "active"){
				itself.innerText = "Activer";
				itself.dataset.isactive = "disabled";
				itself.onclick = function (){ manageItem(item, false,itself); } ;
			}
			else if(itself.dataset.isactive == "disabled"){
				itself.innerText = "Desactiver"
				itself.dataset.isactive = "active";
				itself.onclick = function (){ manageItem(item, true,itself); } ;
			}
		}
	};

	let url = "/employee/liste-article?item="+item+"&enable"
	if(deactivate == true){
		url = "/employee/liste-article?item="+item+"&disable";
	}

	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send("confirm");
}

function deleteItem(item, card_id){
	var xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {

		console.log(this.responseText);
		if(this.responseText === "success"){
			card = document.getElementById(card_id);
			card.remove();
		}
	}
	};

	let url = ("/employee/liste-article?item="+item+"&delete");
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send("confirm");
}

function set_article_active(article){

	const index = active_article.indexOf(article.dataset.id);
	if (index > -1) { // only splice array when item is found
		active_article.splice(index, 1); // 2nd parameter means remove one item only
	}
	else{
		active_article.push(article.dataset.id);
	}
	change_active_class_article(article, active_article);
}

function change_active_class_article(obj_element, active_array_article){

	let elements = [];
	let all_obj = document.querySelectorAll(".article_obj");
	
	for (let i = 0; i < active_array_article.length; i++) {
		elements.push( document.querySelectorAll('[data-id="'+active_array_article[i]+'"]'));	
	}

	all_obj.forEach(obj => {
		obj.classList.remove("active-article")
	});

	elements.forEach(element => {
		if(!element[0].classList.contains("active-article")){
			element[0].classList.add("active-article")
		}
	});

}

function asign_articles(articles){
	var xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		window.location = this.responseText
	}
	};
	
	const urls = window.location;
	const params = new URLSearchParams(urls.search);
	const character_id = params.get('character')

	const body = new URLSearchParams();
	

	if(articles.length < 1){
		body.append("articles[]", "none")
	}
	else{
		articles.forEach(article => {
			body.append("articles[]", article);
		});
	}

	let url = ("/menu/page-personnage/modifier/selection-des-articles?character="+character_id);
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send(body.toString());
}

function suspendCharacter(unsuspend = false){
	var xhttp = new XMLHttpRequest();

		xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {

		window.location = this.responseText;
		
	}
	};
	const urls = window.location;
	const params = new URLSearchParams(urls.search);
	const character_id = params.get('character')

	let url = ("/menu/page-personnage?character="+character_id+"&suspend");
	if(unsuspend == true){
		url = "/menu/page-personnage?character="+character_id+"&unsuspend";
	}

	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send("confirm");
}


function suspendUser(user_id, unsuspend = false, admin = false){
	var xhttp = new XMLHttpRequest();
	let status;

	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		window.location = this.responseText;
	}
	};
	const urls = window.location;
	const params = new URLSearchParams(urls.search);

	if(admin){
		status = "admin";
	}
	else{
		status = "employee"
	}
	let url = ("/"+status+"/liste-utilisateur?user="+user_id+"&suspend");
	if(unsuspend == true){
		url = "/"+status+"/liste-utilisateur?user="+user_id+"&unsuspend";
	}
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send("confirm");
}

function getCookie(name) {
	const value = `; ${document.cookie}`
	const parts = value.split(`; ${name}=`)
	if (parts.length === 2) {
		return parts.pop().split(';').shift()
	}
	return null
}

function searchCharacter(){
	const search_param = document.getElementById("main_searchbar").value;
	const gender_param = document.getElementById("gender_select").value;
	const first_date_param = document.getElementById("first_date").value;
	const second_date_param = document.getElementById("second_date").value;

	let main_screen = document.getElementById("main_screen");
	while(main_screen.firstChild){
	main_screen.removeChild(main_screen.firstChild);
	}	

	var xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		main_screen.innerHTML = this.response
	}
	};

	let url = ("/menu?search="+search_param+"&gender="+gender_param+"&firstdate="+first_date_param+"&seconddate="+second_date_param);

	xhttp.open("GET", url, true);
	xhttp.send();
}

function check_password_strong(elem){
	const password = elem.value;

	const regex_length = /.{12,}/
	const regex_num = /\d/
	const regex_upcase = /[A-ZÀ-ÖØ-Ý]/
	const regex_lowcase = /[a-zà-öø-ÿ]/
	const regex_special_char = /[^A-ZÀ-ÖØ-Ýa-zà-öø-ÿ\d]/

	const check_password = {
		'length' : regex_length.test(password),
		'number' : regex_num.test(password),
		'upcase' : regex_upcase.test(password),
		'lowcase' : regex_lowcase.test(password),
		'special_char' : regex_special_char.test(password)
	};

	document.getElementById('12-character').innerText = (check_password['length'])?'✅':'🟥';
	document.getElementById('1-number').innerText = (check_password['number'])?'✅':'🟥';
	document.getElementById('1-uppercase').innerText = (check_password['upcase'])?'✅':'🟥';
	document.getElementById('1-lowercase').innerText = (check_password['lowcase'])?'✅':'🟥';
	document.getElementById('1-specialcase').innerText = (check_password['special_char'])?'✅':'🟥';
}

document.addEventListener('DOMContentLoaded', ()=>{
	let params = new URLSearchParams(document.location.search);

	const searchbar = document.getElementById('main_searchbar');

	if(params.get("link") == "publiccharacter")
	{
	searchbar.addEventListener("keyup", function(event) {
	event.preventDefault();
	if (event.keyCode === 13) {
		searchCharacter(searchbar.value, true);
	}
	});
	}
	else
	{
	searchbar.addEventListener("keyup", function(event) {
	event.preventDefault();
	if (event.keyCode === 13) {
		searchCharacter(searchbar.value, false);
	}
	});
	}
})