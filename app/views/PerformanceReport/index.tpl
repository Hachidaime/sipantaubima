<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}

{block 'content'}
<div class="card rounded-0">
  <div class="card-header bg-gradient-navy rounded-0">
    <h3 class="card-title text-warning">{$subtitle}</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <!-- form start -->
    <form id="my_form" role="form" method="POST">
      <div class="form-group row">
        <label for="fiscal_year" class="col-lg-3 col-sm-4 col-form-label">
          Tahun Anggaran
          <sup class="fas fa-asterisk text-red"></sup>
          <span class="float-sm-right d-sm-inline d-none">:</span>
        </label>
        <div class="col-lg-1 col-sm-2 col-3">
          <input
            type="text"
            class="form-control rounded-0 text-center"
            id="fiscal_year"
            name="fiscal_year"
            value="{$smarty.session.FISCAL_YEAR}"
            autocomplete="off"
            data-toggle="datetimepicker"
            data-target="#fiscal_year"
          />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label for="fiscal_month" class="col-lg-3 col-sm-4 col-form-label">
          Bulan
          <sup class="fas fa-asterisk text-red"></sup>
          <span class="float-sm-right d-sm-inline d-none">:</span>
        </label>
        <div class="col-lg-1 col-sm-2 col-3">
          <input
            type="text"
            class="form-control rounded-0 text-center"
            id="fiscal_month"
            name="fiscal_month"
            value="{$smarty.now|date_format:'%m'}"
            autocomplete="off"
            data-toggle="datetimepicker"
            data-target="#fiscal_month"
          />
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label for="prg_code" class="col-lg-3 col-sm-4 col-form-label">
          Program
          <sup class="fas fa-asterisk text-red"></sup>
          <span class="float-sm-right d-sm-inline d-none">:</span>
        </label>
        <div class="col-lg-9 col-sm-8">
          <select class="custom-select rounded-0" name="prg_code" id="prg_code">
            <option value="">-- Pilih --</option>
            {section inner $program}
            <option value="{$program[inner].prg_code}">
              {$program[inner].prg_name}
            </option>
            {/section}
          </select>
          <div class="invalid-feedback"></div>
        </div>
      </div>

      <div class="form-group row">
        <label for="act_code" class="col-lg-3 col-sm-4 col-form-label">
          Kegiatan
          <sup class="fas fa-asterisk text-red"></sup>
          <span class="float-sm-right d-sm-inline d-none">:</span>
        </label>
        <div class="col-lg-9 col-sm-8">
          <select class="custom-select rounded-0" name="act_code" id="act_code">
            <option value="">-- Pilih --</option>
            {section inner $activity}
            <option value="{$activity[inner].act_code}">
              {$activity[inner].act_name}
            </option>
            {/section}
          </select>
          <div class="invalid-feedback"></div>
        </div>
      </div>
    </form>
  </div>
  <!-- /.card-body -->

  <div class="card-footer">
    <!-- prettier-ignore -->
    {include 'Templates/buttons/search.tpl'}
  </div>
</div>

<div class="card rounded-0 sr-only result-container">
  <div class="card-body"></div>
</div>

<div class="card rounded-0 sr-only chart-container">
  <div class="card-body">
    <canvas
      id="canvas"
      style="
        min-height: 400px;
        height: 400px;
        max-height: 400px;
        max-width: 100%;
      "
    ></canvas>
  </div>
</div>
<!-- prettier-ignore -->
{/block}

