{block 'targetList'}
<div
  class="modal fade"
  id="targetModal"
  data-backdrop="static"
  data-keyboard="false"
  tabindex="-1"
  aria-labelledby="targetModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="targetModalLabel"></h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="row m-2">
        <div class="col-12">
          <button
            type="button"
            class="btn btn-flat bg-gradient-success"
            style="width: 100px;"
            id="targetAddBtn"
          >
            Tambah
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table table-bordered table-sm" id="targetList">
              <thead>
                <tr>
                  <th class="align-middle text-center" width="10%">
                    Minggu Ke
                  </th>
                  <th class="align-middle text-center" width="10%">
                    Tanggal Periode
                  </th>
                  <th class="align-middle text-center" width="15%">
                    Fisik<br />(%)
                  </th>
                  <th class="align-middle text-center" width="*">
                    Keuangan<br />(Rp)
                  </th>
                  <th width="20%">&nbsp;</th>
                </tr>
              </thead>
              <tbody id="result_data"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block}

{block 'targetScript'}
{literal}
<script>
  $(document).ready(function () {
    $('#targetFormModal').on('hidden.bs.modal', function (e) {
      $('#target_form').trigger('reset')
      clearErrorMessage()
    })

    $('#targetAddBtn').click(() => {
      showTargetForm()
    })

    $(document).on('click', '#targetList .btn-edit', function () {
      showTargetForm(this.dataset.id)
    })

    $(document).on('click', '#targetList .btn-delete', function () {
      deleteTarget(this.dataset.id)
    })
  })

  let targetList = document.querySelector('#targetList #result_data')

  let showTargetForm = (id = 0) => {
    $('#targetFormModal').modal('show')
    $('#targetFormModalLabel').text(id == 0 ? 'Tambah Target' : 'Ubah Target')

    const data = $(`#targetList input[data-id=${id}]`).data()
    $.each(data, (key, value) => {
      key = key
        .replace(/\.?([A-Z]+)/g, function (x, y) {
          return '_' + y.toLowerCase()
        })
        .replace(/^_/, '')

      $(`#target_form #${key}`).val(value)
    })
  }

  let searchTarget = (pkgd_id) => {
    $.post(
      `${BASE_URL}/target/search`,
      { pkgd_id: pkgd_id },
      (res) => {
        $('#targetList #emptyRow').remove()
        targetList.innerHTML = ''
        if (res.length > 0) {
          const list = res
          for (let index in list) {
            let tRow = null
            let trgWeek = null,
              trgDate = null,
              trgPhysical = null,
              trgFinance = null,
              action = null,
              detail = null

            trgWeek = createElement({
              element: 'td',
              class: ['text-right'],
              children: [list[index].trg_week],
            })

            trgDate = createElement({
              element: 'td',
              children: [list[index].trg_date],
            })

            trgPhysical = createElement({
              element: 'td',
              class: ['text-right'],
              children: [list[index].trg_physical],
            })

            trgFinance = createElement({
              element: 'td',
              class: ['text-right'],
              children: [list[index].trg_finance],
            })

            let editBtn = null,
              deleteBtn = null

            editBtn = createElement({
              element: 'a',
              class: [
                'badge',
                'badge-pill',
                'badge-warning',
                'btn-edit',
                'mr-1',
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

            action = createElement({
              element: 'td',
              children: [editBtn, deleteBtn],
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
                trgWeek,
                trgDate,
                trgPhysical,
                trgFinance,
                action,
                detail,
              ],
            })

            targetList.appendChild(tRow)
          }
        }
      },
      'JSON'
    )
  }

  let deleteTarget = (id) => {
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
          let url = `${BASE_URL}/target/remove`
          $.post(
            url,
            data,
            (res) => {
              if (res.success) {
                const data = $(`#targetList input[data-id=${id}]`).data()
                flash(res.msg, 'success')
                searchTarget(data.pkgdId)
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
</script>
{/literal} {/block}
