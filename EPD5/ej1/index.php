<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Validación de Horarios</title>
</head>
<body>
<h2>Validar archivo de horarios</h2>

<form action="procesar.php" method="post" enctype="multipart/form-data">
    <label>Email del empleado:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Nombre del instructor:</label><br>
    <input type="text" name="nombre" maxlength="100" required><br><br>

    <label>Archivo de horarios (.txt):</label><br>
    <input type="file" name="archivo" accept=".txt" required><br><br>

    <input type="submit" value="Validar archivo">
</form>

</body>
</html>
