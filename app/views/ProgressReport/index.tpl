<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}

{block 'content'}
<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="card rounded-0">
        <div class="card-header bg-gradient-navy rounded-0">
          <h3 class="card-title text-warning">{$subtitle}</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <!-- form start -->
          <form id="my_form" role="form" method="POST">
            <div class="form-group row">
              <label
                for="pkg_fiscal_year"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Tahun Anggaran
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-1 col-sm-2 col-3">
                <input
                  type="text"
                  class="form-control rounded-0 text-center"
                  id="pkg_fiscal_year"
                  name="pkg_fiscal_year"
                  value="{$smarty.session.FISCAL_YEAR}"
                  autocomplete="off"
                  data-toggle="datetimepicker"
                  data-target="#pkg_fiscal_year"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="prg_code" class="col-lg-3 col-sm-4 col-form-label">
                Program
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <select
                  class="custom-select rounded-0"
                  name="prg_code"
                  id="prg_code"
                >
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
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <select
                  class="custom-select rounded-0"
                  name="act_code"
                  id="act_code"
                >
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

            <div class="form-group row">
              <label for="pkgd_id" class="col-lg-3 col-sm-4 col-form-label">
                Paket
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <select
                  class="custom-select rounded-0"
                  name="pkgd_id"
                  id="pkgd_id"
                >
                  <option value="">-- Pilih --</option>
                  {section inner $packageDetail}
                  <option value="{$packageDetail[inner].id}">
                    {$packageDetail[inner].pkgd_name}
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
    </div>
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
    $('#pkg_fiscal_year').datetimepicker({
      viewMode: 'years',
      format: 'YYYY',
    })

    $('#btn_search').click(() => {
      search()
    })

    $(document).on('click', '.btn-spreadsheet', function (event) {
      spreadsheet()
    })

    // createChart()
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
            children: ['LAPORAN PERKEMBANGAN CAPAIAN KINERJA'],
          })

          title2 = createElement({
            element: 'h5',
            class: ['text-center'],
            children: ['BINA MARGA KAB. SEMARANG'],
          })

          title3 = createElement({
            element: 'h5',
            class: ['text-center', 'mb-3'],
            children: [`THN ANGGARAN: ${params.pkg_fiscal_year}`],
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
            let headNo = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '50px',
              },
              children: ['No.'],
            })

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
              children: ['Nilai Awal Kontrak (Rp)'],
            })

            let headCntValueEnd = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '10%',
              },
              children: ['Nilai Kontrak Akhir (Rp)'],
            })

            let headPkgdDebtCeiling = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '10%',
              },
              children: ['Pagu Anggaran Fisik (Rp)'],
            })

            let headWeek = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '75px',
              },
              children: ['Minggu Ke'],
            })

            let headDate = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                rowspan: 2,
                width: '100px',
              },
              children: ['Tanggal Periode'],
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
              children: [/*html*/ `Indi-<br>kator`],
            })

            let theadRow1 = createElement({
              element: 'tr',
              children: [
                headNo,
                headPackage,
                headCntValue,
                headCntValueEnd,
                headPkgdDebtCeiling,
                headWeek,
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
              children: ['Fisik<br>(%)'],
            })

            let headTrgFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '60px',
              },
              children: ['Keuangan<br>(%)'],
            })

            let headProgPhysical = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '50px',
              },
              children: ['Fisik<br>(%)'],
            })

            let headProgFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '60px',
              },
              children: ['Keuangan<br>(%)'],
            })

            let headDevnPhysical = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '50px',
              },
              children: ['Fisik<br>(%)'],
            })

            let headDevnFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '60px',
              },
              children: ['Keuangan<br>(%)'],
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

            //#region Tbody
            let tbody = createElement({
              element: 'tbody',
            })

            let labels = []
            let trgPhysical = []
            let progPhysical = []
            let trgFinance = []
            let progFinance = []

            let n = 1
            for (idx in res[index].detail) {
              let packageDetail = res[index].detail[idx]
              for (pkgd in packageDetail) {
                n = packageDetail[pkgd].pkgd_no != '' ? n : n - 1
                let bodyNo = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [packageDetail[pkgd].pkgd_no != '' ? n : `&nbsp;`],
                })
                n++

                let bodyPackage = createElement({
                  element: 'td',
                  children: [
                    packageDetail[pkgd].pkgd_no != ''
                      ? `${packageDetail[pkgd].pkgd_name}`
                      : '',
                  ],
                })

                let bodyCntValue = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].cnt_value}`],
                })

                let bodyCntValueEnd = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].cnt_value_end}`],
                })

                let bodyPkgdDebtCeiling = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].pkgd_debt_ceiling}`],
                })

                let bodyWeek = createElement({
                  element: 'td',
                  class: ['text-center'],
                  children: [`${packageDetail[pkgd].week}`],
                })
                labels.push(packageDetail[pkgd].week)

                let bodyDate = createElement({
                  element: 'td',
                  class: ['text-center'],
                  children: [`${packageDetail[pkgd].trg_date}`],
                })

                let bodyTrgPhysical = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].trg_physical}`],
                })
                trgPhysical.push(
                  Number(packageDetail[pkgd].trg_physical.replace(',', '.'))
                )

                let bodyTrgFinance = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].trg_finance_pct}`],
                })
                trgFinance.push(
                  Number(packageDetail[pkgd].trg_finance_pct.replace(',', '.'))
                )

                let bodyProgPhysical = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].prog_physical}`],
                })
                progPhysical.push(
                  Number(packageDetail[pkgd].prog_physical.replace(',', '.'))
                )

                let bodyProgFinance = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].prog_finance_pct}`],
                })
                progFinance.push(
                  Number(packageDetail[pkgd].prog_finance_pct.replace(',', '.'))
                )

                let bodyDevnPhysical = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].devn_physical}`],
                })

                let bodyDevnFinance = createElement({
                  element: 'td',
                  class: ['text-right'],
                  children: [`${packageDetail[pkgd].devn_finance_pct}`],
                })

                let bodyIndicator = createElement({
                  element: 'td',
                  class: [`bg-${packageDetail[pkgd].indicator}`],
                })

                let bodyRow = createElement({
                  element: 'tr',
                  children: [
                    bodyNo,
                    bodyPackage,
                    bodyCntValue,
                    bodyCntValueEnd,
                    bodyPkgdDebtCeiling,
                    bodyWeek,
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

            if ($('#pkgd_id').val() != '') {
              chartContainer.classList.remove('sr-only')
              let chartData = {
                id: 'lineChart',
                labels: labels,
                xLabel: 'Minggu Ke',
                yLabel: 'Persentase',
                datasets: [
                  {
                    label: 'Target Fisik',
                    backgroundColor: '#e83e8c',
                    borderColor: '#e83e8c',
                    data: trgPhysical,
                    lineTension: 0,
                    pointStyle: 'triangle',
                    pointRadius: '8',
                    pointHoverRadius: '10',
                  },
                  {
                    label: 'Realisasi Fisik',
                    backgroundColor: '#007bff',
                    borderColor: '#007bff',
                    data: progPhysical,
                    lineTension: 0,
                    pointStyle: 'rect',
                    pointRadius: '8',
                    pointHoverRadius: '10',
                  },
                  {
                    label: 'Target Keuangan',
                    backgroundColor: '#ffc107',
                    borderColor: '#ffc107',
                    data: trgFinance,
                    lineTension: 0,
                    pointStyle: 'circle',
                    pointRadius: '8',
                    pointHoverRadius: '10',
                  },
                  {
                    label: 'Realisasi Keuangan',
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    data: progFinance,
                    lineTension: 0,
                    pointStyle: 'rectRot',
                    pointRadius: '8',
                    pointHoverRadius: '10',
                  },
                ],
              }

              createChart(chartData)
            }
          }
        } else {
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
    let chartData = {
      labels: params.labels,
      datasets: params.datasets,
    }

    let chartOptions = {
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        position: 'right',
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
    }

    //-------------
    //- LINE CHART -
    //--------------
    let lineChartCanvas = $(`#canvas`).get(0).getContext('2d')
    let lineChartOptions = jQuery.extend(true, {}, chartOptions)
    let lineChartData = jQuery.extend(true, {}, chartData)

    for (let i = 0; i < params.datasets.length; i++) {
      lineChartData.datasets[i].fill = false
    }
    lineChartOptions.datasetFill = false

    let lineChart = new Chart(lineChartCanvas, {
      type: 'line',
      data: lineChartData,
      options: lineChartOptions,
    })
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
