<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="../styles/publish-insertion.css">
    <title>AutoPulse</title>
</head>
<body>
    <?php
        /**
         * @file
         * @brief Form for posting the insertion.
         * 
         * In case of form being returned by the processing script, triggers popup message providing context of the occured error,
         * and pre-fills form fields with the previous inputs of the user. Pre-fill is secured by htmlspecialchars() function to prevent XSS attempts.  
        */
        if (isset($_GET["error"]) && $_GET["error"] != "publish_wrong_size_or_format_photo1" 
            && $_GET["error"] != "publish_wrong_size_or_format_photo2"
            && $_GET["error"] != "publish_wrong_size_or_format_photo3" 
            && $_GET["error"] != "publish_wrong_size_or_format_photo4"
            && $_GET["error"] != "publish_wrong_size_or_format_photo5") {
                include "authFailurePopUp.php";} 
    ?>
        
    <?php include "header-plain.html"; ?>
    <div class="form-container">
        <h1>+Přidat Inzerát</h1>
        <form action="process-publishInsertion.php" method="post" id="insertion_form" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="make">Značka:</label>
                    <select id="make"  name="make">
                        <option value="default">Vybrat značku</option>
                    </select>
                    <div class="info hidden" id="make-empty">! Tato položka je nutná k vyplnění</div>
                </div>
                <div class="form-group">
                    <label for="model">Model:</label>
                    <select id="model" name="model" disabled>
                        <option value="default">Vybrat model</option>
                    </select>
                    <div class="info hidden" id="model-empty">! Tato položka je nutná k vyplnění</div>
                </div>
                <div class="form-group">
                    <label for="short_description">Krátký popis (zobrazí se v názvu inzerátu):</label>
                    <input type="text" id="short_description" name="short_description" value="<?php echo htmlspecialchars($_GET['short_description'] ?? '');?>">
                    <div class="error hidden" id="short_description-wrongformat">! Nesprávný formát popisu. Maximální povolená délka 50 znaků.</div>
                </div>
                <div class="form-group">
                    <label for="price">Cena (Kč):</label>
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($_GET['price'] ?? '');?>">
                    <div class="info hidden" id="price-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="price-wrongformat">! Nesprávný formát hodnoty ceny. Povolené rozmezí: 10000 až 20000000</div>
                </div>
                <div class="form-group">
                    <label for="year">Rok první registrace:</label>
                    <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($_GET['year'] ?? '');?>">
                    <div class="info hidden" id="year-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="year-wrongformat">! Nesprávný formát hodnoty roku. Povolené rozmezí: 1980 až 2026</div>
                </div>
                <div class="form-group">
                    <label for="mileage">Najeto km:</label>
                    <input type="number" id="mileage" name="mileage" value="<?php echo htmlspecialchars($_GET['mileage'] ?? '');?>">
                    <div class="info hidden" id="mileage-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="mileage-wrongformat">! Nesprávný formát hodnoty. Povolené rozmezí: 0 až 3000000</div>
                </div>
                <div class="form-group">
                    <label for="power">Výkon (kW):</label>
                    <input type="number" id="power" name="power" value="<?php echo htmlspecialchars($_GET['power'] ?? '');?>">
                    <div class="info hidden" id="power-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="power-wrongformat">! Nesprávný formát hodnoty výkonu. Povolené rozmezí: 30 až 5000</div>
                </div>
                <div class="form-group">
                    <label for="fuel">Typ paliva:</label>
                    <select id="fuel" name="fuel">
                        <option value="default">Vybrat typ paliva</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Petrol">Petrol</option>
                        <option value="Gas">Gas</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="EV">EV</option>
                    </select>
                    <div class="info hidden" id="fuel-empty">! Tato položka je nutná k vyplnění</div>
                </div>
                <div class="form-group">
                    <label for="engine_capacity">Objem motoru (cm3):</label>
                    <input type="number" id="engine_capacity" name="engine_capacity" value="<?php echo htmlspecialchars($_GET['engine_capacity'] ?? '');?>">
                    <div class="info hidden" id="engine_capacity-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error hidden" id="engine_capacity-wrongformat">! Nesprávný formát hodnoty. Povolené rozmezí: 1000 až 8000</div>
                </div>
                <div class="form-group">
                    <label for="description">Popis:</label>
                    <textarea id="description" name="description"></textarea>
                    <div class="error hidden" id="description-wrongformat">! Nesprávný formát popisu. Maximální povolená délka 600 znaků.</div>
                </div>
                <div class="form-group photo-upload">
                    <label for="photo1">Fotografie - nutně 5, první se použije jako hlavní,<br>povolený typ - jpg, max.velikost - 40MB, preferovaný poměr - 4:3</label>
                    <input type="file" id="photo1" name="photo1" accept=".jpg" >
                    <div class="info hidden" id="photo1-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error"><?php if (isset($_GET['error']) && $_GET['error'] == "publish_wrong_size_or_format_photo1"):
                         echo 'Špatný typ nebo velikost vloženého souboru v této položce. Povolený typ - .jpg. Max. povolená velikost - 40MB'; endif;?></div>
                </div>
                <div class="form-group photo-upload">
                    <input type="file" id="photo2" name="photo2" accept=".jpg" >
                    <div class="info hidden" id="photo2-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error"><?php if (isset($_GET['error']) && $_GET['error'] == "publish_wrong_size_or_format_photo2"):
                         echo 'Špatný typ nebo velikost vloženého souboru v této položce. Povolený typ - .jpg. Max. povolená velikost - 40MB'; endif;?></div>
                </div>
                <div class="form-group photo-upload">
                    <input type="file" id="photo3" name="photo3" accept=".jpg" >
                    <div class="info hidden" id="photo3-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error"><?php if (isset($_GET['error']) && $_GET['error'] == "publish_wrong_size_or_format_photo3"):
                         echo 'Špatný typ nebo velikost vloženého souboru v této položce. Povolený typ - .jpg. Max. povolená velikost - 40MB'; endif;?></div>
                </div>
                <div class="form-group photo-upload">
                    <input type="file" id="photo4" name="photo4" accept=".jpg" >
                    <div class="info hidden" id="photo4-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error"><?php if (isset($_GET['error']) && $_GET['error'] == "publish_wrong_size_or_format_photo4"):
                         echo 'Špatný typ nebo velikost vloženého souboru v této položce. Povolený typ - .jpg. Max. povolená velikost - 40MB'; endif;?></div>
                </div>
                <div class="form-group photo-upload">
                    <input type="file" id="photo5" name="photo5" accept=".jpg" >
                    <div class="info hidden" id="photo5-empty">! Tato položka je nutná k vyplnění</div>
                    <div class="error"><?php if (isset($_GET['error']) && $_GET['error'] == "publish_wrong_size_or_format_photo5"):
                         echo 'Špatný typ nebo velikost vloženého souboru v této položce. Povolený typ - .jpg. Max. povolená velikost - 40MB'; endif;?></div>
                </div>
            </div>
            <div class="form-footer">
                <button type="submit">Přidat inzerát</button>
            </div>
        </form>
    </div>
    <?php include "footer.html"; ?>
    <script src="../scripts/make-model-ajax.js" defer></script>
    <script src="../scripts/publish-insertion.js" defer></script>
</body>
</html>
