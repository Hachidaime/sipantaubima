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
    <!-- Tempusdominus Bbootstrap 4 -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"
    />
    <!-- Theme style -->
    <link
      rel="stylesheet"
      href="{$smarty.const.BASE_URL}/assets/dist/css/adminlte.min.css"
    />
    <!-- Google Font: Source Sans Pro -->
    <link
      href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700"
      rel="stylesheet"
    />
    {block name='style'}{/block}
  </head>

  <body
    class="hold-transition {if $smarty.session.ACTIVE.controller eq 'LoginController'}login-page{else}layout-fixed{/if}"
  >
    {block name='content'}{/block}

    <!-- jQuery -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{$smarty.const.BASE_URL}/assets/plugins/moment/moment.min.js"></script>
    <script src="{$smarty.const.BASE_URL}/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{$smarty.const.BASE_URL}/assets/dist/js/adminlte.min.js"></script>

    {block name='script'}{/block}
  </body>
</html>
