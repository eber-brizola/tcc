    <?php

    include "includes/header.php";
    include "includes/sidebar.php";

    ?>


    <div class="content">

        <?php include "includes/navbar.php"; ?>


        <div class="container p-4">


            <h2>
                Adicionar Documento
            </h2>


            <form action="salvar_documento.php" method="POST" enctype="multipart/form-data">


                <div class="mb-3">

                    <label>
                        Nome do Documento
                    </label>

                    <input type="text" class="form-control" name="nome">

                </div>



                <div class="mb-3">

                    <label>
                        Categoria
                    </label>


                    <select name="categoria" class="form-control">

                        <option value="1">
                            atas
                        </option>

                        <option value="2">
                            leis
                        </option>

                        <option value="3">
                            regimentos
                        </option>


                    </select>


                </div>



                <div class="mb-3">

                    <label>
                        Arquivo
                    </label>


                    <input type="file" name="arquivo" class="form-control">


                </div>



                <!--
    BACK-END:

    Enviar arquivo para pasta uploads/

    Salvar dados no banco.

    -->



                <button type="submit" class="btn btn-primary">

                    Salvar Documento

                </button>


            </form>


        </div>

    </div>


    <?php

    include "includes/footer.php";

    ?>