<?php
$uid=$_SESSION['user_id'];
$cvs=$db->prepare("SELECT * FROM cv_profiles WHERE user_id=? ORDER BY created_at DESC");
$cvs->execute([$uid]);$list=$cvs->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Tus CV</h1>

<!-- Crear -->
<form method="post" action="index.php?page=create_cv" style="margin-bottom:32px">
  <input name="titulo" placeholder="Título nuevo CV (opcional)">
  <button class="primary">Crear nuevo CV</button>
</form>

<!-- Listado -->
<?php if(!$list): ?>
  <p>No tienes CV guardados todavía.</p>
<?php else: ?>
  <table>
    <thead><tr><th>Título</th><th>Creado</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach($list as $cv): ?>
        <tr>
          <td><?=htmlspecialchars($cv['titulo'])?></td>
          <td><?=date('d/m/Y H:i',strtotime($cv['created_at']))?></td>
          <td>
            <a href="index.php?page=personal_data&cv_id=<?=$cv['id']?>">Editar</a> |
            <a href="index.php?page=cv_template1&cv_id=<?=$cv['id']?>" target="_blank">Ver</a> |
            <a href="index.php?page=delete_cv&cv_id=<?=$cv['id']?>"
               onclick="return confirm('¿Eliminar este CV?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
<?php endif; ?>
