<?php
$uid=$_SESSION['user_id'];$cv=$_SESSION['cv_id'];
$pd=$db->prepare("SELECT * FROM personal_data WHERE user_id=? AND cv_id=?");
$pd->execute([$uid,$cv]);
$d=$pd->fetch(PDO::FETCH_ASSOC)?:['nombre'=>'','apellidos'=>'','fecha_nacimiento'=>'','direccion'=>'','telefono'=>''];
?>
<h2>Datos personales</h2>
<form method="post" action="index.php?page=personal_data&cv_id=<?=$cv?>">
  <input name="nombre"           value="<?=htmlspecialchars($d['nombre'])?>"           placeholder="Nombre" required>
  <input name="apellidos"        value="<?=htmlspecialchars($d['apellidos'])?>"        placeholder="Apellidos" required>
  <input name="fecha_nacimiento" value="<?=htmlspecialchars($d['fecha_nacimiento'])?>" placeholder="Fecha de nacimiento (YYYY-MM-DD)">
  <input name="direccion"        value="<?=htmlspecialchars($d['direccion'])?>"        placeholder="Dirección">
  <input name="telefono"         value="<?=htmlspecialchars($d['telefono'])?>"         placeholder="Teléfono">
  <button class="primary">Guardar</button>
</form>
