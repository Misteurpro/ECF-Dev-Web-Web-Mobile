<?php
error_reporting(E_ALL ^ E_WARNING);

if(!check_if_user_blocked($_SESSION["id_utilisateur"])):
    if(check_character_ownership($_GET['character'])){

        if(isset($_GET["character"]) && isset($_POST['articles'])){
            echo add_articles_to_character($_POST['articles'], $_GET["character"]);
            exit;
        };
        ?>
            <script>
                let active_article = [];
            </script>
            <html lang="fr">
                <head>
                    <?php require_once('layout/header/head-data.html'); ?>
                    <link rel="stylesheet" href="/CSS/charactercreator.style.css">
                    <title>Selection d'articles<?php echo TITLE_PAGE ?></title>
                    <script defer src="\JS\CharacterCreator.js"></script>
                </head>
                <body>
                    <div class="topPart">
                        <div class="gradient1">
                            <div class="gradient2">
                                <?php require_once('layout/header/header.php'); ?>
                                
                                <main>
                                    <H1>Sélectionnez vos objets que le personnage portera</H1>
                                    <?php                                         
                                        $personnage = get_characters(false, '', 0,0, $_GET['character']); 
                                        render_characters($personnage, false);
                                    ?>
                                    <button class="color-secondary-darker" onclick="asign_articles(active_article)">Confirmer</button>
                                    <div class="articles_section">
                                        <?php get_articles() ?>
                                    </div>
                                </main>
                                    
                            </div>
                        </div>
                    </div>
                    <?php
                        require_once("layout/footer/footer.php");
                    ?>

                    <div id="debug">

                    </div>
                </body>
                </html>
        <?php
    }
    else{
        header("Location: /erreur403");
    }
?>

<?php
else:
	header('Location: /compte-suspendu');
endif;
?>