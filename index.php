<?php
session_start();

/* ---------- Conexión SQLite ---------- */
$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* ---------- Esquema ---------- */
$schema = [
  "users(id INTEGER PRIMARY KEY AUTOINCREMENT,
         nombre TEXT,email TEXT,username TEXT UNIQUE,password TEXT)",

  /* cv_profiles: status = draft / published */
  "cv_profiles(id INTEGER PRIMARY KEY AUTOINCREMENT,
               user_id INTEGER,titulo TEXT,
               created_at TEXT,status TEXT DEFAULT 'draft')",

  "personal_data(id INTEGER PRIMARY KEY AUTOINCREMENT,
         user_id INTEGER,cv_id INTEGER,
         nombre TEXT,apellidos TEXT,fecha_nacimiento TEXT,direccion TEXT,telefono TEXT)",

  "competencias(id INTEGER PRIMARY KEY AUTOINCREMENT,
         user_id INTEGER,cv_id INTEGER,
         competencia TEXT,nivel TEXT)",

  "hobbies(id INTEGER PRIMARY KEY AUTOINCREMENT,
         user_id INTEGER,cv_id INTEGER,
         hobby TEXT)",

  "presentacion(id INTEGER PRIMARY KEY AUTOINCREMENT,
         user_id INTEGER,cv_id INTEGER,
         texto TEXT)",

  "experiencia(id INTEGER PRIMARY KEY AUTOINCREMENT,
         user_id INTEGER,cv_id INTEGER,
         empresa TEXT,puesto TEXT,inicio TEXT,fin TEXT,descripcion TEXT)",

  "formacion(id INTEGER PRIMARY KEY AUTOINCREMENT,
         user_id INTEGER,cv_id INTEGER,
         titulo TEXT,institucion TEXT,inicio TEXT,fin TEXT,descripcion TEXT)"
];
foreach($schema as $sql){ $db->exec("CREATE TABLE IF NOT EXISTS $sql"); }

/* ---------- Usuario inicial ---------- */
if (!$db->query("SELECT COUNT(*) FROM users")->fetchColumn()){
  $db->prepare("INSERT INTO users(nombre,email,username,password)
                VALUES(?,?,?,?)")->execute([
      'Agustín Morcillo Aguado','info@agusmadev.es',
      'agusmadev',password_hash('agusmadev',PASSWORD_DEFAULT)
  ]);
}

/* ---------- Helpers ---------- */
function setCurrentCV($db,$uid){
  if(!isset($_SESSION['cv_id'])){
    $cid=$db->prepare("SELECT id FROM cv_profiles WHERE user_id=? ORDER BY id LIMIT 1");
    $cid->execute([$uid]);
    $_SESSION['cv_id']=$cid->fetchColumn();
  }
}

/* ---------- Logout ---------- */
if (isset($_GET['logout'])){
  session_destroy();header('Location: index.php?page=login');exit;
}

/* ---------- Página solicitada ---------- */
$page = $_GET['page'] ?? (isset($_SESSION['user_id']) ? 'dashboard' : 'login');

/* ---------- Cambio de CV (GET) ---------- */
if(isset($_GET['cv_id']) && ctype_digit($_GET['cv_id'])){
  $_SESSION['cv_id']=(int)$_GET['cv_id'];
}

