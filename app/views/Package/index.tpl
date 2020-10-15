<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}
{include 'Templates/pagination.tpl'}

{block name='content'}
<div class="row mb-3">
  <div class="col-12">
    {include 'Templates/buttons/add.tpl'}
    <a
      href="javascript:void(0)"
      class="btn btn-flat bg-gradient-light btn-spreadsheet"
      style="width: 150px;"
    >
      <i class="fas fa-download mr-2"></i>
      Unduh XLS
    </a>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card rounded-0">
      <div class="card-header bg-gradient-navy rounded-0">
        <h3 class="card-title text-warning">{$subtitle}</h3>
        <div class="card-tools">
          <div class="input-group input-group-sm" style="width: 150px;">
            <input
              type="text"
              id="keyword"
              name="keyword"
              class="form-control float-right"
              value="{$keyword}"
              data-title="Cari Tahun Anggaran"
            />
            <div class="input-group-append">
              <button type="button" class="btn btn-default" id="searchBtn">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th class="align-middle text-right" width="50px">#</th>
              <th class="align-middle text-center" width="*">
                Tahun Anggaran
              </th>
              <th class="align-middle text-center" width="25%">Program</th>
              <th class="align-middle text-center" width="25%">Kegiatan</th>
              <th class="align-middle text-center" width="15%">
                Pagu Anggaran<br />(Rp)
              </th>
              <th width="22%px">&nbsp;</th>
            </tr>
          </thead>
          <tbody id="result_data"></tbody>
        </table>
      </div>
      <!-- /.card-body -->

      <div class="card-footer clearfix">{block 'pagination'}{/block}</div>
    </div>
    <!-- /.card -->
  </div>
</div>

<!-- Modal -->
<div
  class="modal fade"
  id="expiresFormModal"
  data-backdrop="static"
  data-keyboard="false"
  tabindex="-1"
  aria-labelledby="expiresFormModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="expiresFormModalLabel"></h5>
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
        <form id="expires_form" role="form" method="POST">
          <input type="hidden" id="id" name="id" value="" />

          <div class="form-group row">
            <label for="pkg_pho_date" class="col-5 col-form-label">
              Tanggal Periode
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-4">
              <input
                type="text"
                class="form-control rounded-0"
                id="pkg_pho_date"
                name="pkg_pho_date"
                autocomplete="off"
                data-toggle="datetimepicker"
                data-target="#pkg_pho_date"
              />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="form-group row">
            <label for="pkg_contract_fv" class="col-5 col-form-label">
              Keuangan
              <sup class="fas fa-asterisk text-red"></sup>
            </label>
            <div class="col-7">
              <input
                class="form-control rounded-0 money-format"
                id="pkg_contract_fv"
                name="pkg_contract_fv"
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

{block 'script'} 
{block 'paginationJS'}{/block}

<script src="{$smarty.const.BASE_URL}/assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
{literal}
<script>
  $(document).ready(function () {
    search()

    formTooltip('keyword', 'warning', 'top')

    $('.btn-spreadsheet').click(() => {
      spreadsheet()
    })

    $('#searchBtn').click(() => {
      search()
    })

    /* Delete Button */
    $(document).on('click', '.btn-delete', function (event) {
      deleteData($(this).data('id'))
    })

    $(document).on('click', '.btn-expires', function () {
      showExpiresForm(this.dataset.id)
    })

    $('#pkg_pho_date').datetimepicker({
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

    $('#btn_save').click(() => {
      saveExpires()
    })
  })

  let search = (page = 1) => {
    let params = {}
    params['page'] = page
    params['keyword'] = $('#keyword').val()

    const ROWS_PER_PAGE = '{/literal}{$smarty.const.ROWS_PER_PAGE}{literal}'

    $.post(
      `${MAIN_URL}/search`,
      params,
      (res) => {
        let paging = res.info

        let list = res.list
        let tBody = document.getElementById('result_data')
        tBody.innerHTML = ''

        for (let index in list) {
          let tRow = null
          let no = null,
            pkgFiscalYear = null,
            prgName = null,
            actName = null,
            pkgDebtCeiling = null,
            action = null,
            detail = null

          no = createElement({
            element: 'td',
            class: ['text-right'],
          })

          pkgFiscalYear = createElement({
            element: 'td',
            class: ['text-center'],
            children: [list[index].pkg_fiscal_year],
          })

          prgName = createElement({
            element: 'td',
            children: [list[index].prg_name],
          })

          actName = createElement({
            element: 'td',
            children: [list[index].act_name],
          })

          pkgDebtCeiling = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].pkg_debt_ceiling],
          })

          let editBtn = null,
            deleteBtn = null,
            expiresBtn = null

          editBtn = createElement({
            element: 'a',
            class: ['badge', 'badge-pill', 'badge-warning', 'mr-1'],
            attribute: {
              href: `${MAIN_URL}/edit/${list[index].id}`,
            },
            children: ['Edit'],
          })

          deleteBtn = createElement({
            element: 'a',
            class: [
              'badge',
              'badge-pill',
              'badge-danger',
              'mr-1',
              'btn-delete',
            ],
            data: {
              id: list[index].id,
            },
            attribute: {
              href: `javascript:void(0)`,
            },
            children: ['Hapus'],
          })

          expiresBtn = createElement({
            element: 'a',
            class: ['badge', 'badge-pill', 'badge-light', 'btn-expires'],
            data: {
              id: list[index].id,
            },
            attribute: {
              href: `javascript:void(0)`,
            },
            children: ['Kontrak Berakhir'],
          })

          action = createElement({
            element: 'td',
            children: [editBtn, deleteBtn, expiresBtn],
          })

          detail = createElement({
            element: 'input',
            attribute: {
              type: 'hidden',
            },
          })
          Object.entries(list[index]).forEach(([key, value]) => {
            detail.dataset[camelCase(key)] = value
          })

          tRow = createElement({
            element: 'tr',
            children: [
              no,
              pkgFiscalYear,
              prgName,
              actName,
              pkgDebtCeiling,
              action,
              detail,
            ],
          })

          if (list[index].pkg_pho_date != null) {
            tRow.classList.add('bg-success')
          }

          tBody.appendChild(tRow)
        }
        reArrange('#result_data tr', paging.currentPage)
        createPagination(page, paging, 'pagination')
      },
      'JSON'
    )
  }

  let spreadsheet = () => {
    let params = {}
    params['keyword'] = $('#keyword').val()
    $.post(
      `${MAIN_URL}/spreadsheet`,
      params,
      (res) => {
        download(res)
      },
      'JSON'
    )
  }

  let showExpiresForm = (id) => {
    $('#expiresFormModal').modal('show')
    $('#expiresFormModalLabel').text('Kontrak Berakhir')

    let data = $(`#result_data input[data-id=${id}]`).data()
    $.each(data, (key, value) => {
      key = key
        .replace(/\.?([A-Z]+)/g, function (x, y) {
          return '_' + y.toLowerCase()
        })
        .replace(/^_/, '')

      $(`#expires_form #${key}`).val(value)
    })
  }

  let saveExpires = () => {
    $.post(
      `${MAIN_URL}/submitexpires`,
      $('#expires_form').serialize(),
      (res) => {
        if (!res.success) {
          if (typeof res.msg === 'object') {
            $.each(res.msg, (id, message) => {
              showErrorMessage(id, message)
            })
          } else flash(res.msg, 'error')
        } else {
          flash(res.msg, 'success')
          $('#expiresFormModal').modal('hide')
          search()
        }
      },
      'JSON'
    )
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
