<?php

/**
 * @file
 * @brief This script provides validation of inputs of user publish-insertion form, including the attached files.
 * 
 * Validates every field of the form. Resizes images and saves them in the local /media/ folder by newly generated names.
 * Avatar is resized and saved separately by the newly generated name in the local /avatar/ folder. Creates new record in the
 * 'insertions' table of the database and saves insertion data, including the address of the avatar.
 * Addresses of the regular images are saved into the 'images' table by insertion_id and their order_number among the images of this
 * particular insertion. Terminates the scripts, deletes all the recently-saved images of current insertion in /media/ and /avatar/ folders
 * and redirects back to publish-insertion form with error context parameter and all the user inputs except files and values of select fields
 * as parameters ofthe redirecting GET request in case of error of data validation or database communication.
 */

session_start();

/**
 * Redirects to the publish-insertion page with error context parameter and all the user inputs except files and values of select fields
 * as parameters ofthe redirecting GET request in case of error of data validation or database communication.
 * 
 * @param string $errorParam The error parameter to append to the query string.
 */
function terminatePublishInsertionWithError($errorParam) {
    $postData = $_POST;
    $postData['error'] = $errorParam;
    unset($postData['make'], $postData['model'], $postData['fuel']);
    $queryString = http_build_query($postData);
    header("Location: publish-insertion.php?$queryString");
    exit;
}

/**
 * Cleans up uploaded files and redirects with the specified error parameter.
 * 
 * @param string $errorParam The error parameter to append to the query string.
 * @param array $mediaFiles The list of media files to remove.
 * @param string $avatarFile The avatar file to remove.
 */
function terminateWithRemove($errorParam, $mediaFiles, $avatarFile) {
    $mediaDir = 'media/';
    $avatarDir = 'avatar/';

    foreach ($mediaFiles as $file) {
        $filePath = $mediaDir . $file;
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
    }

    $avatarPath = $avatarDir . $avatarFile;
    if (file_exists($avatarPath)) {
        unlink($avatarPath); 
    }

    terminatePublishInsertionWithError($errorParam);
}

if (!isset($_SESSION['user_id'])) {
    terminatePublishInsertionWithError("session_invalid_id");
}

/**
 * Validates if a number is within a specified range.
 * 
 * @param mixed $value The value to validate.
 * @param int $min_value The minimum allowed value.
 * @param int $max_value The maximum allowed value.
 * @return bool True if valid, false otherwise.
 */
function is_valid_number($value, $min_value, $max_value) {
    if (!isset($value) || !is_numeric($value)) {
        return false;
    }
    if ((int)$value < $min_value || (int)$value > $max_value) {
        return false;
    }
    return true;
}

/**
 * Validates the length of a text field.
 * 
 * @param string $value The text value to validate.
 * @param int $max_length The maximum allowed length.
 * @return bool True if valid, false otherwise.
 */
function is_valid_text($value, $max_length) {
    if (isset($value) && strlen($value) > $max_length) {
        return false;
    }
    return true;
}

/**
 * Validates if a selection is valid (not default or unset).
 * 
 * @param string $value The value to validate.
 * @return bool True if valid, false otherwise.
 */
function is_valid_select($value) {
    if (!isset($value) || $value === "default") {
        return false;
    }
    return true;
}

/**
 * Resizes an image to the specified dimensions while maintaining aspect ratio.
 * 
 * @param resource $sourceImage The source image resource.
 * @param int $newWidth The new width of the image.
 * @param int $newHeight The new height of the image.
 * @return resource The resized image resource.
 */
function resizeImage($sourceImage, $newWidth, $newHeight) {
    $originalWidth = imagesx($sourceImage);
    $originalHeight = imagesy($sourceImage);

    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    $black = imagecolorallocate($resizedImage, 0, 0, 0);
    imagefill($resizedImage, 0, 0, $black);

    $scale = min($newWidth / $originalWidth, $newHeight / $originalHeight);
    $scaledWidth = (int)($originalWidth * $scale);
    $scaledHeight = (int)($originalHeight * $scale);

    $xOffset = (int)(($newWidth - $scaledWidth) / 2);
    $yOffset = (int)(($newHeight - $scaledHeight) / 2);

    imagecopyresampled(
        $resizedImage, $sourceImage,
        $xOffset, $yOffset, 0, 0,
        $scaledWidth, $scaledHeight,
        $originalWidth, $originalHeight
    );

    return $resizedImage;
}

