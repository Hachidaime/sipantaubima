<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}
{include 'Templates/pagination.tpl'}

{block 'style'}
<!-- Ekko Lightbox -->
<link
  rel="stylesheet"
  href="{$smarty.const.BASE_URL}/assets/plugins/ekko-lightbox/ekko-lightbox.css"
/>
<!-- prettier-ignore -->
{/block}

{block name='content'}
<div class="container">
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
        <div class="card-body table-responsive p-0 border">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th class="align-middle text-right" width="40px">#</th>
                <th class="align-middle text-center" width="5%">
                  Tahun Anggaran
                </th>
                <th class="align-middle text-center" width="10%">
                  Program
                </th>
                <th class="align-middle text-center" width="10%">
                  Kegiatan
                </th>
                <th class="align-middle text-center" width="5%">
                  Minggu Ke
                </th>
                <th class="align-middle text-center" width="*">Nama Paket</th>
                <th class="align-middle text-center" width="5%">
                  Tanggal Periode
                </th>
                <th class="align-middle text-center" width="80px">
                  Progres Fisik
                </th>
                <th class="align-middle text-center" width="150px">
                  Progres Keuangan
                </th>
                <th width="20%">&nbsp;</th>
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
</div>
<!-- prettier-ignore -->
{/block} 

{block 'script'}
<!-- Ekko Lightbox -->
<script src="{$smarty.const.BASE_URL}/assets/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>

<!-- prettier-ignore -->
{block 'paginationJS'}{/block}
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

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
      event.preventDefault()
      $(this).ekkoLightbox({
        alwaysShowClose: false,
      })
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
            progFiscalYear = null,
            prgName = null,
            actName = null,
            progWeek = null,
            pkgdName = null,
            progDate = null,
            progPhysical = null,
            progFinance = null,
            action = null

          no = createElement({
            element: 'td',
            class: ['text-right'],
          })

          progFiscalYear = createElement({
            element: 'td',
            children: [list[index].prog_fiscal_year],
          })

          prgName = createElement({
            element: 'td',
            children: [list[index].prg_name],
          })

          actName = createElement({
            element: 'td',
            children: [list[index].act_name],
          })

          progWeek = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].prog_week],
          })

          pkgdName = createElement({
            element: 'td',
            children: [list[index].pkgd_name],
          })

          progDate = createElement({
            element: 'td',
            children: [list[index].prog_date],
          })

          progPhysical = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].prog_physical],
          })

          progFinance = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].prog_finance],
          })

          let editBtn = null,
            deleteBtn = null,
            imgBtn = null,
            docBtn = null

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

          imgBtn = createElement({
            element: 'a',
            class: ['badge', 'badge-info', 'badge-pill', 'mr-1'],
            data: {
              toggle: 'lightbox',
            },
            attribute: {
              href:
                list[index].prog_img != '' && list[index].prog_img != null
                  ? `${BASE_URL}/upload/img/progress/${list[index].id}/${list[index].prog_img}`
                  : 'javascript:void(0)',
            },
            children: ['Foto'],
          })

          docBtn = createElement({
            element: 'a',
            class: ['badge', 'badge-secondary', 'badge-pill'],
            attribute:
              list[index].prog_doc != '' && list[index].prog_doc != null
                ? {
                    href: `${BASE_URL}/upload/pdf/progress/${list[index].id}/${list[index].prog_doc}`,
                    target: 'blank_',
                  }
                : {
                    href: 'javascript:void(0)',
                  },
            children: ['PDF'],
          })

          action = createElement({
            element: 'td',
            children: [editBtn, deleteBtn, imgBtn, docBtn],
          })

          tRow = createElement({
            element: 'tr',
            children: [
              no,
              progFiscalYear,
              prgName,
              actName,
              progWeek,
              pkgdName,
              progDate,
              progPhysical,
              progFinance,
              action,
            ],
          })

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
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
