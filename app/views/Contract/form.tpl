{block 'contractForm'}
<!-- Modal -->
<div
  class="modal fade"
  id="contractFormModal"
  data-backdrop="static"
  data-keyboard="false"
  tabindex="-1"
  aria-labelledby="contractFormModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contractFormModalLabel"></h5>
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
        <form id="contract_form" role="form" method="POST">
          <input type="hidden" id="pkgd_id" name="pkgd_id" value="" />
          <input type="hidden" id="id" name="id" value="" />
          <div class="form-group row">
            <label
              for="cnt_contractor_name"
              class="col-lg-4 col-sm-6 col-form-label"
            >
              Nama Kontraktor
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-8 col-sm-6">
              <input
                type="text"
                class="form-control rounded-0"
                id="cnt_contractor_name"
                name="cnt_contractor_name"
                autocomplete="off"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="cnt_no" class="col-lg-4 col-sm-6 col-form-label">
              Nomor Kontrak
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-8 col-sm-6">
              <input
                type="text"
                class="form-control rounded-0"
                id="cnt_no"
                name="cnt_no"
                autocomplete="off"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="cnt_date" class="col-lg-4 col-sm-6 col-form-label">
              Tanggal Kontrak
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-3 col-sm-4">
              <input
                type="text"
                class="form-control rounded-0 date-picker"
                id="cnt_date"
                name="cnt_date"
                autocomplete="off"
                data-toggle="datetimepicker"
                data-target="#cnt_date"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="cnt_wsw_date" class="col-lg-4 col-sm-6 col-form-label">
              Tanggal SPMK
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-3 col-sm-4">
              <input
                type="text"
                class="form-control rounded-0 date-picker"
                id="cnt_wsw_date"
                name="cnt_wsw_date"
                autocomplete="off"
                data-toggle="datetimepicker"
                data-target="#cnt_wsw_date"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="cnt_days" class="col-lg-4 col-sm-6 col-form-label">
              Waktu Pelaksanaan (hari)
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-2 col-sm-3 col-6">
              <input
                type="number"
                class="form-control rounded-0 text-right"
                id="cnt_days"
                name="cnt_days"
                autocomplete="off"
                min="0"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label
              for="cnt_plan_pho_date"
              class="col-lg-4 col-sm-6 col-form-label"
            >
              Tanggal Rencana PHO
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-3 col-sm-4">
              <input
                type="text"
                class="form-control rounded-0"
                id="cnt_plan_pho_date"
                name="cnt_plan_pho_date"
                readonly
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="cnt_value" class="col-lg-4 col-sm-6 col-form-label">
              Nilai Kontrak (Rp)
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-3 col-sm-6">
              <input
                type="text"
                class="form-control rounded-0 money-format text-right"
                id="cnt_value"
                name="cnt_value"
                autocomplete="off"
                placeholder="0,00"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label
              for="cnt_consultant_name"
              class="col-lg-4 col-sm-6 col-form-label"
            >
              Nama Konsultan
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-lg-8 col-sm-6">
              <input
                type="text"
                class="form-control rounded-0"
                id="cnt_consultant_name"
                name="cnt_consultant_name"
                autocomplete="off"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          {for $order=1 to 8}
          <div class="row {cycle values='bg-light,bg-white'} border-top">
            <div class="col">
              <legend>Addendum {$order}</legend>
              <input
                type="hidden"
                id="add_id{$order}"
                name="add_id[{$order}]"
                value=""
              />
              <input
                type="hidden"
                name="add_order[{$order}]"
                value="{$order}"
              />
              <div class="form-group row">
                <label
                  for="add_no{$order}"
                  class="col-lg-4 col-sm-6 col-form-label"
                >
                  Nomor Addendum
                </label>
                <div class="col-lg-8 col-sm-6">
                  <input
                    type="text"
                    class="form-control rounded-0 add-no"
                    id="add_no{$order}"
                    name="add_no[{$order}]"
                    autocomplete="off"
                    data-order="{$order}"
                  />
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              <div class="form-group row">
                <label
                  for="add_date{$order}"
                  class="col-lg-4 col-sm-6 col-form-label"
                >
                  Tanggal Addendum
                </label>
                <div class="col-lg-3 col-sm-4">
                  <input
                    type="text"
                    class="form-control rounded-0 date-picker add-date"
                    id="add_date{$order}"
                    name="add_date[{$order}]"
                    autocomplete="off"
                    data-toggle="datetimepicker"
                    data-target="#add_date{$order}"
                    data-order="{$order}"
                  />
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              <div class="form-group row">
                <label
                  for="add_days{$order}"
                  class="col-lg-4 col-sm-6 col-form-label"
                >
                  Masa Addendum (hari)
                </label>
                <div class="col-lg-2 col-sm-3 col-6">
                  <input
                    type="number"
                    class="form-control rounded-0 text-right add-days"
                    id="add_days{$order}"
                    name="add_days[{$order}]"
                    autocomplete="off"
                    min="0"
                    data-order="{$order}"
                  />
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              <div class="form-group row">
                <label
                  for="add_plan_pho_date{$order}"
                  class="col-lg-4 col-sm-6 col-form-label"
                >
                  Tanggal Rencana PHO Addendum
                </label>
                <div class="col-lg-3 col-sm-4">
                  <input
                    type="text"
                    class="form-control rounded-0 add-plan-pho-date"
                    id="add_plan_pho_date{$order}"
                    name="add_plan_pho_date[{$order}]"
                    data-order="{$order}"
                    readonly
                  />
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              <div class="form-group row">
                <label
                  for="add_value{$order}"
                  class="col-lg-4 col-sm-6 col-form-label"
                >
                  Nilai Addendum
                </label>
                <div class="col-lg-3 col-sm-6">
                  <input
                    type="text"
                    class="form-control rounded-0 money-format text-right"
                    id="add_value{$order}"
                    name="add_value[{$order}]"
                    autocomplete="off"
                    placeholder="0,00"
                  />
                  <div class="invalid-feedback"></div>
                </div>
              </div>
            </div>
          </div>
          {/for}
        </form>
      </div>

      <div class="modal-footer">
        <button
          type="button"
          class="btn bg-gradient-light btn-flat mr-0"
          data-dismiss="modal"
          style="width: 125px;"
        >
          <i class="fas fa-window-close mr-2"></i>
          Batal
        </button>
        <button
          type="button"
          class="btn bg-gradient-success btn-flat ml-0"
          style="width: 125px;"
          id="btn_save"
        >
          <i class="fas fa-save mr-2"></i>
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block}

