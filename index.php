<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CEEDO - Portal de Transparência Escolar</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS próprio -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


<!-- Navbar -->

<?php include('includes/navbar.php')?>



<div class="container">


    <!-- Banner -->

    <div class="hero">


        <h1>
            Portal de Transparência Escolar
        </h1>


        <p class="lead mt-3">

            Consulte documentos, atas, leis, regimentos e trabalhos
            disponibilizados pela instituição.

        </p>


        <a href="#documentos" class="btn btn-light btn-lg mt-3">

            Ver Documentos

        </a>


    </div>




    <!-- Categorias -->

    <section class="mt-5">


        <h2 class="text-center mb-4">

            Categorias

        </h2>


        <!--
            BACK-END:

            Buscar categorias do banco de dados.

            Exemplo:
            SELECT * FROM categorias
        -->


        <div class="row g-4">



            <div class="col-md-6 col-lg-3">

                <div class="card shadow-sm h-100">


                    <div class="card-body text-center">


                        <i class="bi bi-journal-text fs-1 text-primary"></i>


                        <h5 class="mt-3">

                            Trabalhos

                        </h5>


                        <p class="text-muted">

                            Trabalhos e projetos escolares.

                        </p>


                    </div>


                </div>


            </div>




            <div class="col-md-6 col-lg-3">


                <div class="card shadow-sm h-100">


                    <div class="card-body text-center">


                        <i class="bi bi-file-earmark-text fs-1 text-success"></i>


                        <h5 class="mt-3">

                            Atas

                        </h5>


                        <p class="text-muted">

                            Atas de reuniões e conselhos.

                        </p>


                    </div>


                </div>


            </div>




            <div class="col-md-6 col-lg-3">


                <div class="card shadow-sm h-100">


                    <div class="card-body text-center">


                        <i class="bi bi-bank fs-1 text-danger"></i>


                        <h5 class="mt-3">

                            Leis

                        </h5>


                        <p class="text-muted">

                            Leis, normas e resoluções.

                        </p>


                    </div>


                </div>


            </div>




            <div class="col-md-6 col-lg-3">


                <div class="card shadow-sm h-100">


                    <div class="card-body text-center">


                        <i class="bi bi-folder-check fs-1 text-warning"></i>


                        <h5 class="mt-3">

                            Regimentos

                        </h5>


                        <p class="text-muted">

                            Regulamentos internos.

                        </p>


                    </div>


                </div>


            </div>



        </div>


    </section>





    <!-- Documentos -->


    <section id="documentos" class="mt-5 mb-5">


        <h2 class="text-center mb-4">

            Últimos Documentos Publicados

        </h2>



        <!--
            BACK-END:

            Buscar documentos no banco.

            Campos:

            id
            nome
            categoria
            arquivo
            data_upload

        -->



        <div class="table-responsive">


            <table class="table table-striped table-hover">


                <thead class="table-primary">


                    <tr>

                        <th>Documento</th>

                        <th>Categoria</th>

                        <th>Data</th>

                        <th>Ações</th>

                    </tr>


                </thead>



                <tbody>



                    <tr>

                        <td>Ata da Reunião Pedagógica.pdf</td>

                        <td>Atas</td>

                        <td>13/07/2026</td>


                        <td>


                            <button class="btn btn-primary btn-sm">

                                <i class="bi bi-eye"></i>
                                Visualizar

                            </button>


                            <button class="btn btn-success btn-sm">

                                <i class="bi bi-download"></i>
                                Download

                            </button>


                        </td>


                    </tr>



                </tbody>


            </table>


        </div>


    </section>



</div>





<footer class="bg-dark text-white text-center py-4">


    <p class="mb-0">

        © 2026 CEEDO - Portal de Transparência Escolar

    </p>


</footer>





<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>