/* ---------- Procesamiento POST ---------- */
if ($_SERVER['REQUEST_METHOD']==='POST'){
  switch($page){

    /* ------- Registro ------- */
    case 'register':
      $db->prepare("INSERT INTO users(nombre,email,username,password)
                    VALUES(?,?,?,?)")->execute([
          $_POST['nombre'],$_POST['email'],
          $_POST['username'],password_hash($_POST['password'],PASSWORD_DEFAULT)
      ]);
      header('Location: index.php?page=login');exit;

    /* ------- Login ------- */
    case 'login':
      $st=$db->prepare("SELECT * FROM users WHERE username=?");
      $st->execute([$_POST['username']]);$user=$st->fetch(PDO::FETCH_ASSOC);
      if($user && password_verify($_POST['password'],$user['password'])){
        $_SESSION['user_id']=$user['id'];$_SESSION['nombre']=$user['nombre'];
        setCurrentCV($db,$user['id']);
        header('Location: index.php?page=dashboard');exit;
      }$error='Credenciales inválidas';break;

    /* ------- Crear borrador de CV ------- */
    case 'create_cv':
      $uid=$_SESSION['user_id'];
      $titulo=trim($_POST['titulo']?:'Nuevo CV');
      $db->prepare("INSERT INTO cv_profiles(user_id,titulo,created_at,status)
                    VALUES(?,?,datetime('now'),'draft')")->execute([$uid,$titulo]);
      $_SESSION['cv_id']=$db->lastInsertId();
      header('Location: index.php?page=personal_data');exit;

    /* ------- PUBLICAR CV (botón Guardar en pre-vista) ------- */
    case 'publish_cv':
      $uid=$_SESSION['user_id'];$cid=(int)$_GET['cv_id'];
      $db->prepare("UPDATE cv_profiles SET status='published' WHERE id=? AND user_id=?")
         ->execute([$cid,$uid]);
      header('Location: index.php?page=dashboard');exit;

    /* ------- Guardar secciones ------- */
    default:
      if(!isset($_SESSION['user_id'],$_SESSION['cv_id'])) break;
      $uid=$_SESSION['user_id'];$cv=$_SESSION['cv_id'];

      /* Datos personales */
      if($page==='personal_data'){
        $db->prepare("DELETE FROM personal_data WHERE user_id=? AND cv_id=?")
           ->execute([$uid,$cv]);
        $db->prepare("INSERT INTO personal_data(user_id,cv_id,nombre,apellidos,fecha_nacimiento,direccion,telefono)
                      VALUES(?,?,?,?,?,?,?)")->execute([
            $uid,$cv,$_POST['nombre'],$_POST['apellidos'],$_POST['fecha_nacimiento'],
            $_POST['direccion'],$_POST['telefono']
        ]);
        header('Location: index.php?page=dashboard');exit;
      }

      /* Competencias */
      if($page==='competencias'){
        $db->prepare("DELETE FROM competencias WHERE user_id=? AND cv_id=?")->execute([$uid,$cv]);
        foreach($_POST['competencia'] as $i=>$c){
          if(trim($c)==='') continue;
          $db->prepare("INSERT INTO competencias(user_id,cv_id,competencia,nivel)
                        VALUES(?,?,?,?)")->execute([$uid,$cv,$c,$_POST['nivel'][$i]]);
        }header('Location: index.php?page=dashboard');exit;
      }

      /* Hobbies */
      if($page==='hobbies'){
        $db->prepare("DELETE FROM hobbies WHERE user_id=? AND cv_id=?")->execute([$uid,$cv]);
        foreach($_POST['hobby'] as $h){
          if(trim($h)==='') continue;
          $db->prepare("INSERT INTO hobbies(user_id,cv_id,hobby) VALUES(?,?,?)")->execute([$uid,$cv,$h]);
        }header('Location: index.php?page=dashboard');exit;
      }

      /* Presentación */
      if($page==='presentacion'){
        $db->prepare("DELETE FROM presentacion WHERE user_id=? AND cv_id=?")->execute([$uid,$cv]);
        $db->prepare("INSERT INTO presentacion(user_id,cv_id,texto) VALUES(?,?,?)")
           ->execute([$uid,$cv,$_POST['texto']]);
        header('Location: index.php?page=dashboard');exit;
      }

      /* Experiencia */
      if($page==='experiencia'){
        $db->prepare("DELETE FROM experiencia WHERE user_id=? AND cv_id=?")->execute([$uid,$cv]);
        foreach($_POST['empresa'] as $i=>$emp){
          if(trim($emp)==='') continue;
          $db->prepare("INSERT INTO experiencia(user_id,cv_id,empresa,puesto,inicio,fin,descripcion)
                        VALUES(?,?,?,?,?,?,?)")->execute([
              $uid,$cv,$emp,$_POST['puesto'][$i],$_POST['inicio'][$i],
              $_POST['fin'][$i],$_POST['descripcion'][$i]
          ]);
        }header('Location: index.php?page=dashboard');exit;
      }

      /* Formación */
      if($page==='formacion'){
        $db->prepare("DELETE FROM formacion WHERE user_id=? AND cv_id=?")->execute([$uid,$cv]);
        foreach($_POST['titulo'] as $i=>$tit){
          if(trim($tit)==='') continue;
          $db->prepare("INSERT INTO formacion(user_id,cv_id,titulo,institucion,inicio,fin,descripcion)
                        VALUES(?,?,?,?,?,?,?)")->execute([
              $uid,$cv,$tit,$_POST['institucion'][$i],$_POST['inicio'][$i],
              $_POST['fin'][$i],$_POST['descripcion'][$i]
          ]);
        }header('Location: index.php?page=dashboard');exit;
      }
  }
}

/* ---------- Acciones GET protegidas ---------- */
if($page==='delete_cv' && isset($_SESSION['user_id'],$_GET['cv_id'])){
  $uid=$_SESSION['user_id'];$cid=(int)$_GET['cv_id'];
  $tables=['personal_data','competencias','hobbies','presentacion','experiencia','formacion'];
  foreach($tables as $t){
    $db->prepare("DELETE FROM $t WHERE cv_id=? AND user_id=?")->execute([$cid,$uid]);
  }
  $db->prepare("DELETE FROM cv_profiles WHERE id=? AND user_id=?")->execute([$cid,$uid]);
  unset($_SESSION['cv_id']);
  header('Location: index.php?page=dashboard');exit;
}

/* ---------- Protección páginas privadas ---------- */
$public=['login','register'];
if(!in_array($page,$public) && !isset($_SESSION['user_id'])){
  header('Location: index.php?page=login');exit;
}

/* ---------- Post-login: CV actual ---------- */
if(isset($_SESSION['user_id'])) setCurrentCV($db,$_SESSION['user_id']);
$cvCurrent=$_SESSION['cv_id']??null;
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>MagnetCV</title><link rel="stylesheet" href="style.css"></head>
<body>
<header>MagnetCV – Gestor de Currículum</header>

<div class="wrapper">
<?php if(isset($_SESSION['user_id'])): ?>
  <aside class="sidebar">
    <div class="menu-top">
      <?php
        $links=[
          'dashboard'=>'Inicio',
          'personal_data'=>'Datos personales',
          'competencias'=>'Competencias',
          'hobbies'=>'Hobbies',
          'presentacion'=>'Presentación',
          'experiencia'=>'Experiencia',
          'formacion'=>'Formación'];
        foreach($links as $k=>$txt){
          $active=$page===$k?'active':'';
          echo "<a class=\"$active\" href=\"index.php?page=$k&cv_id=$cvCurrent\">$txt</a>";
        }
      ?>
      <div class="sep"></div>
      <a href="index.php?page=cv_template1&cv_id=<?=$cvCurrent?>" target="_blank">Ver CV</a>
    </div>
    <a class="logout" href="index.php?page=login&logout=1">Cerrar sesión</a>
  </aside>
<?php endif; ?>

  <main class="content">
    <?php include __DIR__ . "/templates/{$page}.php"; ?>
  </main>
</div>
</body></html>
