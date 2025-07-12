<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}

if (!isset($_FILES['photos']) || count($_FILES['photos']['name']) < 3) {
    $_SESSION['upload_error'] = "You must upload at least 3 flat photos.";
    header("Location: offer_flat_step1.php");
    exit;
}

$tempDir = 'uploads/temp/';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}

$photoPaths = [];
foreach ($_FILES['photos']['name'] as $i => $name) {
    if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $newName = uniqid("flat_", true) . '.' . $ext;
        $dest = $tempDir . $newName;

        if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $dest)) {
            $photoPaths[] = $dest;
        }
    }
}

$_SESSION['flat_offer'] = [
    'owner_id'          => $_SESSION['user']['id'],
    'location'          => $_POST['location'],
    'address'           => $_POST['address'],
    'rent_cost'         => $_POST['rent_cost'],
    'available_from'    => $_POST['available_from'],
    'available_to'      => $_POST['available_to'],
    'bedrooms'          => $_POST['bedrooms'],
    'bathrooms'         => $_POST['bathrooms'],
    'size'              => $_POST['size'],
    'rent_conditions'   => $_POST['rent_conditions'],
    'has_heating'       => isset($_POST['has_heating']) ? 1 : 0,
    'has_ac'            => isset($_POST['has_ac']) ? 1 : 0,
    'has_access_control'=> isset($_POST['has_access_control']) ? 1 : 0,
    'has_parking'       => isset($_POST['has_parking']) ? 1 : 0,
    'has_playground'    => isset($_POST['has_playground']) ? 1 : 0,
    'has_storage'       => isset($_POST['has_storage']) ? 1 : 0,
    'has_backyard'      => $_POST['has_backyard'],
    'photos'            => $photoPaths
];

header("Location: offer_flat_step2.php");
exit;
