<?php /* Template Name: Logora App Page */ ?>
<!DOCTYPE html>
<html class="no-js mh-disable-sb" lang="fr-FR">
    <head>
        <title>L'espace de débat</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8" />
        <?php wp_head(); ?>
    </head>
    <body>
        <div id="logora_app"></div>
        <?php
            $embed_vars = Logora_Debate::embed_vars();
        ?>
        <script>
            var logora_config = <?php echo json_encode( $embed_vars ); ?>;
            <?php
                $api_debate_url = 'https://api.logora.fr/debat.js';
            ?>    
            (function() {
                var d = document, s = d.createElement('script');
                s.src = "<?php echo $api_debate_url ?>";
                (d.head || d.body).appendChild(s);
            })();
        </script>
    </body>
</html>