{block 'pagination'}
<div
  class="d-flex flex-column flex-sm-row justify-content-between align-items-center"
  id="pagination"
>
  <span>
    <strong>Jumlah Data:</strong>
    <span id="totalRows"></span>
  </span>
  <div style="width: 150px;">
    <div class="input-group">
      <div class="input-group_prepend">
        <button class="btn bg-gradient-blue btn-flat" id="previousBtn">
          <i class="fas fa-caret-left"></i>
        </button>
      </div>
      <div style="width: 80px;">
        <select class="custom-select rounded-0" id="page"> </select>
      </div>
      <div class="input-group_append">
        <button class="btn bg-gradient-blue btn-flat" id="nextBtn">
          <i class="fas fa-caret-right"></i>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- prettier-ignore -->
{/block}

{block 'paginationJS'}
{literal}
<script>
  $(document).ready(function () {
    $('#page').change(function () {
      search(this.value)
    })

    $('#previousBtn').click(function () {
      search(this.dataset.id)
    })

    $('#nextBtn').click(function () {
      search(this.dataset.id)
    })
  })
</script>
<!-- prettier-ignore -->
{/literal}
{/block}
