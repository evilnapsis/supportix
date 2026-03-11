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
  <div class="card-header bg-primary text-white"><i class="bi-ticket-perforated"></i> Tickets</div>
  <div class="card-body">
	<a href="./?view=tickets&opt=new" class="btn btn-secondary"><i class='bi-ticket-perforated'></i> Nuevo Ticket</a>
<br><br>
<form class="form-horizontal" role="form">
<input type="hidden" name="view" value="tickets">
<input type="hidden" name="opt" value="all">
        <?php
$projects = ProjectData::getAll();
$categories = CategoryData::getAll();
        ?>

  <div class="row mb-3">
    <div class="col-lg-2">
      <input type="text" name="q" value="<?php if(isset($_GET["q"]) && $_GET["q"]!=""){ echo $_GET["q"]; } ?>" class="form-control" placeholder="Palabra clave">
    </div>
    <div class="col-lg-2">
<select name="project_id" class="form-control">
<option value="">PROYECTO</option>
  <?php foreach($projects as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if(isset($_GET["project_id"]) && $_GET["project_id"]==$p->id){ echo "selected"; } ?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-lg-2">
<select name="category_id" class="form-control">
<option value="">CATEGORIA</option>
  <?php foreach($categories as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if(isset($_GET["category_id"]) && $_GET["category_id"]==$p->id){ echo "selected"; } ?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-lg-3">
      <input type="date" name="date_at" value="<?php if(isset($_GET["date_at"]) && $_GET["date_at"]!=""){ echo $_GET["date_at"]; } ?>" class="form-control" placeholder="Fecha">
    </div>

    <div class="col-lg-2">
    <button class="btn btn-primary btn-block">Buscar</button>
    </div>

  </div>
</form>

		<?php
$tickets= array();
if((isset($_GET["q"]) && isset($_GET["project_id"]) && isset($_GET["category_id"]) && isset($_GET["date_at"])) && ($_GET["q"]!="" || $_GET["project_id"]!="" || $_GET["category_id"]!="" || $_GET["date_at"]!="") ) {
$sql = "select * from ticket where ";
if($_GET["q"]!=""){
	$sql .= " (title like '%$_GET[q]%' or description like '%$_GET[q]%') ";
}

if($_GET["project_id"]!=""){
if($_GET["q"]!=""){
	$sql .= " and ";
}
	$sql .= " project_id = ".$_GET["project_id"];
}

if($_GET["category_id"]!=""){
if($_GET["q"]!=""||$_GET["project_id"]!=""){
	$sql .= " and ";
}

	$sql .= " category_id = ".$_GET["category_id"];
}

if($_GET["date_at"]!=""){
if($_GET["q"]!=""||$_GET["project_id"]!="" ||$_GET["category_id"]!="" ){
	$sql .= " and ";
}

	$sql .= " date(created_at) = \"".$_GET["date_at"]."\"";
}

		$tickets = TicketData::getBySQL($sql);
}else{
		$tickets = TicketData::getAll();

}
		if(count($tickets)>0){
			?>
			<div class="table-responsive">
			<table class="table table-bordered table-hover">
			<thead>
			<th>Asunto</th>
			<th>Proyecto</th>
			<th>Prioridad</th>
			<th>Estado</th>
			<th>Fecha</th>
			<th></th>
			</thead>
			<?php
			foreach($tickets as $ticket):
				$project  = $ticket->getProject();
				$priority = $ticket->getPriority();
				?>
				<tr>
				<td><?php echo $ticket->title; ?></td>
				<td><?php echo $project->name; ?></td>
				<td><?php echo $priority->name; ?></td>
				<td><?php echo $ticket->getStatus()->name; ?></td>
				<td><?php echo $ticket->created_at; ?></td>
				<td style="width:180px;">
				<a href="index.php?view=tickets&opt=edit&id=<?php echo $ticket->id;?>" class="btn btn-warning btn-sm"><i class="bi-pencil"></i></a>
				<a href="index.php?action=tickets&opt=del&id=<?php echo $ticket->id;?>" class="btn btn-danger btn-sm"><i class="bi-trash"></i></a>
				</td>
				</tr>
				<?php

			endforeach; ?>
</table>
</div>
<?php
		}else{
			?>
			<p class="alert alert-warning">No hay tickets</p>
			<?php
		}


		?>
  </div>
</div>

	</div>
</div>
</section>
<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="new"):?>
<?php
$projects = ProjectData::getAll();
$priorities = PriorityData::getAll();
$statuses = StatusData::getAll();
$kinds = KindData::getAll();
?>
<section class="">
<div class="row">
<div class="col-md-12">
<div class="card">
  <div class="card-header bg-success text-white"><i class="bi-plus-circle"></i> Nuevo Ticket</div>
  <div class="card-body">
<form class="form-horizontal" role="form" method="post" action="./?action=tickets&opt=add">

  <div class="form-group mb-3">
    <label class="form-label">Tipo</label>
<select name="kind_id" class="form-control" required>
  <?php foreach($kinds as $p):?>
    <option value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
  </div>
  <div class="form-group mb-3">
    <label class="form-label">Titulo</label>
      <input type="text" name="title" required class="form-control" placeholder="Titulo">
  </div>

  <div class="form-group mb-3">
    <label class="form-label">Descripcion</label>
    <textarea class="form-control" name="description" required placeholder="Descripcion"></textarea>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
    <label class="form-label">Proyecto</label>
<select name="project_id" class="form-control" required>
<option value="">-- SELECCIONE --</option>
  <?php foreach($projects as $p):?>
    <option value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-md-6">
    <label class="form-label">Categoria</label>
<select name="category_id" class="form-control" required>
<option value="">-- SELECCIONE --</option>
  <?php foreach(CategoryData::getAll() as $p):?>
    <option value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
    <label class="form-label">Prioridad</label>
<select name="priority_id" class="form-control" required>
<option value="">-- SELECCIONE --</option>
  <?php foreach($priorities as $p):?>
    <option value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>

    <div class="col-md-6">
    <label class="form-label">Estado</label>
<select name="status_id" class="form-control" required>
  <?php foreach($statuses as $p):?>
    <option value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
  </div>

  <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary">Agregar Ticket</button>
  </div>
</form>
</div>
</div>
</div>
</div>
</section>

<?php elseif(isset($_GET["opt"]) && $_GET["opt"]=="edit"):?>
<?php 
$reservation = TicketData::getById($_GET["id"]);
$pacients = ProjectData::getAll();
$medics = PriorityData::getAll();
$statuses = StatusData::getAll();
$payments = KindData::getAll();
?>
<section class="">
<div class="row">
	<div class="col-md-12">

<div class="card">
  <div class="card-header bg-info text-white"><i class="bi-pencil-square"></i> Modificar Ticket</div>
  <div class="card-body">
<form class="form-horizontal" role="form" method="post" action="./?action=tickets&opt=upd">

  <div class="form-group mb-3">
    <label class="form-label">Tipo</label>
<select name="kind_id" class="form-control" required>
  <?php foreach($payments as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if($p->id==$reservation->kind_id){ echo "selected"; }?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
  </div>
  <div class="form-group mb-3">
    <label class="form-label">Titulo</label>
      <input type="text" name="title" value="<?php echo $reservation->title; ?>" required class="form-control" placeholder="Asunto">
  </div>

  <div class="form-group mb-3">
    <label class="form-label">Descripcion</label>
    <textarea class="form-control" name="description" placeholder="Descripcion"><?php echo $reservation->description;?></textarea>

  </div>
  <div class="row mb-3">
    <div class="col-md-6">
    <label class="form-label">Proyecto</label>
<select name="project_id" class="form-control" required>
<option value="">-- SELECCIONE --</option>
  <?php foreach($pacients as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if($p->id==$reservation->project_id){ echo "selected"; }?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-md-6">
    <label class="form-label">Categoria</label>
<select name="category_id" class="form-control" required>
<option value="">-- SELECCIONE --</option>
  <?php foreach(CategoryData::getAll() as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if($p->id==$reservation->category_id){ echo "selected"; }?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
    <label class="form-label">Prioridad</label>
<select name="priority_id" class="form-control" required>
<option value="">-- SELECCIONE --</option>
  <?php foreach($medics as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if($p->id==$reservation->priority_id){ echo "selected"; }?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-md-6">
    <label class="form-label">Estado</label>
<select name="status_id" class="form-control" required>
  <?php foreach($statuses as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if($p->id==$reservation->status_id){ echo "selected"; }?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
  </div>

  <div class="d-grid gap-2">
    <input type="hidden" name="id" value="<?php echo $reservation->id; ?>">
      <button type="submit" class="btn btn-primary">Actualizar Ticket</button>
  </div>
</form>
</div>
</div>
	</div>
</div>
</section>
<?php endif; ?>
