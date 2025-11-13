<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Subir Configuración</title>
</head>
<body>
  <h2>Subir archivo JSON de ejercicios</h2>
  <form action="procesar_json.php" method="post" enctype="multipart/form-data">
    <label>Nombre del entrenador:</label><br>
    <input type="text" name="entrenador" required><br><br>

    <label>Tipo de rutina:</label><br>
    <select name="tipo_rutina" required>
      <option value="Principiante">Principiante</option>
      <option value="Intermedio">Intermedio</option>
      <option value="Avanzado">Avanzado</option>
      <option value="Rehabilitación">Rehabilitación</option>
    </select><br><br>

    <label>Archivo JSON (.json):</label><br>
    <input type="file" name="archivo_json" accept=".json" required><br><br>

    <input type="submit" value="Procesar configuración">
  </form>
</body>
</html>
