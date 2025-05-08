<?php
$uid=$_SESSION['user_id'];$cv=$_SESSION['cv_id'];
$st=$db->prepare("SELECT * FROM formacion WHERE user_id=? AND cv_id=?");
$st->execute([$uid,$cv]);$rows=$st->fetchAll(PDO::FETCH_ASSOC) ?: [['titulo'=>'','institucion'=>'','inicio'=>'','fin'=>'','descripcion'=>'']];
?>
<h2>Formación académica</h2>
<form method="post" action="index.php?page=formacion&cv_id=<?=$cv?>">
  <?php foreach($rows as $r): ?>
    <hr>
    <input name="titulo[]"      value="<?=htmlspecialchars($r['titulo'])?>"      placeholder="Título o grado">
    <input name="institucion[]" value="<?=htmlspecialchars($r['institucion'])?>" placeholder="Institución">
    <input name="inicio[]"      value="<?=htmlspecialchars($r['inicio'])?>"      placeholder="Inicio (YYYY-MM)">
    <input name="fin[]"         value="<?=htmlspecialchars($r['fin'])?>"         placeholder="Fin (YYYY-MM)">
    <textarea name="descripcion[]" rows="3" placeholder="Descripción"><?=htmlspecialchars($r['descripcion'])?></textarea>
  <?php endforeach;?>
  <button class="primary">Guardar</button>
</form>
