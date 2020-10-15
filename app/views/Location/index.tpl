<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}
{include 'Templates/pagination.tpl'}

{block name='content'}
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
              data-title="Cari Kode Lokasi"
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
              <th class="text-right align-middle" width="40px">#</th>
              <th class="text-center align-middle" width="20%">
                Kode Lokasi
              </th>
              <th class="text-center align-middle" width="*">Nama Lokasi</th>
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

    $.post(
      `${MAIN_URL}/search`,
      params,
      (res) => {
        let paging = res.info
        let list = res.list
        let tBody = document.querySelector('#result_data')
        tBody.innerHTML = ''
        for (let index in list) {
          let tRow = null,
            no = null,
            locCode = null,
            locName = null,
            action = null

          no = createElement({
            element: 'td',
            class: ['text-right'],
          })

          locCode = createElement({
            element: 'td',
            children: [list[index].loc_code],
          })

          locName = createElement({
            element: 'td',
            children: [list[index].loc_name],
          })

          let editBtn = null,
            deleteBtn = null

          action = createElement({
            element: 'td',
            children: [
              createElement({
                element: 'a',
                class: ['badge', 'badge-pill', 'badge-warning', 'mr-1'],
                attribute: {
                  href: `${MAIN_URL}/edit/${list[index].id}`,
                },
                children: ['Edit'],
              }),
              createElement({
                element: 'a',
                class: ['badge', 'badge-pill', 'badge-danger', 'btn-delete'],
                data: {
                  id: list[index].id,
                },
                attribute: {
                  href: `javascript:void(0)`,
                },
                children: ['Hapus'],
              }),
            ],
          })

          tRow = createElement({
            element: 'tr',
            children: [no, locCode, locName, action],
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
