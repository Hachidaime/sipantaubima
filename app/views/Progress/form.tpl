<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'} 

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
  <div class="row">
    <div class="col-12">
      <div class="card rounded-0 m-0">
        <div class="card-header bg-gradient-navy rounded-0">
          <h3 class="card-title text-warning">{$subtitle}</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form id="my_form" role="form" method="POST">
          <input type="hidden" id="id" name="id" value="{$id}" />
          <div class="card-body">
            <div class="form-group row">
              <label
                for="prog_fiscal_year"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Tahun Anggaran
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-1 col-sm-2 col-3">
                <input
                  type="text"
                  class="form-control rounded-0 text-center"
                  id="prog_fiscal_year"
                  name="prog_fiscal_year"
                  value="{$smarty.session.FISCAL_YEAR}"
                  autocomplete="off"
                  data-toggle="datetimepicker"
                  data-target="#prog_fiscal_year"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="prog_week" class="col-lg-3 col-sm-4 col-form-label">
                Minggu Ke
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-1 col-sm-2 col-3">
                <input
                  type="number"
                  class="form-control rounded-0 text-right"
                  id="prog_week"
                  name="prog_week"
                  min="1"
                  step="1"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="prog_date" class="col-lg-3 col-sm-4 col-form-label">
                Tanggal Periode
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-2 col-sm-3 col-6">
                <input
                  type="text"
                  class="form-control rounded-0"
                  id="prog_date"
                  name="prog_date"
                  autocomplete="off"
                  data-toggle="datetimepicker"
                  data-target="#prog_date"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="prg_code" class="col-lg-3 col-sm-4 col-form-label">
                Nama Paket
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <select
                  class="custom-select rounded-0"
                  name="pkgd_id"
                  id="pkgd_id"
                >
                  <option value="">-- Pilih --</option>
                  {section inner $package_detail}
                  <option value="{$package_detail[inner].id}">
                    {$package_detail[inner].pkgd_name}
                  </option>
                  {/section}
                </select>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label
                for="prog_physical"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Progres Fisik (%)
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-1 col-sm-2 col-3">
                <input
                  type="number"
                  class="form-control rounded-0 text-right"
                  id="prog_physical"
                  name="prog_physical"
                  autocomplete="off"
                  min="0"
                  max="100"
                  step=".10"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label
                for="prog_finance"
                class="col-lg-3 col-sm-4 col-form-label"
              >
                Keuangan (Rp)
                <!-- <sup class="fas fa-asterisk text-red"></sup> -->
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-2 col-sm-3">
                <input
                  class="form-control rounded-0 text-right"
                  id="prog_finance"
                  name="prog_finance"
                  autocomplete="off"
                  placeholder="0,00"
                />
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="form-group row">
              <label for="prog_img" class="col-lg-3 col-sm-4 col-form-label">
                Foto
                <sup class="fas fa-asterisk text-red"></sup>
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <button
                  type="button"
                  class="btn bg-gradient-info btn-flat btn-upload"
                >
                  <i class="fas fa-upload mr-2"></i> Upload
                </button>

                <input
                  type="file"
                  class="file-upload sr-only"
                  accept="image/*"
                  data-id="prog_img"
                />

                <input
                  type="hidden"
                  class="input-file"
                  name="prog_img"
                  id="prog_img"
                  data-id="prog_img"
                />
                <div class="invalid-feedback"></div>

                <div id="preview_prog_img" class="mt-2" style="display: none;">
                  <a href="" data-toggle="lightbox">
                    <img src="" class="img-fluid" width="300" />
                  </a>
                </div>

                <ul
                  id="file_action_prog_img"
                  class="list-group mt-2"
                  style="display: none;"
                >
                  <li
                    class="list-group-item d-flex justify-content-between align-items-center py-1 px2"
                  >
                    <span class="filename"></span>
                    <a
                      class="badge badge-light badge-pill"
                      title="Download"
                      href=""
                      download
                      ><i class="fas fa-download"></i
                    ></a>
                  </li>
                </ul>
              </div>
            </div>

            <div class="form-group row">
              <label for="prog_doc" class="col-lg-3 col-sm-4 col-form-label">
                Dokumen Pendukung
                <span class="float-sm-right d-sm-inline d-none">:</span>
              </label>
              <div class="col-lg-9 col-sm-8">
                <button
                  type="button"
                  class="btn bg-gradient-info btn-flat btn-upload"
                >
                  <i class="fas fa-upload mr-2"></i> Upload
                </button>

                <input
                  type="file"
                  class="file-upload sr-only"
                  accept="application/pdf"
                  data-id="prog_doc"
                />

                <input
                  type="hidden"
                  class="input-file"
                  name="prog_doc"
                  id="prog_doc"
                  data-id="prog_doc"
                />

                <ul
                  id="file_action_prog_doc"
                  class="list-group mt-2"
                  style="display: none;"
                >
                  <li
                    class="list-group-item d-flex justify-content-between align-items-center py-1 px2"
                  >
                    <span class="filename"></span>
                    <a
                      class="badge badge-light badge-pill"
                      title="Download"
                      href=""
                      download
                      ><i class="fas fa-download"></i
                    ></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <!-- prettier-ignore -->
            {include 'Templates/buttons/submit.tpl'}
            {include 'Templates/buttons/form_back.tpl'}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block} 

