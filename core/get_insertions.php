<?php
/**
 * @file
 * @brief This script executes retrieving and displaying insertions to the user in various modes according to the parameters of GET request.
 * 
 * 1) Defines the current mode of displaying the insertions. Modes are the following:
 *   - "main_page", if GET request parameters are empty or contain the number of page to display only. In this mode, the only
 *  available filter and order of displaying the insertions is descending by the date of their posting. Only published insertions
 *  are displayed. Searchabar for the detailed search by make and model is displayed in this mode.
 *   - "personal", if there's "personal" parameter of the value of "true" among the current GET request parametres. Displays
 *  all the existing insertions with the value of 'seller_id' column that equals to the 'user_id' in $_SESSION (all the insertions of the current user).
 *  Terminates and redirects with error, if 'user_id' isn't set in $_SESSION. In this mode, the only available filter and order of displaying the
 *  insertions is descending by the date of their posting. Also is the only mode where current status of the insertion (whether 'waiting' or 'published')
 *  is displayed.
 *   - "admin", if there's "status" parameter of the value of "waiting" among the current GET request parametres. Checks if the user is in fact an
 *  authorized admin (first by the contents of $_SESSION, then by the value of 'admin' column by the current 'user_id' of $_SESSION), terminates
 *  and redirects with error
 *  in case of inability to provide the complete check or negative results of the check. Displays all the insertions that are currently awaiting
 *  to be confirmed to be published by the website admins. Retrieves all the insertions with the 'waiting' value of the column 
 *  'insertion_status' of the 'insertions' table. In this mode, the only available filter and order of displaying the
 *  insertions is descending by the date of their posting.
 *   - "default", if there's 'make' and 'model' among the current GET request parameters. Displays all the 'published' insertions of the make and model
 *  that are set in GET parameters. Results can be filtered and ordered: descending by the date of their posting, descedning by the price,
 *  ascending by the price. Filtering is set by optional 'filter' and 'order' parameters of the current GET request, users can set them through
 *  the select field available above the insertions list. Changing the value of the select triggers redirect to the new insertions page with new
 *  'filter' and 'order' parameters. Initial displayed value of the select is set according to 'filter' and 'order' parameters of the current GET request
 *  by filter_insertions.js script included at the bottom of <body>. 
 * 2) Connects to the database.
 * 3) Retrieves the insertions according to the current mode and GET parameters.
 * 4) Displays the retrived insertions list with minor changes of the interface according to the mode (name of the list, available options of the filter,
 * insertions status). In case of no insertions been found for the current GET parameters, an error occuring during the communication with database
 * or invalid values of make or model in the URL (user altering the URL), displays the message that no insertions were found or the URL is invalid.
 * 5) In case of 1 or more insertions been found, displays page index buttons below. Insertions are displayed at maximum of 10 per page according
 * to the 'page' parameter of the current GET request. 'page=1' displays 1st to 10th insertions of the whole insertions list that is retrieved.
 * Among the page index buttons are links to the pages of the: second previous 10 in the list, if they exist; previous 10 in the list, if they exist;
 * current page; next 10 in the list, if they exist; second next 10 insertions in the list, if they exist.  
 *  
*/

/**
 * Redirects the user to a specified location with an error parameter.
 *
 * @param string $errorParam The error parameter to append to the URL.
 */
