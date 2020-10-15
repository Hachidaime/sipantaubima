<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'} 

{block name='content'}
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
        <label for="loc_code" class="col-lg-3 col-sm-4 col-form-label">
          Kode Lokasi
          <sup class="fas fa-asterisk text-red"></sup>
          <span class="float-sm-right d-sm-inline d-none">:</span>
        </label>
        <div class="col-lg-2 col-sm-3 col-6">
          <input
            type="text"
            class="form-control rounded-0"
            id="loc_code"
            name="loc_code"
            autocomplete="off"
          />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label for="loc_name" class="col-lg-3 col-sm-4 col-form-label">
          Nama Lokasi
          <sup class="fas fa-asterisk text-red"></sup>
          <span class="float-sm-right d-sm-inline d-none">:</span>
        </label>
        <div class="col-lg-9 col-sm-8">
          <input
            type="text"
            class="form-control rounded-0"
            id="loc_name"
            name="loc_name"
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
      saveData()
    })
  })

  let getDetail = (data_id) => {
    $.post(
      `${MAIN_URL}/detail`,
      { id: data_id },
      function (res) {
        $.each(res, (id, value) => {
          $(`#${id}`).val(value)
        })
      },
      'JSON'
    )
  }

  let saveData = () => {
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