{block 'script'}
<!-- bs-custom-file-input -->
<script src="{$smarty.const.BASE_URL}/assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- Ekko Lightbox -->
<script src="{$smarty.const.BASE_URL}/assets/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
<!-- Input Mask -->
<script src="{$smarty.const.BASE_URL}/assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>

{literal}
<script>
  $(document).ready(function () {
    let id = document.getElementById('id').value
    if (id) {
      getDetail(id)
    }

    $('#prog_fiscal_year').datetimepicker({
      viewMode: 'years',
      format: 'YYYY',
    })

    $('#prog_date').datetimepicker({
      format: 'DD/MM/YYYY',
      locale: 'id',
    })

    $('#prog_finance').inputmask({
      alias: 'numeric',
      groupSeparator: '.',
      radixPoint: ',',
      placeholder: '0,00',
      numericInput: true,
      autoGroup: true,
      autoUnmask: true,
    })

    $('.btn-upload').click(function () {
      $(this).next('.file-upload').click()
    })

    $('#btn_submit').click(() => {
      clearErrorMessage()
      save()
    })

    $('.file-upload').change(function () {
      upload(this)
    })

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
      event.preventDefault()
      $(this).ekkoLightbox({
        alwaysShowClose: false,
      })
    })

    bsCustomFileInput.init()
  })

  let getDetail = (data_id) => {
    $.post(
      `${MAIN_URL}/detail`,
      { id: data_id },
      function (res) {
        $.each(res, (id, value) => {
          $(`#${id}`).val(value)
        })

        if (res.prog_img != '') {
          const prog_img = {
            filename: res.prog_img,
            source: `${BASE_URL}/upload/img/progress/${res.id}/${res.prog_img}`,
          }
          showPreview('prog_img', prog_img)
          showFileAction('prog_img', prog_img)
        }

        if (res.prog_doc != '') {
          const prog_doc = {
            filename: res.prog_doc,
            source: `${BASE_URL}/upload/pdf/progress/${res.id}/${res.prog_doc}`,
          }
          showFileAction('prog_doc', prog_doc)
        }
      },
      'JSON'
    )
  }

  let save = () => {
    $.post(
      `${MAIN_URL}/submit`,
      $('#my_form').serialize(),
      (res) => {
        if (!res.success) {
          if (typeof res.msg === 'object') {
            $.each(res.msg, (id, message) => {
              showErrorMessage(id, message)
            })
          } else flash(res.msg, 'error')
        } else window.location = MAIN_URL
      },
      'JSON'
    )
  }
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
