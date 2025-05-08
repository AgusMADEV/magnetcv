<?php
$uid=$_SESSION['user_id'];$cv=$_SESSION['cv_id'];
$st=$db->prepare("SELECT hobby FROM hobbies WHERE user_id=? AND cv_id=?");
$st->execute([$uid,$cv]);$rows=$st->fetchAll(PDO::FETCH_COLUMN) ?: [''];
?>
<h2>Hobbies</h2>
<form method="post" action="index.php?page=hobbies&cv_id=<?=$cv?>">
  <?php foreach($rows as $h): ?>
    <input name="hobby[]" value="<?=htmlspecialchars($h)?>" placeholder="Hobby">
  <?php endforeach;?>
  <button class="primary">Guardar</button>
</form>
