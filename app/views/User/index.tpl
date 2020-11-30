<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}
{include 'Templates/pagination.tpl'}

{block name='content'}
<div class="container">
  <div class="row mb-3">
    <div class="col-12">
      {include 'Templates/buttons/add.tpl'}
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
                data-title="Cari Nama User"
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
                <th class="align-middle text-right" width="40px" rowspan="2">
                  #
                </th>
                <th class="align-middle text-center" width="*" rowspan="2">
                  Nama
                </th>
                <th class="align-middle text-center" width="20%" rowspan="2">
                  Username
                </th>
                <th class="align-middle text-center" colspan="4">
                  Privilege
                </th>
                <th class="align-middle text-center" width="120px" rowspan="2">
                  &nbsp;
                </th>
              </tr>
              <tr>
                <th class="align-middle text-center px-0" width="10%">
                  Master
                </th>
                <th class="align-middle text-center px-0" width="10%">
                  Paket
                </th>
                <th class="align-middle text-center px-0" width="10%">
                  Progress Paket
                </th>
                <th class="align-middle text-center px-0" width="10%">
                  Laporan
                </th>
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
{block 'paginationJS'}{/block}
{literal}
<script>
  $(document).ready(function () {
    search()

    formTooltip('keyword', 'warning', 'top')

    $('#searchBtn').click(() => {
      search()
    })

    /* Delete Button */
    $(document).on('click', '.btn-delete', function (event) {
      deleteData($(this).data('id'))
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
        let tRow = null

        for (let index in list) {
          let tRow = null
          let no = null,
            usrName = null,
            usrUsername = null,
            usrIsMaster = null,
            usrIsPackage = null,
            usrIsProgress = null,
            usrIsReport = null,
            action = null

          no = createElement({
            element: 'td',
            class: ['text-right'],
          })

          usrName = createElement({
            element: 'td',
            children: [list[index].usr_name],
          })

          usrUsername = createElement({
            element: 'td',
            children: [list[index].usr_username],
          })

          usrIsMaster = createElement({
            element: 'td',
            class: ['text-center'],
            children: [list[index].usr_is_master == 1 ? yesText : noText],
          })

          usrIsPackage = createElement({
            element: 'td',
            class: ['text-center'],
            children: [list[index].usr_is_package == 1 ? yesText : noText],
          })

          usrIsProgress = createElement({
            element: 'td',
            class: ['text-center'],
            children: [list[index].usr_is_progress == 1 ? yesText : noText],
          })

          usrIsReport = createElement({
            element: 'td',
            class: ['text-center'],
            children: [list[index].usr_is_report == 1 ? yesText : noText],
          })

          let editBtn = null,
            deleteBtn = null

          editBtn = createElement({
            element: 'a',
            class: ['badge', 'badge-pill', 'badge-warning', 'mr-1'],
            attribute: {
              href: `${MAIN_URL}/edit/${list[index].id}`,
            },
            children: ['Edit'],
          })

          action = createElement({
            element: 'td',
            children: [editBtn],
          })

          let sessionUserId = '{/literal}{$smarty.session.USER.id}{literal}'
          if (list[index].id != sessionUserId) {
            deleteBtn = createElement({
              element: 'a',
              class: ['badge', 'badge-pill', 'badge-danger', 'btn-delete'],
              data: {
                id: list[index].id,
              },
              attribute: {
                href: `javascript:void(0)`,
              },
              children: ['Hapus'],
            })
            action.appendChild(deleteBtn)
          }

          tRow = createElement({
            element: 'tr',
            children: [
              no,
              usrName,
              usrUsername,
              usrIsMaster,
              usrIsPackage,
              usrIsProgress,
              usrIsReport,
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
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
