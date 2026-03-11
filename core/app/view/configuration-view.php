<?php 
// si el usuario no esta logeado
if(!isset($_SESSION["user_id"])){ Core::redir("./");}
$user= UserData::getById($_SESSION["user_id"]);
// si el id  del usuario no existe.
if($user==null){ Core::redir("./");}
?>
<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"):?>
<section class="">
<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
<div class="card">
  <div class="card-header bg-warning text-white"><i class="bi-key"></i> Cambiar Contraseña</div>
  <div class="card-body">
	<form class="form-horizontal" id="changepasswd" method="post" action="index.php?action=configuration&opt=changepasswd" role="form">
  <div class="form-group mb-3">
    <label class="form-label">Contraseña Actual</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña Actual">
  </div>

  <div class="form-group mb-3">
    <label class="form-label">Nueva Contraseña</label>
      <input type="password" class="form-control"  id="newpassword" name="newpassword" placeholder="Nueva Contraseña">
  </div>

  <div class="form-group mb-3">
    <label class="form-label">Confirmar Nueva Contraseña</label>
      <input type="password" class="form-control" id="confirmnewpassword" name="confirmnewpassword" placeholder="Confirmar Nueva Contraseña">
  </div>

  <div class="d-grid gap-2">
      <button type="submit" class="btn btn-success">Cambiar Contraseña</button>
  </div>
</form>

<script>
$("#changepasswd").submit(function(e){
	if($("#password").val()=="" || $("#newpassword").val()=="" || $("#confirmnewpassword").val()==""){
		e.preventDefault();
		alert("No debes dejar espacios vacios.");

	}else{
		if($("#newpassword").val() == $("#confirmnewpassword").val()){
//			alert("Correcto");			
		}else{
			e.preventDefault();
			alert("Las nueva contraseña no coincide con la confirmacion.");
		}
	}
});

</script>
</div>
</div>
	</div>
</div>
</section>
<?php endif; ?>
