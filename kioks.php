<?php
// Veritabanına bağlanma
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopping_mall";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Veritabanından mağaza ve kiosk konumlarını almak
$stores = array();
$kiosks = array();
$closed = array();

$sql = "SELECT * FROM stores";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $stores[$row["name"]] = array($row["x"], $row["y"]);
  }
}

$sql = "SELECT * FROM kiosks";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $kiosks[$row["id"]] = array($row["x"], $row["y"]);
  }
}

$sql = "SELECT * FROM closed";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $closed[] = array($row["x"], $row["y"]);
  }
}

// Seçilen kiosk ve mağazayı almak
$kiosk_id = $_POST["kiosk_id"];
$store_name = $_POST["store_name"];

// Kiosk ve mağazanın koordinatlarını almak
$kiosk_x = $kiosks[$kiosk_id][0];
$kiosk_y = $kiosks[$kiosk_id][1];
$store_x = $stores[$store_name][0];
$store_y = $stores[$store_name][1];

// Kiosk ve mağaza arasındaki en kısa yolu hesaplamak
$path = array();
$path[] = array($kiosk_x, $kiosk_y); // Başlangıç noktası

// X ekseninde hareket etmek
if ($kiosk_x < $store_x) {
  for ($i = $kiosk_x + 1; $i <= $store_x; $i++) {
    if (in_array(array($i, $kiosk_y), $closed)) { // Kapalı noktaya gelirse
      break; // X ekseninde hareket etmeyi durdur
    } else {
      $path[] = array($i, $kiosk_y); // Yolu güncelle
    }
  }
} else if ($kiosk_x > $store_x) {
  for ($i = $kiosk_x - 1; $i >= $store_x; $i--) {
    if (in_array(array($i, $kiosk_y), $closed)) { // Kapalı noktaya gelirse
      break; // X ekseninde hareket etmeyi durdur
    } else {
      $path[] = array($i, $kiosk_y); // Yolu güncelle
    }
  }
}

// Son noktanın koordinatlarını almak
$last_x = end($path)[0];
$last_y = end($path)[1];

// Y ekseninde hareket etmek
if ($last_y < $store_y) {
  for ($j = $last_y + 1; $j <= $store_y; $j++) {
    if (in_array(array($last_x, $j), $closed)) { // Kapalı noktaya gelirse
      break; // Y ekseninde hareket etmeyi durdur
    } else {
      $path[] = array($last_x, $j); // Yolu güncelle
    }
  }
} else if ($last_y > $store_y) {
  for ($j = $last_y - 1; $j >= $store_y; $j--) {
    if (in_array(array($last_x, $j), $closed)) { // Kapalı noktaya gelirse
      break; // Y ekseninde hareket etmeyi durdur
    } else {
      $path[] = array($last_x, $j); // Yolu güncelle
    }
  }
}

// Bitiş noktasını eklemek
$path[] = array($store_x, $store_y);

// Yolu ekranda göstermek
echo "<table border='1'>";
for ($y = 1; $y <= 11; $y++) {
  echo "<tr>";
  for ($x = 1; $x <= 11; $x++) {
    echo "<td>";
    if (in_array(array($x, $y), $path)) { // Yolun bir parçasıysa
      echo "X"; // X işareti koy
    } else if (in_array(array($x, $y), $closed)) { // Kapalı noktaysa
      echo "C"; // C işareti koy
    } else if ($x == $kiosk_x && $y == $kiosk_y) { // Kiosksa
      echo "K"; // K işareti koy
    } else if ($x == $store_x && $y == $store_y) { // Mağazaysa
      echo "M"; // M işareti koy
    } else {
      echo "&nbsp;"; // Boşluk bırak
    }
    echo "</td>";
  }
  echo "</tr>";
}
echo "</table>";
?>