function terminateGetInsertionsWithError($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

$mode = null;
$error = null;

/**
 * Determines the operational mode based on the `$_GET` parameters.
 */
if (!$_GET || (count($_GET) == 1 && isset($_GET['page']))){
    $mode = "main_page";
}
elseif(isset($_GET['personal']) && $_GET['personal'] == true){
    $mode = "personal";
}
elseif(isset($_GET['status']) && $_GET['status'] == 'waiting'){
    $mode = "admin";
}elseif(isset($_GET['make']) && isset($_GET['model'])){
    $mode = "default";
} else {
    exit;
}

/**
 * Starts a session if it has not been started already.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "connection.php";

$records = [];

/**
 * Calculates the offset for pagination based on the `page` parameter in `$_GET`.
 */
if (!isset($_GET['page']) || !is_numeric($_GET['page'])){
    $offset = 0;
}
else {
    $offset = ((int)$_GET['page'] - 1) * 10;
}

/**
 * Initializes the feed name and filter declaration for the main page view.
 */
$feed_name = "Naposledy zveřejněno";
$feed_filter_declare = '<form action="get_insertions.php" method="get" id="filter-form">
                            <label for="filter-select">Seřadit podle:</label>
                            <select id="filter-select" class="feed-filter">
                                <option value="date">Datum vložení</option>
                            </select>
                        </form>';

/**
 * Handles the "main_page" mode: retrieves published insertions and their count.
 */
if ($mode === "main_page" && $error === null){
    $insertion_status = "published";
    try {
        $stmt_count = $pdo->prepare("SELECT COUNT(*) AS record_count FROM insertions WHERE insertion_status = :insertion_status");
        $stmt_count->bindParam(':insertion_status', $insertion_status, PDO::PARAM_STR);
        $stmt_count->execute();        
        $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
        $total_number_of_records = $row_count['record_count'];

        $stmt = $pdo->prepare("SELECT id, seller_id, model, make, short_description, price, year, mileage, avatar_path FROM insertions WHERE insertion_status = :insertion_status ORDER BY id DESC LIMIT 10 OFFSET $offset");
        $stmt->bindParam(':insertion_status', $insertion_status, PDO::PARAM_STR);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "gi_getdata_failed";
    }
}

/**
 * Handles the "personal" mode: retrieves the user's own insertions.
 */
if($mode === "personal" && $error === null){
    if (!isset($_SESSION['user_id'])){
        terminateGetInsertionsWithError('session_invalid_id');
    }
    try {
        $stmt_count = $pdo->prepare("SELECT COUNT(*) AS record_count FROM insertions WHERE seller_id = :seller_id");
        $stmt_count->execute(['seller_id' => $_SESSION['user_id']]);
        $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
        $total_number_of_records = $row_count['record_count'];

        $stmt = $pdo->prepare("SELECT id, model, make, short_description, price, year, mileage, avatar_path, insertion_status FROM insertions WHERE seller_id = :seller_id ORDER BY id DESC LIMIT 10 OFFSET $offset");
        $stmt->bindParam(':seller_id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "gi_getdata_failed";
    }
    $feed_name = "Moje inzeráty";
}

/**
 * Handles the "admin" mode: retrieves insertions awaiting approval by the admin.
 */
elseif($mode === "admin" && $error === null){
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1){
        terminateGetInsertionsWithError("wrong_admin_rights");
    }
    try {
        $stmt = $pdo->prepare("SELECT admin FROM users WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || (int)$user['admin'] != 1) {
            terminateGetInsertionsWithError("wrong_admin_rights");
        }  
    } catch (PDOException $e){
        $error = "gi_getdata_failed";
    }

    if ($error === null){
        $insertion_status = "waiting";
        try {
            $stmt_count = $pdo->prepare("SELECT COUNT(*) AS record_count FROM insertions WHERE insertion_status = :insertion_status");
            $stmt_count->execute(['insertion_status' => $insertion_status]);
            $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
            $total_number_of_records = $row_count['record_count'];

            $stmt = $pdo->prepare("SELECT id, seller_id, model, make, short_description, price, year, mileage, avatar_path FROM insertions WHERE insertion_status = :insertion_status ORDER BY id DESC LIMIT 10 OFFSET $offset");
            $stmt->bindParam(':insertion_status', $insertion_status, PDO::PARAM_STR);
            $stmt->execute();
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "gi_getdata_failed";
        }
    }
    $feed_name = "Žádosti o zveřejnění";
}

/**
 * Handles the "default" mode: retrieves insertions filtered by make and model.
 */
elseif($mode === "default" && $error === null){
    if (!isset($_GET['order']) || ($_GET['order'] != 'asc' && $_GET['order'] != 'desc')){
        $order = 'desc';
    }
    else {
        $order = $_GET['order'];
    }

    if (!isset($_GET['filter']) || ($_GET['filter'] != 'id' && $_GET['filter'] != 'price')){
        $filter_property = 'id';
    }
    else {
        $filter_property = $_GET['filter'];
    }
    $insertion_status = "published";
    try {
        $stmt_count = $pdo->prepare("SELECT COUNT(*) AS model_count FROM models WHERE make = :make AND model = :model");
        $stmt_count->bindParam(':make', $_GET['make'], PDO::PARAM_STR);
        $stmt_count->bindParam(':model', $_GET['model'], PDO::PARAM_STR);
        $stmt_count->execute();
        $row_count_array = $stmt_count->fetch(PDO::FETCH_ASSOC);
        $row_count = $row_count_array['model_count'];
        if ((int)$row_count === 0){
            $error = "gi_wrong_model_or_make_request";
        } 
    } catch (PDOException $e) {
        $error = "gi_getdata_failed";
    }

    if ($error === null){
        try {
            $stmt_count = $pdo->prepare("SELECT COUNT(*) AS record_count FROM insertions WHERE make = :make AND model = :model AND insertion_status = :insertion_status");
            $stmt_count->bindParam(':make', $_GET['make'], PDO::PARAM_STR);
            $stmt_count->bindParam(':model', $_GET['model'], PDO::PARAM_STR);
            $stmt_count->bindParam(':insertion_status', $insertion_status, PDO::PARAM_STR);
            $stmt_count->execute();
            $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
            $total_number_of_records = $row_count['record_count'];

            $stmt = $pdo->prepare("SELECT id, seller_id, model, make, short_description, price, year, mileage, avatar_path FROM insertions WHERE make = :make AND model = :model AND insertion_status = :insertion_status ORDER BY {$filter_property} {$order} LIMIT 10 OFFSET $offset");
            $stmt->bindParam(':make', $_GET['make'], PDO::PARAM_STR);
            $stmt->bindParam(':model', $_GET['model'], PDO::PARAM_STR);
            $stmt->bindParam(':insertion_status', $insertion_status, PDO::PARAM_STR);
            $stmt->execute();
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "gi_getdata_failed";
        }
    }
    if ($error !== "gi_wrong_model_or_make_request"){ $feed_name = "Vyhledávání: " . $_GET['make'] . " " . $_GET['model']; }
    else { $feed_name = "Chyba vyhledávání";}
}
if ($mode !== "personal" && $error === null){
    try {
        foreach ($records as &$record) {
            $sellerStmt = $pdo->prepare("SELECT person_type, company_name 
                                        FROM users 
                                        WHERE id = :seller_id 
                                        LIMIT 1");
            $sellerStmt->bindParam(':seller_id', $record['seller_id'], PDO::PARAM_INT);
            $sellerStmt->execute();
            $seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);

            if ($seller) {
                if ($seller['person_type'] === 'company') {
                    $record['seller_name'] = $seller['company_name'];
                } elseif ($seller['person_type'] === 'private') {
                    $record['seller_name'] = 'Soukromý prodejce';
                } else {
                    $record['seller_name'] = 'Neznámý prodejce';
                }
            } else {
                $record['seller_name'] = 'Neznámý prodejce';
            }
        }
    } catch (PDOException $e) {
        $error = "gi_getdata_failed";
    }
}

foreach ($records as &$record) {
    $record['price'] = number_format((int)$record['price'], 0, "", " ");
    $record['mileage'] = number_format((int)$record['mileage'], 0, "", " ");
}
unset($record)
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="../styles/get_insertions.css">
    <title>AutoPulse</title>
</head>
<body>
    <?php if($mode !== "main_page" || isset($_GET['page'])){
        include "header.php";} ?>
    
    <?php if(($mode === "main_page" && $offset != 0) || $mode === "default" || (isset($_GET['page']) && ($mode === "main_page" || $mode === "default"))){
        include "searchbar.php";} ?>

    <div class="external-container">
        <div class="feed-header">
            <div class="feed-name"><?php echo $feed_name?></div>
            <?php if ($mode === "main_page" || $mode === "admin" || $mode === "personal"):?>
                <form action="get_insertions.php" method="get" id="filter-form">
                    <label for="filter-select">Seřadit podle:</label>
                    <select id="filter-select" class="feed-filter">
                        <option value="date">Datum vložení</option>
                    </select>
                </form>
            <?php elseif ($mode === "default" && $order === "asc"):?>
                <form action="get_insertions.php" method="get" id="filter-form">
                    <label for="filter-select">Seřadit podle:</label>
                    <select id="filter-select" class="feed-filter">
                        <option value="date">Datum vložení</option>
                        <option value="price asc" selected>Cena vzestupně</option>
                        <option value="price desc">Cena sestupně</option>
                    </select>
                </form>
            <?php elseif ($mode === "default" && $filter_property === "price"):?>
                <form action="get_insertions.php" method="get" id="filter-form">
                    <label for="filter-select">Seřadit podle:</label>
                    <select id="filter-select" class="feed-filter">
                        <option value="date">Datum vložení</option>
                        <option value="price asc" >Cena vzestupně</option>
                        <option value="price desc" selected>Cena sestupně</option>
                    </select>
                </form>
            <?php elseif ($mode === "default"):?>
            <form action="get_insertions.php" method="get" id="filter-form">
                <label for="filter-select">Seřadit podle:</label>
                <select id="filter-select" class="feed-filter">
                    <option value="date">Datum vložení</option>
                    <option value="price asc" >Cena vzestupně</option>
                    <option value="price desc">Cena sestupně</option>
                </select>
            </form>
            <?php endif?>
        </div>
        
        <?php if ($mode === "default" && $error === "gi_wrong_model_or_make_request"):?><div class="error-message"><?php echo "Nesprávná značka nebo model."; ?></div>
        <?php elseif ($error === "db_access_failed" || $error === "gi_getdata_failed" || count(value: $records) === 0):?><div class="error-message"><?php echo "Inzeráty nenalezeny."; ?></div>
        <?php else: ?>
            <?php
            /**
             * Single insertion block display. A block is in fact a link to the page of the insertion of particular id.
             */
                for ($i = 0; $i < 10; $i++) {
                    if (count($records) >= $i + 1) {
                        echo '<a href="' . "insertion.php?id=" . htmlspecialchars($records[$i]['id'], ENT_QUOTES, 'UTF-8') . '" class="insertion">';
                        echo '    <div class="insertion-img-container">';
                        echo '        <img src="' . '../avatar/' . htmlspecialchars($records[$i]['avatar_path'], ENT_QUOTES, 'UTF-8') . '">';
                        echo '    </div>';
                        echo '    <div class="insertion-info-container">';
                        echo '        <div class="insertion-name">' . htmlspecialchars($records[$i]['make'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($records[$i]['model'], ENT_QUOTES, 'UTF-8') . '</div>';
                        echo '        <div class="insertion-short-decription">' . htmlspecialchars($records[$i]['short_description'], ENT_QUOTES, 'UTF-8') . '</div>';
                        echo '        <div class="insertion-details">' . htmlspecialchars($records[$i]['year'], ENT_QUOTES, 'UTF-8') . ", " . htmlspecialchars($records[$i]['mileage'], ENT_QUOTES, 'UTF-8') . " km" . '</div>';
                        echo '        <div class="insertion-price">' . htmlspecialchars($records[$i]['price'], ENT_QUOTES, 'UTF-8') . " Kč" . '</div>';
                        if ($mode === "personal") {
                            echo '        <div class="insertion-status">Status: ' . htmlspecialchars($records[$i]['insertion_status'], ENT_QUOTES, 'UTF-8') . '</div>';
                        }
                        else {
                            echo '        <div class="insertion-seller">' . htmlspecialchars($records[$i]['seller_name'], ENT_QUOTES, 'UTF-8') . '</div>';
                        }
                        echo '    </div>';
                        echo '</a>';
                    }
                }
            ?>

        <?php endif; ?>

    </div>
    
    <?php if (count($records) > 0): ?>
        <div class="page-index-buttons-container">
            <?php if (($offset / 10 - 2) >= 0):?>
                <a href="<?php 
                    $query = $_GET; 
                    $query['page'] -= 2; 
                    $queryString = http_build_query($query);
                    echo "get_insertions.php?$queryString"?>" class="page-index-button"><?php echo $query['page']?></a>
            <?php endif;?>
            <?php if (($offset / 10 - 1) >= 0):?>
                <a href="<?php 
                    $query = $_GET; 
                    $query['page'] -= 1; 
                    $queryString = http_build_query($query);
                    echo "get_insertions.php?$queryString"?>" class="page-index-button"><?php echo $query['page']?></a>
            <?php endif;?>
            <a href="<?php
                $query = $_GET;
                $query['page'] = $offset/10+1; 
                $queryString = http_build_query($query);
                echo "get_insertions.php?$queryString"?>" class="current-page-index-button"><?php echo $query['page']?></a>
            <?php if ($total_number_of_records - $offset - 10 > 0): ?>
                <a href="<?php 
                    $query = $_GET; 
                    $query['page'] = isset($query['page']) ? $query['page'] + 1 : 2; 
                    $queryString = http_build_query($query);
                    echo "get_insertions.php?$queryString"?>" class="page-index-button"><?php echo $query['page']?></a>
            <?php endif;?>
            <?php if ($total_number_of_records - $offset - 10 > 10): ?>
                <a href="<?php 
                    $query = $_GET; 
                    $query['page'] = isset($query['page']) ? $query['page'] + 2 : 3; 
                    $queryString = http_build_query($query);
                    echo "get_insertions.php?$queryString"?>" class="page-index-button"><?php echo $query['page']?></a>
            <?php endif;?>
        </div>
    <?php endif; ?>

    <?php if($mode !== "main_page" || isset($_GET['page'])){
        include "footer.html";} ?>
    <script src="../scripts/filter_insertions.js" defer></script>
</body>
</html>

