{block 'targetForm'}
<!-- Modal -->
<div
  class="modal fade"
  id="targetFormModal"
  data-backdrop="static"
  data-keyboard="false"
  tabindex="-1"
  aria-labelledby="targetFormModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="targetFormModalLabel"></h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="target_form" role="form" method="POST">
          <input type="hidden" id="pkgd_id" name="pkgd_id" value="" />
          <input type="hidden" id="id" name="id" value="" />
          <div class="form-group row">
            <label for="trg_week" class="col-5 col-form-label">
              Minggu Ke
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-4">
              <input
                type="number"
                class="form-control rounded-0 text-right"
                id="trg_week"
                name="trg_week"
                min="1"
                step="1"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="form-group row">
            <label for="trg_date" class="col-5 col-form-label">
              Tanggal Periode
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-4">
              <input
                type="text"
                class="form-control rounded-0"
                id="trg_date"
                name="trg_date"
                autocomplete="off"
                data-toggle="datetimepicker"
                data-target="#trg_date"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="form-group row">
            <label for="trg_physical" class="col-5 col-form-label">
              Fisik
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-4">
              <input
                type="number"
                class="form-control rounded-0 text-right"
                id="trg_physical"
                name="trg_physical"
                min="0"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="form-group row">
            <label for="trg_finance" class="col-5 col-form-label">
              Keuangan
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-7">
              <input
                class="form-control rounded-0 money-format"
                id="trg_finance"
                name="trg_finance"
                autocomplete="off"
                placeholder="0,00"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-light btn-flat"
          data-dismiss="modal"
          style="width: 125px;"
        >
          Batal
        </button>
        <button
          type="button"
          class="btn btn-success btn-flat"
          id="btn_save"
          style="width: 125px;"
        >
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block}

{block 'targetFormScript'}

{literal}
<script>
  $(document).ready(function () {
    $('#trg_date').datetimepicker({
      format: 'DD/MM/YYYY',
      locale: 'id',
    })

    $('#targetFormModal').on('hidden.bs.modal', function (e) {
      $('#detail_form').trigger('reset')
      $('.btn-radio').removeClass('active')
      clearErrorMessage()
    })

    $('#targetFormModal #btn_save').click(() => {
      clearErrorMessage()
      saveTarget()
    })
  })

  let saveTarget = () => {
    $.post(
      `${BASE_URL}/target/submit`,
      $('#target_form').serialize(),
      (res) => {
        if (!res.success) {
          if (typeof res.msg === 'object') {
            $.each(res.msg, (id, message) => {
              showErrorMessage(id, message)
            })
          } else flash(res.msg, 'error')
        } else {
          flash(res.msg, 'success')
          $('#targetFormModal').modal('hide')
          searchTarget($('#target_form #pkgd_id').val())
        }
      },
      'JSON'
    )
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
