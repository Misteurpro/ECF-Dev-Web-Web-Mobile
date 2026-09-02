<?php
//error_reporting(E_ALL ^ E_WARNING);

if(!check_if_user_blocked($_SESSION["id_utilisateur"])):
    $is_edit = $edit;
    $edit = $is_edit && check_character_ownership($_REQUEST["character"]) && check_if_character_blocked($_GET["character"]) == 1? true : "refused";

    if($edit === "refused" && $is_edit){
        header("Location: /erreur403");
        exit;
    }

    $skin_color = "";
    $hair_color = "";
    $eye_color = "";
    $eye_shape = "";
    $nose_shape = "";
    $mouth_shape = "";
    $current_gender = "";

    if($edit && $is_edit){
        $character = get_characters(false, "", 0, 0, $_GET["character"]);
        $skin_color = $character[0]["couleur_peau"];
        $hair_color = $character[0]["couleur_cheveux"];
        $eye_color = $character[0]["couleur_yeux"];
        $eye_shape = $character[0]["forme_yeux"];
        $nose_shape = $character[0]["forme_nez"];
        $mouth_shape = $character[0]["forme_bouche"];
        $current_gender = $character[0]["genre"];
    }

    if(isset($_REQUEST["name"]) && isset($_REQUEST["gender"]) && isset($_REQUEST["skin_color"]) && isset($_REQUEST["eyes_color"])
        && isset($_REQUEST["eyes_shape"]) && isset($_REQUEST["hair_color"]) && isset($_REQUEST["nose_shape"]) && isset($_REQUEST["mouth_shape"]) && isset($_FILES["blob"]))
    {
        
        if(is_connected())
            {
            $name = $_REQUEST["name"];
            $genre = $_REQUEST["gender"];
            $skin_color = $_REQUEST["skin_color"];
            $eyes_color = $_REQUEST["eyes_color"];
            $eyes_shape = $_REQUEST["eyes_shape"];
            $hair_color = $_REQUEST["hair_color"];
            $nose_shape = $_REQUEST["nose_shape"];
            $mouth_shape = $_REQUEST["mouth_shape"];


            $blob = file_get_contents($_FILES["blob"]["tmp_name"]);
            
            $name_result = check_character_name($name);
            if(isset($_REQUEST["is_edit"]) && $_REQUEST["is_edit"] == "true"){
                $user_id = $_SESSION['id_utilisateur'];
                $insert_character = $dbh->prepare("UPDATE `personnage` SET `genre` = ?, `couleur_peau` = ?, `couleur_yeux` = ?, `couleur_cheveux` = ?, `forme_yeux` = ?, `forme_nez` = ?, `forme_bouche` = ?, `autorise` = '0', `image` = ? WHERE `personnage`.`id_personnage` = ?");  
                $insert_character->bindParam(1, $genre);
                $insert_character->bindParam(2, $skin_color);
                $insert_character->bindParam(3, $eyes_color);
                $insert_character->bindParam(4, $hair_color);
                $insert_character->bindParam(5, $eyes_shape);
                $insert_character->bindParam(6, $nose_shape);
                $insert_character->bindParam(7, $mouth_shape);
                $insert_character->bindParam(8, $blob, PDO::PARAM_LOB);
                $insert_character->bindParam(9, $_GET["character"]);            
                $insert_character->execute();

                insert_logs(true, $_GET["character"]);

                echo'Le personnage a été modifié avec succès';
                exit();
            }
            else if($name_result === true){
                $user_id = $_SESSION['id_utilisateur'];
                $insert_character = $dbh->prepare("INSERT INTO `personnage` (`id_personnage`, `nom`, `genre`, `couleur_peau`, `couleur_yeux`, `couleur_cheveux`, `forme_yeux`, `forme_nez`, `forme_bouche`, `partage`, `autorise`, `date`, `image`, `id_utilisateur`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, '0', '0', NOW(), ?, ?)");  
                $insert_character->bindParam(1, $name);
                $insert_character->bindParam(2, $genre);
                $insert_character->bindParam(3, $skin_color);
                $insert_character->bindParam(4, $eyes_color);
                $insert_character->bindParam(5, $hair_color);
                $insert_character->bindParam(6, $eyes_shape);
                $insert_character->bindParam(7, $nose_shape);
                $insert_character->bindParam(8, $mouth_shape);
                $insert_character->bindParam(9, $blob, PDO::PARAM_LOB);
                $insert_character->bindParam(10, $user_id);
                $insert_character->execute();

                $characterId = $dbh->lastInsertId();

                insert_logs(false, $characterId);

                echo'Le personnage a été créé avec succès';
                exit();
            }
            elseif($name_result === false){
                echo'ERREUR : Le nom a déjà été utilisé, veuillez en choisir un nouveau.';
            }
            elseif($name_result === "toolong"){
                echo'ERREUR : Le nom est trop long pour être utilisé, veuillez choisir un maximum de 14 characters.';
            }
            elseif($name_result === "empty"){
                echo'ERREUR : Le nom est vide, veuillez mettre un nom.';
            }
        }
        else
        {
            echo("ERREUR : Vous n'êtes pas connecté, veuillez vous connecter");
        }
        exit();
    }
    elseif(isset($_REQUEST["name"])){

        $final_name = check_character_name($_REQUEST["name"]);

        if($final_name === true){
            echo true;
        }elseif($final_name === "toolong"){
            echo"toolong";
        }else{
            echo false;
        }
        exit();
    }
