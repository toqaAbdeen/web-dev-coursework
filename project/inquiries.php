<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit;
}

$params = [];
$where = [];

if (!empty($_GET['from_date'])) {
    $where[] = "f.available_from >= :from_date";
    $params[':from_date'] = $_GET['from_date'];
}
if (!empty($_GET['to_date'])) {
    $where[] = "f.available_to <= :to_date";
    $params[':to_date'] = $_GET['to_date'];
}
if (!empty($_GET['available_on'])) {
    $where[] = ":available_on BETWEEN f.available_from AND f.available_to";
    $params[':available_on'] = $_GET['available_on'];
}
if (!empty($_GET['location'])) {
    $where[] = "f.location LIKE :location";
    $params[':location'] = '%' . $_GET['location'] . '%';
}
if (!empty($_GET['owner'])) {
    $where[] = "o.name LIKE :owner";
    $params[':owner'] = '%' . $_GET['owner'] . '%';
}
if (!empty($_GET['customer'])) {
    $where[] = "c.name LIKE :customer";
    $params[':customer'] = '%' . $_GET['customer'] . '%';
}

$allowedSort = ['reference_number', 'rent_cost', 'start_date', 'end_date', 'location'];
$sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'r.start_date';
$dir = ($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$sql = "SELECT r.*, f.reference_number, f.rent_cost, f.location, f.flat_id,
               o.name AS owner_name, o.user_id AS owner_id,
               c.name AS customer_name, c.user_id AS customer_id
        FROM rentals r
        JOIN flats f ON r.flat_id = f.flat_id
        JOIN users o ON f.owner_id = o.user_id
        JOIN users c ON r.customer_id = c.user_id";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY $sort $dir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manager Inquiry</title>
  <link rel="stylesheet" href="style.css">

</head>
<body>
<?php include 'header.php'; include 'nav.php'; ?>
<main>
  <section class="card">
    <h2>Manager Inquiry</h2>
    <form method="GET" class="form-inline">
      <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>" placeholder="From">
      <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>" placeholder="To">
      <input type="date" name="available_on" value="<?= htmlspecialchars($_GET['available_on'] ?? '') ?>" placeholder="Available On">
      <input type="text" name="location" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>" placeholder="Location">
      <input type="text" name="owner" value="<?= htmlspecialchars($_GET['owner'] ?? '') ?>" placeholder="Owner">
      <input type="text" name="customer" value="<?= htmlspecialchars($_GET['customer'] ?? '') ?>" placeholder="Customer">
      <button type="submit" class="btn">Search</button>
    </form>

    <?php if (empty($results)): ?>
      <p>No results found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th><a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'reference_number', 'dir' => $dir === 'ASC' ? 'DESC' : 'ASC'])) ?>">Ref #</a></th>
            <th><a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'rent_cost', 'dir' => $dir === 'ASC' ? 'DESC' : 'ASC'])) ?>">Rent</a></th>
            <th><a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'start_date', 'dir' => $dir === 'ASC' ? 'DESC' : 'ASC'])) ?>">Start</a></th>
            <th><a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'end_date', 'dir' => $dir === 'ASC' ? 'DESC' : 'ASC'])) ?>">End</a></th>
            <th><a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'location', 'dir' => $dir === 'ASC' ? 'DESC' : 'ASC'])) ?>">Location</a></th>
            <th>Owner</th>
            <th>Customer</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $r): ?>
          <tr>
            <td><a href="flat_details.php?id=<?= $r['flat_id'] ?>" class="btn-link" target="_blank"><?= htmlspecialchars($r['reference_number']) ?></a></td>
            <td><?= number_format($r['rent_cost'], 2) ?> NIS</td>
            <td><?= htmlspecialchars($r['start_date']) ?></td>
            <td><?= htmlspecialchars($r['end_date']) ?></td>
            <td><?= htmlspecialchars($r['location']) ?></td>
            <td><a href="user_card.php?id=<?= $r['owner_id'] ?>" target="_blank"><?= htmlspecialchars($r['owner_name']) ?></a></td>
            <td><a href="user_card.php?id=<?= $r['customer_id'] ?>" target="_blank"><?= htmlspecialchars($r['customer_name']) ?></a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
