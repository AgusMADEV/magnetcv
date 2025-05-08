<?php
$uid=$_SESSION['user_id'];
$cv = $_GET['cv_id'] ?? $_SESSION['cv_id'];

/* datos */
$personal=$db->prepare("SELECT * FROM personal_data WHERE user_id=? AND cv_id=?");
$personal->execute([$uid,$cv]);$personal=$personal->fetch(PDO::FETCH_ASSOC);

$competencias=$db->prepare("SELECT * FROM competencias WHERE user_id=? AND cv_id=?");
$competencias->execute([$uid,$cv]);$competencias=$competencias->fetchAll(PDO::FETCH_ASSOC);

$hobbies=$db->prepare("SELECT hobby FROM hobbies WHERE user_id=? AND cv_id=?");
$hobbies->execute([$uid,$cv]);$hobbies=$hobbies->fetchAll(PDO::FETCH_COLUMN);

$present=$db->prepare("SELECT texto FROM presentacion WHERE user_id=? AND cv_id=?");
$present->execute([$uid,$cv]);$present=$present->fetchColumn();

$experiencia=$db->prepare("SELECT * FROM experiencia WHERE user_id=? AND cv_id=?");
$experiencia->execute([$uid,$cv]);$experiencia=$experiencia->fetchAll(PDO::FETCH_ASSOC);

$formacion=$db->prepare("SELECT * FROM formacion WHERE user_id=? AND cv_id=?");
$formacion->execute([$uid,$cv]);$formacion=$formacion->fetchAll(PDO::FETCH_ASSOC);

/* estado del CV */
$status=$db->prepare("SELECT status FROM cv_profiles WHERE id=? AND user_id=?");
$status->execute([$cv,$uid]);$status=$status->fetchColumn();
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($personal['nombre']??'')?> – CV</title>
<link rel="stylesheet" href="style.css">
<style>body{background:#fff}.container{box-shadow:none;width:90%;max-width:800px}</style>
</head><body>
<div class="container">
  <?php if($status==='draft'): ?>
    <form method="post" action="index.php?page=publish_cv&cv_id=<?=$cv?>">
      <button class="btn-save" type="submit">Guardar CV</button>
    </form>
  <?php endif; ?>

  <h1><?=htmlspecialchars($personal['nombre']??'')?> <?=htmlspecialchars($personal['apellidos']??'')?></h1>
  <?php if($personal): ?>
    <p><strong>Fecha de Nacimiento:</strong> <?=htmlspecialchars($personal['fecha_nacimiento'])?></p>
    <p><strong>Dirección:</strong> <?=htmlspecialchars($personal['direccion'])?>
       | <strong>Teléfono:</strong> <?=htmlspecialchars($personal['telefono'])?></p>
    <hr>
  <?php endif; ?>

  <?php if($present): ?>
    <h2>Presentación</h2>
    <p><?=nl2br(htmlspecialchars($present))?></p><hr>
  <?php endif; ?>

  <?php if($competencias): ?>
    <h2>Competencias</h2>
    <ul>
      <?php foreach($competencias as $c): ?>
        <li><?=htmlspecialchars($c['competencia'])?> – <?=htmlspecialchars($c['nivel'])?></li>
      <?php endforeach;?>
    </ul><hr>
  <?php endif; ?>

  <?php if($experiencia): ?>
    <h2>Experiencia Profesional</h2>
    <?php foreach($experiencia as $e): ?>
      <div>
        <strong><?=htmlspecialchars($e['empresa'])?></strong> – <?=htmlspecialchars($e['puesto'])?>
        (<?=htmlspecialchars($e['inicio'])?> a <?=htmlspecialchars($e['fin'])?>)
        <p><?=nl2br(htmlspecialchars($e['descripcion']))?></p>
      </div>
    <?php endforeach;?><hr>
  <?php endif; ?>

  <?php if($formacion): ?>
    <h2>Formación</h2>
    <?php foreach($formacion as $f): ?>
      <div>
        <strong><?=htmlspecialchars($f['titulo'])?></strong> – <?=htmlspecialchars($f['institucion'])?>
        (<?=htmlspecialchars($f['inicio'])?> a <?=htmlspecialchars($f['fin'])?>)
        <p><?=nl2br(htmlspecialchars($f['descripcion']))?></p>
      </div>
    <?php endforeach;?><hr>
  <?php endif; ?>

  <?php if($hobbies): ?>
    <h2>Hobbies</h2>
    <p><?=implode(', ',array_map('htmlspecialchars',$hobbies))?></p>
  <?php endif; ?>
</div>
</body></html>
