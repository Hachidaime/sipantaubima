{assign 'active_child' 'active active-child text-navy'}
<div class="sidebar">
  <!-- Sidebar Menu -->
  <nav class="mt-2">
    <ul
      class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent"
      data-widget="treeview"
      role="menu"
      data-accordion="false"
    >
      <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
      <li class="nav-item">
        <a
          href="{$smarty.const.BASE_URL}"
          class="nav-link {if $smarty.session.ACTIVE.name eq 'dashboard'}active text-warning{/if}"
        >
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>Dashboard</p>
        </a>
      </li>

      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-book"></i>
          <p>
            Master
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a
              href="{$smarty.const.BASE_URL}/program"
              class="nav-link {if $smarty.session.ACTIVE.name eq 'program'}{$active_child}{/if}"
            >
              <i class="nav-icon fas fa-chalkboard-teacher"></i>
              <p>Program</p>
            </a>
          </li>
          <li class="nav-item">
            <a
              href="{$smarty.const.BASE_URL}/activity"
              class="nav-link {if $smarty.session.ACTIVE.name eq 'activity'}{$active_child}{/if}"
            >
              <i class="nav-icon fas fa-running"></i>
              <p>Kegiatan</p>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-handshake"></i>
          <p>
            Transaksi
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a
              href="{$smarty.const.BASE_URL}/packet"
              class="nav-link {if $smarty.session.ACTIVE.name eq 'packet'}{$active_child}{/if}"
            >
              <i class="nav-icon fas fa-box"></i>
              <p>Paket</p>
            </a>
          </li>
          <li class="nav-item">
            <a
              href="{$smarty.const.BASE_URL}/progress"
              class="nav-link {if $smarty.session.ACTIVE.name eq 'progress'}{$active_child}{/if}"
            >
              <i class="nav-icon fas fa-tasks"></i>
              <p>Progres Paket</p>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-print"></i>
          <p>
            Laporan
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a
              href="{$smarty.const.BASE_URL}/report"
              class="nav-link {if $smarty.session.ACTIVE.name eq 'report'}{$active_child}{/if}"
            >
              <i class="nav-icon fas fa-file-invoice"></i>
              <p>Laporan Progres Paket</p>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a
          href="{$smarty.const.BASE_URL}/user"
          class="nav-link {if $smarty.session.ACTIVE.name eq 'user'}active text-warning{/if}"
        >
          <i class="nav-icon fas fa-user"></i>
          <p>User</p>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.sidebar-menu -->
</div>