// Variables initialized from POST data.
$make = $_POST['make'] ?? '';
$model = $_POST['model'] ?? '';
$short_description = $_POST['short_description'] ?? '';
$price = $_POST['price'] ?? '';
$year = $_POST['year'] ?? '';
$mileage = $_POST['mileage'] ?? '';
$power = $_POST['power'] ?? '';
$fuel = $_POST['fuel'] ?? '';
$engine_capacity = $_POST['engine_capacity'] ?? '';
$description = $_POST['description'] ?? '';

// Trimming text fields.
$short_description = trim($short_description);
$description = trim($description);

// Minimum and maximum values for number fields.
$number_fields_min_values = [
    'price' => 10000,
    'year' => 1980,
    'mileage' => 0,
    'power' => 30,
    'engine_capacity' => 1000,
];

$number_fields_max_values = [
    'price' => 20000000,
    'year' => 2026,
    'mileage' => 3000000,
    'power' => 5000,
    'engine_capacity' => 8000,
];

// Maximum lengths for text fields.
$text_fields_max_lengths = [
    'short_description' => 50,
    'description' => 600
];

// Form validation.
$isFormValid = true;
$isFormValid = is_valid_select($make) && $isFormValid;
$isFormValid = is_valid_select($model) && $isFormValid;
$isFormValid = is_valid_select($fuel) && $isFormValid;
$isFormValid = is_valid_number($price, $number_fields_min_values['price'], $number_fields_max_values['price']) && $isFormValid;
$isFormValid = is_valid_number($year, $number_fields_min_values['year'], $number_fields_max_values['year']) && $isFormValid;
$isFormValid = is_valid_number($mileage, $number_fields_min_values['mileage'], $number_fields_max_values['mileage']) && $isFormValid;
$isFormValid = is_valid_number($power, $number_fields_min_values['power'], $number_fields_max_values['power']) && $isFormValid;
$isFormValid = is_valid_number($engine_capacity, $number_fields_min_values['engine_capacity'], $number_fields_max_values['engine_capacity']) && $isFormValid;
$isFormValid = is_valid_text($short_description, $text_fields_max_lengths['short_description']) && $isFormValid;
$isFormValid = is_valid_text($description, $text_fields_max_lengths['description']) && $isFormValid;

// Terminate if the form is invalid.
if (!$isFormValid) {
    terminatePublishInsertionWithError("publish_user_input_invalid");
}

// Check for uploaded files.
if (empty($_FILES)) {
    terminatePublishInsertionWithError("publish_error_loading_files");
}

// Directory setup for file uploads.
$uploadDir = __DIR__ . '/media/';
$avatar_uploadDir = __DIR__ . '/avatar/';

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    terminatePublishInsertionWithError("publish_insertion_failed");
}

if (!is_dir($avatar_uploadDir) && !mkdir($avatar_uploadDir, 0777, true)) {
    terminatePublishInsertionWithError("publish_insertion_failed");
}

// File validation and resizing.
$maxFileSize = 40 * 1024 * 1024; // 40 MB
$allowedExtension = 'jpg';
$savedFiles = [];
$avatar_uniqueFileName = "filename";

// Process each uploaded file.
for ($i = 1; $i <= 5; $i++) {
    $fileKey = "photo{$i}";
    if (empty($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_PARTIAL || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        terminateWithRemove("publish_error_loading_files", $savedFiles, $avatar_uniqueFileName);
    }

    $file = $_FILES[$fileKey];

    if ($file['size'] > $maxFileSize) {
        terminateWithRemove("publish_wrong_size_or_format_photo{$i}", $savedFiles, $avatar_uniqueFileName);
    }

    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($fileExtension) !== $allowedExtension) {
        terminateWithRemove("publish_wrong_size_or_format_photo{$i}", $savedFiles, $avatar_uniqueFileName);
    }

    $tempFilePath = $file['tmp_name'];
    $sourceImage = imagecreatefromjpeg($tempFilePath);
    $resizedImage = resizeImage($sourceImage, 800, 600);
    $uniqueFileName = uniqid("photo{$i}_", true) . ".{$allowedExtension}";

    if ($i == 1) {
        $avatar = resizeImage($sourceImage, 200, 150);
        $avatar_uniqueFileName = uniqid("avatar_", true) . ".{$allowedExtension}";
        $avatar_path = $avatar_uploadDir . $avatar_uniqueFileName;
        if (!imagejpeg($avatar, $avatar_path, 100)) {
            terminateWithRemove("publish_insertion_failed", $savedFiles, $avatar_uniqueFileName);
        }
        imagedestroy($avatar);
    }

    $destinationPath = $uploadDir . $uniqueFileName;
    if (!imagejpeg($resizedImage, $destinationPath, 100)) {
        terminateWithRemove("publish_insertion_failed", $savedFiles, $avatar_uniqueFileName);
    }

    imagedestroy($sourceImage);
    imagedestroy($resizedImage);

    $savedFiles[] = $uniqueFileName;
}

