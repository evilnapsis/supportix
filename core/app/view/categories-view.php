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
  <div class="card-header bg-primary text-white"><i class="bi-list"></i> Categorias</div>
  <div class="card-body">
	<a href="./?view=categories&opt=new" class="btn btn-secondary"><i class='bi-list'></i> Nueva Categoria</a>
<br><br>
		<?php

		$categories = CategoryData::getAll();
		if(count($categories)>0){
			?>
			<div class="table-responsive">
			<table class="table table-bordered table-hover">
			<thead>
			<th>Nombre</th>
			<th style="width:120px;"></th>
			</thead>
			<?php
			foreach($categories as $cat):
				?>
				<tr>
				<td><?php echo $cat->name; ?></td>
				<td style="width:120px;">
				<a href="index.php?view=categories&opt=edit&id=<?php echo $cat->id;?>" class="btn btn-warning btn-sm"><i class='bi-pencil'></i></a>
				<a href="index.php?action=categories&opt=del&id=<?php echo $cat->id;?>" class="btn btn-danger btn-sm"><i class='bi-trash'></i></a>
				</td>
				</tr>
				<?php

			endforeach; ?>
</table>
</div>
<?php
		}else{
			echo "<p class='alert alert-warning'>No hay Categorias</p>";
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
  <div class="card-header bg-success text-white"><i class="bi-plus-circle"></i> Nueva Categoria</div>
  <div class="card-body">
		<form class="form-horizontal" method="post" action="index.php?action=categories&opt=add" role="form">

  <div class="form-group mb-3">
    <label class="form-label">Nombre*</label>
      <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre">
  </div>

  <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary">Agregar Categoria</button>
  </div>
</form>
</div>
</div>

	</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):?>
<?php $category = CategoryData::getById($_GET["id"]);?>
<section class="">
<div class="row">
	<div class="col-md-12">
<div class="card">
  <div class="card-header bg-info text-white"><i class="bi-pencil-square"></i> Editar Categoria</div>
  <div class="card-body">

		<form class="form-horizontal" method="post" action="index.php?action=categories&opt=upd" role="form">

  <div class="form-group mb-3">
    <label class="form-label">Nombre*</label>
      <input type="text" name="name" value="<?php echo $category->name;?>" class="form-control" id="name" placeholder="Nombre">
  </div>

  <div class="d-grid gap-2">
    <input type="hidden" name="category_id" value="<?php echo $category->id;?>">
      <button type="submit" class="btn btn-primary">Actualizar Categoria</button>
  </div>
</form>
</div>
</div>
	</div>
</div>
</section>
<?php endif; ?>
