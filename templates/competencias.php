<?php
$uid=$_SESSION['user_id'];$cv=$_SESSION['cv_id'];
$st=$db->prepare("SELECT * FROM competencias WHERE user_id=? AND cv_id=?");
$st->execute([$uid,$cv]);$rows=$st->fetchAll(PDO::FETCH_ASSOC) ?: [['competencia'=>'','nivel'=>'']];
?>
<h2>Competencias</h2>
<form method="post" action="index.php?page=competencias&cv_id=<?=$cv?>">
  <?php foreach($rows as $r): ?>
    <div>
      <input name="competencia[]" value="<?=htmlspecialchars($r['competencia'])?>" placeholder="Competencia">
      <select name="nivel[]">
        <?php foreach(['Básico','Intermedio','Avanzado','Experto'] as $n): ?>
          <option <?= $r['nivel']===$n?'selected':''?>><?= $n ?></option>
        <?php endforeach;?>
      </select>
    </div>
  <?php endforeach;?>
  <button class="primary">Guardar</button>
</form>
