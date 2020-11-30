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
              <label for="act_code" class="col-lg-3 col-sm-4 col-form-label">
                Kode Kegiatan
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-4 col-md-6">
                <input
                  type="text"
                  class="form-control rounded-0"
                  id="act_code"
                  name="act_code"
                  autocomplete="off"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="act_name" class="col-lg-3 col-sm-4 col-form-label">
                Nama Kegiatan
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <input
                  type="text"
                  class="form-control rounded-0"
                  id="act_name"
                  name="act_name"
                  autocomplete="off"
                />
                <div class="invalid-feedback"></div>
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
  })

  let getDetail = (data_id) => {
    $.post(
      `${MAIN_URL}/detail`,
      { id: data_id },
      (res) => {
        $.each(res, (id, value) => {
          $(`#${id}`).val(value)
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