{block 'contractFormScript'}
<!-- Input Mask -->
<script src="{$smarty.const.BASE_URL}/assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>

{literal}
<script>
  $(document).ready(function () {
    $('.date-picker').datetimepicker({
      format: 'DD/MM/YYYY',
      locale: 'id',
    })

    $('.money-format').inputmask({
      alias: 'numeric',
      groupSeparator: '.',
      radixPoint: ',',
      placeholder: '0,00',
      numericInput: true,
      autoGroup: true,
      autoUnmask: true,
    })

    $('#cnt_wsw_date').on('change.datetimepicker', () => {
      setPlanPHODate()
    })

    $('#cnt_days').on('change', () => {
      setPlanPHODate()
    })

    $('.add-date').on('change.datetimepicker', function () {
      setAddPlanPHODate(this.dataset.order)
    })

    $('.add-days').on('change', function () {
      setAddPlanPHODate(this.dataset.order)
    })

    $('#contractFormModal').on('hidden.bs.modal', function (e) {
      $('#contract_form').trigger('reset')
      $('#addendum_form').trigger('reset')
      clearErrorMessage()
    })

    $('#contractFormModal #btn_save').click(function () {
      clearErrorMessage()
      saveContract()
    })
  })

  let setPlanPHODate = () => {
    let cntWswDate = $('#cnt_wsw_date').val()
    const cntDays = $('#cnt_days').val() - 1
    const dateFormat = 'DD/MM/YYYY'

    let dt = moment(cntWswDate, dateFormat)
    if (cntDays > 0) {
      dt.add(cntDays, 'days')
    }

    cntWswDate = dt.format(dateFormat)
    $('#cnt_plan_pho_date').val(cntWswDate)
  }

  let setAddPlanPHODate = (order) => {
    let addDate = $(`#add_date${order}`).val()
    const addDays = $(`#add_days${order}`).val()
    const dateFormat = 'DD/MM/YYYY'

    let dt = moment(addDate, dateFormat)
    if (addDays > 0) {
      dt.add(addDays, 'days')
    }

    addDate = dt.format(dateFormat)
    $(`#add_plan_pho_date${order}`).val(addDate)
  }

  let saveContract = () => {
    $.post(
      `${BASE_URL}/contract/submit`,
      $('#contract_form').serialize(),
      (res) => {
        if (!res.success) {
          if (typeof res.msg === 'object') {
            $.each(res.msg, (id, message) => {
              showErrorMessage(id, message)
            })
          } else flash(res.msg, 'error')
        } else {
          flash(res.msg, 'success')
          $('#contractFormModal').modal('hide')
          pkgdSearch()
        }
      },
      'JSON'
    )
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