{block 'script'}
<!-- ChartJS -->
<script src="{$smarty.const.BASE_URL}/assets/plugins/chart.js/Chart.min.js"></script>
{literal}
<script>
  $(document).ready(function () {
    $('#fiscal_year').datetimepicker({
      viewMode: 'years',
      format: 'YYYY',
    })

    $('#fiscal_month').datetimepicker({
      viewMode: 'months',
      format: 'MM',
    })

    $('#btn_search').click(() => {
      search()
    })

    $(document).on('click', '.btn-spreadsheet', function (event) {
      spreadsheet()
    })
  })

  let search = () => {
    const data = $('#my_form').serializeArray()

    let params = {}
    $.map(data, function (n, i) {
      params[n['name']] = n['value']
    })

    $('.result-container').removeClass('sr-only')

    let resultWrapper = document.querySelector('.result-container .card-body')
    resultWrapper.innerHTML = ''
    $.post(
      `${MAIN_URL}/search`,
      params,
      (res) => {
        if (res.length > 0) {
          let downloadBtn = null,
            title1 = null,
            title2 = null,
            title3 = null

          downloadBtn = createElement({
            element: 'a',
            class: [
              'btn',
              'btn-flat',
              'bg-gradient-light',
              'btn-spreadsheet',
              'mb-3',
            ],
            attribute: {
              href: 'javascript:void(0)',
              style: 'width: 150px;',
            },
            children: [
              /*html*/ `<i class="fas fa-download mr-2"></i>
              Unduh XLS`,
            ],
          })

          title1 = createElement({
            element: 'h5',
            class: ['text-center'],
            children: ['LAPORAN CAPAIAN KINERJA BULANAN'],
          })

          title2 = createElement({
            element: 'h5',
            class: ['text-center'],
            children: ['BINA MARGA KAB. SEMARANG'],
          })

          title3 = createElement({
            element: 'h5',
            class: ['text-center', 'mb-3'],
            children: [`THN ANGGARAN: ${params.fiscal_year}`],
          })

          resultWrapper.append(downloadBtn, title1, title2, title3)

          for (index in res) {
            //#region Program
            let progLabel = createElement({
              element: 'div',
              class: ['col-lg-2', 'col-md-3', 'col-sm-4', 'col-6'],
              children: [/*html*/ `Program <span class="float-right">:</span>`],
            })

            let progValue = createElement({
              element: 'div',
              class: ['col-lg-10', 'col-md-9', 'col-sm-8', 'col-6'],
              children: [res[index].prg_name],
            })

            let progContainer = createElement({
              element: 'div',
              class: ['row'],
              children: [progLabel, progValue],
            })
            //#endregion

            //#region Activity
            let actLabel = createElement({
              element: 'div',
              class: ['col-lg-2', 'col-md-3', 'col-sm-4', 'col-6'],
              children: [
                /*html*/ `Kegiatan <span class="float-right">:</span>`,
              ],
            })

            let actValue = createElement({
              element: 'div',
              class: ['col-lg-10', 'col-md-9', 'col-sm-8', 'col-6'],
              children: [res[index].act_name],
            })

            let actContainer = createElement({
              element: 'div',
              class: ['row', 'mb-2'],
              children: [actLabel, actValue],
            })
            //#endregion

            //#region Package
            //#region Table
            //#region Thead
            //#region Thead Row 1

            let headPackage = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '*',
              },
              children: ['Paket Kegiatan'],
            })

            let headCntValue = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '10%',
              },
              children: [/* html */ `Nilai Awal Kontrak<br>(Rp)`],
            })

            let headCntValueEnd = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '10%',
              },
              children: [/* html */ `Nilai Kontrak Akhirr<br>(Rp)`],
            })

            let headPkgdDebtCeiling = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '10%',
              },
              children: [/* html */ `Pagu Anggaran<br>(Rp)`],
            })

            let headDate = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '100px',
              },
              children: ['Tanggal Periode Terakhir'],
            })

            let headTarget = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                colspan: 2,
                width: '110px',
              },
              children: ['Target'],
            })

            let headProgress = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                colspan: 2,
                width: '110px',
              },
              children: ['Realisasi'],
            })

            let headDeviation = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                colspan: 2,
                width: '110px',
              },
              children: ['Deviasi'],
            })

            let headIndicator = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '50px',
              },
              children: [`Indi-\nkator`],
            })

            let theadRow1 = createElement({
              element: 'tr',
              children: [
                headPackage,
                headCntValue,
                headCntValueEnd,
                headPkgdDebtCeiling,
                headDate,
                headTarget,
                headProgress,
                headDeviation,
                headIndicator,
              ],
            })
            //#endregion

            //#region Thead Row 2
            let headTrgPhysical = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '50px',
              },
              children: [/* html */ `Fisik<br>(%)`],
            })

            let headTrgFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '60px',
              },
              children: [/* html */ `Keuangan<br>(%)`],
            })

            let headProgPhysical = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '50px',
              },
              children: [/* html */ `Fisik<br>(%)`],
            })

            let headProgFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '60px',
              },
              children: [/* html */ `Keuangan<br>(%)`],
            })

            let headDevnPhysical = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '50px',
              },
              children: [/* html */ `Fisik<br>(%)`],
            })

            let headDevnFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '60px',
              },
              children: [/* html */ `Keuangan<br>(%)`],
            })

            let theadRow2 = createElement({
              element: 'tr',
              children: [
                headTrgPhysical,
                headTrgFinance,
                headProgPhysical,
                headProgFinance,
                headDevnPhysical,
                headDevnFinance,
              ],
            })
            //#endregion

            let thead = createElement({
              element: 'thead',
              children: [theadRow1, theadRow2],
            })
            //#endregion

            //#region Table
            let tbody = createElement({
              element: 'tbody',
            })

            // let labels = []
            let detail = res[index].detail

            for (idx in detail) {
              let bodyPackage = createElement({
                element: 'td',
                children: [
                  detail[idx].pkgd_no != '' ? `${detail[idx].pkgd_name}` : '',
                ],
              })

              let bodyCntValue = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].cnt_value}`],
              })

              let bodyCntValueEnd = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].cnt_value_end}`],
              })

              let bodyPkgdDebtCeiling = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].pkgd_debt_ceiling}`],
              })

              let bodyDate = createElement({
                element: 'td',
                children: [`${detail[idx].pkgd_last_prog_date}`],
              })

              let bodyTrgPhysical = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].trg_physical}`],
              })

              let bodyTrgFinance = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].trg_finance_pct}`],
              })

              let bodyProgPhysical = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].prog_physical}`],
              })

              let bodyProgFinance = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].prog_finance_pct}`],
              })

              let bodyDevnPhysical = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].devn_physical}`],
              })

              let bodyDevnFinance = createElement({
                element: 'td',
                class: ['text-right'],
                children: [`${detail[idx].devn_finance_pct}`],
              })

              let bodyIndicator = createElement({
                element: 'td',
                class: [`bg-${detail[idx].indicator}`],
              })

              let bodyRow = createElement({
                element: 'tr',
                children: [
                  bodyPackage,
                  bodyCntValue,
                  bodyCntValueEnd,
                  bodyPkgdDebtCeiling,
                  bodyDate,
                  bodyTrgPhysical,
                  bodyTrgFinance,
                  bodyProgPhysical,
                  bodyProgFinance,
                  bodyDevnPhysical,
                  bodyDevnFinance,
                  bodyIndicator,
                ],
              })

              tbody.appendChild(bodyRow)
            }

            //#endregion

            let table = createElement({
              element: 'table',
              class: ['table', 'table-bordered', 'table-sm'],
              attribute: {
                width: '100%',
              },
              children: [thead, tbody],
            })
            //#endregion

            let packageWrapper = createElement({
              element: 'div',
              class: ['col-12', 'table-responsive'],
              children: [table],
            })

            let package = createElement({
              element: 'div',
              class: ['row', 'mb-3'],
              children: [packageWrapper],
            })
            //#endregion
            resultWrapper.append(progContainer, actContainer, package)

            let chartContainer = document.querySelector('.chart-container')
            chartContainer.classList.add('sr-only')

            if ($('#act_code').val() != '') {
              chartContainer.classList.remove('sr-only')
              let chartData = {
                id: 'barChart',
                xLabel: 'Subtotal',
                yLabel: 'Persentase',
                labels: [
                  'Target Fisik',
                  'Realiasi Fisik',
                  'Target Keuangan',
                  'Realisasi Keuangan',
                ],
                datasets: [
                  {
                    // label: 'Subtotal',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: false,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    data: [
                      Number(detail[idx].trg_physical.replace(',', '.')),
                      Number(detail[idx].trg_finance_pct.replace(',', '.')),
                      Number(detail[idx].prog_physical.replace(',', '.')),
                      Number(detail[idx].prog_finance_pct.replace(',', '.')),
                    ],
                  },
                ],
              }
              createChart(chartData)
            }
          }
        } else {
          let chartContainer = document.querySelector('.chart-container')
          chartContainer.classList.add('sr-only')
          resultWrapper.innerHTML = /*html*/ `<h3 class="text-center">Data tidak ditemukan.</h3>`
        }
      },
      'JSON'
    )
  }

  let spreadsheet = () => {
    const data = $('#my_form').serializeArray()

    let params = {}
    $.map(data, function (n, i) {
      params[n['name']] = n['value']
    })

    $.post(
      `${MAIN_URL}/spreadsheet`,
      params,
      (res) => {
        download(res)
      },
      'JSON'
    )
  }

  let createChart = (params) => {
    console.log(params)
    var ctx = document.getElementById('canvas').getContext('2d')
    window.myBar = new Chart(ctx, {
      type: 'bar',
      data: params,
      options: {
        responsive: false,
        legend: {
          display: false,
        },
        title: {
          display: false,
        },
        scales: {
          xAxes: [
            {
              display: true,
              scaleLabel: {
                display: true,
                labelString: params.xLabel,
              },
            },
          ],
          yAxes: [
            {
              display: true,
              scaleLabel: {
                display: true,
                labelString: params.yLabel,
              },
              ticks: {
                min: 0,
                max: 100,
                stepSize: 10,
              },
            },
          ],
        },
      },
    })
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
