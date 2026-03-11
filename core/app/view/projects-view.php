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
	<div class="col-md-12">
<div class="card">
  <div class="card-header bg-primary text-white"><i class="bi-folder"></i> Proyectos</div>
  <div class="card-body">
	<a href="./?view=projects&opt=new" class="btn btn-secondary"><i class='bi-folder'></i> Nuevo Proyecto</a>
<br><br>
		<?php

		$projects = ProjectData::getAll();
		if(count($projects)>0){
			?>
			<div class="table-responsive">
			<table class="table table-bordered table-hover">
			<thead>
			<th>Nombre</th>
			<th style="width:120px;"></th>
			</thead>
			<?php
			foreach($projects as $project):
				?>
				<tr>
				<td><?php echo $project->name; ?></td>
				<td style="width:120px;">
				<a href="index.php?view=projects&opt=edit&id=<?php echo $project->id;?>" class="btn btn-warning btn-sm"><i class='bi-pencil'></i></a>
				<a href="index.php?action=projects&opt=del&id=<?php echo $project->id;?>" class="btn btn-danger btn-sm"><i class='bi-trash'></i></a>
				</td>
				</tr>
				<?php

			endforeach; ?>
</table>
</div>
<?php
		}else{
			echo "<p class='alert alert-warning'>No hay Proyectos</p>";
		}


		?>
  </div>
</div>

	</div>
</div>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<section class="">
<div class="row">
	<div class="col-md-12">

<div class="card">
  <div class="card-header bg-success text-white"><i class="bi-plus-circle"></i> Nuevo Proyecto</div>
  <div class="card-body">
		<form class="form-horizontal" method="post" action="index.php?action=projects&opt=add" role="form">

  <div class="form-group mb-3">
    <label class="form-label">Nombre*</label>
      <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre">
  </div>

  <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary">Agregar Proyecto</button>
  </div>
</form>
</div>
</div>

	</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):?>
<?php $project = ProjectData::getById($_GET["id"]);?>
<section class="">
<div class="row">
	<div class="col-md-12">
<div class="card">
  <div class="card-header bg-info text-white"><i class="bi-pencil-square"></i> Editar Proyecto</div>
  <div class="card-body">
		<form class="form-horizontal" method="post" action="index.php?action=projects&opt=upd" role="form">

  <div class="form-group mb-3">
    <label class="form-label">Nombre*</label>
      <input type="text" name="name" value="<?php echo $project->name;?>" class="form-control" id="name" placeholder="Nombre">
  </div>

  <div class="d-grid gap-2">
    <input type="hidden" name="project_id" value="<?php echo $project->id;?>">
      <button type="submit" class="btn btn-primary">Actualizar Proyecto</button>
  </div>
</form>
</div>
</div>
	</div>
</div>
</section>
<?php endif; ?>
