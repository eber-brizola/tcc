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


<form>


<div class="mb-3">

<label>
Nome do Documento
</label>

<input type="text" class="form-control">

</div>



<div class="mb-3">

<label>
Categoria
</label>


<select class="form-control">

<option>
Atas
</option>

<option>
Leis
</option>

<option>
Regimentos
</option>


</select>


</div>



<div class="mb-3">

<label>
Arquivo
</label>


<input type="file" class="form-control">


</div>



<!--
BACK-END:

Enviar arquivo para pasta uploads/

Salvar dados no banco.

-->



<button class="btn btn-primary">

Salvar Documento

</button>


</form>


</div>

</div>


<?php

include "includes/footer.php";
include "includes/scripts.php";

?>