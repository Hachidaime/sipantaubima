<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link
      rel="shortcut icon"
      href="{$smarty.const.BASE_URL}/favicon.ico?t={$smarty.now}"
      type="image/x-icon"
    />
    <title>{$title} - {$smarty.const.PROJECT_NAME}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/plugins/fontawesome-free/css/all.min.css"
    />
    <!-- Theme style -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/dist/css/adminlte.min.css"
    />
    <!-- SweetAlert2 -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css"
    />
    <!-- Tempusdominus Bbootstrap 4 -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"
    />
    <!-- overlayScrollbars -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"
    />
    <!-- Google Font: Source Sans Pro -->
    <link
      href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700"
      rel="stylesheet"
    />
    <!-- Custom Style -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/custom/css/style.css?t={$smarty.now}"
    />
    {block name='style'}{/block}
  </head>

  <body class="hold-transition layout-top-nav layout-fixed">
    <div class="wrapper">
      <!-- prettier-ignore -->
      {include file='Templates/header.tpl'}

      <!-- Navbar -->
      {include file='Templates/navbar.tpl'}
      <!-- /.navbar -->

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        {if $smarty.session.USER.id}
        <!-- Content Header (Page header) -->
        {include file='Templates/title.tpl'}
        <!-- /.content-header -->
        {/if}

        <!-- Main content -->
        <div class="content px-0">
          <!-- <div class="container"> -->
          {block name='content'}{/block}
          <!-- </div> -->
          <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
          <h5>Title</h5>
          <p>Sidebar content</p>
        </div>
      </aside>
      <!-- /.control-sidebar -->

      <!-- Main Footer -->
      {include file='Templates/footer.tpl'}
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/moment/moment-with-locales.min.js"></script>
    <script src="{$smarty.const.BASE_URL}/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{$smarty.const.BASE_URL}/assets/dist/js/adminlte.js"></script>
    <!-- Custom JavaScript -->
    {literal}
    <script>
      let BASE_URL = '{/literal}{$smarty.const.BASE_URL}{literal}'
      let ACTIVE_NAME = '{/literal}{$smarty.session.ACTIVE.name}{literal}'
      let MAIN_URL = `${BASE_URL}/${ACTIVE_NAME}`
      const ROWS_PER_PAGE = '{/literal}{$smarty.const.ROWS_PER_PAGE}{literal}'
    </script>
    {/literal}
    <script src="{$smarty.const.BASE_URL}/assets/custom/js/script.js?t={$smarty.now}"></script>
    {if $flash}
    <script>
      flash('{$flash.message}', '{$flash.type}')
    </script>
    <!-- prettier-ignore -->
    {/if}
    {block name='script'}{/block}
  </body>
</html>
