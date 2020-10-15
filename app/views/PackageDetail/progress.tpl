{block 'progressForm'}
<!-- Modal -->
<div
  class="modal fade"
  id="progressModal"
  data-backdrop="static"
  data-keyboard="false"
  tabindex="-1"
  aria-labelledby="progressModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="progressModalLabel"></h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="form-group row">
          <label class="col-form-label col-sm-6" for="pkgdLastProgWeek">
            Minggu Ke
            <span class="float-sm-right d-sm-inline d-none">:</span>
          </label>
          <div class="col-sm-6">
            <input
              type="text"
              class="form-control-plaintext border px-2"
              id="pkgdLastProgWeek"
              readonly
            />
          </div>
        </div>

        <div class="form-group row">
          <label class="col-form-label col-sm-6" for="pkgdLastProgDate">
            Tanggal Terakhir Update
            <span class="float-sm-right d-sm-inline d-none">:</span>
          </label>
          <div class="col-sm-6">
            <input
              type="text"
              class="form-control-plaintext border px-2"
              id="pkgdLastProgDate"
              readonly
            />
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-6" for="pkgd_name">
            Total Progres Fisik
            <span class="float-sm-right d-sm-inline d-none">:</span>
          </label>
          <div class="col-sm-6">
            <input
              type="text"
              class="form-control-plaintext text-right border px-2"
              id="pkgdSumProgPhysical"
              readonly
            />
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-6" for="pkgd_name">
            Total Progres Keuangan
            <span class="float-sm-right d-sm-inline d-none">:</span>
          </label>
          <div class="col-sm-6">
            <input
              type="text"
              class="form-control-plaintext text-right border px-2"
              id="pkgdSumProgFinance"
              readonly
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block}
