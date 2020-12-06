{block 'detailStyle'}
<!-- Ekko Lightbox -->
<link
  rel="stylesheet"
  href="{$smarty.const.BASE_URL}/assets/plugins/ekko-lightbox/ekko-lightbox.css"
/>
<!-- prettier-ignore -->
{/block}

{block 'detailList'}
<legend>Detail</legend>
<div class="row mb-3">
  <div class="col-12">
    <button
      type="button"
      class="btn btn-flat bg-gradient-success"
      style="width: 100px;"
      id="detailAddBtn"
    >
      Tambah
    </button>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="table-responsive">
      <table class="table table-bordered table-sm" id="detailList">
        <thead>
          <tr>
            <th class="align-middle text-right" width="40px">#</th>
            <th class="align-middle text-center" width="12%">Nomor Paket</th>
            <th class="align-middle text-center" width="*">Nama Paket</th>
            <th class="align-middle text-center" width="12%">
              Pagu Anggaran Fisik (Rp)
            </th>
            <th class="align-middle text-center" width="10%">Sumber Dana</th>
            <th class="align-middle text-center" width="15%">
              Lokasi Pekerjaan
            </th>
            <th width="20%px">&nbsp;</th>
          </tr>
        </thead>
        <tbody id="result_data"></tbody>
      </table>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block}

{block 'detailJS'}
<!-- Ekko Lightbox -->
<script src="{$smarty.const.BASE_URL}/assets/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>

