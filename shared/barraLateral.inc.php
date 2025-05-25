<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="../core/index.php">
          <i class="bi bi-grid"></i>
          <span>Menu</span>
        </a>
      </li><!-- End Menu Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-bounding-box"></i><span>Pacientes</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="../pacientes/agregar_pacientes.php">
              <i class="bi bi-circle"></i><span>Agregar</span>
            </a>
          </li>
          <li>
            <a href="../pacientes/listados_pacientes.php">
              <i class="bi bi-circle"></i><span>Listados</span>
            </a>
          </li>
        </ul>
      </li><!-- End Clientes Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Turnos</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="../turnos/agregar_turnos.php">
              <i class="bi bi-circle"></i><span>Agregar</span>
            </a>
          </li>
          <li>
            <a href="../turnos/listados_turnos.php">
              <i class="bi bi-circle"></i><span>Listados</span>
            </a>
          </li>
        </ul>
      </li><!-- End Turnos Nav -->

    </ul>

  </aside><!-- End Sidebar-->
