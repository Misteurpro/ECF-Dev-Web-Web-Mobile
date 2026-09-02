	const canvas = document.getElementById("canvas");
	const ctx = canvas.getContext("2d");
	ctx.imageSmoothingEnabled = false;

	const eyesSelect = document.getElementById("eyes-select");


	const base = new Image();
	base.src = "/assets/frontend/Character/template-perso.png";
	const head = new Image();
	head.src = "/assets/frontend/Character/template-face.png";
	const eyes = new Image();
	const nose = new Image();
	const mouth = new Image();
	const hair = new Image();

	is_edit = false;

	if(window.location.href.indexOf("modifier") > -1){
		is_edit = true;
	}

	const colorInputHead = document.getElementById("color-head");
	const colorInputHair = document.getElementById("color-hair");
	const colorInputEyes = document.getElementById("color-eyes");
	const inputEyesSelect = document.getElementById("eyes-select");
	const inputNoseSelect = document.getElementById("nose-select");
	const inputMouthSelect = document.getElementById("mouth-select");
	const charMaxLengh = document.getElementById("char_max_lengh");

	let color_skin = "";
	let color_hair = "";
	let color_eyes = "";
	let shape_eyes = "";
	let shape_nose = "";
	let shape_mouth = "";

	let name_answer;

	const character_name = document.getElementById("name_id");
	const character_gender = document.getElementById("gender-select");
	let item = document.getElementById("temp");

	const sumbit_button = document.getElementById("submit_button");
	sumbit_button.addEventListener("click", ()=>{
		let color_skin = colorInputHead.options[colorInputHead.selectedIndex].text  
		let color_hair = colorInputHair.options[colorInputHair.selectedIndex].text  
		let color_eyes = colorInputEyes.options[colorInputEyes.selectedIndex].text  
		let shape_eyes = inputEyesSelect.options[inputEyesSelect.selectedIndex].text
		let shape_nose = inputNoseSelect.options[inputNoseSelect.selectedIndex].text
		let shape_mouth = inputMouthSelect.options[inputMouthSelect.selectedIndex].text

		create_character(character_name.value, character_gender.value, color_skin, color_eyes, shape_eyes, color_hair, shape_nose, shape_mouth, is_edit);
	})
	
	const bodyColors = [
		"#DFDFDF",
		"#C1C1C1",
		"#A7A7A7",
		"#888888",
		"#5B5B5B",
		"#4F4F4F",
		"#3A3A3A",
		"#252525",
		"#131313",
		"#090909",
	];

	async function draw() {
		eyes.src = eyesSelect.value;
		nose.src = inputNoseSelect.value;
		mouth.src = inputMouthSelect.value;
		hair.src = "/assets/frontend/Character/template-hair-"+character_gender.value+".png"
		

		if (!base.complete || !head.complete || !eyes.complete || !nose.complete || !mouth.complete || !hair.complete) {
		setTimeout(() => {
			draw();
		}, 200);
		return;
		}
		ctx.clearRect(0, 0, canvas.width, canvas.height);

		const headColored = recolorImage(
		head, 
		bodyColors, 
		colorInputHead.value
		);

		const noseColored = recolorImage(
		nose,
		["#E2E2E2FF", "#CBCBCBFF"],
		colorInputHead.value
		);

		const mouthColored = recolorImage(
		mouth,
		["empty", "#CBCBCBFF"],
		colorInputHead.value
		);

		let eyesColored = recolorImage(
		eyes,
		["#EEEEEEFF"],
		colorInputEyes.value,
		);

		eyesColored = recolorImage(
		eyesColored,
		["empty", "#CBCBCBFF"],
		colorInputHead.value,
		);

		hairColored = recolorImage(
		hair,
		["#686868","#595959","#4B4B4B","#3C3C3C"],
		colorInputHair.value
		);


		ctx.drawImage(base, 0, 0, canvas.width, canvas.height);
		ctx.drawImage(headColored, 0, 0, canvas.width, canvas.height);
		ctx.drawImage(eyesColored, 0, 0, canvas.width, canvas.height);
		ctx.drawImage(noseColored, 0, 0, canvas.width, canvas.height);
		ctx.drawImage(mouthColored, 0, 0, canvas.width, canvas.height);
		ctx.drawImage(hairColored, 0, 0, canvas.width, canvas.height)
	}

	function hexToRgbArray(hex) {
		// support format court #fff
		if (hex.length === 4) {
		hex = hex
			.split("")
			.map((c) => c + c)
			.join("");
		}
		return hex.match(/\w\w/g).map((x) => parseInt(x, 16));
	}

	function recolorImage(img, colorHexList, newColorHex) {
		const cvs = document.createElement("canvas");
		const ctx = cvs.getContext("2d");
		const w = img.width;
		const h = img.height;

		cvs.width = w;
		cvs.height = h;

		// draw the image on the temporary canvas
		ctx.drawImage(img, 0, 0, w, h);

		// pull the entire image into an array of pixel data
		const imageData = ctx.getImageData(0, 0, w, h);

		// examine every pixel,
		// change any old rgb to the new-rgb
		for (let i = 0; i < imageData.data.length; i += 4) {
		for (let j = 0; j < colorHexList.length; j++) {
			const searchRGB = hexToRgbArray(colorHexList[j]);
			const newRGB = toDarker(
			hexToRgbArray(newColorHex),
			colorHexList.length,
			j,
			);

			// is this pixel the searchRGB?
			if (
			imageData.data[i] == searchRGB[0] &&
			imageData.data[i + 1] == searchRGB[1] &&
			imageData.data[i + 2] == searchRGB[2]
			) {
			// change to your new rgb
			imageData.data[i] = newRGB[0];
			imageData.data[i + 1] = newRGB[1];
			imageData.data[i + 2] = newRGB[2];
			break;
			}
		}
		}
		// put the altered data back on the canvas
		ctx.putImageData(imageData, 0, 0);
		// put the re-colored image back on the image
		return cvs;
	}

	function toDarker(rgbArray, countOfShades, currentShades) {
		return rgbArray.map((v) =>
		Math.round(v * (1 - currentShades / countOfShades)),
		); //Getting darker and darker
	}
	/*
	function updateColor() {
		const alpha = alphaInput.value;
		const red = redInput.value;
		const green = greenInput.value;
		const blue = blueInput.value;

		colorInput.value = `rgba(${red}, ${green}, ${blue}, ${alpha})`;

		draw();
	}
	*/
	function updateTricolor() {
		//const rgb = hexToRgbArray(colorInput.value);
		//redInput.value = rgb[0];
		//greenInput.value = rgb[1];
		//blueInput.value = rgb[2];

		draw();
	}

	function create_character(name, gender, skin_color, eyes_color, eyes_shape, hair_color, nose_shape, mouth_shape, is_edit){

		let params = new URLSearchParams(document.location.search)
		if(is_edit){
			canvas.toBlob((blob) => {
			const formData = new FormData();
			formData.append("name", name);
			formData.append("gender", gender);
			formData.append("skin_color", skin_color);
			formData.append("eyes_color", eyes_color);
			formData.append("eyes_shape", eyes_shape);
			formData.append("hair_color", hair_color);
			formData.append("nose_shape", nose_shape);
			formData.append("mouth_shape", mouth_shape);
			formData.append("is_edit", is_edit);
			formData.append("blob", blob);

			async function sendForm(formData) {

				const response = await fetch("/menu/page-personnage/modifier?character="+params.get("character"), {
					method: "POST",
					body: formData
				});
				let responseText = await response.text();
				if(responseText === "Le personnage a été modifié avec succès"){
					window.location = "/menu/page-personnage/modifier/selection-des-articles?character="+params.get("character");
				}
				else{ 
					window.location = "/personnage-creer-erreur"
				}
			}

			sendForm(formData);
			}, "image/png");
		}else{
			check_name(name).then(function(result){
			if(result === "true"){
				canvas.toBlob((blob) => {
				const formData = new FormData();
				formData.append("name", name);
				formData.append("gender", gender);
				formData.append("skin_color", skin_color);
				formData.append("eyes_color", eyes_color);
				formData.append("eyes_shape", eyes_shape);
				formData.append("hair_color", hair_color);
				formData.append("nose_shape", nose_shape);
				formData.append("mouth_shape", mouth_shape);
				formData.append("is_edit", is_edit);
				formData.append("blob", blob);
	
				async function sendForm(formData) {
					const response = await fetch("/menu/createur-de-personnage", {
						method: "POST",
						body: formData
					});
					let responseText = await response.text();
					if(responseText === "Le personnage a été créé avec succès"){
						window.location = "/personnage-creer-avec-succes"
					}
					else{
						window.location = "/personnage-creer-erreur"
					}
				}
	
				sendForm(formData);
				}, "image/png");
				}
			});
		}
	
	}

	function check_name(name){
		
		return check_name_ajax(name).then(result =>{
			if(name.length <= 14 && name.length != 0){	
				charMaxLengh.style.color = "green";
				charMaxLengh.style.display = "block";
				charMaxLengh.textContent = "disponible et en dessous de 14 character";
				answer = "true";
			}
			else if(name.length > 14){	
				charMaxLengh.style.color = "red"
				charMaxLengh.style.display = "block";
				charMaxLengh.textContent = "au dessus de 14 character!"
				answer = "false";
			}else{
				charMaxLengh.style.color = "red"
				charMaxLengh.style.display = "block";
				charMaxLengh.textContent = "Veuillez mettre un nom!"
				answer = "false";
			}
			return answer;
		}).catch(result =>{
			charMaxLengh.style.color = "red"
			charMaxLengh.style.display = "block";
			charMaxLengh.textContent ="indisponible car nom déjà utilisé!"
			answer = "false";
			return answer;
		});
	}


	function check_name_ajax(name){
		return new Promise(function(resolve, reject) {
			let xhttp = new XMLHttpRequest();
			let response;
	
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					if(this.responseText == true || this.responseText == "toolong"){
						resolve(this.responseText)
					}
					else{
						reject("toolong")
					}
				}
			};
	
			let url = "/menu/createur-de-personnage"
			xhttp.open("POST", url, true);
			xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhttp.send("name="+name);
		})
	}

	colorInputHead.addEventListener("input", updateTricolor);
	//colorInputHair.addEventListener("change", updateTricolor);
	colorInputEyes.addEventListener("input", updateTricolor);
	inputEyesSelect.addEventListener("input", updateTricolor);
	inputNoseSelect.addEventListener("input", updateTricolor);
	inputMouthSelect.addEventListener("input", updateTricolor);
	character_gender.addEventListener("input", updateTricolor);
	colorInputHair.addEventListener("input", updateTricolor);



    document.addEventListener("DOMContentLoaded", draw);