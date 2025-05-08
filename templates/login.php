<div class="container">
  <h1>Acceso al Portal</h1>
  <?php if(isset($error)): ?><p style="color:red"><?=htmlspecialchars($error)?></p><?php endif;?>

  <h2>Iniciar sesión</h2>
  <form method="post" action="index.php?page=login">
    <input type="text" name="username" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button class="primary">Entrar</button>
  </form>

  <h2>Registrarse</h2>
  <form method="post" action="index.php?page=register">
    <input type="text" name="nombre" placeholder="Nombre completo" required>
    <input type="email" name="email"  placeholder="Email" required>
    <input type="text" name="username" placeholder="Usuario deseado" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button class="primary">Crear cuenta</button>
  </form>
</div>
