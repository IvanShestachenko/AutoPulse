<?php
/**
 * @file
 * @brief This script handles retrieving of detailed data of the particular insertion from the database and displaying it as a separate page
 * with some insertion management interface provided for the authorized users.
 * 
 * Displaying of all the retrieved data is secured by htmlspecialchars() function to prevent XSS attempts. Terminates and redirects to the
 * main page in case of error occuring during communication with the database, absense of required user authorization (insertion can still have
 * 'waiting' value of its insertion_status, and such insertions are available to their owners and admins only) or absense of record with the provided id
 * in the 'insertions' table of the database. Id of the insertion to display is set as a parameter of the current GET request.
 */

/**
 * Starts a new session or resumes the current session.
 */
session_start();

/**
 * Redirects the user to the index page with an error parameter and exits the script.
 *
 * @param string $errorParam The error parameter to be appended to the URL.
 */
function terminateInsertion($errorParam){
    header("Location: index.php?error=$errorParam");
    exit;
}

require "connection.php";

/**
 * Checks for a database access error and terminates the script if it occurs.
 */
if (isset($error) && $error === "db_access_failed"){
    terminateInsertion("ins_getdata_failed");
}

/** 
 * Retrieves the current user's ID from the session or sets it to null if not available.
 */
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

/** 
 * Determines the mode based on the user's admin status.
 * If the user is an admin, the mode is "admin", otherwise, it's "user".
 */
$mode = (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) ? "admin" : "user";

/**
 * Validates the presence of the 'id' parameter in the GET request.
 */
if (!isset($_GET['id'])){
    terminateInsertion("insertion_does_not_exist");
}

