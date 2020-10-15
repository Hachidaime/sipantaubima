<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}
{include 'Templates/pagination.tpl'}
{include 'Profile/form.tpl'}

{block name='content'}
<div class="row">
  {block 'formContent'}{/block}
  <div class="col-lg-6">
    <div class="card rounded-0">
      <div class="card-header bg-gradient-navy rounded-0">
        <h3 class="card-title text-warning">Log Aktivitas</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th class="text-right align-middle" width="40px">#</th>
              <th class="text-center align-middle" width="*">Aktivitas</th>
              <th class="text-center align-middle" width="30%">
                Tanggal Jam
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
<!-- prettier-ignore -->
{/block} 

{block 'script'}
{block 'paginationJS'}{/block}
{block 'formScript'}{/block}
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
            logDesc = null,
            logDT = null

          no = createElement({
            element: 'td',
            class: ['text-right'],
          })

          logDesc = createElement({
            element: 'td',
            children: [list[index].log_description],
          })

          logDT = createElement({
            element: 'td',
            children: [list[index].created_at],
          })

          tRow = createElement({
            element: 'tr',
            children: [no, logDesc, logDT],
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
