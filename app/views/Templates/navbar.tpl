<nav
  class="main-header navbar navbar-expand-md navbar-dark navbar-indigo sticky-top d-flex flex-column p-0 border-0"
>
  <div
    class="header sticky-top py-3 d-flex flex-column flex-sm-row bg-gradient-navy align-sm-center justify-content-between px-2 w-100"
  >
    <div class="main-title lead d-flex flex-column flex-sm-row">
      <div class="mr-sm-1">SIPANTAUBIMA</div>
      <div>(Sistem Pantauan Bina Marga)</div>
    </div>
    <div class="now-time text-right align-middle" id="ct">time</div>
  </div>
  {if $smarty.session.USER.id}
  <div class="container">
    <button
      class="navbar-toggler order-1"
      type="button"
      data-toggle="collapse"
      data-target="#navbarCollapse"
      aria-controls="navbarCollapse"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="fas fa-th my-1"></span>
    </button>

    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="{$smarty.const.BASE_URL}">Dashboard</a>
        </li>
        {if $smarty.session.USER.usr_is_master eq 1}
        <li class="nav-item dropdown">
          <a
            id="dropdownSubMenu1"
            href="#"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            class="nav-link dropdown-toggle"
          >
            Master
          </a>
          <ul
            aria-labelledby="dropdownSubMenu1"
            class="dropdown-menu border-0 shadow"
          >
            <li>
              <a href="{$smarty.const.BASE_URL}/user" class="dropdown-item">
                User
              </a>
            </li>
            <li>
              <a href="{$smarty.const.BASE_URL}/program" class="dropdown-item">
                Program
              </a>
            </li>
            <li>
              <a href="{$smarty.const.BASE_URL}/activity" class="dropdown-item">
                Kegiatan
              </a>
            </li>
            <li>
              <a href="{$smarty.const.BASE_URL}/location" class="dropdown-item">
                Lokasi
              </a>
            </li>
          </ul>
        </li>
        <!-- prettier-ignore -->
        {/if}

        {if $smarty.session.USER.usr_is_package eq 1 || $smarty.session.USER.usr_is_progress eq 1}
        <li class="nav-item dropdown">
          <a
            id="dropdownSubMenu1"
            href="#"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            class="nav-link dropdown-toggle"
          >
            Paket Pekerjaan
          </a>
          <ul
            aria-labelledby="dropdownSubMenu1"
            class="dropdown-menu border-0 shadow"
          >
            {if $smarty.session.USER.usr_is_package eq 1}
            <li>
              <a href="{$smarty.const.BASE_URL}/package" class="dropdown-item">
                Pemaketan
              </a>
            </li>
            <!-- prettier-ignore -->
            {/if}
            
            {if $smarty.session.USER.usr_is_progress eq 1}
            <li>
              <a href="{$smarty.const.BASE_URL}/progress" class="dropdown-item">
                Progres Paket
              </a>
            </li>
            {/if}
          </ul>
        </li>
        <!-- prettier-ignore -->
        {/if}

        {if $smarty.session.USER.usr_is_report eq 1}
        <li class="nav-item dropdown">
          <a
            id="dropdownSubMenu1"
            href="#"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            class="nav-link dropdown-toggle"
          >
            Laporan
          </a>
          <ul
            aria-labelledby="dropdownSubMenu1"
            class="dropdown-menu border-0 shadow"
          >
            <li>
              <a
                href="{$smarty.const.BASE_URL}/progressreport"
                class="dropdown-item"
              >
                Perkembangan Capaian Kinerja
              </a>
            </li>
            <li>
              <a
                href="{$smarty.const.BASE_URL}/performancereport"
                class="dropdown-item"
              >
                Capaian Kinerja Bulanan
              </a>
            </li>
          </ul>
        </li>
        {/if}
      </ul>
    </div>

    <!-- Right navbar links -->
    <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
      <li class="nav-item">
        <span class="nav-link"
          >TA:
          <strong class="text-warning"> {$smarty.session.FISCAL_YEAR}</strong>
        </span>
      </li>

      <div class="user-panel p-0 m-0 d-flex">
        <div class="info m-0 p-0">
          <a
            href="{$smarty.const.BASE_URL}/profile"
            class="d-block nav-link"
            title="Profil"
          >
            <i class="fas fa-user-circle"></i>
          </a>
        </div>
      </div>

      <li class="nav-item">
        <a
          class="nav-link"
          href="{$smarty.const.BASE_URL}/logout"
          title="Logout"
        >
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
    </ul>
  </div>
  {/if}
</nav>
