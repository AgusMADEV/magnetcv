<?php
$uid=$_SESSION['user_id'];$cv=$_SESSION['cv_id'];
$st=$db->prepare("SELECT * FROM experiencia WHERE user_id=? AND cv_id=?");
$st->execute([$uid,$cv]);$rows=$st->fetchAll(PDO::FETCH_ASSOC) ?: [['empresa'=>'','puesto'=>'','inicio'=>'','fin'=>'','descripcion'=>'']];
?>
<h2>Experiencia profesional</h2>
<form method="post" action="index.php?page=experiencia&cv_id=<?=$cv?>">
  <?php foreach($rows as $r): ?>
    <hr>
    <input name="empresa[]"     value="<?=htmlspecialchars($r['empresa'])?>"     placeholder="Empresa">
    <input name="puesto[]"      value="<?=htmlspecialchars($r['puesto'])?>"      placeholder="Puesto">
    <input name="inicio[]"      value="<?=htmlspecialchars($r['inicio'])?>"      placeholder="Inicio (YYYY-MM)">
    <input name="fin[]"         value="<?=htmlspecialchars($r['fin'])?>"         placeholder="Fin (YYYY-MM o Actual)">
    <textarea name="descripcion[]" rows="3" placeholder="Descripción"><?=htmlspecialchars($r['descripcion'])?></textarea>
  <?php endforeach;?>
  <button class="primary">Guardar</button>
</form>
