<?php

include "includes/header.php";
include "includes/sidebar.php";

?>


<div class="content">

    <?php include "includes/navbar.php"; ?>


    <div class="container p-4">


        <h2>
            Categorias
        </h2>


        <button class="btn btn-primary mb-3">

            Nova Categoria

        </button>


        <table class="table">


            <tr>

                <th>
                    Categoria
                </th>

                <th>
                    Ação
                </th>

            </tr>


            <tr>

                <td>
                    Atas
                </td>

                <td>

                    <button class="btn btn-warning btn-sm">
                        Editar
                    </button>

                    <button class="btn btn-danger btn-sm">
                        Excluir
                    </button>

                </td>


            </tr>


        </table>


        <!--
BACK-END:

CRUD categorias.

-->



    </div>

</div>


<?php

include "includes/footer.php";
include "includes/scripts.php";

?>