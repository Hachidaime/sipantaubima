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
        <label for="pkg_fiscal_year" class="col-lg-3 col-sm-4 col-form-label">
          Tahun Anggaran
          <sup class="fas fa-asterisk text-red"></sup>
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
          <sup class="fas fa-asterisk text-red"></sup>
          <span class="float-sm-right d-sm-inline d-none">:</span>
        </label>
        <div class="col-lg-9 col-sm-8">
          <select class="custom-select rounded-0" name="prg_code" id="prg_code">
            <option value="">-- Pilih --</option>
            {section inner $program}
            <option
              value="{$program[inner].prg_code}"
              data-value="{$program[inner].prg_name}"
            >
              {$program[inner].prg_code}
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
            <option
              value="{$activity[inner].act_code}"
              data-value="{$activity[inner].act_name}"
            >
              {$activity[inner].act_code}
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
<!-- prettier-ignore -->
{/block}

{block 'script'}
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
              children: ['Nilai Contract (Rp)'],
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
                width: '200px',
              },
              children: ['Target'],
            })

            let headProgress = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                colspan: 2,
                width: '200px',
              },
              children: ['Realisasi'],
            })

            let headDeviation = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                colspan: 2,
                width: '200px',
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
                width: '75px',
              },
              children: ['Fisik (%)'],
            })

            let headTrgFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '125px',
              },
              children: ['Keuangan (Rp)'],
            })

            let headProgPhysical = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '75px',
              },
              children: ['Fisik (%)'],
            })

            let headProgFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '125px',
              },
              children: ['Keuangan (Rp)'],
            })

            let headDevnPhysical = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '75px',
              },
              children: ['Fisik (%)'],
            })

            let headDevnFinance = createElement({
              element: 'th',
              class: ['text-center', 'align-middle'],
              attribute: {
                width: '125px',
              },
              children: ['Keuangan (Rp)'],
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
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