if ($mode == "user"){
    try {
        /**
         * Fetches the insertion details for the specified ID.
         */
        $stmt = $pdo->prepare("SELECT * FROM insertions WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
        $stmt->execute();
        $insertion = $stmt->fetch(PDO::FETCH_ASSOC);

        /**
         * Validates that the insertion exists and belongs to the user or is published.
         */
        if (!$insertion || ($insertion['seller_id'] != $user_id && $insertion['insertion_status'] != "published")){
            terminateInsertion("insertion_does_not_exist");
        }

        /**
         * Fetches the images associated with the specified insertion, ordered by their order number.
         */
        $stmt = $pdo->prepare("SELECT * FROM images WHERE insertion_id = :insertion_id ORDER BY order_number ASC");
        $stmt->bindParam(':insertion_id', $_GET['id'], PDO::PARAM_STR);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /**
         * Validates that the images for the insertion exist.
         */
        if (!$images){
            terminateInsertion("ins_getdata_failed");
        }

        /**
         * Updates the mode to "personal" if the user is the seller of the insertion.
         */
        if ($insertion['seller_id'] == $user_id){
            $mode = "personal";
        }
    } catch (PDOException $e) {
        /**
         * Handles errors during the data retrieval process.
         */
        terminateInsertion("ins_getdata_failed");
    }
} elseif ($mode == "admin"){
    try {
        /**
         * Validates the admin rights of the current user.
         */
        $stmt = $pdo->prepare("SELECT admin FROM users WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || (int)$user['admin'] != 1) {
            terminateInsertion("wrong_admin_rights");
        }

        /**
         * Fetches the insertion details for the specified ID.
         */
        $stmt = $pdo->prepare("SELECT * FROM insertions WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
        $stmt->execute();
        $insertion = $stmt->fetch(PDO::FETCH_ASSOC);

        /**
         * Validates that the insertion exists.
         */
        if (!$insertion){
            terminateInsertion("insertion_does_not_exist");
        }

        /**
         * Fetches the images associated with the specified insertion, ordered by their order number.
         */
        $stmt = $pdo->prepare("SELECT * FROM images WHERE insertion_id = :insertion_id ORDER BY order_number ASC");
        $stmt->bindParam(':insertion_id', $_GET['id'], PDO::PARAM_STR);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /**
         * Validates that the images for the insertion exist.
         */
        if (!$images){
            terminateInsertion("ins_getdata_failed");
        }
    } catch (PDOException $e) {
        /**
         * Handles errors during the data retrieval process.
         */
        terminateInsertion("ins_getdata_failed");
    }
}

try {
    $sellerStmt = $pdo->prepare("SELECT person_type, company_name 
                                FROM users 
                                WHERE id = :seller_id 
                                LIMIT 1");
    $sellerStmt->bindParam(':seller_id', $insertion['seller_id'], PDO::PARAM_INT);
    $sellerStmt->execute();
    $seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);

    if ($seller) {
        if ($seller['person_type'] === 'company') {
            $insertion['seller_name'] = $seller['company_name'];
        } elseif ($seller['person_type'] === 'private') {
            $insertion['seller_name'] = 'Soukromý prodejce';
        } else {
            $insertion['seller_name'] = 'Neznámý prodejce';
        }
    } else {
        $insertion['seller_name'] = 'Neznámý prodejce';
    }
    
} catch (PDOException $e) {
    terminateInsertion("ins_getdata_failed");
}

$insertion['price'] = number_format((int)$insertion['price'], 0, "", " ");
$insertion['mileage'] = number_format((int)$insertion['mileage'], 0, "", " ");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href="../styles/insertion.css">
    <title><?php echo htmlspecialchars($insertion['make'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($insertion['model'], ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="container">
        <div class="carousel">
            <div class="carousel-images">
                <?php foreach ($images as $image): ?>
                    <img src="<?php echo "../media/" . htmlspecialchars($image['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Obrázek inzerátu">
                <?php endforeach; ?>
            </div>
            <button class="carousel-button prev">&#10094;</button>
            <button class="carousel-button next">&#10095;</button>
        </div>
        <div class="insertion_info_container">
            <div class="insertion_header_first_line">
                <div class="insertion_name"><?php echo htmlspecialchars($insertion['make'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($insertion['model'], ENT_QUOTES, 'UTF-8') . ","; ?></div>
                <div class="insertion_short_description"><?php echo htmlspecialchars($insertion['short_description'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="insertion_header_second_line">
                <div class="insertion_header_second_line_entry"><?php echo htmlspecialchars($insertion['year'], ENT_QUOTES, 'UTF-8') . ","; ?></div>
                <div class="insertion_header_second_line_entry"><?php echo htmlspecialchars($insertion['mileage'], ENT_QUOTES, 'UTF-8') . " km"; ?></div>
            </div>
            <div class="insertion_price"><?php echo htmlspecialchars($insertion['price'], ENT_QUOTES, 'UTF-8') . " kč"; ?></div>
            <div class="insertion_seller"><?php echo htmlspecialchars($insertion['seller_name'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="insertion_info_entry">
                <div class="insertion_info_label">Výkon:</div>
                <div class="insertion_info_value"><?php echo htmlspecialchars($insertion['power'], ENT_QUOTES, 'UTF-8') . " kW"; ?></div>                
            </div>
            <div class="insertion_info_entry">
                <div class="insertion_info_label">Palivo:</div>
                <div class="insertion_info_value"><?php echo htmlspecialchars($insertion['fuel'], ENT_QUOTES, 'UTF-8'); ?></div>                
            </div>
            <div class="insertion_info_entry">
                <div class="insertion_info_label">Objem motoru:</div>
                <div class="insertion_info_value"><?php echo htmlspecialchars($insertion['engine_capacity'], ENT_QUOTES, 'UTF-8') . " cm3"; ?></div>                
            </div>

            <div class="insertion-buttons">
            <?php
            /**
             * @brief provides user interface according to the current user authorization. 
             */
            if ($mode === "admin" && $insertion['insertion_status'] == "waiting"): ?>
                <a href="confirm-publish.php?id=<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>" class="insertion-button">Zveřejnit</a>
                <a href="delete_insertion.php?id=<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>" class="insertion-button">Zamítnout</a>
            <?php elseif ($mode === "admin" || $mode === "personal"): ?>
                <a href="delete_insertion.php?id=<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>" class="insertion-button">Smázat</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include "footer.html"; ?>
    <script src="../scripts/insertion.js"></script>
</body>
</html>