{literal}
<script>
  $(document).ready(function () {
    // pkgdRowEmpty()

    pkgdSearch()

    $('#detailAddBtn').click(() => {
      showDetailForm()
    })

    $(document).on('click', '.btn-progress', function () {
      showProgress(this.dataset.id)
    })

    $(document).on('click', '.btn-target', function () {
      showTarget(this.dataset.id)
    })

    $(document).on('click', '.btn-contract', function () {
      showContract(this.dataset.id)
    })

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
      event.preventDefault()
      $(this).ekkoLightbox({
        alwaysShowClose: false,
      })
    })

    $(document).on('click', '#detailList .btn-edit', function () {
      showDetailForm(this.dataset.id)
    })

    $(document).on('click', '#detailList .btn-delete', function () {
      deleteDetail(this.dataset.id)
    })
  })

  let detailList = document.querySelector('#detailList #result_data')

  let showDetailForm = (id = 0) => {
    $('#detailFormModal').modal('show')
    $('#detailFormModalLabel').text(id > 0 ? 'Ubah Paket' : 'Tambah Paket')

    const data = $(`#detailList input[data-id=${id}]`).data()
    $.each(data, (key, value) => {
      key = key
        .replace(/\.?([A-Z]+)/g, function (x, y) {
          return '_' + y.toLowerCase()
        })
        .replace(/^_/, '')

      $(`#detail_form #${key}`).val(value)
    })
  }

  let pkgdSearch = (is_save = false) => {
    $.post(
      `${BASE_URL}/packagedetail/search`,
      { pkg_id: $('#my_form #id').val() },
      (res) => {
        $('#pkg_debt_ceiling').val(res.pkg_debt_ceiling)

        if (is_save) {
          save(true)
        }

        let list = res.list

        if (list.length > 0) {
          $('#detailList #emptyRow').remove()
          detailList.innerHTML = ''

          for (let index in list) {
            let tRow = null
            let no = null,
              pkgdNo = null,
              pkgdName = null,
              pkgdDebtCeiling = null,
              pkgdSOFName = null,
              pkgdLocName = null,
              action = null,
              detail = null

            no = createElement({
              element: 'td',
              class: ['text-right'],
            })

            pkgdNo = createElement({
              element: 'td',
              children: [list[index].pkgd_no],
            })

            pkgdName = createElement({
              element: 'td',
              children: [list[index].pkgd_name],
            })

            pkgdDebtCeiling = createElement({
              element: 'td',
              class: ['text-right'],
              children: [list[index].pkgd_debt_ceiling],
            })

            pkgdSOFName = createElement({
              element: 'td',
              children: [list[index].pkgd_sof_name],
            })

            pkgdLocName = createElement({
              element: 'td',
              children: [list[index].pkgd_loc_name],
            })

            let actionBtns = null,
              contractBtn = null,
              targetBtn = null,
              progressBtn = null,
              imgBtn = null,
              editBtn = null,
              deleteBtn = null,
              expiresBtn = null

            contractBtn = createElement({
              element: 'a',
              class: ['btn', 'btn-info', 'btn-sm', 'btn-contract'],
              data: {
                id: list[index].id,
              },
              attribute: {
                href: 'javascript:void(0)',
              },
              children: ['Kontraktor'],
            })

            targetBtn = createElement({
              element: 'a',
              class: ['btn', 'btn-info', 'btn-sm', 'btn-target'],
              data: {
                id: list[index].id,
              },
              attribute: {
                href: 'javascript:void(0)',
              },
              children: ['Target'],
            })

            progressBtn = createElement({
              element: 'a',
              class: ['btn', 'btn-info', 'btn-sm', 'btn-progress'],
              data: {
                id: list[index].id,
              },
              attribute: {
                href: 'javascript:void(0)',
              },
              children: ['Progres'],
            })

            imgBtn = createElement({
              element: 'a',
              class: ['btn', 'btn-info', 'btn-sm'],
              data: {
                toggle: 'lightbox',
              },
              attribute: {
                href:
                  list[index].pkgd_last_prog_img != '' &&
                  list[index].pkgd_last_prog_img != null
                    ? `${BASE_URL}/upload/${list[index].pkgd_last_prog_img}`
                    : 'javascript:void(0)',
              },
              children: ['Foto'],
            })
            imgBtn.disabled =
              [index].pkgd_last_prog_img != '' &&
              list[index].pkgd_last_prog_img != null
                ? false
                : true

            editBtn = createElement({
              element: 'a',
              class: [
                'badge',
                'badge-pill',
                'badge-warning',
                'mr-1',
                'btn-edit',
              ],
              data: {
                id: list[index].id,
              },
              attribute: {
                href: `javascript:void(0)`,
              },
              children: ['Edit'],
            })

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

            actionBtns = createElement({
              element: 'div',
              class: ['btn-group'],
              children: [contractBtn, targetBtn, progressBtn, imgBtn],
            })

            action = createElement({
              element: 'td',
              children: [actionBtns, editBtn, deleteBtn, expiresBtn],
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
                pkgdNo,
                pkgdName,
                pkgdDebtCeiling,
                pkgdSOFName,
                pkgdLocName,
                action,
                detail,
              ],
            })

            detailList.appendChild(tRow)
          }
          reArrange('#detailList #result_data tr')
        } else pkgdRowEmpty()
      },
      'JSON'
    )
  }

  let deleteDetail = (id) => {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn bg-gradient-danger ml-2',
        cancelButton: 'btn bg-gradient-light',
      },
      buttonsStyling: false,
    })

    swalWithBootstrapButtons
      .fire({
        position: 'top',
        title: 'Apakah Anda yakin?',
        text: 'Anda tidak akan dapat mengembalikan data ini!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
      })
      .then((result) => {
        if (result.value) {
          let data = `id=${id}`
          let url = `${BASE_URL}/packagedetail/remove`
          $.post(
            url,
            data,
            (res) => {
              if (res.success) {
                flash(res.msg, 'success')
                pkgdSearch()
              } else {
                flash(res.msg, 'error')
              }
            },
            'JSON'
          )
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          flash('Hapus data batal.', 'error')
        }
      })
  }

  let pkgdRowEmpty = () => {
    detailList.innerHTML = ''
    const tCol = createElement({
      element: 'td',
      class: ['text-center'],
      attribute: {
        colspan: 7,
      },
      children: ['Data Kosong'],
    })

    const tRow = createElement({
      element: 'tr',
      attribute: {
        id: 'emptyRow',
      },
      children: [tCol],
    })

    detailList.appendChild(tRow)
  }

  let showProgress = (id) => {
    let data = $(`#detailList input[data-id=${id}]`).data()

    $('#progressModal').modal('show')
    $('#progressModalLabel').text(`Progres ${data.pkgdNo}`)

    $('#pkgdLastProgWeek').val(data.pkgdLastProgWeek)
    $('#pkgdLastProgDate').val(data.pkgdLastProgDate)
    $('#pkgdSumProgPhysical').val(`${data.pkgdSumProgPhysical} %`)
    $('#pkgdSumProgFinance').val(`Rp ${data.pkgdSumProgFinance}`)
  }

  let showContract = (pkgd_id) => {
    const data = $(`#detailList input[data-id=${pkgd_id}]`).data()

    $('#contractFormModal').modal('show')
    $('#contractFormModalLabel').text(`Kontraktor ${data.pkgdNo}`)

    $('#contract_form #pkgd_id').val(pkgd_id)
    $('#addendum_form #pkgd_id').val(pkgd_id)

    getContractDetail(pkgd_id)
  }

  let getContractDetail = (pkgd_id) => {
    $.post(
      `${BASE_URL}/contract/detail`,
      { pkgd_id: pkgd_id },
      function (res) {
        $.each(res, (id, value) => {
          $(`#contract_form #${id}`).val(value)
        })

        if (res.addendum.length > 0) {
          const addendum = res.addendum
          let idx = 0
          for (let order = 1; order <= 8; order++) {
            $(`#contract_form #add_id${order}`).val(addendum[idx].id)
            $.each(addendum[idx], (id, value) => {
              $(`#contract_form #${id}${order}`).val(value)
            })
            idx++
          }
        }
      },
      'JSON'
    )
  }

  let showTarget = (id) => {
    const data = $(`#detailList input[data-id=${id}]`).data()

    $('#targetModal').modal('show')
    $('#targetModalLabel').text(`Target ${data.pkgdNo}`)
    $('#target_form #pkgd_id').val(id)

    searchTarget(id)
  }
</script>
{/literal} {/block}
