<?php
$uid=$_SESSION['user_id'];$cv=$_SESSION['cv_id'];
$txt=$db->prepare("SELECT texto FROM presentacion WHERE user_id=? AND cv_id=?");
$txt->execute([$uid,$cv]);$texto=$txt->fetchColumn();
?>
<h2>Presentación</h2>
<form method="post" action="index.php?page=presentacion&cv_id=<?=$cv?>">
  <textarea name="texto" rows="8" placeholder="Cuéntanos sobre ti..."><?=htmlspecialchars($texto)?></textarea>
  <button class="primary">Guardar</button>
</form>
