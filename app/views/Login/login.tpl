<!-- Source; https://w3hubs.com/Bootstrap-Login-Form-With-Background-Image/ -->

<!-- prettier-ignore -->
{extends 'Templates/mainlayout.tpl'}

{block 'style'}
{literal}
<style>
  .login-container {
    background-image: url({/literal}{$smarty.const.LOGIN_BG}?t={$smarty.now}{literal});
    background-position: center;
    height: 100vh;
    background-size: cover;
    width: 100%;
  }
</style>
{/literal}
<!-- prettier-ignore -->
{/block} 

{block 'content'}
<div class="login-container d-flex justify-content-center align-content-center">
  <div class="login-box d-flex flex-column justify-content-center">
    <div class="login-logo">
      <a href="{$smarty.const.BASE_URL}">SIPANTAUBIMA <b>Console</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="card rounded-0">
      <div class="card-header bg-gradient-navy rounded-0">
        <h3 class="card-title text-warning">Log in to start your session</h3>
      </div>
      <div class="card-body login-card-body">
        <form
          action="{$smarty.const.BASE_URL}/login"
          method="post"
          id="login_form"
        >
          <div class="form-group row">
            <label for="fiscal_year" class="col-5 col-form-label">
              Tahun Anggaran
            </label>
            <div class="col-4">
              <input
                type="text"
                class="form-control rounded-0 text-center"
                id="fiscal_year"
                name="fiscal_year"
                autocomplete="off"
                data-toggle="datetimepicker"
                data-target="#fiscal_year"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="usr_username" class="col-5 col-form-label">
              Username
            </label>
            <div class="col-7">
              <input
                type="text"
                class="form-control rounded-0"
                id="usr_username"
                name="usr_username"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="usr_password" class="col-5 col-form-label">
              Password
            </label>
            <div class="col-7">
              <input
                type="password"
                class="form-control rounded-0"
                id="usr_password"
                name="usr_password"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="row">
            <!-- /.col -->
            <div class="col-12">
              <button
                type="button"
                class="btn btn-block btn-flat bg-gradient-warning text-navy"
                id="btn_login"
              >
                Log In
              </button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
</div>
<!-- /.login-box -->
<!-- prettier-ignore -->
{/block}

{block 'script'}

{literal}
<script>
  $(document).ready(function () {
    $('#fiscal_year').datetimepicker({
      viewMode: 'years',
      format: 'YYYY',
    })

    $('#btn_login').click(() => {
      let data = $('#login_form').serialize()
      let url = '{/literal}{$smarty.const.BASE_URL}{literal}/login/submit'

      $('.form-control').removeClass('is-invalid').next().html('')
      let result = $.post(
        url,
        data,
        (res) => {
          if (!res.success) {
            $.each(res.msg, (id, error) => {
              $(`#${id}`).addClass('is-invalid').next().html(error)
            })
          } else {
            window.location = '{/literal}{$smarty.const.BASE_URL}{literal}'
          }
        },
        'JSON'
      )
    })

    $('.form-control').keypress((e) => {
      if (e.keyCode == 13) {
        e.preventDefault()
        $('#btn_login').click()
      }
    })
  })
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