?>
<html lang="fr">
<head>
    <?php require_once('layout/header/head-data.html'); ?>
    <link rel="stylesheet" href="/CSS/Charactercreator.style.css">
    <title>Créateur de personnage<?php echo TITLE_PAGE ?></title>
    <script defer src="\JS\characterCreator.js"></script>
</head>
<body>
    <div class="topPart">
        <div class="gradient1">
            <div class="gradient2">
                <?php require_once('layout/header/header.php'); ?>
                
                <main>
                    <div class="character_creator_asset">
                        <aside>
                            <div>
                                <span id="skin_color">
                                    <H2>Couleur de peau</H2>
                                    <div class="slidecontainer">
                                        <select id="color-head">
                                        <?php
                                            $colorsHead = [
                                                ["name"=>"Porcelaine","hex"=>"#f7d0c0"],
                                                ["name"=>"Beige","hex"=>"#e7ac92"],
                                                ["name"=>"Sable chaud","hex"=>"#D9A066"],
                                                ["name"=>"Miel","hex"=>"#C68642"],
                                                ["name"=>"Caramel","hex"=>"#A97142"],
                                                ["name"=>"Brun hâlé","hex"=>"#8D5524"],
                                                ["name"=>"Châtaigne","hex"=>"#7B4B2A"],
                                                ["name"=>"Moka","hex"=>"#6F4E37"],
                                                ["name"=>"Cacao","hex"=>"#5C4033"],
                                                ["name"=>"Expresso","hex"=>"#4B2E1F"],
                                                ["name"=>"Ébène","hex"=>"#2F1B0C"],
                                            ];
                                            forEach($colorsHead as $color){
                                                $is_selected = $color['name'] == $skin_color? "selected" : "";
                                                echo '<option value="'.$color['hex'].'" '.$is_selected.'>'.$color['name'].'</option>';
                                            }
                                        ?>
                                        </select>
                                    </div>                        
                                </span>
                                <span id="eye_color">
                                    <H2>Couleur des yeux</H2>
                                    <div class="slidecontainer">
                                        <select id="eyes-select">
                                            <?php
                                                $listEyesName = [
                                                    "Sérieux",
                                                    "Amandes",
                                                    "Suspicieux",    
                                                    "Ronds",    
                                                    "Fatigués",    
                                                    "Ecartés",    
                                                    "Repliés",    
                                                    "Bruce Lee"
                                                ];
                                                for($i = 0; $i < 8; $i++){
                                                    $is_selected = $listEyesName[$i] == $eye_shape? "selected" : "";
                                                    echo '<option value="/assets/frontend/Character/template-eyes-'.str_pad($i+1, 2, "0", STR_PAD_LEFT).'.png" '.$is_selected.'>'.$listEyesName[$i].'</option>';
                                                }
                                            ?>
                                        </select>
                                        <select id="color-eyes">
                                            <?php
                                            $colorsEyes = [
                                                ["name" => "Bleu","hex" => "#6FA8DC"],
                                                ["name" => "Vert","hex" => "#6AA84F"],
                                                ["name" => "Noisette","hex" => "#8E7618"],
                                                ["name" => "Ambre","hex" => "#C27C0E"],
                                                ["name" => "Marron clair","hex" => "#A52A2A"],
                                                ["name" => "Marron foncé","hex" => "#654321"],
                                                ["name" => "Gris","hex" => "#9EA7AA"],
                                                ["name" => "Bleu glace","hex" => "#A9D0F5"],
                                                ["name" => "Vert olive","hex" => "#708238"],
                                                ["name" => "Noir","hex" => "#1C1C1C"]
                                            ];
                                            forEach($colorsEyes as $color){
                                                $is_selected = $color['name'] == $eye_color? "selected" : "";
                                                echo '<option value="'.$color['hex'].'" '.$is_selected.'>'.$color['name'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>    
                                </span>
                                
                                <span id="hair_shape">
                                    <H2>Cheveux</H2>
                                        <div class="slidecontainer">
                                            <select id="color-hair">
                                                <?php
                                                $hairColors = [
                                                    ["name" => "Blond platine","hex" => "#F4E2C8"],
                                                    ["name" => "Blond","hex" => "#E6BE8A"],
                                                    ["name" => "Blond doré","hex" => "#D4AF37"],
                                                    ["name" => "Châtain clair","hex" => "#A67B5B"],
                                                    ["name" => "Châtain","hex" => "#6F4E37"],
                                                    ["name" => "Châtain foncé","hex" => "#4B3621"],
                                                    ["name" => "Noir","hex" => "#1C1C1C"],
                                                    ["name" => "Auburn","hex" => "#A52A2A"],
                                                    ["name" => "Roux cuivré","hex" => "#B87333"],
                                                    ["name" => "Gris","hex" => "#B0B0B0"]
                                                ];
                                                forEach($colorsEyes as $color){
                                                    $is_selected = $color['name'] == $hair_color? "selected" : "";
                                                    echo '<option value="'.$color['hex'].'" '.$is_selected.'>'.$color['name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                </span>
                                <span id="nose">
                                    <H2>Nez</H2>
                                    <select id="nose-select">
                                    <?php
                                        $listNoseName = [
                                        "Écraser",
                                        "Petit",
                                        "Fin",    
                                        "Alongé",    
                                        "Plat",
                                        ];
                                        for($i = 0; $i < 5; $i++){
                                            $is_selected = $listNoseName[$i] == $nose_shape? "selected" : "";
                                            echo '<option value="/assets/frontend/Character/template-nose-'.str_pad($i+1, 2, "0", STR_PAD_LEFT).'.png" '.$is_selected.'>'.$listNoseName[$i].'</option>';
                                        }
                                    ?>                                         
                                    </select>
                                </span>
                                <span id="mouth">
                                    <H2>Bouche</H2>
                                    <select id="mouth-select">
                                    <?php
                                        $listMouthName = [
                                        "Plat",
                                        "Relevé",
                                        "Plein",    
                                        "Chat",    
                                        "Énervé",    
                                        "Surélevé",    
                                        "Repliés",    
                                        "Souriant"
                                        ];
                                        for($i = 0; $i < 8; $i++){
                                            $is_selected = $listMouthName[$i] == $mouth_shape? "selected" : "";
                                            echo '<option value="/assets/frontend/Character/template-mouth-'.str_pad($i+1, 2, "0", STR_PAD_LEFT).'.png" '.$is_selected.'>'.$listMouthName[$i].'</option>';
                                        }
                                    ?>                                         
                                    </select>
                                </span>
                            </div>
                        </aside>
                        
                        <div id="main_column">
                            <div id="main_screen">
                                <div class='character_sheet'>
                                    <div id="images">
                                        <canvas id="canvas" width="200" height="200"></canvas>
                                    </div>
                                </div>

                                <div class="tab">
                                        <H1>Créateur de personnages</H1>
                                        <label for="name_id">Nom :</label>
                                        <input type="text" class="user_input" name="name" id="name_id" <?php echo $edit && $is_edit? "disabled":"" ?> oninput="check_name(this.value)" value="<?php echo $edit && $is_edit? get_character_name($_GET["character"]):"" ?>">
                                        <p id="char_max_lengh"></p>
                                        <label for="gender">Genre :</label>
                                        <select name="gender" class="user_input" id="gender-select">
                                            <option value="male">Homme</option>
                                            <option value="female" <?php echo $current_gender == "female"? "selected" : "" ?>>Femme</option>
                                        </select>
                                        
                                        <button id="submit_button" class="user_input" ><?php echo $edit && $is_edit? "Choisir les articles" : "Créer"?></button>
                                </div>
                                <div id="temp"></div>
                            </div>
                        </div>
                    </div>
                </main>
                    
            </div>
        </div>
    </div>
    <?php
        require_once("layout/footer/footer.php");
    ?>
</body>
</html>

<?php
else:
	header('Location: /compte-suspendu');
endif;
?>