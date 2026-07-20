<?php

include "includes/header.php";
include "includes/sidebar.php";

?>

<div class="content">

<?php include "includes/navbar.php"; ?>


<div class="container-fluid p-4">


<h2 class="mb-1">
Dashboard
</h2>

<p class="text-muted">
Gerenciamento do Portal de Transparência CEEDO
</p>



<!-- CARDS -->

<div class="row g-4 mt-3">


<div class="col-md-4">

<div class="card dashboard-card shadow-sm">

<div class="card-body">


<div class="d-flex justify-content-between">


<div>

<h6 class="text-muted">
Documentos
</h6>


<h2>
120
</h2>


</div>


<i class="bi bi-file-earmark-text fs-1 text-primary"></i>


</div>


</div>

</div>

</div>





<div class="col-md-4">

<div class="card dashboard-card shadow-sm">

<div class="card-body">


<div class="d-flex justify-content-between">


<div>

<h6 class="text-muted">
Categorias
</h6>


<h2>
06
</h2>


</div>


<i class="bi bi-folder fs-1 text-success"></i>


</div>


</div>

</div>

</div>






<div class="col-md-4">

<div class="card dashboard-card shadow-sm">

<div class="card-body">


<div class="d-flex justify-content-between">


<div>

<h6 class="text-muted">
Uploads recentes
</h6>


<h2>
15
</h2>


</div>


<i class="bi bi-cloud-upload fs-1 text-warning"></i>


</div>


</div>

</div>

</div>



</div>





<!-- AÇÕES RÁPIDAS -->


<div class="card shadow-sm mt-5">

<div class="card-body">


<h5>

Ações rápidas

</h5>


<hr>



<div class="row g-3">


<div class="col-md-4">


<a href="novo_documento.php" 
class="btn btn-primary w-100 p-3">


<i class="bi bi-upload"></i>

<br>

Adicionar Documento


</a>


</div>





<div class="col-md-4">


<a href="documentos.php"
class="btn btn-success w-100 p-3">


<i class="bi bi-folder"></i>

<br>

Gerenciar Documentos


</a>


</div>





<div class="col-md-4">


<a href="categorias.php"
class="btn btn-warning w-100 p-3">


<i class="bi bi-tags"></i>

<br>

Gerenciar Categorias


</a>


</div>


</div>


</div>

</div>






<!-- DOCUMENTOS RECENTES -->


<div class="card shadow-sm mt-5">


<div class="card-body">


<h5>
Documentos recentes
</h5>


<table class="table table-hover mt-3">


<thead class="table-primary">


<tr>

<th>
Documento
</th>


<th>
Categoria
</th>


<th>
Data
</th>


<th>
Status
</th>


</tr>


</thead>



<tbody>


<tr>

<td>
Ata reunião pedagógica.pdf
</td>

<td>
Atas
</td>

<td>
13/07/2026
</td>

<td>

<span class="badge bg-success">
Publicado
</span>

</td>


</tr>



<tr>

<td>
Regimento escolar.pdf
</td>

<td>
Regimentos
</td>

<td>
10/07/2026
</td>

<td>

<span class="badge bg-success">
Publicado
</span>

</td>


</tr>


</tbody>


</table>


</div>


</div>




</div>

</div>



<?php

include "includes/footer.php";
include "includes/scripts.php";

?>