// Database insertion.
require "connection.php"; ///< Includes the database connection file.

if (isset($error) && $error === "db_access_failed") {
    /**
     * Checks if there was a database access error.
     * If so, terminates the operation and removes temporary files.
     *
     * @param string $errorCode Error code to pass.
     * @param array $savedFiles List of saved files to be removed.
     * @param string $avatar_uniqueFileName Avatar file name to be removed.
     */
    terminateWithRemove("publish_db_access_failed", $savedFiles, $avatar_uniqueFileName);
}

try {
    /**
     * Prepares and executes an SQL statement to insert data into the `insertions` table.
     *
     * @param int    $_SESSION['user_id']   Seller ID (current user session).
     * @param string $make                  Make of the item being inserted.
     * @param string $model                 Model of the item being inserted.
     * @param string $short_description     Short description of the item.
     * @param float  $price                 Price of the item.
     * @param int    $year                  Year of manufacture.
     * @param int    $mileage               Mileage of the item.
     * @param int    $power                 Power specification.
     * @param string $fuel                  Fuel type.
     * @param float  $engine_capacity       Engine capacity.
     * @param string $avatar_uniqueFileName Path to the avatar image.
     * @param string $description           Detailed description of the item.
     */
    $stmt = $pdo->prepare(
        "INSERT INTO
            insertions
            (seller_id, 
            make, 
            model, 
            short_description, 
            price, 
            year, 
            mileage, 
            power, 
            fuel, 
            engine_capacity,
            avatar_path,
            description) 
        VALUES 
            (:seller_id, 
            :make, 
            :model, 
            :short_description, 
            :price, 
            :year, 
            :mileage, 
            :power, 
            :fuel, 
            :engine_capacity,
            :avatar_path,
            :description)"
    );
    
    $stmt->execute([
        ':seller_id' => $_SESSION['user_id'],
        ':make' => $make,
        ':model' => $model,
        ':short_description' => $short_description,
        ':price' => $price,
        ':year' => $year,
        ':mileage' => $mileage,
        ':power' => $power,
        ':fuel' => $fuel,
        ':engine_capacity' => $engine_capacity,
        ':avatar_path' => $avatar_uniqueFileName,
        'description' => $description
    ]);

    $insertion_id = $pdo->lastInsertId(); ///< Gets the last inserted ID for the `insertions` table.
} catch (PDOException $e) {
    /**
     * Handles PDO exceptions during insertion into `insertions` table.
     * Terminates the operation and removes temporary files.
     */
    terminateWithRemove("publish_insertion_failed", $savedFiles, $avatar_uniqueFileName);
}

try {
    /**
     * Prepares and executes an SQL statement to insert image data into the `images` table.
     *
     * @param int    $insertion_id ID of the insertion in the `insertions` table.
     * @param int    $i            Order number of the image.
     * @param string $savedFiles   Array containing the paths of saved images.
     */
    $stmt = $pdo->prepare("INSERT INTO images (insertion_id, order_number, image_path) VALUES (:insertion_id, :order_number, :image_path)");
    for ($i = 1; $i <= 5; $i++) {
        $stmt->execute([
            ':insertion_id' => $insertion_id,
            ':order_number' => $i,
            ':image_path' => $savedFiles[$i-1]
        ]);
    }
} catch (PDOException $e) {
    /**
     * Handles PDO exceptions during insertion into `images` table.
     * Terminates the operation and removes temporary files.
     */
    terminateWithRemove("publish_insertion_failed", $savedFiles, $avatar_uniqueFileName);
}

$pdo = null; ///< Closes the database connection.
header("Location: index.php"); ///< Redirects to the main page after successful insertion.
exit; ///< Terminates the script.