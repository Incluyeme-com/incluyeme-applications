<?php
function incluyeme_applicants_shortcode()
{
    return "
  <style>
    #main-content .container:before {
        background-color: none !important;
    }</style>
<div id='vueApplications' name='vueApp' class='container'>
	<template id='vueApplications1'>
		<div v-if='step===1' class='container'>
			<h1>Elije el candidato</h1>
			<div class='row'>
				<div class='col-12  mt-3'>
					<div class='form-group'>
						<label class='font-weight-bold' for='emailCandidate'>Búsqueda por email</label>
						<input v-model='emailCan' type='email' class='form-control' id='emailCandidate'
						       aria-describedby='emailHelp'
						       placeholder='gmail@gmail.com' required>
					</div>
					<button @click.prevent='searchCandidate(`" . plugins_url() . "`, 1)' type='submit'
					        class='btn btn-primary'
					        style='float: right;'>Buscar
					</button>
				</div>
				<div class='col-12  mt-3'>
					<div class='form-group'>
						<label class='font-weight-bold' for='names'>Búsqueda por nombre y apellido</label>
						<input v-model='name' type='text' class='form-control' id='names' aria-describedby='names'
						       placeholder='Juan Perez'
						       required>
					</div>
					<button @click.prevent='searchCandidate( `" . plugins_url() . "`, 2)' type='submit'
					        class='btn btn-primary'
					        style='float: right;'>Buscar
					</button>
				</div>
				<div class='col-12  mt-3'>
					<div class='form-group'>
						<label class='font-weight-bold' for='keyword'>Búsqueda por palabra clave</label>
						<input v-model='keyWord' type='text' class='form-control' id='keyword'
						       aria-describedby='keyword'
						       required>
					</div>
					<button @click.prevent='searchCandidate(`" . plugins_url() . "`, 3)' type='submit'
					        class='btn btn-primary'
					        style='float: right;'>Buscar
					</button>
				</div>
			</div>
		</div>
		<div v-if='step===2' class='container'>
			<h1>Confirma los candidatos</h1>
			<div class='row' style='float: right' v-for='(data, index) of candidatesInformation'>
				<div class='col-md-6 mt-3'>
					<div class='row'>
						<div class='col-12 mt-3'>
							<label class='font-weight-bold' for='email'>Candidato {{index + 1}}</label>
						</div>
						<div class='col-12 mt-3'>
							<input type='checkbox'>{{data.first_name + ' ' + data.last_name}}
							<small>{{data.user_email}}</small>
						</div>
					</div>
				
				</div>
				<div class='col-md-6 mt-3'>
					<div class='row'>
						<div class='col-12 mt-3'>
							<button type='submit' class='btn btn-success' v-on:click='openUrl(data.guid)'>Ver CV
							                                                                             cargado
							</button>
						</div>
					</div>
				
				</div>
				<div class='col-md-12'>
					<div class='row'>
						<div class='col-md-6 mt-3'>
							<input disabled type='checkbox' :value='data.CV'>Archivo con CV encontrado
						</div>
						<div class='col-md-6 mt-3'>
							<input disabled :value='data.CUD' type='checkbox'>TE aplicante encontrado
						</div>
					</div>
				</div>
				<hr class='w-100'>
			</div>
			<div class='mt-4 row'>
				<div class='col-md-12 text-right'>
					<button @click.prevent='changeScreens(1)' type='submit' class='btn btn-primary'>Atras</button>
					<button @click.prevent='changeScreens(3)' type='submit' class='btn btn-primary'>Continuar</button>
				</div>
			</div>
		</div>
		<div v-if='step===3' class='container'>
			<h1>Elije donde aplicar</h1>
			<div class='row'>
				<div class='col-12  mt-3'>
					<div class='form-group'>
						<label class='font-weight-bold' for='email'>Búsqueda por nombre de empleo</label>
						<input type='text' class='form-control' id='empleoname' aria-describedby='empleoname'
						       required>
					</div>
					<button @click.prevent='changeScreens(4)' type='submit' class='btn btn-primary'
					        style='float: right;'>
						Buscar
					</button>
				</div>
				<div class='col-12  mt-3'>
					<div class='form-group'>
						<label class='font-weight-bold' for='names'>Búsqueda por nombre de empresa</label>
						<input type='text' class='form-control' id='workname' aria-describedby='names'
						       required>
					</div>
					<button @click.prevent='changeScreens(4)' type='submit' class='btn btn-primary'
					        style='float: right;'>
						Buscar
					</button>
				</div>
				<div class='col-12  mt-3'>
					<div class='form-group'>
						<label class='font-weight-bold' for='keyword'>Búsqueda por Job ID</label>
						<input type='text' class='form-control' id='jodid' aria-describedby='keyword' required>
					</div>
					<button @click.prevent='changeScreens(4)' type='submit' class='btn btn-primary'
					        style='float: right;'>
						Buscar
					</button>
				</div>
			
			</div>
			<div class='mt-4 row'>
				<div class='col-md-12 text-right'>
					<button @click.prevent='changeScreens(2)' type='submit' class='btn btn-primary'>Atras</button>
					<button @click.prevent='changeScreens(4)' type='submit' class='btn btn-success'>Mostar todos los
					                                                                                empleos
					</button>
				</div>
			</div>
		</div>
		<div v-if='step===4' class='container'>
			<h1>Selecciona los empleos a aplicar</h1>
			<div class='mt-1'>
				<input type='checkbox'>Titulo + Nombre empresa
			</div>
			<div class='mt-4 row'>
				<div class='col-md-12 text-right'>
					<button @click.prevent='changeScreens(3)' type='submit' class='btn btn-primary'>Atras</button>
					<button @click.prevent='changeScreens(5)' type='submit' class='btn btn-primary'>Continuar</button>
				</div>
			</div>
		</div>
		<div v-if='step===5' class='container'>
			<h1>Indica el texto a mostrar junto con la aplicacion de empleo (Opcional)</h1>
			<div class='mt-1'>
				<textarea class='form-control' rows='3'></textarea>
			</div>
			
			<h1 class='mt-5'>Indica el texto a enviar al aplicante (Opcional)</h1>
			<div class='mt-1'>
				<textarea class='form-control' rows='15'></textarea>
			</div>
			<div class='mt-4 row'>
				<div class='col-md-12 text-right'>
					<button @click.prevent='changeScreens(4)' type='submit' class='btn btn-primary'>Atras</button>
					<button @click.prevent='changeScreens(6)' type='submit' class='btn btn-primary'>Continuar</button>
				</div>
			</div>
		</div>
	</template>
</div>
";
}

