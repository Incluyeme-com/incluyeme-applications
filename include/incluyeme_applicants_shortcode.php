<?php
function incluyeme_applicants_shortcode()
{
    return "
<div>
   <h1>Elije el candidato</h1>
   <div class='row'>
      <form class='col-12  mt-3'>
         <div class='form-group'>
            <label  class='font-weight-bold' for='email'>Búsqueda por email</label>
            <input type='email' class='form-control' id='email' aria-describedby='emailHelp' placeholder='gmail@gmail.com' required>
         </div>
         <button type='submit' class='btn btn-primary' style='float: right;'>Buscar</button>
      </form>
      <form class='col-12  mt-3'>
         <div class='form-group'>
            <label  class='font-weight-bold' for='names'>Búsqueda por nombre y apellido</label>
            <input type='text' class='form-control' id='names' aria-describedby='names' placeholder='Juan Perez' required>
         </div>
         <button type='submit' class='btn btn-primary' style='float: right;'>Buscar</button>
      </form>
      <form class='col-12  mt-3'>
         <div class='form-group'>
            <label  class='font-weight-bold' for='keyword'>Búsqueda por palabra clave</label>
            <input type='text' class='form-control' id='keyword' aria-describedby='keyword' required>
         </div>
         <button type='submit' class='btn btn-primary' style='float: right;'>Buscar</button>
      </form>
   </div>
</div>
    ";
}