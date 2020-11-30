<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'} 

{block name='content'}
<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="card rounded-0">
        <div class="card-header bg-gradient-navy rounded-0">
          <h3 class="card-title text-warning">{$subtitle}</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form id="my_form" role="form" method="POST">
          <input type="hidden" id="id" name="id" value="{$id}" />
          <div class="card-body">
            <div class="form-group row">
              <label for="usr_name" class="col-lg-3 col-sm-4 col-form-label">
                Nama
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <input
                  type="text"
                  class="form-control rounded-0"
                  id="usr_name"
                  name="usr_name"
                  autocomplete="off"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label
                for="usr_username"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Username
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <input
                  type="text"
                  class="form-control rounded-0"
                  id="usr_username"
                  name="usr_username"
                  autocomplete="off"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label
                for="usr_password"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Password
                <!-- prettier-ignore -->
                {if $detail.id < 1}
                <sup class="fas fa-asterisk text-red"></sup>
                {/if}
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <input
                  type="password"
                  class="form-control rounded-0"
                  id="usr_password"
                  name="usr_password"
                  autocomplete="off"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label
                for="usr_consultant_name"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Nama Konsultan
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <input
                  type="text"
                  class="form-control rounded-0"
                  id="usr_consultant_name"
                  name="usr_consultant_name"
                  autocomplete="off"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label
                for="usr_is_master"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Privilege Master
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8 pt-2">
                <input
                  type="checkbox"
                  id="usr_is_master"
                  name="usr_is_master"
                  data-bootstrap-switch
                  data-off-color="danger"
                  data-on-color="success"
                  data-on-text="YES"
                  data-off-text="NO"
                  value="1"
                />
              </div>
            </div>

            <div class="form-group row">
              <label
                for="usr_is_package"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Privilege Paket
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8 pt-2">
                <input
                  type="checkbox"
                  id="usr_is_package"
                  name="usr_is_package"
                  data-bootstrap-switch
                  data-off-color="danger"
                  data-on-color="success"
                  data-on-text="YES"
                  data-off-text="NO"
                  value="1"
                />
              </div>
            </div>

            <div class="form-group row">
              <label
                for="usr_is_progress"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Privilege Progress Paket
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8 pt-2">
                <input
                  type="checkbox"
                  id="usr_is_progress"
                  name="usr_is_progress"
                  data-bootstrap-switch
                  data-off-color="danger"
                  data-on-color="success"
                  data-on-text="YES"
                  data-off-text="NO"
                  value="1"
                />
              </div>
            </div>

            <div class="form-group row">
              <label
                for="usr_is_report"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Privilege Laporan
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8 pt-2">
                <input
                  type="checkbox"
                  id="usr_is_report"
                  name="usr_is_report"
                  data-bootstrap-switch
                  data-off-color="danger"
                  data-on-color="success"
                  data-on-text="YES"
                  data-off-text="NO"
                  value="1"
                />
              </div>
            </div>
          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <!-- prettier-ignore -->
            {include 'Templates/buttons/submit.tpl'}
            {include 'Templates/buttons/back.tpl'}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block} 

{block 'script'} 
{literal}
<!-- Bootstrap Switch -->
<script src="{/literal}{$smarty.const.BASE_URL}{literal}/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
  $(document).ready(function () {
    let id = document.getElementById('id').value
    if (id) {
      getDetail(id)
    }

    $('#btn_submit').click(() => {
      clearErrorMessage()
      save()
    })

    $('input[data-bootstrap-switch]').each(function () {
      $(this).bootstrapSwitch('state', $(this).prop('checked'))
    })
  })

  let getDetail = (data_id) => {
    $.post(
      `${MAIN_URL}/detail`,
      { id: data_id },
      (res) => {
        let switchField = [
          'usr_is_master',
          'usr_is_package',
          'usr_is_progress',
          'usr_is_report',
        ]
        $.each(res, (id, value) => {
          if (switchField.indexOf(id) < 0) $(`#${id}`).val(value)
          else {
            $(`#${id}`).bootstrapSwitch('state', false)
            if (value == 1) {
              $(`#${id}`).bootstrapSwitch('state', true)
            }
          }
        })
      },
      'JSON'
    )
  }

  let save = () => {
    $.post(
      `${MAIN_URL}/submit`,
      $('#my_form').serialize(),
      (res) => {
        if (!res.success) {
          if (typeof res.msg === 'object') {
            $.each(res.msg, (id, message) => {
              showErrorMessage(id, message)
            })
          } else flash(res.msg, 'error')
        } else window.location = MAIN_URL
      },
      'JSON'
    )
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
