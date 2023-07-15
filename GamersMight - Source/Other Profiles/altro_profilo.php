<?php
$conn = pg_connect("host=localhost port=5432 dbname=GMDB user=postgres password=pass") 
or die('Could not connect: ' . pg_last_error());
session_start();
if (!isset($_SESSION['id']) && isset($_COOKIE['rememberMe'])) {
  $userId = $_COOKIE['rememberMe'];

  $id = pg_escape_string($conn, $userId);
  $query = "SELECT * FROM utente WHERE id = '$id'";
  $result = pg_query($conn, $query);
  $user = pg_fetch_assoc($result);

  if ($user) {
      $_SESSION['id'] = $user['id'];
      if (isset($_GET['username'])) {
        $username = $_GET['username'];
        $query = "SELECT * FROM utente WHERE username=$1";
        $result = pg_query_params($conn, $query, array($username));
        if (!$result) {
          echo "Errore durante l'esecuzione della query.";
        exit();
      }
    
    $user = pg_fetch_assoc($result);
    $description=$user['description'];
    $profile_image=$user['profile_image'];
    $categoria=$user['categoria'];
    
    $query = "SELECT * FROM post WHERE autore = '$username'";
    $PostResult = pg_query($conn, $query);
    $posts = pg_fetch_all($PostResult);
    }
  } else {
      header("Location: ../login/log.php");
      exit();
  }
  pg_close($conn);
}
else if (!isset($_SESSION['id'])) {
  header("Location: ../login/log.php");
  exit();
}
else{
$id = $_SESSION['id'];
if (isset($_GET['username'])) {
  $username = $_GET['username'];
  $query = "SELECT * FROM utente WHERE username=$1";
$result = pg_query_params($conn, $query, array($username));
if (!$result) {
echo "Errore durante l'esecuzione della query.";
exit();
}

$user = pg_fetch_assoc($result);
$description=$user['description'];
$profile_image=$user['profile_image'];
$categoria=$user['categoria'];

$query = "SELECT * FROM post WHERE autore = '$username'";
$PostResult = pg_query($conn, $query);
$posts = pg_fetch_all($PostResult);
}
pg_close($conn);
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gamersmight</title>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="../bootstrap/js/bootstrap.js">
        <link rel="stylesheet" href="../css/home_style.css">
        <link rel="stylesheet" href="../css/profilo_style.css">
        <link rel="stylesheet" href="../css/other_posts.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="profilo.js"></script>
    </head>
    <script>
      window.addEventListener('DOMContentLoaded', function() {
            var showPostImages = document.getElementsByClassName('profile-media');
            for (var i = 0; i < showPostImages.length; i++) {
                var image = showPostImages[i];
                var imageRatio = image.naturalWidth / image.naturalHeight;
                if (imageRatio < 1) {
                    image.classList.add('vertical-image');
                }
                else{
                  image.classList.add('horizontal-image');
                }             
            }
            });
    </script>
    <body class="profilo-body">
    <header>
            <div class="navb">
                <div class="container-navb a">
                    <div class="hamburger-icon" onclick="toggleSidebar()">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </div>                      
                    <a href="../home/home.html"><img class="desktop-img" src="../img/entireLogo - Copia.png" alt="home_pic"></a>
                    <a class="mobile-img" href="../home/home.html"><img class="mobile-img" src="../img/logo.png" alt="home_pic"></a>
                </div>
                <form class="container-navb m" action="../searches/search.php" method="POST">
                    <button id="submitBtn" type="submit" disabled><img class="search-icon" src="../img/search.png" alt=""></button>
                    <input class="search-bar" name="searchInput" type="text" placeholder="Search..." onkeyup="validateSearchInput()">
                </form>
                <div class="container-navb d">
                    <a href="../post/post.php"><img src="../img/add.png" alt="" class="post"></a>
                    <a href="../newsletter/newsletter.html"><img src="../img/group-users.png" alt="" class="community"></a>
                    <a href="../impostazioni/gestione.php"><img src="../img/settings.png" alt="" class="dark_mode"></a>
                </div>
            </div>
        </header>
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="x-icon" onclick="toggleSidebar()">
                    <span class="close-line" ></span>
                    <span class="close-line"></span>
                </div>
            </div>
            <hr>
            <a href="../home/home.html">Home</a>
            <hr class="linea">
            <a href="../contatti/contact_us.html">Contattaci</a>
            <hr class="linea">
            
            <a href="../profilo/profilo.php">Profilo</a>
            <hr class="linea">
            <a href="../logout/logout.php">Logout</a>
            <hr class="linea">
            <div class="non-mobile icons">
                <a href="../post/post.php">Post</a>
                <hr class="linea">
                <a href="../newsletter/newsletter.html">Newsletter</a>
                <hr class="linea">
                <a href="../impostazioni/gestione.php">Impostazioni</a>
                <hr class="linea">
            </div>
        </div>
          <div class="top">
            <div class="top-left">
              <img class="profile_pic" src=<?php echo $profile_image?> alt="Profile_Img">
            </div>
            <div class="top-right">
              <div class="container-top-right">
                <h1> <?php echo $username ?></h1>
                <p><?php echo $categoria ?></p>             
                <p><?php echo $description?></p>
              </div>
            </div>
          </div>
          <hr class="linea">
          <div class="mid">
            <h1>Post</h1>
          <hr class="linea">
          <div class="post-show">
            <?php foreach ($posts as $post) {?>
              <div class="elem-post">
              <?php 
                  $file_path = $post['file_path'];
                  $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                  if ($file_extension === 'jpg' || $file_extension === 'jpeg' || $file_extension === 'png') {
                    echo '<img class="profile-media" src="' . $file_path . '" alt="">';
                  } elseif ($file_extension === 'mp4') {
                    echo '<video controls class="profile-media"><source src="' . $file_path . '" type="video/mp4"></video>';
                  } ?>
                  <h1><?php echo $post['titolo']; ?></h1>
                  <textarea name="descrizione" cols="30" rows="10" readonly><?php echo $post['descrizione']; ?></textarea>
              </div>
              <?php }?>
            </div>
          </div>
    </body>
</html>