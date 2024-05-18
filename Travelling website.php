<?php
$host = 'localhost';
$db = 'travel_website';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $email]);

    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
<?php
include 'db.php';

$stmt = $pdo->query("SELECT * FROM destinations");
$destinations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Destinations</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Travel Destinations</h1>
    <div class="destinations">
        <?php foreach ($destinations as $destination): ?>
            <div class="destination">
                <h2><?php echo htmlspecialchars($destination['name']); ?></h2>
                <p><?php echo htmlspecialchars($destination['description']); ?></p>
                <p>Price: $<?php echo number_format($destination['price'], 2); ?></p>
                <img src="images/<?php echo htmlspecialchars($destination['image']); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>">
                <a href="book.php?id=<?php echo $destination['id']; ?>">Book Now</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $destination_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    $booking_date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, destination_id, booking_date) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $destination_id, $booking_date]);

    echo "Booking confirmed!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Trip</title>
</head>
<body>
    <h2>Booking Confirmation</h2>
    <p>Your booking has been confirmed. Thank you for choosing our service!</p>
    <a href="index.php">Go back to Destinations</a>
</body>
</html>
<?php
session_start();
session_destroy();
header('Location: login.php');
?>
