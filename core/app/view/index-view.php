<div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="card mb-4 text-white bg-warning">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold"><?php echo count(TicketData::getAllPendings()); ?> </div>
                    <div>Pendientes</div>
                  </div>
                </div>
                <br>

              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card mb-4 text-white bg-success">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold"><?php echo count(ProjectData::getAll()); ?> </div>
                    <div>Proyectos</div>
                  </div>
                </div>
                <br>

              </div>
            </div>
            <!-- /.col-->
            <div class="col-sm-6 col-lg-3">
              <div class="card mb-4 text-white bg-danger">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold"><?php echo count(CategoryData::getAll()); ?> </div>
                    <div>Categorias</div>
                  </div>
                </div>
                <br>

              </div>
            </div>
            <!-- /.col-->
            <div class="col-sm-6 col-lg-3">
              <div class="card mb-4 text-white bg-primary">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold"><?php echo count(UserData::getAll()); ?></div>
                    <div>Usuarios</div>
                  </div>
                </div>
                <br>

              </div>
            </div>
            <!-- /.col-->
          </div>



            <div class="row">
            <div class="col-md-12">
              <div class="card mb-4">
                <div class="card-header">Bienvenido</div>
                <div class="card-body">
                  <p>Bienvenido al Sistema Supportix.</p>

                </div>
              </div>
            </div>
            <!-- /.col-->
          </div>