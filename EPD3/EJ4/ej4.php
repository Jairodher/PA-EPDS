<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php
            $tabla = array (
                1 => array (
                    "a" => array (
                        array ("sku" => "CCFWT-000500", "cantidad" => 7),
                        array ("sku" => "CCFWT-005000", "cantidad" => 5),
                        array ("sku" => "CCT-025000", "cantidad" => 8)
                    ),
                    "b" => array (
                        array ("sku" => "COS-025000", "cantidad" => 5)
                    ),
                    "c" => array (
                        array ("sku" => "COS-025000", "cantidad" => 9),
                        array ("sku" => "CCT-025000", "cantidad" => 1),
                        array ("sku" => "CCT-025000", "cantidad" => 8)
                    )
                ),
                2 => array (
                    "a" => array (
                        array ("sku" => "CCT-025000", "cantidad" => 8),
                        array ("sku" => "CCT-025000", "cantidad" => 9)
                    )
                )
            );
            
            function buscar_sku ($sku_buscado) {
                global $tabla;

                foreach ($tabla as $pasillo => $estantes) {
                    foreach ($estantes as $estante => $productos) {
                        foreach ($productos as $producto) {
                            if ($producto["sku"] === $sku_buscado) {
                                echo "Pasillo " . $pasillo . " - Estante " . $estante . " - " . $producto["cantidad"] . " unidades<br>";
                            }
                        }
                    }
                }
            }

            buscar_sku("CCT-025000");
        ?>
    </body>
</html>