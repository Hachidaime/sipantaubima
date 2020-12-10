<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}

{block name='content'}
<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="card rounded-0">
        <div class="card-header bg-gradient-navy rounded-0">
          <h3 class="card-title text-warning">Informasi Kegiatan</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th class="text-right align-middle" rowspan="2" width="50px">
                  #
                </th>
                <th class="text-center align-middle" rowspan="2" width="*">
                  Nama Kegiatan
                </th>
                <th class="text-center align-middle" rowspan="2" width="75px">
                  Paket Kegiatan
                </th>
                <th class="text-center align-middle" colspan="3">Indikator</th>
                <th class="text-center align-middle" rowspan="2" width="75px">
                  Kontrak Selesai
                </th>
              </tr>
              <tr>
                <th class="text-center px-0" width="50px">
                  <i class="fas fa-frown text-red"></i>
                </th>
                <th class="text-center px-0" width="50px">
                  <i class="fas fa-meh text-yellow"></i>
                </th>
                <th class="text-center px-0" width="50px">
                  <i class="fas fa-smile text-green"></i>
                </th>
              </tr>
            </thead>
            <tbody id="result_data">
              {section inner $activityInfo}
              <tr>
                <th class="text-right">{$smarty.section.inner.index+1}</th>
                <th>{$activityInfo[inner].act_name}</th>
                <th class="text-right">{$activityInfo[inner].all}</th>
                <th class="text-right">{$activityInfo[inner].red}</th>
                <th class="text-right">{$activityInfo[inner].yellow}</th>
                <th class="text-right">{$activityInfo[inner].green}</th>
                <th class="text-right">{$activityInfo[inner].finish}</th>
              </tr>
              {sectionelse}
              <tr>
                <th class="text-center" colspan="7">Data kosong ...</th>
              </tr>
              {/section}
            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block}

{block 'script'}
{literal}
<script>
  $(document).ready(function () {
    searchActivityInfo()
  })

  let displayActivityInfo = () => {
    let refresh = 5000
    setTimeout('searchActivityInfo()', refresh)
  }

  let searchActivityInfo = () => {
    $.get(
      '{/literal}{$smarty.const.BASE_URL}{literal}/activityinfo',
      (list) => {
        let tBody = document.querySelector('#result_data')
        tBody.innerHTML = ''

        for (index in list) {
          let tRow = null,
            no = null,
            actName = null,
            all = null,
            red = null,
            yellow = null,
            green = null,
            finish = null

          no = createElement({
            element: 'td',
            class: ['text-right'],
          })

          actName = createElement({
            element: 'td',
            children: [list[index].act_name],
          })

          all = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].all],
          })

          red = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].red],
          })

          yellow = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].yellow],
          })

          green = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].green],
          })

          finish = createElement({
            element: 'td',
            class: ['text-right'],
            children: [list[index].finish],
          })

          tRow = createElement({
            element: 'tr',
            children: [no, actName, all, red, yellow, green, finish],
          })

          tBody.appendChild(tRow)
        }

        reArrange('#result_data tr')
        displayActivityInfo()
      },
      'JSON'
    )
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
