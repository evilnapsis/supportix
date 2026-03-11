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
  <div class="card-header bg-primary text-white"><i class="bi-bar-chart"></i> Reporte de Tickets</div>
  <div class="card-body">
<form class="form-horizontal" role="form">
<input type="hidden" name="view" value="reports">
<input type="hidden" name="opt" value="all">
        <?php
$projects = ProjectData::getAll();
$priorities = PriorityData::getAll();
$statuses = StatusData::getAll();
$kinds = KindData::getAll();
        ?>

  <div class="row mb-3">
    <div class="col-lg-3">
<select name="project_id" class="form-control">
<option value="">PROYECTO</option>
  <?php foreach($projects as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if(isset($_GET["project_id"]) && $_GET["project_id"]==$p->id){ echo "selected"; } ?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-lg-3">
<select name="priority_id" class="form-control">
<option value="">PRIORIDAD</option>
  <?php foreach($priorities as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if(isset($_GET["priority_id"]) && $_GET["priority_id"]==$p->id){ echo "selected"; } ?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-lg-3">
      <input type="date" name="start_at" value="<?php if(isset($_GET["start_at"]) && $_GET["start_at"]!=""){ echo $_GET["start_at"]; } ?>" class="form-control" placeholder="Inicio">
    </div>
    <div class="col-lg-3">
      <input type="date" name="finish_at" value="<?php if(isset($_GET["finish_at"]) && $_GET["finish_at"]!=""){ echo $_GET["finish_at"]; } ?>" class="form-control" placeholder="Fin">
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-lg-3">
<select name="status_id" class="form-control">
  <?php foreach($statuses as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if(isset($_GET["status_id"]) && $_GET["status_id"]==$p->id){ echo "selected"; } ?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-lg-3">
<select name="kind_id" class="form-control">
  <?php foreach($kinds as $p):?>
    <option value="<?php echo $p->id; ?>" <?php if(isset($_GET["kind_id"]) && $_GET["kind_id"]==$p->id){ echo "selected"; } ?>><?php echo $p->name; ?></option>
  <?php endforeach; ?>
</select>
    </div>
    <div class="col-lg-6">
    <button class="btn btn-primary btn-block">Procesar</button>
    </div>

  </div>
</form>

		<?php
$tickets= array();
if((isset($_GET["status_id"]) && isset($_GET["kind_id"]) && isset($_GET["project_id"]) && isset($_GET["priority_id"]) && isset($_GET["start_at"]) && isset($_GET["finish_at"]) ) && ($_GET["status_id"]!="" ||$_GET["kind_id"]!="" || $_GET["project_id"]!="" || $_GET["priority_id"]!="" || ($_GET["start_at"]!="" && $_GET["finish_at"]!="") ) ) {
$sql = "select * from ticket where ";
if($_GET["status_id"]!=""){
	$sql .= " status_id = ".$_GET["status_id"];
}

if($_GET["kind_id"]!=""){
if($_GET["status_id"]!=""){
	$sql .= " and ";
}
	$sql .= " kind_id = ".$_GET["kind_id"];
}


if($_GET["project_id"]!=""){
if($_GET["status_id"]!=""||$_GET["kind_id"]!=""){
	$sql .= " and ";
}
	$sql .= " project_id = ".$_GET["project_id"];
}

if($_GET["priority_id"]!=""){
if($_GET["status_id"]!=""||$_GET["project_id"]!=""||$_GET["kind_id"]!=""){
	$sql .= " and ";
}

	$sql .= " priority_id = ".$_GET["priority_id"];
}



if($_GET["start_at"]!="" && $_GET["finish_at"]){
if($_GET["status_id"]!=""||$_GET["project_id"]!="" ||$_GET["priority_id"]!="" ||$_GET["kind_id"]!="" ){
	$sql .= " and ";
}

	$sql .= " ( date_at >= \"".$_GET["start_at"]."\" and date_at <= \"".$_GET["finish_at"]."\" ) ";
}

		$tickets = TicketData::getBySQL($sql);

}else{
		$tickets = TicketData::getAll();

}
		if(count($tickets)>0){
			$_SESSION["report_data"] = $tickets;
			?>
			<div class="table-responsive">
			<table class="table table-bordered table-hover">
			<thead>
			<th>Asunto</th>
			<th>Proyecto</th>
			<th>Tipo</th>
			<th>Categoria</th>
			<th>Prioridad</th>
			<th>Estado</th>
			<th>Fecha</th>
			<th>Ultima Actualizacion</th>
			</thead>
			<?php
			foreach($tickets as $ticket):
				$project  = $ticket->getProject();
				$priority = $ticket->getPriority();
				?>
				<tr>
				<td><?php echo $ticket->title; ?></td>
				<td><?php echo $project->name; ?></td>
				<td><?php echo $ticket->getKind()->name; ?></td>
				<td><?php echo $ticket->getCategory()->name; ?></td>
				<td><?php echo $priority->name; ?></td>
				<td><?php echo $ticket->getStatus()->name; ?></td>
				<td><?php echo $ticket->created_at; ?></td>
				<td><?php echo $ticket->updated_at; ?></td>
				</tr>
				<?php

			endforeach; ?>
			</table>
			</div>
			<?php

		}else{
			echo "<p class='alert alert-warning'>No hay tickets</p>";
		}


		?>
  </div>
</div>

	</div>
</div>
</section>
<?php endif; ?>
