<!DOCTYPE html>
<html lang="cz">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="styles/searchbar.css">
</head>
<body>
    <?php 
    /**
     * @file
     * @brief This script handles the display and behaviour of the searchbar for the user detailed search among the exisiting insertions by make and model.
     * 
     * make-model-ajax.js script is included at the bottom of <body>. Script's purpose is to load the existing makes on-page-load and load the existing
     * models of the make chosen by user and populate the loaded results into the <option>'s of the drop-down-lists of the searchbar.
    */
    ?>
    <div class="searchbar-container">
        <form class="searchbar" method="get" action="get_insertions.php">
            <select id="make" class="searchbar-left-select" name="make">
                <option value="default">Vybrat značku</option>
            </select>
            <select id="model" class="searchbar-middle-select" name="model" disabled>
                <option value="default">Vybrat model</option>
            </select>
            <button id="search" class="searchbar-right-button" type="submit" disabled>Vyhledat</button>
        </form>
    </div>
    <script src="scripts/make-model-ajax.js"></script>
</body>
</html>